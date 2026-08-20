<?php

// middleware is a checkpoint that runs before the main API logic.
// simply.... it tells the client that our API response is JSON.

class JsonMiddleware
{
    public static function handle()
    {
        header("Content-Type: application/json");
    }
}