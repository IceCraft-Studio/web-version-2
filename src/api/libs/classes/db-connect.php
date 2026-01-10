<?php
/**
 * A "singleton" class for keeping a single database connection for the entire life-cycle of a page.
 */
class DbConnect
{
    /**
     * Static reference to the only instance of the class.
     * @var DbConnect
     */
    private static ?self $instance = null;

    /**
     * Here is the connection stored, accessed by `getConnection` method.
     * @var array
     */
    private bool|mysqli $connection;

    private function __construct($access)
    {
        $this->connection = mysqli_connect(
            $access->hostname, 
            $access->username, 
            $access->password,
            $access->database
        );
    }

    /**
     * Static function to return an exisiting database connection. 
     * @param object $access
     * @return bool|mysqli
     */
    public static function getConnection($access): bool|mysqli
    {
        if (self::$instance === null) {
            self::$instance = new self($access);
        }
        return self::$instance->connection;
    }

    /**
     * Static function to close an exisiting connection and make a new one. 
     * @param object $access
     * @return bool|mysqli
     */
    public static function resetConnection($access): bool|mysqli {
        if (self::$instance->connection) {
            self::$instance->connection->close();
        }
        self::$instance = new self($access);
        return self::$instance->connection;
    }

    // Prevent cloning and unserializing
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}