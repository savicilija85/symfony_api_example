<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validate{

    private $validator;

    public function __construct(ValidatorInterface $validator){
        $this->validator = $validator;
    }

    public function validateRequest($data){
        $errors = $this->validator->validate($data);
        $response = [];
        if($errors->count() > 0){
            $response = [
                'code' => 1,
                'message' => 'Validation error',
                'error' => [
                    'field' => $errors->get(0)->getPropertyPath(),
                    'message' => $errors->get(0)->getMessage()
                ],
                'result' => null
            ];

            return $response;
        }
        return $response;

    }
}