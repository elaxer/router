# Elaxer Router

Simple and fast HTTP request router.

## Installation

Via composer

```bash
composer require elaxer/router
```

## Usage

```php
<?php

use Elaxer\Router\{PatternParser\PatternParser,RoutesCollection, RoutesFactory, RoutesFinder};

require __DIR__ . '/vendor/autoload.php';

// Contains routes and methods for adding
$collection = new RoutesCollection();

// Contains methods for processing URL pattern strings
$patternParser = new PatternParser();

// Provides a method for creating routes
$routesFactory = new RoutesFactory($patternParser);

// Adding Routes
$collection->addRoute($routesFactory->createRoute(['GET'], '/', 'indexHandler'));
// If it doesn't matter what the method should be, then the first parameter must be passed null
$collection->addRoute($routesFactory->createRoute(null, '/posts/{id}', fn(string $id): string => "Post with id $id"));
// You can define a parameter in a pattern
$collection->addRoute($routesFactory->createRoute(['GET'], '/news/{id}', fn(string $id): string => "News with id $id"));
// You can define a rule for a parameter as a regular expression
$collection->addRoute($routesFactory->createRoute(['DELETE'], '/users/{id:\d+}', 'deleteUserItemHandler'));


$path = '/news/13-01-news';
$method = 'GET';

// Finding a route by HTTP request
$routesFinder = new RoutesFinder($collection, $patternParser);
$foundRoute = $routesFinder->findRoute($path, $method);

// If route is found
if ($foundRoute !== null) {
    // Retrieve the handler passed as the third argument in the Route
    $handler = $foundRoute->getHandler();
    // Extracting parameters from url path
    $params = $patternParser->extractParametersFromPath($foundRoute->getPattern(), $path);

    // Further actions with the handler...
    if (is_callable($handler)) {
        // The response will be the string "News with id 13-01-news"
        $response = call_user_func_array($handler, $params);
        echo $response;
    }
}
```

## Route naming

You can name the route by specifying it as the fourth parameter of the constructor:

```php
$route = $routesFactory->createRoute(['GET'], '/users/{id}', 'getUserHandler', 'get-user');

$collection->addRoute($route);
```

You can find a route in the router by name

```php
$route = $routesFinder->findRouteByName('get-user');
```

## Url path compilation

Using the Route::createPath method, passing parameters to it, you can create a path:

```php
$route = $routesFactory->createRoute(['GET'], '/users/{id}', 'getUserHandler');

echo $route->createPath(['id' => 25]); // will output /users/25
```
