<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package Bouledepate\JsonRpc\Model
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final readonly class Dataset
{
    use PropertyAccessorTrait;

    /**
     * @var array The decoded content of the JSON-RPC request.
     */
    private array $content;

    /**
     * Parses the JSON body of the HTTP request and stores the result.
     *
     * @param ServerRequestInterface $request The incoming HTTP request.
     *
     * @throws ParseErrorException If the request body is not valid JSON.
     */
    public function __construct(private ServerRequestInterface $request)
    {
        $content = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException();
        }

        $this->content = $content;
    }

    /**
     * Retrieves the content of the dataset.
     *
     * @return array An associative array containing the JSON-RPC request data.
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Checks whether the content of this dataset represents a batch request.
     *
     * A batch request is defined as an array of JSON-RPC request objects
     * where the array is numerically indexed (i.e., a JSON-RPC batch format).
     *
     * @return bool True if the content is a batch request; false otherwise.
     */
    public function isBatchRequest(): bool
    {
        return array_is_list($this->getContent());
    }
}
