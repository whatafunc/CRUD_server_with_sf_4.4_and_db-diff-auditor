<?php
// PHP Version
echo "PHP Version: " . PHP_VERSION . "<br> Timestamp:".time()."<br>";

// Redis Connection
// $redis = new Redis();
// try {
//     $redis->connect('localhost', 6379);
//     echo "Connection to Redis successful!"."<br>";
//     echo "Redis Version: " . $redis->info('server')['redis_version'] . "<br>";
// } catch (RedisException $e) {
//     $exceptionMessage = $e->getMessage();
//     echo "AHTUNG: Exception{$exceptionMessage}. Not glued with the Redis key value store." . PHP_EOL;
// }
echo "PHP Version: " . PHP_VERSION . "<br> Timestamp:".time()."<br>";

// MySQL Connection
$dbHost = 'localhost';
$dbPort = '3306';
$dbName = 'sf_skeleton';
$dbUser = 'root';
$dbPass = '12345678';
$charset = 'utf8mb4';

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

echo "Testing direct PDO connection...\n";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    echo "SUCCESS! PHP can connect to the MySQL database using PDO.\n";
} catch (PDOException $e) {
    echo "ERROR! Connection failed.\n";
    echo "PDOException: " . $e->getMessage() . "\n";
    echo "PHP File: " . $e->getFile() . "\n";
    echo "PHP Line: " . $e->getLine() . "\n";
}

phpinfo();