<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Builtin;

use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Command\Data\DtoCollectorInterface;
use JRPC\Kernel\Command\Data\DtoValidatorInterface;
use JRPC\Kernel\Command\Exception\ValidationFailedException;
use JRPC\Kernel\Exception\JRPC\InvalidParamsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class DefaultDTOFactory implements DtoCollectorInterface
{
    public function __construct(
        private SerializerInterface   $serializer,
        private DtoValidatorInterface $validator
    )
    {
    }

    /**
     * @throws InvalidParamsException|ValidationFailedException
     */
    public function collectDTO(string $dtoClass, string|array $parameters): CommandDTO
    {
        if (is_array($parameters)) {
            $parameters = $this->serializer->serialize($parameters, 'json');
        }
        try {
            $DTO = $this->serializer->deserialize($parameters, $dtoClass, 'json', $this->deserializationOptions());
            $this->validator->validate($DTO);

            if (false === $this->validator->isValid()) {
                throw new ValidationFailedException(errors: $this->validator->getErrors());
            }
        } catch (PartialDenormalizationException $e) {
            $this->handleInvalidTypesException($e);
        }

        return $DTO;
    }

    private function deserializationOptions(): array
    {
        return [
            DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true
        ];
    }

    /** @throws InvalidParamsException */
    private function handleInvalidTypesException(PartialDenormalizationException $exception): never
    {
        $errors = [];
        $exceptions = $exception->getErrors();

        foreach ($exceptions as $exception) {
            $path = $exception->getPath() ?? 'unknown';
            $message = $exception->getMessage();
            if (false === in_array('unknown', $exception->getExpectedTypes())) {
                $message = $this->formatTypeErrorMessage($exception);
            }
            $errors[$path] = $message;
        }

        throw new InvalidParamsException($errors);
    }

    private function formatTypeErrorMessage(NotNormalizableValueException $exception): string
    {
        if (null === $exception->getPath()) {
            return $exception->getMessage();
        }
        return sprintf(
            'The type must be one of `%s` (`%s` given).',
            implode(', ', $exception->getExpectedTypes()),
            $exception->getCurrentType()
        );
    }
}