<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

use Bouledepate\JsonRpc\Exceptions\ParseErrorException;
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
     * Dataset constructor.
     *
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
}
