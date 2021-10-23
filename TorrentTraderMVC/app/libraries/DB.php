<?php

class DB
{
    protected static $instance = null;
    protected function __construct() {}
    protected function __clone() {}

    public static function instance()
    {
        if (self::$instance === null) {
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            );
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
		try {
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
        } catch (\PDOException $e) {
            die('The Database Details Are Incorrect');
        }
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (!$args) {
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
    
    // Update 
    public static function update($table, $data, $where)
    {
        //merge data and where together
        $collection = array_merge($data, $where);
        //collect the values from collection
        $values = array_values($collection);
        //setup fields
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }
        $stmt = self::run("UPDATE $table SET $fieldDetails WHERE $whereDetails", $values);
        return $stmt->rowCount();
    }
}