<?php

use App\Controller\BlogController;
use App\Controller\Api\BlogController as ApiBlogController;


return [
    [
        'uri' => "/",
        'method' => 'GET',
        'controller' => BlogController::class,
        'action' => 'index',
    ],
    [
        'uri' => "/api/post",
        'method' => 'POST',
        'controller' => ApiBlogController::class,
        'action' => 'sendPost',
    ],
];
