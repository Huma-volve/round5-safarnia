<?php

namespace App\Trait;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


trait ApiValidationHandler
{
     protected function failedValidation(Validator $validator)
    {
        if($this->is('api/*')){
            $response = ApiResponse::sendResponse(422,'Validation Error',$validator->errors());
            throw new ValidationException($validator , $response); 
        }
    }
}
