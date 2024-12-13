<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Validator;

use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Model\Dataset;

/**
 * @package Bouledepate\JsonRpc\Validator
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class RequestValidator implements ValidatorInterface
{
    private const AVAILABLE_TYPES = [
        'jsonrpc' => ['string'],
        'method' => ['string'],
        'params' => ['array'],
        'id' => ['string', 'integer']
    ];

    private const REQUIRED_PROPERTIES = [
        'jsonrpc',
        'method'
    ];

    public function validate(Dataset $dataset): void
    {
        $errors = [];

        $this->validateRequiredProperties($dataset, $errors);
        $this->validatePropertyTypes($dataset, $errors);
        $this->validateJsonrpcVersion($dataset, $errors);

        if (!empty($errors)) {
            throw new InvalidRequestException(content: $errors);
        }
    }

    private function validateRequiredProperties(Dataset $dataset, array &$errors): void
    {
        foreach (self::REQUIRED_PROPERTIES as $property) {
            if (!$dataset->hasProperty($property)) {
                $errors[$property][] = sprintf('The attribute `%s` is required.', $property);
            }
        }
    }

    private function validatePropertyTypes(Dataset $dataset, array &$errors): void
    {
        foreach (self::AVAILABLE_TYPES as $property => $types) {
            if ($dataset->hasProperty($property)) {
                $actualType = gettype($dataset->getProperty($property));

                if (!in_array($actualType, $types, true)) {
                    $errors[$property][] = sprintf(
                        'The attribute `%s` must be of type: %s; %s given.',
                        $property,
                        implode(', ', $types),
                        $actualType
                    );
                }
            }
        }
    }

    private function validateJsonrpcVersion(Dataset $dataset, array &$errors): void
    {
        if ($dataset->hasProperty('jsonrpc') && $dataset->getProperty('jsonrpc') !== '2.0') {
            $errors['jsonrpc'][] = 'The `jsonrpc` attribute must be "2.0".';
        }
    }
}
