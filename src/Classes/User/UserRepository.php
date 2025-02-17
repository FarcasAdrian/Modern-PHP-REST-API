<?php

declare(strict_types=1);

namespace Classes\User;

use Exception;
use Interfaces\DatabaseInterface;
use Interfaces\EntityInterface;
use Interfaces\EntityTransformerInterface;
use Interfaces\RepositoryInterface;
use InvalidArgumentException;

class UserRepository implements RepositoryInterface
{
    private const ENTITY_NAME = 'user';

    public function __construct(private DatabaseInterface $database, private EntityTransformerInterface $entityTransformerInterface) {}

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
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

        $query = 'SELECT * FROM ' . $this->getEntityName() . ' WHERE id = ?';
        $statement = $this->database->executeQuery($query, 'i', $id);

        return $this->database->getRow($statement);
    }

    /**
     * Create a new user.
     * @param UserEntity $user
     * @return array
     * @throws Exception
     */
    public function create(EntityInterface $userEntity): array
    {
        $data = $this->entityTransformerInterface->toArray($userEntity);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $types = $this->getTypes($data);

        $query = 'INSERT INTO ' . $this->getEntityName() . ' (' . $columns . ') VALUES (' . $placeholders . ')';
        $statement = $this->database->executeQuery($query, $types, ...array_values($data));
        $insert_id = $this->database->getInsertId($statement);

        return $this->getById($insert_id);
    }

    /**
     * Update an existing user.
     * @param int $id
     * @param UserEntity $user
     * @return array|null
     * @throws Exception
     */
    public function update(int $id, EntityInterface $userEntity): ?array
    {
        $this->validateId($id);

        $data = $this->entityTransformerInterface->toArray($userEntity);
        unset($data['id']);

        if (empty($data)) {
            throw new InvalidArgumentException('Update data cannot be empty.');
        }

        $set_clause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $query = 'UPDATE ' . $this->getEntityName() . ' SET ' . $set_clause . ' WHERE id = ?';

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

        $query = 'DELETE FROM ' . $this->getEntityName() . ' WHERE id = ?';
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
        $query = 'SELECT * FROM ' . $this->getEntityName();
        $statement = $this->database->executeQuery($query);

        return $this->database->getAll($statement);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function findBy(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Search criteria cannot be empty.');
        }

        $conditions = [];
        foreach ($data as $column => $value) {
            $conditions[] = "$column = ?";
        }

        $placeholders = implode(' AND ', $conditions);
        $types = $this->getTypes($data);
        $query = 'SELECT * FROM ' . $this->getEntityName() . ' WHERE ' . $placeholders;
        $statement = $this->database->executeQuery($query, $types, ...array_values($data));

        return $this->database->getRow($statement);
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
            match ($value) {
                is_int($value) => $types . 'i',
                is_float($value) => $types . 'd',
                default => $types . 's',
            };
        }, '');
    }
}
