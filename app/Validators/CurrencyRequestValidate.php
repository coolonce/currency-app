<?php

namespace App\Validators;

use Psr\Http\Message\ServerRequestInterface as Request;

class CurrencyRequestValidate
{
    public static function validate(Request $request): bool
    {
        $queryParams = $request->getQueryParams();

        return !empty($queryParams['date']) && !empty($queryParams['currency']) && !empty($queryParams['baseCurrency']);
    }
}