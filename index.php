<?php

/**
 * Relaxo - Basic REST for FileMaker PHP API
 *
 * index - Main controller
 *
 * @author Tim Culbert <timculbert@gmail.com>
 */

// Load Resources

function load($class)
{
    $dir = 'resources/';
    
    $file = $dir . $class . '.php';
    if (is_file($file)) {
        require($file);
    }
}

spl_autoload_register('load');

// Create array of Database objects - based on dbconfig files

$db_array = array();
$db_dir = 'dbconfig/';
$handle = opendir($db_dir);

while (false !== ($file = readdir($handle))) {
    if ($file != '.' && $file != '..') {
        require_once($db_dir . $file);
        $db = new Database($hostname, $database, $username, $password, $layout, $id);
        $db_array[$alias] = $db;
    }
}

// Parse request

$request = new Request();
if (isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$request->method = strtoupper($_SERVER['REQUEST_METHOD']);
switch ($request->method) {
    case 'GET':
        $request->params = $_GET;
        break;
    case 'POST':
        $request->params = $_POST;
        break;
    case 'DELETE':
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $request->params);
        break;        
}

// Send request to Controller

if (!empty($request->url_elements)) {
    $route = $request->url_elements[0];
    if (!array_key_exists($route, $db_array)) {
        echo 'Invalid database (alias) specified';
        exit;
    }
    $controller = new Controller($db_array[$route]);
    $response_str = $controller->route($request);
} else {
    $response_str = 'Unkown request';
}

// Return the response

$response = Response::create($response_str, $_SERVER['HTTP_USER_AGENT']);
echo $response->render();

?>