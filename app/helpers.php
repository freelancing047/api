<?php
/* api success response */
if (!function_exists('apiSuccess')) :
    function apiSuccess($data = [], $message = '')
    {
        $response = array_merge($data, ['success' => 1, 'message' => $message]);

        return response()->json($response);
    }
endif;

/* api error response */
if (!function_exists('apiError')) :
    function apiError($message, $code = 500, $errorBag = [])
    {
        /* always send 200 http response with actual error code in json */
        return response()->json([
            'error' => 1,
            'message' => $message,
            'code' => $code,
            'errorBag' => $errorBag
        ], 200);
    }
endif;
