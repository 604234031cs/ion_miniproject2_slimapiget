<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-type:application/json",true);
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';
$app = new Slim\App([
    "settings"  => [
        "determineRouteBeforeAppMiddleware" => true,
    ]
]);
$app->add(function($request, $response, $next) {
    $route = $request->getAttribute("route");

    $methods = [];

    if (!empty($route)) {
        $pattern = $route->getPattern();

        foreach ($this->router->getRoutes() as $route) {
            if ($pattern === $route->getPattern()) {
                $methods = array_merge_recursive($methods, $route->getMethods());
            }
        }
        //Methods holds all of the HTTP Verbs that a particular route handles.
    } else {
        $methods[] = $request->getMethod();
    }

    $response = $next($request, $response);


    return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
});

$app->get("/api/{id}", function($request, $response, $arguments) {
});

$app->post("/api/{id}", function($request, $response, $arguments) {
});

$app->map(["DELETE", "PATCH"], "/api/{id}", function($request, $response, $arguments) {
});

// Pay attention to this when you are using some javascript front-end framework and you are using groups in slim php
$app->group('/api', function () {
    // Due to the behaviour of browsers when sending PUT or DELETE request, you must add the OPTIONS method. Read about preflight.
    $this->map(['PUT', 'OPTIONS'], '/{user_id:[0-9]+}', function ($request, $response, $arguments) {
        // Your code here...
    });
});

$app->options('/(:name+)', function() use($app) {                  
    $response = $app->response();
    $app->response()->status(200);
    $response->header('Access-Control-Allow-Origin', '*'); 
    $response->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-authentication, X-client');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->header("Content-type:application/json",true);
 });
session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();

