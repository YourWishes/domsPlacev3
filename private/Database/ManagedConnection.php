<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Database.DatabaseType');
import('Configuration.Configuration');

/*
 * Basically a wrapper for PHP's PDO, just helps to find managed connections.
 * For information on the Database markup used by this software read into the
 * Database.VirtualDatabase.VirtualDatabase file.
 */
class ManagedConnection {
    private static $MANAGED_CONNECTIONS;
    
    public static function getAll() {
        return self::$MANAGED_CONNECTIONS;
    }
    
    public static function closeAll() {
        foreach(ManagedConnection::$MANAGED_CONNECTIONS as $connection) {
            try {
                $connection->close();
            } catch (Exception $ex) {
                $trace = $ex->getTraceAsString();
            }
        }
    }
    
    /**
     * 
     * @return ManagedConnection
     * @throws PDOException 
     */
    public static function createFromConfig() {
        $x = DatabaseType::getDefault();
        $conn = new ManagedConnection(
            getconf('DATABASE_USERNAME'),
            getconf('DATABASE_PASSWORD'), 
            $x, 
            getconf('DATABASE_HOST'), 
            getconf('DATABASE_PORT'),
            getconf('DATABASE_SCHEMA')
        );
        return $conn;
    }
    
    private static $main_connection = null;
    public static function getMainConnection() {
        if(!isset(ManagedConnection::$main_connection)) ManagedConnection::$main_connection = ManagedConnection::createFromConfig();
        return ManagedConnection::$main_connection;
    }
    
    //Instance
    private $pdo;
    private $type;
    
    /**
     * 
     * @param string $username
     * @param string $password
     * @param DatabaseType $type
     * @param string $host
     * @param string $port
     * @param string $db
     * @throws PDOException 
     */
    public function __construct($username, $password, &$type=null, $host='localhost', $port=3306, $db=null) {
        if($type === null) $type = DatabaseType::getDefault ();
        if(!($type instanceof DatabaseType)) throw new Exception('Invalid DatabaseType');
        
        if(!(ManagedConnection::$MANAGED_CONNECTIONS instanceof ArrayList)) {
            ManagedConnection::$MANAGED_CONNECTIONS = new ArrayList('ManagedConnection');
        }
        ManagedConnection::$MANAGED_CONNECTIONS->add($this);
        
        $this->type = $type;
        $this->pdo = new PDO($type->getPDO() . ':host=' . $host . ';port=' . $port . ($db == null ? '' : ';dbname=' . $db), $username, $password);
        //$this->pdo = new PDO($type->getPDO() . ':host=' . $host . ':' . $port . ($db == null ? '' : ';dbname=' . $db), $username, $password);
        unset($password);
    }
    
    public function isConnected() {return $this->pdo !== null;}
    
    public function close() {$this->pdo = null;}
    
    /**
     * 
     * @return DatabaseType
     */
    public function getDatabaseType() {return $this->type;}
    
    /**
     * 
     * @return PDO
     */
    public function getPHPDatabaseObject() {return $this->pdo;}
    
    public function executeQuery($query) {
        $res = $this->pdo->exec($query);
        if(isset($res) && is_bool($res) && $res === false) throw new Exception($this->pdo->errorInfo()[2]);
        return true;
    }
    
    /**
     * 
     * @param type $query
     * @return PDOStatement
     */
    public function prepare($query) {
        return $this->pdo->prepare($query);
    }
}