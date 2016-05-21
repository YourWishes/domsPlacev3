<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Database.VirtualDatabase.VirtualTable');
import('Database.VirtualDatabase.VirtualChange.VirtualChange');
import('Database.VirtualDatabase.VirtualChange.onVirtualChange');

/*
 * Hello! And you have taken your first step into a bigger and better world of
 * Database management, setup and usage.
 * 
 * Before we begin we need to do an overiew of the workflow.
 * Create a Virtual Databasing Environment
 * Push Virtual Databasing Environment to a Physical Databasing such as MySQL
 * Pull and Push between the virtual and physical environment
 * 
 * The Virtual Database environment allows you to create exactly what you want
 * to be on the physical database in a standardised and programatically friendly
 * manner, then finalize and set the schemes to the physical database.
 * 
 * VirtualDatabases can work in one of three ways, the first being a completely
 * virtual non affecting way, great for pulling information or testing data,
 * they can work in a directly physical way, such as actual database changing
 * and manipulation on the fly, or finally they can work in a commitable manner,
 * such as to do all the processing on the virtual database and requiring a 
 * commit before pushing to the physical.
 * 
 * So to recap, the three main types of virtual databases we do are:
 *  Virtual
 *  Physical
 *  Comittable
 * 
 * Below are some examples, they each continue from their previous section.
 * 
 * ////Some Examples////
 * 
 * //Creating a Database (Virtual)
 * $mydb = new VirtualDatabase('MyDatabase');
 * 
 * //Creating a Database (Physical)
 * $mydb = new VirtualDatabase('MyDatabase');
 * $mydb->setHandle(ManagedConnection::createFromConfig());
 * $mydb->autoCommit(true);
 * 
 * //Creating a Database (Comittable)
 * $mydb = new VirtualDatabase('MyDatabase');
 * $mydb->setHandle(ManagedConnection::createFromConfig());
 * 
 * 
 * //Creating a table (Virtual)
 * ...
 * $mytable = new VirtualTable('Videos');
 * $field = new VirtualField('id', VirtualFieldType::$INTEGER);
 * $field->auto_increment = true;
 * $mytable->addField($field);
 * 
 * //Creating a table (Physical)
 * ...
 * $mytable = new VirtualTable('Videos');
 * $field = new VirtualField('id', VirtualFieldType::$INTEGER);
 * $field->is_nullable = false;
 * $field->auto_increment = true;
 * $field->max_length = 11;
 * $mytable->addField($field);
 * 
 * //Creating a table (Comittable)
 * ...
 * $mytable = new VirtualTable('Videos');
 * $field = new VirtualField('id', VirtualFieldType::$INTEGER);
 * $field->is_nullable = false;
 * $field->auto_increment = true;
 * $field->max_length = 11;
 * $mytable->addField($field);
 * $mydb->commitChanges();
 * 
 * 
 * //Changing a table (Virtual)
 * $field = new VirtualField('name', VirtualFieldType::$VARCHAR);
 * $field->default_value = null;
 * $mytable->addField($field);
 * 
 * //Changing a table (Physical)
 * $field = new VirtualField('name', VirtualFieldType::$VARCHAR);
 * $field->max_length = 64;
 * $field->default_value = null;
 * $mytable->addField($field);
 * 
 * //Changing a table (Comittable)
 * $field = new VirtualField('name', VirtualFieldType::$VARCHAR);
 * $field->max_length = 64;
 * $field->default_value = null;
 * $mytable->addField($field);
 * $mydb->commitChanges();
 * 
 * So far this should be fairly self explanitory, except there's no massive
 * difference between the types, maybe a few lines, now let's move onto the 
 * benefits of the types.
 * 
 * Let's start by creating some data, asume we have a Table in our database like
 * this:
 * 
 * +------+------------------+
 * |  id  |       name       |
 * +------+------------------+
 * | 0    | "My Video"       |
 * | 1    | "My Other Video" |
 * | 2    | "My Third Video" |
 * +------+------------------+
 * 
 * Now imagine we wanted to delete only Video #1, but have forgot to constrain
 * our delete query, like so:
 * 
 * //Failed Delete (Virtual)
 * $getQuery = $mytable->getInformation();
 * //Forgot to remove these comments, where we'd normally filter.
 * //$myConstraint = new VirtualConstraint();
 * //$myConstraint->table = $mytable;
 * //$myConstraint->field = $mytable->getField('id');
 * //$myConstraint->value = 2;
 * //$getQuery->addConstraint($myConstraint);
 * $results = $getQuery->fetch();
 * foreach($results as $result) {
 *      $mytable->deleteEntry($result);
 * }
 * 
 * The above code has removed all entries from the database, since the result
 * constraints were not enforced properly. If this were to happen on a physical
 * database the results could be catastrophic, deleting so much data that a full
 * backup restoration could be required.
 * 
 * However, when using either a full virtual or comittable database we can help
 * to prevent this, since no data is changed server side until we commit it!
 * 
 * Things to note:
 * Virtual Databases are just that, a virtual database that only lives in its 
 * variable scope.
 * 
 * Physical Databases won't take any changes from a VirtualDatabase until it's
 * been set to autoCommit.
 * 
 * Comittable Databases won't show errors relating to the Physical Database
 * until the commit has been made... An example would be trying to create an
 * existing database.
 * 
 * 
 * 
 * The next tutorial will cover using the VirtualDatabasing environment to do
 * exactly what you would need from the normal databasing method. For the sakes
 * of testing we are going to use a completely virtual database (no MySQL etc).
 * 
 * $db = new VirtualDatabase('Video Store');
 * 
 * $usersTable = $db->addTable(new VirtualTable('Users'));
 * $usersTable->addField(new VirtualField('id', VirtualFieldType::$INTEGER, array(
 *      "nullable" => false,
 *      "auto_increment" => true
 * )));
 * $usersTable->addField(new VirtualField('name', VirtualFieldType::$TEXT, array(
 *      "nullable" => false
 * )));
 * 
 * $videosTable = $db->addTable(new VirtualTable('Videos'));
 * $videosTable->addField(new VirtualField('id', VirtualFieldType::$INTEGER, array(
 *      "nullable" => false,
 *      "auto_increment" => true
 * )));
 * $videosTable->addField(new VirtualField('name', VirtualFieldType::$VARCHAR, array(
 *      "nullable" => false,
 *      "max_length" => 11
 * )));
 * $uploaderField = $videosTable->addForeignField('uploader', $usersTable->getField('id'));
 * $uploaderField->nullable = true;//Stops foreign constraints
 * $uploaderField->default_value = null;
 * 
 * //Database is "Setup", Let's create some users.
 * $admin = $usersTable->createEntry(array(
 *      'id' => 1,
 *      'name' => 'Administrator'
 * ));
 * $user = $usersTable->createEntry(array('name' => 'John Smith'));
 * 
 * //Now we can create some videos
 * $video0 = $videosTable->createEntry(array(
 *      'name' => 'My Video',
 *      'uploader' => $admin
 * ));
 * 
 * $video1 = $videosTable->createEntry(array(
 *      'name' => 'Main Video'
 * ));
 * 
 * $video2 = $videosTable->createEntry(array(
 *      'name' => 'User Video',
 *      'uploader' => $user
 * ));
 * 
 * 
 * There we have made 3 videos very easily, all database connections and in any
 * scope you prefer! Now let's try pulling that data back.
 * 
 * $videoTable = $mydb->getTable('Videos');
 * $getQuery = $videoTable->createGetQuery();
 * //By Default the getQuery has no constraints, at all, be careful!
 * $results = $getQuery->fetch();
 * var_dump($results);
 * die();
 * 
 */
class VirtualDatabase extends EventListener implements JsonSerializable {
    /*
     * Static Methods
     */
    
    //TODO: Valid Database Names
    public static function isValidDatabaseName($name) {
        return true;
    }
    
    public static function convertDateTimeToSQL($datetime) {
        return date_format($datetime, 'Y-m-d H:i:s');
    }
    
    /**
     * 
     * @param type $name
     * @param ManagedConnection $conn
     * @return \VirtualDatabase
     * @throws Exception
     */
    public static function getDatabase($name, $conn) {
        if(!($conn instanceof ManagedConnection)) throw new Exception('Invalid Connection.');
        import('Database.VirtualDatabase.VirtualQuery.Database.DoesDatabaseExistQuery');
        
        //Confirm existance
        $does_database_exist_query = new DoesDatabaseExistQuery($name);
        $result = $does_database_exist_query->fetch($conn);
        if(!$result) throw new Exception('Database does not exist.');
        
        //Gets a Physical database as a virtual database.
        $db = new VirtualDatabase($name);
        $db->new = false;
        
        //Get all associated tables.
        import('Database.VirtualDatabase.VirtualQuery.Database.GetTableNamesQuery');
        $get_table_names_query = new GetTableNamesQuery($db);
        $table_names = $get_table_names_query->fetch($conn);
        
        VirtualTable::getTables($db, $table_names, $conn);
        $db->setHandle($conn);
        
        return $db;
    }
    
    /*
     * Instance
     */
    private $name;
    
    //Internal Database Arrays
    private $tables;
    private $changes;
    
    private $new = true;//Set this to false if this is an existing db.
    private $auto_commit = false;
    
    private $handle;
    
    public function __construct($name) {
        parent::__construct(onVirtualChange::$VIRTUAL_CHANGE_EVENT_TYPE);
        
        if(!VirtualDatabase::isValidDatabaseName($name)) throw new Exception('Invalid Database Name');
        $this->name = $name;
        
        $this->tables = new ArrayList('VirtualTable');
        $this->changes = new ArrayList('VirtualChange');
        
        //VirtualDatabases are uh "messy" at best, setting this to false by any means will not create a database create change event.
        $this->new = true;
        
        $this->register();
    }
    
    public function getName() {return $this->name;}
    public function getHandle() {return $this->handle;}
    
    public function setAutoCommit($auto) {$this->auto_commit = $auto;}
    
    public function isAutoCommitting() {return $this->auto_commit;}
    
    /**
     * 
     * @param string $name
     * @return VirtualTable
     */
    public function getTable($name) {
        $name = strtolower($name);
        foreach($this->tables as $table) {
            if(!($table instanceof VirtualTable)) continue;
            if(strtolower($table->getName()) != $name) continue;
            return $table;
        }
        return null;
    }
    
    public function addChange($change) {
        if(!($change instanceof VirtualChange)) throw new Exception('Invalid Change type');
        $this->changes->add($change);
        $this->checkAutoCommit();
    }
    
    /**
     * Sets the handle to use when commiting changes to a physical database.
     * 
     * @param ManagedConnection $managed_connection
     * @return \VirtualDatabase
     * @throws Exception
     */
    public function setHandle(&$managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception('Invalid ManagedConnection.');
        $this->changes = new ArrayList('VirtualChange');
        $this->handle = $managed_connection;
        
        if($this->new) {
            import('Database.VirtualDatabase.VirtualChange.Database.DatabaseCreatedChange');
            $change = new DatabaseCreatedChange($this);
            $event = new onVirtualChange($change);
            $result = $event->fire();

            $this->addChange($change);
            $this->new = false;
            if(isset($result) && is_bool($result) && !$result) return;
        }
        
        return $this;
    }
    
    public function hasHandle() {return isset($this->handle) && $this->handle instanceof ManagedConnection;}
    
    /**
     * Adds a table to the database.
     * 
     * @param VirtualTable $table
     * @return VirtualTable
     */
    public function addTable($table) {
        if(!($table instanceof VirtualTable)) throw new Exception('Invalid Table');
        if(isset($table->database)) throw new Exception('Table is already attached to a database.');
        $table->database = $this;
        
        //Create our VirtualChange for this commitment.
        import('Database.VirtualDatabase.VirtualChange.Table.TableCreatedChange');
        $change = new TableCreatedChange($table);
        $event = new onVirtualChange($change);
        $result = $event->fire();
        if(isset($result) && is_bool($result) && !$result) return;
        
        $this->addChange($change);
        if(!$this->tables->add($table)) throw new Exception('Failed to addd the table to the database!');
        
        return $table;
    }
    
    public function commitChanges() {
        if(!$this->hasHandle()) throw new Exception('Handle not setup! Connect to a physical database and set the handle!');
        foreach($this->changes->createCopy() as $change) {
            if(!($change instanceof VirtualChange)) continue;
            try {
                $change->commit($this->handle);
            } catch(Exception $e) {
                throw new Exception("Invalid Commit!", 0, $e);
            }
            $this->changes->remove($change);
        }
    }
    
    public function drop() {
        import('Database.VirtualDatabase.VirtualChange.Database.DatabaseDroppedChange');
        $change = new DatabaseDroppedChange($this);
        $event = new onVirtualChange($change);
        $result = $event->fire();
        if(isset($result) && is_bool($result) && !$result) return;
        $this->addChange($change);
        $this->tables = null;
    }
    
    /**
     * 
     * @param Event $event
     */
    public function onEvent(&$event) {
        if(onVirtualChange::$VIRTUAL_CHANGE_EVENT_TYPE !== $event->getEventType()) return;
    }
    
    private function checkAutoCommit() {
        if(!$this->auto_commit) return;
        $this->commitChanges();
    }

    public function jsonSerialize() {
        return array (
            "name" => $this->name,
            "tables" => $this->tables
        );
    }
}