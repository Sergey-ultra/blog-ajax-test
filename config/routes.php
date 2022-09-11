<?php

return [
    "/" => [
        'method' => 'GET',
        'controller' => 'Auth',
        'action' => 'loginView'
    ],
    "/signin" => [
        'method' => 'POST',
        'controller' => 'Auth',
        'action' => 'login'
    ],
    "/registration" => [
        'method' => 'GET',
        'controller' => 'Auth',
        'action' => 'registerView'
    ],
    "/signup" => [
        'method' => 'POST',
        'controller' => 'Auth',
        'action' => 'register'
    ],
    "/profile" => [
        'method' => 'GET',
        'controller' => 'Auth',
        'action' => 'profile'
    ],
    "/logout" => [
        'method' => 'GET',
        'controller' => 'Auth',
        'action' => 'logout'
    ]
];
