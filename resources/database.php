<?php

/**
 * Relaxo - Basic REST for FileMaker PHP API
 *
 * database - Database class
 *
 * @author Tim Culbert <timculbert@gmail.com>
 */
 
class Database
{
    public $hostname;
    public $database;
    public $username;
    public $password;
    public $layout;
    public $fields;
    public $id;
    public $fm;

    public function __construct($hostname, $database, $username, $password, $layout, $id)
    {
        $this->hostname = $hostname;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->layout = $layout;
        $this->id = $id;
        
        return $this;
    }

    public function init()
    {
        // Specify the FileMaker PHP API location
        require_once('../fmi/FileMaker.php');
        
        $this->fm = new FileMaker($this->database, $this->hostname, $this->username, $this->password);
        $this->fields = $this->getLayoutFields();
    }

    public function getLayoutFields()
    {
        $record = $this->fm->getLayout($this->layout);
        
        if (FileMaker::isError($record)) {
            echo 'Error: ' . $record->getMessage();
            exit;
        }
        
        return array_keys($record->getFields());
    }
}

?>