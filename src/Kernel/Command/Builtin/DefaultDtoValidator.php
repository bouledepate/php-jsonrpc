<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Builtin;

use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Command\Data\DtoValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DefaultDtoValidator implements DtoValidatorInterface
{
    private array $errors = [];

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(CommandDTO $DTO): void
    {
        $errors = [];
        $violations = $this->validator->validate($DTO);

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        if (false === empty($errors)) {
            $this->errors = $errors;
        }
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}