<?php
const APP_NAME = "Deploy On Azure";
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        // Set database configuration based on environment
        if ($_ENV['APP_ENV'] === 'production') {
            $this->host = $_ENV['AZURE_DB_HOST'];
            $this->db_name = $_ENV['AZURE_DB_NAME'];
            $this->username = $_ENV['AZURE_DB_USER'];
            $this->password = $_ENV['AZURE_DB_PASSWORD'];
        } else {
            $this->host = $_ENV['DB_HOST'];
            $this->db_name = $_ENV['DB_NAME'];
            $this->username = $_ENV['DB_USER'];
            $this->password = $_ENV['DB_PASSWORD'];
        }

        $this->port = $_ENV['DB_PORT'] ?? 5432;
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
