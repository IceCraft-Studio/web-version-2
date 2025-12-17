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
     * Here the models can write data and views can read them.
     * @var array
     */
    public readonly array $state;

    private function __construct()
    {
        $this->state = [];
    }

    /**
     * Static function to return the only instance or initialise it. 
     * @return ViewData
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Prevent cloning and unserializing
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}