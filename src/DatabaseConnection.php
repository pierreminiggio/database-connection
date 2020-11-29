<?php

namespace PierreMiniggio\DatabaseConnection;

use PDO;
use PDOException;
use PierreMiniggio\DatabaseConnection\Exception\ConnectionException;
use PierreMiniggio\DatabaseConnection\Exception\ExecuteException;
use PierreMiniggio\DatabaseConnection\Exception\QueryException;

class DatabaseConnection
{

    const UTF_8 = 'utf8';

    private ?PDO $connection;

    public function __construct(
        private string $host,
        private string $database,
        private string $username,
        private string $password,
        private string $charset = self::UTF_8
    )
    {}

    /**
     * @throws ConnectionException
     */
    public function start(): void
    {
        try {
            $this->connection = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset,
                $this->username,
                $this->password
            );
        } catch (PDOException $e) {
            throw new ConnectionException(
                message: 'An error occured while trying to connect to database : ' . $e->getMessage(),
                previous: $e,
            );
        } 
    }

    public function stop(): void
    {
        $this->connection = null;
    }

    /**
     * @throws QueryException
     */
    public function query(string $query, array $parameters): array
    {
        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($parameters);
        } catch (PDOException $e) {
            throw new QueryException(
                message: 'An error occured while querying from the database : ' . $e->getMessage(),
                previous: $e,
            );
        }

        return $statement->fetchAll();
    }

    /**
     * @throws ExecuteException
     */
    public function exec(string $query, array $parameters): void
    {
        try {
            $statement = $this->connection->prepare($query);
        } catch (PDOException $e) {
            throw new ExecuteException(
                message: 'An error occured while executing the query : ' . $e->getMessage(),
                previous: $e,
            );
        }

        $statement->execute($parameters);
    }
}
