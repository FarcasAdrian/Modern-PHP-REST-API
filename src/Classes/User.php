<?php

namespace Classes;

use Db\Database as Database;
use Exception;

class User
{
    private ?Database $database;
    private const TABLE = 'user';

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Retrieve an user by id.
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function getById(int $id): array
    {
        $this->validateId($id);

        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE id = ?';
        $statement = $this->database->executeQuery($query, 'i', $id);

        return $this->database->getRow($statement);
    }

    /**
     * Add a new user.
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function add(array $data): array
    {
        if (count($data) == 0) {
            throw new Exception('Parameter $data can not be empty.');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $types = $this->getTypes($data);

        $query = 'INSERT INTO ' . self::TABLE . ' (' . $columns . ') VALUES (' . $placeholders . ')';
        $statement = $this->database->executeQuery($query, $types, ...array_values($data));
        $insert_id = $this->database->getInsertId($statement);

        return $this->getById($insert_id);
    }

    /**
     * Update an existing user.
     * @param int $id
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function update(int $id, array $data): ?array
    {
        $this->validateId($id);

        $set_clause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $query = 'UPDATE ' . self::TABLE . ' SET ' . $set_clause . ' WHERE id = ?';

        $types = $this->getTypes($data) . 'i';
        $values = array_merge(array_values($data), [$id]);
        $statement = $this->database->executeQuery($query, $types, ...$values);

        return $statement ? $this->getById($id) : null;
    }

    /**
     * Delete a user.
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $this->validateId($id);

        $query = 'DELETE FROM ' . self::TABLE . ' WHERE id = ?';
        $statement = $this->database->executeQuery($query, 'i', $id);

        return $this->database->getTotalAffectedRows($statement) > 0;
    }

    /**
     * Retrieve all available users.
     * @return array
     * @throws Exception
     */
    public function getAll(): array
    {
        $query = 'SELECT * FROM ' . self::TABLE;
        $statement = $this->database->executeQuery($query);

        return $this->database->getAll($statement);
    }

    /**
     * User ID validation.
     * @param int $id
     * @return bool
     * @throws Exception
     */
    private function validateId(int $id): bool
    {
        if ($id < 1) {
            throw new Exception('The id should be an integer value higher than 0.');
        }

        return true;
    }

    /**
     * Return database data type for all values.
     * @param array $data
     * @return string
     */
    private function getTypes(array $data): string
    {
        return array_reduce($data, function ($types, $value) {
            if (is_int($value)) {
                return $types . 'i';
            } else if (is_float($value)) {
                return $types . 'd';
            } else {
                return $types . 's';
            }
        }, '');
    }
}
