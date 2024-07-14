# Simple PHP Load Balancer

This repository contains a simple load balancer implemented in PHP using the Round-robin scheduling algorithm. It is designed to distribute incoming HTTP requests evenly across a set of servers.

## Features

- **Round-robin Scheduling**: Distributes requests evenly across all available servers.
- **Dynamic Server Management**: Servers are loaded from a JSON configuration file, allowing easy changes to the server pool without altering the code.
- **Request Forwarding**: Forwards HTTP methods, headers, and body content to the target server seamlessly.

## Prerequisites

- PHP 8.3 or higher
- cURL support enabled in PHP

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/PatriciaLisboa/simple-load-balancer.git

2. Navigate to the repository directory:

    ```bash
    cd simple-load-balancer

## Configuration

Edit the servers.json file to list the URLs of the servers you want to include in the load balancing pool:

```json
{
    "servers": [
        "http://localhost:8081",
        "http://localhost:8082",
        "http://localhost:8083"
    ]
}
```

## Usage

To start the load balancer, run the following command from the root of your repository:

```bash
php -S 0.0.0.0:8080 main.php
```

This will initiate the load balancer to listen for incoming requests and distribute them among the configured servers.

## Example Servers

The repository includes a dummy_server.php file that you can use to simulate a server. To run multiple instances of this server on different ports:

```bash
php -S localhost:8081 dummy_server.php 8081 &
php -S localhost:8082 dummy_server.php 8082 &
php -S localhost:8083 dummy_server.php 8083 &
```

## Testing

You can test if the requests are being forwarded to the different servers, you can use cURL:

```bash
curl http://localhost:8080
```

## License

This project is open-sourced under the GPL 3 License. See the LICENSE file for more details.
