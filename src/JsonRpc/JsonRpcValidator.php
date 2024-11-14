<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Exceptions\InvalidRequestException;
use Bouledepate\JsonRpc\Interfaces\ValidatorInterface;
use Bouledepate\JsonRpc\Model\Dataset;

/**
 * @package Bouledepate\JsonRpc
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final class JsonRpcValidator implements ValidatorInterface
{
    /**
     * @var array<string, array<string>> Defines the expected types for each JSON-RPC property.
     */
    private const AVAILABLE_TYPES = [
        'jsonrpc' => ['string'],
        'method' => ['string'],
        'params' => ['array'],
        'id' => ['string', 'int']
    ];

    /**
     * @var array<string> Defines the required properties for a valid JSON-RPC request.
     */
    private const REQUIRED_PROPERTIES = [
        'jsonrpc',
        'method'
    ];

    /**
     * Validates the provided dataset against JSON-RPC request requirements.
     *
     * @param Dataset $dataset The dataset representing the JSON-RPC request data.
     *
     * @return void
     *
     * @throws InvalidRequestException If the dataset does not conform to the JSON-RPC specification.
     */
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

    /**
     * Validates that all required properties are present in the dataset.
     *
     * @param Dataset $dataset The dataset to validate.
     * @param array $errors The array to accumulate validation errors.
     *
     * @return void
     */
    private function validateRequiredProperties(Dataset $dataset, array &$errors): void
    {
        foreach (self::REQUIRED_PROPERTIES as $property) {
            if (!$dataset->hasProperty($property)) {
                $errors[$property][] = sprintf('The attribute `%s` is required.', $property);
            }
        }
    }

    /**
     * Validates the types of properties in the dataset.
     *
     * @param Dataset $dataset The dataset to validate.
     * @param array $errors The array to accumulate validation errors.
     *
     * @return void
     */
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

    /**
     * Validates that the 'jsonrpc' property has the correct version.
     *
     * @param Dataset $dataset The dataset to validate.
     * @param array $errors The array to accumulate validation errors.
     *
     * @return void
     */
    private function validateJsonrpcVersion(Dataset $dataset, array &$errors): void
    {
        if ($dataset->hasProperty('jsonrpc') && $dataset->getProperty('jsonrpc') !== '2.0') {
            $errors['jsonrpc'][] = 'The `jsonrpc` attribute must be "2.0".';
        }
    }
}
