<?php
// app/Traits/ApiResponse.php
namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, $message = 'OK', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error($message = 'Error', $status = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
