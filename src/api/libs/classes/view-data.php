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
     * Static function to return the state of the only instance. 
     * @return array
     */
    public static function getState(): array
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->state;
    }

    // Prevent cloning and unserializing
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}