<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Command\Exception\ValidationFailedException;
use Kernel\Command\Interfaces\CommandDTO;
use Kernel\Command\Interfaces\CommandDTOFactory;
use Kernel\Exception\JRPC\InvalidParamsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class BuiltinCommandDTOFactory implements CommandDTOFactory
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    )
    {
    }

    /**
     * @throws InvalidParamsException|ValidationFailedException
     */
    public function collectDTO(string $dtoClass, string $params): CommandDTO
    {
        try {
            $DTO = $this->serializer->deserialize($params, $dtoClass, 'json', $this->deserializationOptions());
            $this->validateDTO($DTO);
        } catch (PartialDenormalizationException $e) {
            $errors = [];
            /** @var NotNormalizableValueException $exception */
            foreach ($e->getErrors() as $exception) {
                $path = $exception->getPath() ?? 'unknown';
                $message = $exception->getMessage();
                if (false === in_array('unknown', $exception->getExpectedTypes())) {
                    $message = $this->formatTypeErrorMessage($exception);
                }
                $errors[$path] = $message;
            }
            throw new InvalidParamsException($errors);
        }

        return $DTO;
    }

    /**
     * @throws ValidationFailedException
     */
    private function validateDTO(CommandDTO $DTO): void
    {
        $errors = [];
        $violations = $this->validator->validate($DTO);

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        if (false === empty($errors)) {
            throw new ValidationFailedException($errors);
        }
    }

    private function deserializationOptions(): array
    {
        return [
            DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true
        ];
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