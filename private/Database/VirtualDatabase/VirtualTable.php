<?php

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

import('Database.VirtualDatabase.VirtualDatabase');
import('Database.VirtualDatabase.VirtualField');
import('Database.VirtualDatabase.VirtualEntry');
import('Database.VirtualDatabase.VirtualQuery.*');
import('Database.VirtualDatabase.VirtualChange.Table.*');

import('HashMap.HashMap');

/*
 * What's a VirtualTable?
 * VirtualTable contains the specifications for a table in the VirtualDatabase.
 * 
 * Information: VirtualFields, Table Constraints and the attached Database
 */

class VirtualTable implements JsonSerializable {
    /*
     * Static Methods
     */

    //TODO: Valid Database Names
    public static function isValidTableName($name) {
        return true;
    }

    public static function getTables($db, $table_names, $conn) {
        if (!($table_names instanceof ArrayList))
            throw new Exception('Invaid names.');
        if (!($db instanceof VirtualDatabase))
            throw new Exception('Invalid Database');
        if (!($conn instanceof ManagedConnection))
            throw new Exception('Invalid Connection.');
        import('Database.VirtualDatabase.VirtualQuery.Table.GetTableInformationQuery');

        $tables = new ArrayList('VirtualTable');

        foreach ($table_names as $table) {
            $table = new VirtualTable($table);

            $query = new GetTableInformationQuery($db, $table);
            $fields = $query->fetch($conn);
            $table->fields = $fields;
            foreach ($fields as $field) {
                $field->table = $table;
            }

            $db->addTable($table);
            $tables->add($table);
        }

        return $tables;
    }

    /*
     * Instance
     */

    private $name;
    private $fields;
    private $entries;
    public $database;

    public function __construct($name) {
        if (!VirtualTable::isValidTableName($name))
            throw new Exception('Invalid Table Name');
        $this->name = $name;
        $this->fields = new ArrayList('VirtualField');
        $this->entries = new ArrayList('VirtualEntry');
    }

    public function getName() {
        return $this->name;
    }

    /**
     * Adds a new field to the table.
     * 
     * @param VirutalField $field
     * @return VirtualField
     * @throws Exception
     */
    public function addField($field) {
        if (!($field instanceof VirtualField))
            throw new Exception('Invalid Field');
        if (isset($field->table))
            throw new Exception('Field already belongs to a table.');
        $this->fields->add($field);
        $field->table = $this;
        return $field;
    }

    /**
     * Adds a field referencing another field.
     * 
     * @param string $name
     * @param VirtualField $field
     * @param array $params
     * @return VirtualField
     * @throws Exception
     */
    public function addForeignField($name, $field, $params = array()) {
        if (!($field instanceof VirtualField))
            throw new Exception('Invalid Field');
        if (!isset($field->table))
            throw new Exception('Field must belong to a table');
        $newParams = array(
            "reference" => $field
        );
        $newParams = array_merge($newParams, $params);
        $myField = new VirtualField($name, $field->getFieldType(), $newParams);
        return $this->addField($myField);
    }

    /**
     * Returns the field by the name supplied.
     * 
     * @param string $name
     * @return VirtualField
     */
    public function getField($name) {
        return $this->fields->getByFunctionValue('getName', $name);
    }

    /**
     * Gets a list of all the fields in the table.
     * 
     * @return ArrayList
     */
    public function getFields() {
        return $this->fields->createCopy();
    }

    /**
     * Adds a single entry into the database, confirming data before hand.
     * 
     * @param HashMap $map
     * @return VirtualEntry
     */
    public function createEntry($map) {
        $this->validateMap($map);
        if (!($map instanceof HashMap))
            throw new Exception('Entry Data MUST be a HashMap');
        if (!($map->isValidKeyClass('VirtualField')))
            throw new Exception('Invalid HashMap Key Type');

        $field_value = null;

        //Create our FieldEntry holder
        $map_adjusted = new HashMap('VirtualField', null);

        foreach ($this->fields as $field) {
            if (!($field instanceof VirtualField))
                throw new Exception('VirtualField invalid.');

            //First we need to determine the type
            $value = $map->isKeySet($field) ? $map->get($field) : null;

            /*
             * Things we need to adjust and check for...
             *  null values need to be replaced with their default value(s)
             *  auto_increments need to be adjusted automatically
             *  that primary keys/unique keys aren't taken
             *  that foreign keys exist (if not nullable)
             *  
             *  ..more to come. 
             */

            if ($value instanceof \DateTime) {
                $value = VirtualDatabase::convertDateTimeToSQL($value);
            }

            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            //I'll promise I'll never do drugs again dad.
            if ($field->auto_increment) {
                $value = VirtualField::$AUTO_INCREMENT;
            }

            if ($field->primary_key || $field->unique_key) {
                //TODO: Add a search for those matching these keys.
            }

            if ($field === null) {
                $value = $field->getDefaultValue();
            }

            //TODO: Foreign Key Checking
            if ($value !== null && $field->hasReference()) {
                $reference = $field->getReference();
            }

            //Final Check..
            if (!$field->isOptional() && $value === null) {
                throw new Exception('Value for field "' . $field->getName() . '" is null when it cannot be!');
            }


            $map_adjusted->put($field, $value);
        }


        //Finally, we can create our entry.
        $virtual_entry = new VirtualEntry($this, $map_adjusted);

        import('Database.VirtualDatabase.VirtualChange.Entry.EntryAddedChange');
        $entry_change = new EntryAddedChange($virtual_entry);
        $this->database->addChange($entry_change);
        $event = new onVirtualChange($entry_change);
        $result = $event->fire();
        if (isset($result) && is_bool($result) && !$result)
            throw new Exception();

        $this->entries->add($virtual_entry);

        return $virtual_entry;
    }
        
    public function removeEntry($entry) {
        if(!($entry instanceof VirtualEntry)) throw new Exception ('Invalid Entry');
        if($entry->getTable() !== $this) throw new Exception('Invalid Table');
        
        $lst = new ArrayList('VirtualEntry');
        $lst->add($entry);
        
        $change = new EntriesDeletedChange($this, $lst);
        $this->database->addChange($change);
        $event = new onVirtualChange($change);
        $result = $event->fire();
        if (isset($result) && is_bool($result) && !$result) throw new Exception();
        
        $this->entries->remove($entry);
    }

    /**
     * 
     * @param HashMap $map
     */
    public function validateMap($map, $checkNotNull=true) {
        if (!($map instanceof HashMap))
            throw new Exception('Entry Data MUST be a HashMap');
        if (!($map->isValidKeyClass('VirtualField')))
            throw new Exception('Invalid HashMap Key Type');

        $field_value = null;

        foreach ($this->fields as $field) {
            if (!($field instanceof VirtualField))
                throw new Exception('VirtualField invalid.');

            //Try to pull out of the array
            if (!$map->isKeySet($field) || $map->get($field) === null) {
                if(!$checkNotNull) continue;
                if(!$field->isOptional()) {
                    throw new Exception('Value for field "' . $field->getName() . '" is empty when it cannot be.');
                }
                $field_value = null;
            } else {
                $field_value = $map->get($field);
            }

            //The data exists, now we need to validate the types.
            if ($field->getFieldType() === VirtualFieldType::$INTEGER) {
                /*
                 * Integer Type, let's validate
                 */

                if (!$field->isOptional() && $field_value !== null) {
                    if(!is_int($field_value) && is_numeric($field_value)) {
                        $field_value = intval($field_value);
                    }
                    if (!is_int($field_value))
                        throw new Exception('Value for field "' . $field->getName() . '" is not an int and is not nullable.');
                }

                //Field Type is valid.
                continue;
            }
            if ($field->getFieldType() === VirtualFieldType::$DECIMAL) {
                /*
                 * Decimal Type, let's validate
                 */

                if (!$field->isOptional() && $field_value !== null) {
                    if (!is_numeric($field_value)) {
                        throw new Exception('Value for field "' . $field->getName() . '" is not a float and is not nullable.');
                    }
                }

                //Field Type is valid.
                continue;
            }

            if ($field->getFieldType() === VirtualFieldType::$VARCHAR) {
                if ($field_value === null) {
                    continue;
                }

                if (!is_string($field_value)) {
                    throw new Exception('Value for field "' . $field->getName() . '" is not a string and is not nullable.');
                }

                if ($field->max_length != -1 && strlen($field_value) > $field->max_length) {
                    throw new Exception('Value for field "' . $field->getName() . '" is larger than the max allowed length (' . strlen($field_value) . ' > ' . $field->max_length . ').');
                }

                //Field Type is valid.
                continue;
            }

            if ($field->getFieldType() === VirtualFieldType::$TEXT) {
                if($field->isPrimaryKey()) throw new Exception('FieldType '.$field->getFieldType()->getType() . ' cannot be a primary key!');
                if ($field_value === null) {
                    continue;
                }

                if (!is_string($field_value)) {
                    throw new Exception('Value for field "' . $field->getName() . '" is not a string and is not nullable.');
                }

                //Field Type is valid.
                continue;
            }

            if ($field->getFieldType() === VirtualFieldType::$DATETIME) {
                if ($field->isOptional() && $field_value === null) {
                    continue;
                }

                if (!($field_value instanceof \DateTime)) {
                    throw new Exception('Value for field "' . $field->getName() . '" is not a valid DateTime object, and is not nullable.');
                }

                //Field Type is valid.
                continue;
            }

            if ($field->getFieldType() === VirtualFieldType::$BOOLEAN) {
                if ($field_value === null)
                    continue;
                if (!is_bool($field_value) && !is_int($field_value) && ($field_value != 1 && $field_value != 0)) {
                    throw new Exception('Value for field "' . $field->getName() . '" is not a boolean and is not nullable.');
                }
                continue;
            }

            throw new Exception('Value for field "' . $field->getName() . '" is unknown.');
        }
    }

    public function injectEntry($entry) {
        if(!($entry instanceof VirtualEntry)) throw new Exception();
        //Manually inject an entry into the table, I would avoid using.
        $this->entries->add($entry);
    }

    public function jsonSerialize() {
        return array(
            "name" => $this->name,
            "fields" => $this->fields,
            "entries" => $this->entries
        );
    }

}
