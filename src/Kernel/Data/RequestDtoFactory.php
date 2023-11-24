<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Data;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use WoopLeague\Kernel\Error\JsonRpc\InternalErrorException;
use WoopLeague\Kernel\Error\JsonRpc\InvalidParamsException;
use WoopLeague\Kernel\Helpers\CaseConverter;
use WoopLeague\Kernel\Validation\ValidationException;
use Yiisoft\Validator\Validator;

final readonly class RequestDtoFactory
{
    public function __construct(private Validator $validator)
    {
    }

    /**
     * @throws InternalErrorException
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws ValidationException
     */
    public function produce(string $DTOClass, array $data = []): AbstractDTO
    {
        if (class_exists($DTOClass) === false) {
            throw new InternalErrorException("Could not instantiate the DTO.");
        }

        $reflectionDto = new ReflectionClass($DTOClass);
        $dto = $reflectionDto->newInstance($data);
        $properties = $reflectionDto->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyType = $property->getType();

            // Initialize property by nullable value.
            $property->setValue($dto, null);

            if (!array_key_exists(CaseConverter::toSnakeCase($propertyName), $data)) {
                continue;
            }

            $propertyValue = $data[CaseConverter::toSnakeCase($propertyName)];
            if ($propertyType->isBuiltin()) {
                $propertyValueType = gettype($propertyValue);

                if ($propertyType->getName() === $propertyValueType) {
                    $property->setValue($dto, $propertyValue);
                    continue;
                }

                $value = $this->typecast($property, $propertyValue);
                $property->setValue($dto, $value);
            }
        }
        $this->validate($dto);
        return $dto;
    }

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    private function validate(AbstractDTO $DTO): void
    {
        $result = $this->validator->validate($DTO);

        if ($result->isValid() === false) {
            throw new ValidationException($result->getErrorMessagesIndexedByPath());
        }
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecast(ReflectionProperty $property, mixed $value): mixed
    {
        $propertyName = $property->getName();
        $requiredType = $property->getType();

        if (!$requiredType instanceof ReflectionNamedType || $requiredType->allowsNull() && $value === null) {
            return $value;
        }

        return match (BuiltinType::from($requiredType->getName())) {
            BuiltinType::StringType => $this->typecastToString($value, $propertyName),
            BuiltinType::IntegerType => $this->typecastToInteger($value, $propertyName),
            BuiltinType::FloatType => $this->typecastToFloat($value, $propertyName),
            BuiltinType::ArrayType => $this->typecastToArray($value, $propertyName),
            BuiltinType::BooleanType => $this->typecastToBoolean($value, $propertyName),
            default => $value
        };
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecastToString(mixed $value, string $property): string
    {
        if (is_array($value)) {
            $this->throwInvalidArgumentException($property, gettype($value), BuiltinType::StringType->value);
        }
        return (string)$value;
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecastToInteger(mixed $value, string $property): int
    {
        if (is_array($value)) {
            $this->throwInvalidArgumentException($property, gettype($value), BuiltinType::IntegerType->value);
        }
        if (!is_numeric($value)) {
            $this->throwInvalidArgumentException($property, BuiltinType::StringType->value, BuiltinType::IntegerType->value);
        }
        return (int)$value;
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecastToBoolean(mixed $value, string $property): bool
    {
        if (is_string($value)) {
            $lowerValue = strtolower($value);
            if ($lowerValue === 'true') return true;
            if ($lowerValue === 'false') return false;
        }

        if (!is_scalar($value)) {
            $this->throwInvalidArgumentException($property, gettype($value), BuiltinType::BooleanType->value);
        }
        return (bool)$value;
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecastToFloat(mixed $value, string $property): float
    {
        if (!is_numeric($value)) {
            $this->throwInvalidArgumentException($property, gettype($value), BuiltinType::FloatType->value);
        }
        return (float)$value;
    }

    /**
     * @throws InvalidParamsException
     */
    private function typecastToArray(mixed $value, string $property): array
    {
        if (!is_array($value)) {
            $this->throwInvalidArgumentException($property, gettype($value), BuiltinType::ArrayType->value);
        }
        return $value;
    }

    /** @throws InvalidParamsException */
    private function throwInvalidArgumentException(string $propertyName, string $type, string $expectedType): never
    {
        $message = "The property '$propertyName' requires '$expectedType' value, but '$type' was provided.";
        throw new InvalidParamsException($message);
    }
}