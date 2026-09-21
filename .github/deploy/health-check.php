<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Exercise the deployed application without exposing a review-auth bypass URL.
foreach (['/up', '/en', '/ar'] as $path) {
    $request = Request::create(rtrim(config('app.url'), '/').$path);
    $response = $kernel->handle($request);

    if ($response->getStatusCode() !== 200) {
        throw new RuntimeException("Deployment health check failed for {$path}.");
    }

    if ($path !== '/up' && ! str_contains($response->getContent(), 'data-server-rendered="true"')) {
        throw new RuntimeException("Server-rendered content is missing for {$path}.");
    }

    $kernel->terminate($request, $response);
    echo "Healthy: {$path}\n";
}
