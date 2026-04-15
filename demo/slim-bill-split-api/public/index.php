<?php

declare(strict_types=1);

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Handler\ErrorHandler;
use Bouledepate\JsonRpc\Handler\ErrorHandlerInterface;
use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\JsonRpcMiddleware;
use Demo\BillApi\BillService;
use Demo\BillApi\BillStore;
use Demo\BillApi\JsonRpcMethodProvider;
use DI\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$store = new BillStore(__DIR__ . '/../var/bills.json');
$service = new BillService($store);

$provider = new JsonRpcMethodProvider([
    'bill.create' => [$service, 'createBill'],
    'bill.configure' => [$service, 'configureBill'],
    'bill.addServiceCharge' => [$service, 'addServiceCharge'],
    'bill.joinByCode' => [$service, 'joinByCode'],
    'bill.addOrder' => [$service, 'addOrder'],
    'bill.getSummary' => [$service, 'getSummary'],
]);

$container->set(ResponseFactoryInterface::class, $app->getResponseFactory());
$container->set(MethodProviderInterface::class, $provider);
$container->set(ErrorHandlerInterface::class, new ErrorHandler($app->getResponseFactory()));

$app->add(new JsonRpcMiddleware($container));

$app->post('/rpc', function (ServerRequestInterface $request, ResponseInterface $response) use ($provider): ResponseInterface {
    /** @var JsonRpcRequest|null $jrpc */
    $jrpc = $request->getAttribute(JsonRpcRequest::class);

    $method = $jrpc?->getMethod()?->getName();
    if ($method === null) {
        return $response->withStatus(400);
    }

    $handlers = $provider->getHandlers();
    $params = $jrpc->getParams()?->getData() ?? [];
    $result = $handlers[$method]($params);

    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
