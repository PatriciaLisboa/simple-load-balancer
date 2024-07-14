<?php
// dummy_server.php

declare(strict_types=1);

$port = $argv[1] ?? 8080;

// Set the content type
header("Content-Type: text/plain");

// Output the response
echo "Hello World from server on port $port\n";

// Log the request
file_put_contents('php://stderr', "Received request on port $port\n");
