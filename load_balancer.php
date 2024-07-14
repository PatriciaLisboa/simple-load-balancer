<?php
// load_balancer.php
declare(strict_types=1);

class LoadBalancer {
    private array $servers;
    private string $counterFile;

    public function __construct(string $configFile) {
        $this->loadServers($configFile);
        $this->counterFile = __DIR__ . '/counter.txt';
    }

    private function loadServers(string $configFile): void {
        $jsonContent = file_get_contents($configFile);
        $this->servers = json_decode($jsonContent, true)['servers'] ?? [];
        if (empty($this->servers)) {
            throw new RuntimeException("No servers found in the configuration file.");
        }
    }

    private function getNextIndex(): int {
        $counter = 0;
        if (file_exists($this->counterFile)) {
            $counter = (int)file_get_contents($this->counterFile);
        }
        $nextIndex = $counter % count($this->servers);
        file_put_contents($this->counterFile, $counter + 1);
        return $nextIndex;
    }

    public function getNextServer(): string {
        $index = $this->getNextIndex();
        return $this->servers[$index];
    }

    public function forwardRequest(): void {
        $server = $this->getNextServer();
        echo "Forwarding to server: $server"; // Debug output

        $curl = curl_init($server);
        
        // Forward the original request method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
        
        // Forward the original headers
        $headers = getallheaders();
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
        
        // Forward the original body for POST, PUT, etc.
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
        }
        
        // Set options to return the response instead of outputting it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        
        // Execute the request
        $response = curl_exec($curl);
        
        if ($response === false) {
            http_response_code(502);
            echo "Error forwarding request: " . curl_error($curl);
            return;
        }
        
        // Separate headers and body
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        // Close cURL resource
        curl_close($curl);
        
        // Forward the response headers
        $headersArray = explode("\r\n", $responseHeaders);
        foreach ($headersArray as $header) {
            if (!empty($header) && !strncasecmp($header, 'Transfer-Encoding:', 18)) {
                header($header);
            }
        }
        
        // Output the response body
        echo $body;
    }
}
