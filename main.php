<?php
// main.php
require_once 'load_balancer.php';

$loadBalancer = new LoadBalancer('servers.json');
$loadBalancer->forwardRequest();
