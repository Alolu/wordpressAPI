<?php

ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();

$container["jwt"] = function ($container) {
    return new StdClass;
};

$app->add(new \Slim\Middleware\JwtAuthentication([
	"attribute" => "jwt",
	"secure" => false,
	"path" => "/api",
	"passthrough" => ["/api/customer/add", "/api/customers/compare"],
    "secret" => base64_decode('w/7JdCjPIqyE2/4jW/ZVBMV8Arv+osvYmB5OVsCXS2rkTh12/v1N7nj1CFZfQafJeWZdgRpxA273tcM9YV/FXw=='),
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["jwt"] = $arguments["decoded"];
    },
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));


require '../src/routes/customers.php';
$app->run();