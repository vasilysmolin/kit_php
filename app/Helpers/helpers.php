<?php

if (!function_exists('respondWithToken')) {
    function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}

if (!function_exists('respondWithTokenAndEntity')) {
    function respondWithTokenAndEntity(string $token, bool $isPerson): array
    {
        return [
            'access_token' => $token,
            'is_person' => $isPerson,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}

