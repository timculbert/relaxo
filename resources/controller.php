<?php

/**
 * Relaxo - Basic REST for FileMaker PHP API
 *
 * controller - FileMaker Controller
 *
 * @author Tim Culbert <timculbert@gmail.com>
 */

class Controller
{
    public $db;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->db->init();
    }
    
    public function route($request)
    {
        if ($request->method == 'POST') {
            return $this->post($request);
        } else {
            if (count($request->url_elements) == 1) {
                echo 'An id must be specified with GET, PUT, DELETE';
                exit;
            } else {
                $func = strtolower($request->method);
                return $this->$func($request);
            }
        }
    }
    
    public function recordById($id)
    {
        if ($this->db->id == '') {
            $result = $this->db->fm->getRecordById($this->db->layout, $id);
            
            if (FileMaker::isError($result)) {
                echo 'Error: ' . $result->getMessage();
                exit;
            }
            
            return $result;
        } else {
            $findCommand = $this->db->fm->newFindCommand($this->db->layout);
            $findCommand->addFindCriterion($this->db->id, $id);
            $result = $findCommand->execute();
            
            if (FileMaker::isError($result)) {
                echo 'Error: ' . $result->getMessage();
                exit;
            }
            
            $records = $result->getRecords();
       
            return $records[0];
        }
    }
    
    public function get($request)
    {
        $recid = $request->url_elements[1];
        $record = $this->recordById($recid);
        if (is_string($record)) {
            echo $record;
            exit;
        }
        $out = array();
        
        foreach($this->db->fields as $field) {
            $out[$field] = $record->getField($field);
        }
        
        return $out;
    }
    
    public function put($request)
    {
        $recid = $request->url_elements[1];
        $record = $this->recordById($recid);
        $out = array();
        
        if (count($request->params) > 0) {
            $count = 0;
            foreach ($request->params as $field => $value) {
                if (in_array($field, $this->db->fields)) {
                    $record->setField($field, $value);
                    $count += 1;
                }
            }
            
            if ($count > 0) {
                $result = $record[0]->commit();
                
                if (FileMaker::isError($result)) {
                    echo 'Error: ' . $result->getMessage();
                    exit;
                }
                return 'Put successful';
            } else {
                return 'No parameters matching field names';
            }
        }
        
        return 'No request parameters specified';
    }
    
    public function delete($request)
    {
        $recid = $request->url_elements[1];
        $record = $this->recordById($recid);
        $result = $record->delete();
        
        if (FileMaker::isError($result)) {
            echo 'Error: ' . $result->getMessage();
            exit;
        }
        
        return 'Record deleted';
    }
    
    public function post($request)
    {
        $out = array();
        
        if (count($request->params) > 0) {
            foreach ($request->params as $field => $value) {
                if (in_array($field, $this->db->fields)) {
                    $out[$field] = $value;
                }
            }
            
            $addRequest = $this->db->fm->newAddCommand($this->db->layout, $out);
            $result = $addRequest->execute();
            
            if (FileMaker::isError($result)) {
                echo 'Error: ' . $result->getMessage();
                exit;
            }
            
            return 'Record added';
        }
        
        return 'No parameters (or no valid field names) specified';
    }
}

?>