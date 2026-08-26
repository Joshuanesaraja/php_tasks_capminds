<?php

// Now the Router becomes responsible for this.

// We'll eventually register routes like:
// POST /api/register
// POST /api/login
// GET /api/patients
// POST /api/patients
// PUT /api/patients/{id}
// DELETE /api/patients/{id}

// HTTP Method + URL
//         ↓
// matching route
//         ↓
// middleware
//         ↓
// controller

class Router
{
    private $routes = [];
    // This is where we store all our API routes.

    public function add($method, $pattern, $handler, $middleware = [])
    // registering a route
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch($method, $uri, &$request)
    {
        foreach ($this->routes as $route) {

            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // This is important for routes containing an ID.


                // The Router checks each registered route one by one.
                foreach ($route['middleware'] as $middleware) {
                    $middleware::handle($request);
                }

                $handler = $route['handler'];
                // This tells the Router: Once you find this route, which controller and method should I execute"

                // suppose
                
                // $handler = [
                //     PatientController::class, //[0]
                //     'getAll'                  //[1]
                // ];

                $controller = new $handler[0]();
                // Create the controller object This creates: new PatientController();

                return $controller->{$handler[1]}($request, $matches);
                // 'getAll' if the request is matched 
            }
        }

        Response::json([
            'message' => 'Route not found'
        ], 404);
    }
}
