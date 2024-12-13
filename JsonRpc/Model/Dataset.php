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

    private array $content;

    public function __construct(private ServerRequestInterface $request)
    {
        $content = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException();
        }

        $this->content = $content;
    }

    public function getData(): array
    {
        return $this->content;
    }

    public function isBatchRequest(): bool
    {
        return array_is_list($this->getData());
    }
}
