<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Stack;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;

class JsonRpcResponseItem
{
    private JsonRpcRequest $request;
    private JsonRpcResponse $response;

    public function __construct(JsonRpcRequest $request, JsonRpcResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return JsonRpcRequest
     */
    public function getRequest(): JsonRpcRequest
    {
        return $this->request;
    }

    /**
     * @return JsonRpcResponse
     */
    public function getResponse(): JsonRpcResponse
    {
        return $this->response;
    }

    public function isNotificationResponse(): bool
    {
        return $this->request->isNotification();
    }
}