<?php

declare(strict_types=1);

return [
    'name'      => $_ENV['APP_NAME'],
    'env'       => $_ENV['APP_ENVIRONMENT'],
    'debug'     => '',
    'baseUrl'   => $_ENV['APP_BASE_URL'],
    'url'       => $_ENV['APP_URL'],
    'timezone'  => 'UTC',
    'locale'    => 'en',
    'log' => [
        'display_error_details' => $_ENV['APP_DISPLAY_ERROR_DETAILS'],
        'log_errors'            => $_ENV['APP_LOG_ERRORS'],
        'log_error_details'     => $_ENV['APP_LOG_ERROR_DETAILS'],
    ],
];
