<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /* Validate portion of data instead of request.
    * Helpfull to vlaidate computed data in controller.
    */
    public function validateData(
        $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $validator = $this->getValidationFactory()->make(
            $data,
            $rules,
            $messages,
            $customAttributes
        );

        return $validator->validate();
    }
}
