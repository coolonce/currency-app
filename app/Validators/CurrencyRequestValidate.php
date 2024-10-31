<?php

namespace App\Validators;

use Psr\Http\Message\ServerRequestInterface as Request;

class CurrencyRequestValidate
{
    private static array $requiredParams = ['date', 'currency'];

    public static function validate(Request $request): array
    {
        $queryParams = $request->getQueryParams();

        $isValid = !empty($queryParams['date']) &&
            !empty($queryParams['currency']);

        if ($isValid) {
            return [$isValid, []];
        }

        $missingParams = [];
        foreach (self::$requiredParams as $param) {
            if (!isset($params[$param])) {
                $missingParams[] = $param;
            }
        }

        return [$isValid, $missingParams];
    }
}