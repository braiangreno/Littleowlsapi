<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Show multiples entities
     *
     * @param array $entities
     * @param int $code
    */
    public function success($entities = [], int $code = 200)
    {
        return response()->json(
            [
                'state' => 'success',
                'response' => $entities
            ],
            $code);
    }

    /**
     * Response error
     * @param int $code
    */
    public function error($message = null, int $code = 401)
    {
        return response()->json(
            [
                'state' => 'fail',
                'response' => [
                    'data' => $message
                ]
            ],
            $code);
    }
}
