<?php

namespace App\Utility;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ErrorHandler
{
    public static function handleException(Exception $e)
    {
        switch (true) {
            case $e instanceof ModelNotFoundException:
                return response()->json(['message' => 'Resource not found.'], 404);

            case $e instanceof ValidationException:
                return response()->json(['message' => 'Validation failed.', 'errors' => $e], 422);

            case $e instanceof QueryException:
                return response()->json(['message' => 'Database query failed.', 'data' => $e], 500);

            default:
                return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
