<?php

namespace src\routes;

$app->post('/auth/login', $getAuthLogin);

$app->post('/auth/resource', $getAuthResourceOwner);

$app->post('/auth/close', $closeAuth);

$app->get('/teste', $getTest)->add($rotinaAuth);
