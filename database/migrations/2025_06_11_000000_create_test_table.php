<?php

use Lyra\Database\DB;
use Lyra\Database\Migrations\Migration;

return new class() implements Migration {
    public function up() {
        DB::statement('CREATE TABLE test (id INT AUTO_INCREMENT PRIMARY KEY, test VARCHAR(255))');
    }

    public function down() {
        DB::statement('DROP TABLE test');
    }
};