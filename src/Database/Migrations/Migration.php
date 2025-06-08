<?php

namespace Lyra\Database\Migrations;

interface Migration {
    public function up();
    public function down();
}
