<?php

use App\Services\CurrencyRateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validators\CurrencyRequestValidate;

function registerRoutes($app, $container): void
{

    $app->get('/', function ($request, $response, $args)  {
        $response->getBody()->write("Hello, world!");
        return $response;
    });

    $app->get('/currency', function (Request $request, Response $response, $args) use ($container) {
        // Получение экземпляра валидатора
        $currencyValidator = new CurrencyRequestValidate();
        list($isValid, $missingParams) = $currencyValidator->validate($request);
        // Проверка валидности запроса
        if (!$isValid) {
            $errorResponse = [
                'code'    => 400,
                'message' => "Invalid request. Required params: " . implode(', ', $missingParams),
            ];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $queryParams = $request->getQueryParams();

        // Получение CurrencyRateService из контейнера
        $currencyService = $container->get(CurrencyRateService::class);

        $currenDate  = (new \DateTime($queryParams['date']))->format('d.m.Y');

        // Получение кэшированного курса
        $data = $currencyService->getRateWithDifference(
            $queryParams['date'],
            $queryParams['currency'],
            $queryParams['baseCurrency'] ?? 'RUB'
        );

        // Возврат ответа с курсом
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    });
}