<?php

namespace Lyra\Session;

class Session {
    protected SessionStorage $storage;

    public const FLASH_KEY = '_flash';

    public function __construct(SessionStorage $storage) {
        $this->storage = $storage;
        $this->storage->start();

        if (!$this->storage->has(self::FLASH_KEY)) {
            $this->storage->set(self::FLASH_KEY, ['old' => [], 'new' => []]);
        }
    }

    /**
     * Method added so that we can perform tests without calling explicitly __destruct() method
     *
     * @return void
     */
    public function finalize() {
        foreach ($this->storage->get(self::FLASH_KEY)['old'] as $key) {
            $this->storage->remove($key);
        }
        $this->ageFlashData();
        $this->storage->save();
    }

    public function __destruct() {
        $this->finalize();
    }

    
    public function ageFlashData() {
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['old'] = $flash['new'];
        $flash['new'] = [];
        $this->storage->set(self::FLASH_KEY, $flash);
    }

    public function flash(string $key, mixed $value) {
        $this->storage->set($key, $value);
        // Laravel algorithm to flash something in this session.
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['new'][] = $key;
        $this->storage->set(self::FLASH_KEY, $flash);

    }

    public function id(): string {
        return $this->storage->id();
    }

    public function get(string $key, $default = null) {
        return $this->storage->get($key, $default);
    }

    public function set(string $key, mixed $value) {
        return $this->storage->set($key, $value);
    }

    public function has(string $key): bool {
        return $this->storage->has($key);

    }

    public function remove(string $key) {
        return $this->storage->remove($key);
    }

    public function destroy() {
        return $this->storage->destroy();
    }
}
