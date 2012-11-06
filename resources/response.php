<?php

/**
 * Relaxo - Basic REST for FileMaker PHP API
 *
 * response - Response class
 *
 * @author Tim Culbert <timculbert@gmail.com>
 */

class Response
{
    public static function create($data, $format)
    {
        switch ($format) {
            case 'application/json':
            default:
                $obj = new ResponseJson($data);
                break;
        }
        
        return $obj;
    }
}
 
?>