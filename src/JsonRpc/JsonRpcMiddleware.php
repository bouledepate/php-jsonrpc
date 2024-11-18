<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Model\Dataset;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcMiddleware extends JsonRpcBaseMiddleware
{
    /**
     * Processes an incoming server request and produces a response.
     *
     * @param ServerRequestInterface  $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler to delegate to.
     *
     * @return ResponseInterface The HTTP response.
     *
     * @throws ParseErrorException If there is a parsing error in the request.
     * @throws InvalidRequestException If the request is invalid or batch request is recognized.
     * @throws MethodNotFoundException If the requested method does not exist.
     * @throws PayloadTooLargeException If the payload size exceeds the configured limit.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
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

        if ($this->isMethodAvailable($jrpcRequest)) {
            return $this->processRequest($request, $handler, $jrpcRequest);
        }

        throw new MethodNotFoundException();
    }

    /**
     * Processes the JSON-RPC request by delegating to the request handler and formatting the response.
     *
     * @param ServerRequestInterface  $request     The incoming server request.
     * @param RequestHandlerInterface $handler     The request handler to delegate to.
     * @param JsonRpcRequest          $jrpcRequest The JSON-RPC request object.
     *
     * @return ResponseInterface The formatted JSON-RPC response.
     */
    protected function processRequest(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        JsonRpcRequest $jrpcRequest
    ): ResponseInterface {
        $request = $request->withAttribute(JsonRpcRequest::class, $jrpcRequest);
        $response = $this->formatter->formatResponse($jrpcRequest, $handler->handle($request));

        if ($jrpcRequest->isNotification()) {
            return $this->responseFactory->createResponse(204);
        }

        return $response;
    }
}
