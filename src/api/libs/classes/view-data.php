<?php
/**
 * A "singleton" class for sharing data between models and views for the entire life-cycle of a page.
 */
class ViewData
{
    /**
     * Static reference to the instance, only one should exist for the entire life-cycle of a page.
     * @var 
     */
    private static ?self $instance = null;

    /**
     * Here are the data stored, accessed by `getState` method.
     * @var array
     */
    private array $state;

    private function __construct()
    {
        $this->state = [];
    }

    /**
     * Static function to return the the only instance. 
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set a value in the state.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->state[$key] = $value;
    }

    /**
     * Get a value from the state.
     * @param string $key
     * @param mixed $default
     */
    public function get(string $key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }

    // Prevent cloning and unserializing
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}