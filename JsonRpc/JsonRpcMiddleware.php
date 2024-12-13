<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Model\Dataset;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcMiddleware extends JsonRpcBaseMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->validatePayloadSize($request);
            $dataset = new Dataset($request);

            if ($dataset->isBatchRequest()) {
                throw new InvalidRequestException(content: ['Batch requests are not supported']);
            }

            $this->validator->validate($dataset);

            $jrpcRequest = new JsonRpcRequest(
                id: $dataset->getProperty('id'),
                method: $dataset->getProperty('method'),
                params: $dataset->getProperty('params'),
                isNotification: !$dataset->hasProperty('id')
            );

            return $this->processRequest($request, $handler, $jrpcRequest);
        } catch (Throwable $exception) {
            return $this->errorHandler->handle($jrpcRequest ?? null, $exception);
        }
    }

    protected function processRequest(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        JsonRpcRequest $jrpcRequest
    ): ResponseInterface {
        if ($this->isMethodAvailable($jrpcRequest)) {
            $request = $request->withAttribute(JsonRpcRequest::class, $jrpcRequest);
            $response = $this->formatter->formatResponse($jrpcRequest, $handler->handle($request));

            if ($jrpcRequest->isNotification()) {
                return $this->responseFactory->createResponse(204);
            }

            return $response;
        }

        throw new MethodNotFoundException();
    }
}
