# Elaxer Router
Simple and fast HTTP request router. Requires PSR-7 implementation package
## Installation
Via composer
```bash
composer require elaxer/router
```
## Usage
```php
<?php

// Include autoloader of a composer
require 'vendor/autoload.php';

// Router creation instance
$router = new \Router\Router();

// Adding Routes
$router->addRoute('GET', '/', 'indexHandler');
// The type of handler can be any
$router->addRoute('GET', '/posts', function () {
    return 'Posts';
});
// We can define a parameter in a pattern
$router->addRoute('DELETE', '/posts/{id}', fn(string $id) => "Deleting post with id $id");
// You can define a rule for a parameter as a regular expression
$router->addRoute('GET', '/users/{id:\d+}', 'getUserItemHandler');

// Finding a route by HTTP request
try {
    // Variable $request is a class object that implements the RequestInterface from the psr/http-message package
    $vars = $router->findRoute($request)['vars'];
} catch (\Router\RouteNotFoundException $e) {
    // If no route matches the request
    http_response_code(404);
    exit($e->getMessage());
}
```