<?php

declare(strict_types=1);

namespace Kernel\Validation;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Validator\DataSetInterface;

final readonly class ValidationData implements DataSetInterface
{
    private ?array $requestBody;
    private ?array $requestAttributes;

    public function __construct(private ServerRequestInterface $request)
    {
        $this->requestBody = is_array($this->request->getParsedBody()) ? $this->request->getParsedBody() : [];
        $this->requestAttributes = $this->request->getAttributes();
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->requestBody[$attribute] ?? $this->requestAttributes[$attribute] ?? null;
    }

    public function getData(): ?array
    {
        return array_merge($this->requestAttributes, $this->requestBody);
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->requestBody) ||
            array_key_exists($attribute, $this->requestAttributes);
    }
}