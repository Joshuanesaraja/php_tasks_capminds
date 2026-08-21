<?php

function setJsonHeaders()
{
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
}