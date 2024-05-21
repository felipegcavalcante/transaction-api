<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    $app->post('/transfer', Infrastructure\Http\Controllers\TransferController::class);
};
