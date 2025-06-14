<?php

namespace Lyra\Database\Drivers;

use PDO;

/**
 * Concrete database driver implemented with PhP PDO.
 */
class PdoDriver implements DatabaseDriver {
    protected ?PDO $pdo;

    public function connect(
        string $protocol,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password
    ) {
        $dsn = "$protocol:host=$host;port=$port;dbname=$database";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function close() {
        // garbage colector will destroy the object.
        $this->pdo = null;
    }

    public function statement(string $query, array $bind = []): mixed {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
