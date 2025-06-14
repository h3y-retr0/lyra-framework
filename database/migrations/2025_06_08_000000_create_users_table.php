<?php

use Lyra\Database\DB;
use Lyra\Database\Migrations\Migration;

return new class() implements Migration {
    public function up() {
        DB::statement('
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255),
                password VARCHAR(255),
                created_at DATETIME,
                updated_at DATETIME NULL
            )
        ');
    }

    public function down() {
        DB::statement('DROP TABLE users');
    }
};