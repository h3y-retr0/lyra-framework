<?php

namespace Lyra\Database;

use Lyra\Database\Drivers\DatabaseDriver;

abstract class Model {
    // $table and $primaryKey should be overwritten by user if needed.
    protected ?string $table = null;

    protected string $primaryKey = "id";

    protected array $hidden = [];

    protected array $fillable = [];

    protected array $attributes = [];

    protected bool $insertTimestamps = true;

    private static ?DatabaseDriver $driver = null;

    public static function setDatabaseDriver(DatabaseDriver $driver) {
        self::$driver = $driver;
    }

    public function __construct() {
        if (is_null($this->table)) {
            // asume it's model name.
            // therefore transform ProductOwner => 'product_owners'
            $subclass = new \ReflectionClass(static::class);
            $this->table = snake_case("{$subclass->getShortName()}s");
        }
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __sleep() {
        foreach ($this->hidden as $hide) {
            unset($this->attributes[$hide]);
        }

        return array_keys(get_object_vars($this));
    }

    protected function setAttributes(array $attributes): static {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }

        return $this;
    }

    protected function massAsign(array $attributes): static {
        /*
            Laravel's idea where you can't massAsign attributes that are not
            specified as `fillable`.
        */
        if (count($this->fillable) == 0) {
            throw new \Error("Model " . static::class . " does not have fillable attributes");
        }

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->__set($key, $value);
            }
        }
        return $this;
    }

    public function toArray(): array {
        return array_filter(
            $this->attributes,
            fn ($attr) => !in_array($attr, $this->hidden)
        );
    }

    public function save(): static {
        if ($this->insertTimestamps) {
            $this->attributes["created_at"] = date("Y-m-d H:m:s");
        }

        $databaseColumns = implode(",", array_keys($this->attributes));
        $bind = implode(",", array_fill(0, count($this->attributes), "?"));

        self::$driver->statement(
            "INSERT INTO $this->table ($databaseColumns) VALUES ($bind)",
            array_values($this->attributes)
        );

        $this->{$this->primaryKey} = self::$driver->lastInsertId();

        return $this;
    }

    public function update(): static {
        if ($this->insertTimestamps) {
            $this->attributes["updated_at"] = date("Y-m-d H:m:s");
        }

        $dbColumns = array_keys($this->attributes);
        $bind = implode(",", array_map(fn ($column) => "$column = ?", $dbColumns));
        $id = $this->attributes[$this->primaryKey];

        self::$driver->statement(
            "UPDATE $this->table SET $bind WHERE $this->primaryKey = $id",
            array_values($this->attributes)
        );

        return $this;
    }

    public function delete(): static {
        self::$driver->statement(
            "DELETE FROM $this->table WHERE $this->primaryKey = {$this->attributes[$this->primaryKey]}"
        );

        return $this;
    }

    public static function create(array $attributes): static {
        return (new static())->massAsign($attributes)->save();
    }

    public static function first(): ?static {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table ORDER BY $model->primaryKey LIMIT 1");

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    public static function firstWhere(string $column, mixed $value): ?static {
        $model = new static();
        $rows = self::$driver->statement(
            "SELECT * FROM $model->table WHERE $column = ? LIMIT 1",
            [$value]
        );

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    public static function find(int | string $id): ?static {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table WHERE $model->primaryKey = ?", [$id]);

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    public static function all(): array {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table");

        if (count($rows) == 0) {
            return [];
        }

        $models = [$model->setAttributes($rows[0])];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setAttributes($rows[$i]);
        }

        return $models;
    }

    public static function where(string $column, mixed $value): array {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table WHERE $column = ?", [$value]);

        if (count($rows) == 0) {
            return [];
        }

        $models = [$model->setAttributes($rows[0])];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setAttributes($rows[$i]);
        }

        return $models;
    }



}
