<?php

declare(strict_types=1);

namespace Classes\Db;

use Exception;
use mysqli;
use mysqli_stmt;

class Database
{
    private ?mysqli $database;

    public function __construct()
    {
        $this->database = DatabaseMySQLi::getInstance();
    }

    /**
     * Prepare a query and execute it. This should be used on all database actions (select, insert, delete, etc..).
     * @param string $query
     * @param string $types
     * @param ...$params
     * @return mysqli_stmt|null
     * @throws Exception
     */
    public function executeQuery(string $query, string $types = '', ...$params): ?mysqli_stmt
    {
        $stmt = $this->database->prepare($query);

        if ($stmt === false) {
            throw new Exception('Failed to prepare statement: ' . $this->database->error);
        }

        if ($types && $params && !$stmt->bind_param($types, ...$params)) {
            throw new Exception('Failed to bind parameters: '. $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception('Failed to execute statement: ' . $stmt->error);
        }

        return $stmt;
    }

    /**
     * Retrieve first result for a select statement.
     * @param mysqli_stmt $statement
     * @return array
     * @throws Exception
     */
    public function getRow(mysqli_stmt $statement): array
    {
        $result = $statement->get_result();

        if ($result === false) {
            throw new Exception('Failed to get results: ' . $statement->error);
        }

        return $result->fetch_assoc() ?: [];
    }

    /**
     * Retrieve all results for a select statement.
     * @param mysqli_stmt $statement
     * @return array
     * @throws Exception
     */
    public function getAll(mysqli_stmt $statement): array
    {
        $result = $statement->get_result();

        if ($result === false) {
            throw new Exception('Failed to get result: ' . $statement->error);
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Retrieve the id of the inserted row for a statement.
     * @param mysqli_stmt $statement
     * @return int
     */
    public function getInsertId(mysqli_stmt $statement): int
    {
        return $statement->insert_id;
    }

    /**
     * Retrieve the total affected rows for a statement.
     * @param mysqli_stmt $statement
     * @return int
     */
    public function getTotalAffectedRows(mysqli_stmt $statement): int
    {
        return $statement->affected_rows;
    }
}
