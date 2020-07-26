<?php

namespace App\Traits;

use App\Enum\Status;

trait CustomAjaxResponse
{
    public function customErrorResponse($message, $key = null)
    {
        $message = empty($key) ? $message : [$key => $message];
        return ['status' => Status::$_ERROR, 'validation' => $message];
    }

    public function customResponse($status, $message, $data = [])
    {
        return ['status' => $status, 'data' => $data, 'message' => $message];
    }
}
