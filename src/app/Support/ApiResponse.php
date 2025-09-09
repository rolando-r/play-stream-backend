<?php

namespace App\Support;

class ApiResponse
{
    public static function success(string $key, array $data = [], int $httpStatus = 200)
    {
        $codes = self::codes();
        $code = $codes[$key]['code'] ?? 0;
        $message = $codes[$key]['message'] ?? 'Success';

        return response()->json([
            'success' => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $httpStatus);
    }

    public static function error(string $key, array $errors = [], int $httpStatus = 400)
    {
        $codes = self::codes();
        $code = $codes[$key]['code'] ?? 0;
        $message = $codes[$key]['message'] ?? 'Oops! Something went wrong';

        return response()->json([
            'success' => false,
            'code'    => $code,
            'message' => $message,
            'errors'  => $errors,
        ], $httpStatus);
    }

    private static function codes(): array
    {
        $path = resource_path('lang/codes.json');
        return json_decode(file_get_contents($path), true) ?? [];
    }
}
