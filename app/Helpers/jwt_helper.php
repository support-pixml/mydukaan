<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new UserModel();
    $query = $userModel->where('phone', $decodedToken->phone)->get();
    $result = $query->getRow();
    if (!$result) 
            throw new Exception('User does not exist for specified phone number.');
}

function getSignedJWTForUser(string $phone)
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'phone' => $phone,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration*1000,
    ];

    $jwt = JWT::encode($payload, Services::getSecretKey());
    return $jwt;
}