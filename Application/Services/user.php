<?php

$app->get('/user/{id}', function (Application $app, Request $request, $id) {
    $controller = new \Controller\UserController($app);
    return $controller->dispatch($request, $id);
});

$app->get('/user/{id}', function (Application $app, Request $request, $id) {
    $controller = new \Controller\UserController($app);
    return $controller->dispatch($request, $id);
});


$app->post('/user/{id}', function (Application $app, Request $request, $id) {
    $controller = new \Controller\UserController($app);
    return $controller->dispatch($request, $id);
});
