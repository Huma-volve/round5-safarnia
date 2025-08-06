<?php

namespace App\Helpers;

class Helpers 
{
    static function createToken($user , $tokenName)
    {
        return $user->createToken($tokenName)->plainTextToken;
    }
}