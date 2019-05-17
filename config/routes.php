<?php
/**
 * Routing configurations - http://www.slimframework.com/docs/v3/objects/router.html
 */

$app->get('/', \App\Controllers\BaseController::class . ':index')
    ->setName('home');

$app->get('/hello/{name}', \App\Controllers\BaseController::class . ':greetings')
    ->setName('greetings');