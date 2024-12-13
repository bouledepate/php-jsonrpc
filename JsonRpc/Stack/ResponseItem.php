<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Stack;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;

class ResponseItem
{
    private JsonRpcRequest $request;
    private JsonRpcResponse $response;

    public function __construct(JsonRpcRequest $request, JsonRpcResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): JsonRpcRequest
    {
        return $this->request;
    }

    public function getResponse(): JsonRpcResponse
    {
        return $this->response;
    }

    public function isNotificationResponse(): bool
    {
        return $this->request->isNotification();
    }
}