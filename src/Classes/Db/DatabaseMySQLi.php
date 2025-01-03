<?php

declare(strict_types=1);

namespace Classes\Db;

use Exception;
use mysqli;
use mysqli_sql_exception;

class DatabaseMySQLi
{
    private static ?mysqli $instance = null;

    private function __construct() {}

    public function __destruct()
    {
        self::closeConnection();
    }

    /**
     * Create the database instance only if it's not created yet and return it.
     * @return mysqli|null
     */
    public static function getInstance(): ?mysqli
    {
        if (self::$instance === null) {
            self::connect();
        }

        return self::$instance;
    }

    /**
     * Create the connection with database.
     * @throws mysqli_sql_exception
     * @return void
     */
    private static function connect(): void
    {
        try {
            self::$instance = new mysqli($_ENV['DB_HOSTNAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], (int) $_ENV['DB_PORT'], $_ENV['DB_SOCKET']);

            if (self::$instance->connect_error) {
                throw new mysqli_sql_exception("Connection failed: " . self::$instance->connect_error);
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            throw new mysqli_sql_exception("Database connection error: " . $exception->getMessage());
        }
    }

    /**
     * Close the database connection if it's opened.
     * @return void
     */
    public static function closeConnection(): void
    {
        if (self::$instance !== null) {
            self::$instance->close();
            self::$instance = null;
        }
    }
}
