<?php

namespace Lyra\Database;

class DB {
    public static function statement(string $query, array $bind = []) {
        return db()->statement($query, $bind);
    }
}
