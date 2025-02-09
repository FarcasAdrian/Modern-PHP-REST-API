<?php

declare(strict_types=1);

namespace Classes\Db;

use Exception;
use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

final class DatabaseMySQLType extends AbstractTransactionalDatabaseType
{
    public static function getInstance(): ?self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function connect(): void
    {
        try {
            $this->databaseInstance = new mysqli($_ENV['DB_HOSTNAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], (int) $_ENV['DB_PORT'], $_ENV['DB_SOCKET']);

            if ($this->databaseInstance->connect_error) {
                throw new mysqli_sql_exception("Connection failed: " . $this->databaseInstance->connect_error);
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            throw new mysqli_sql_exception("Database connection error: " . $exception->getMessage());
        }
    }

    protected function closeConnection(): void
    {
        if ($this->databaseInstance !== null) {
            $this->databaseInstance->close();
            $this->databaseInstance = null;
        }
    }

    public function prepare(string $query): ?mysqli_stmt
    {
        return $this->databaseInstance->prepare($query);
    }

    public function beginTransaction(): void
    {
        $this->databaseInstance->begin_transaction();
    }

    public function commit(): void
    {
        $this->databaseInstance->commit();
    }

    public function rollBack(): void
    {
        $this->databaseInstance->rollback();
    }
}
