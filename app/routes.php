<?php

use App\Services\CurrencyRateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validators\CurrencyRequestValidate;
function registerRoutes($app): void
{
    $app->get('/currency', function (Request $request, Response $response, $args) {
        // Получение экземпляра валидатора
        $currencyValidator = new CurrencyRequestValidate();

        // Проверка валидности запроса
        if (!$currencyValidator->validate($request)) {
            return $response->withStatus(400, 'Bad Request');
        }

        // Получение CurrencyRateService из контейнера
        $currencyService = $this->get(CurrencyRateService::class);

        // Получение кэшированного курса
        $rate = $currencyService->getCachedRate();

        // Возврат ответа с курсом
        return $response->withBody($rate);
    });
}