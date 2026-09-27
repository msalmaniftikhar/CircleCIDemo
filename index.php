<?php

// Simple PHP application for CircleCI pipeline testing

$appName = "CircleCI Demo";
$message = "Hello World";

echo "=== {$appName} ===" . PHP_EOL;
echo $message . PHP_EOL;
echo "PHP Version: " . PHP_VERSION . PHP_EOL;
echo "Pipeline test completed successfully!" . PHP_EOL;
