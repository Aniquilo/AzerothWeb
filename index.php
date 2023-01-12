<?php

define('ROOTPATH', dirname(__FILE__));

require_once ROOTPATH . '/engine/core.php';

$request = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '/home';

// In case you are hosting the website in a sub directory of your web servers root
if (substr($request, 1, strlen(basename(__DIR__))) == basename(__DIR__))
{
	$request = substr($request, strlen(basename(__DIR__)) + 1);
}

// Check for unsafe url symbols
if (preg_match('/[{}|\\^~\[\]`]/', $request))
{
  	die ('Invalid url string.');
}

$requestParts = explode('/', substr($request, 1));

$directory = ROOTPATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
$controllerPrefix = '';
$controller = $requestParts[0];
$method = (isset($requestParts[1]) && !empty($requestParts[1]) ? $requestParts[1] : 'index');

// In case we have controller directory
if (is_dir($directory . $requestParts[0]))
{
    $directory = $directory.strtolower($requestParts[0]).DIRECTORY_SEPARATOR;
    $controllerPrefix = strtolower($requestParts[0]).'/';
    $controller = (isset($requestParts[1]) && !empty($requestParts[1]) ? $requestParts[1] : 'home');
    $method = (isset($requestParts[2]) && !empty($requestParts[2]) ? $requestParts[2] : 'index');
}

$controller = ucfirst($controller);
$e404 = false;

if (empty($controller) OR !file_exists($directory.strtolower($controller).'.php'))
{
    $e404 = TRUE;
}
else
{
    require_once($directory.strtolower($controller).'.php');

    if (!class_exists($controller, FALSE) OR $method[0] === '_' OR method_exists('Core_Controller', $method))
    {
        $e404 = TRUE;
    }
    elseif (!method_exists($controller, $method))
    {
        $e404 = TRUE;
    }
    /**
     * DO NOT CHANGE THIS, NOTHING ELSE WORKS!
     *
     * - method_exists() returns true for non-public methods, which passes the previous elseif
     * - is_callable() returns false for PHP 4-style constructors, even if there's a __construct()
     * - method_exists($controller, '__construct') won't work because CI_Controller::__construct() is inherited
     * - People will only complain if this doesn't work, even though it is documented that it shouldn't.
     *
     * ReflectionMethod::isConstructor() is the ONLY reliable check,
     * knowing which method will be executed as a constructor.
     */
    elseif (!is_callable(array($controller, $method)))
    {
        $reflection = new ReflectionMethod($controller, $method);
        if (!$reflection->isPublic() OR $reflection->isConstructor())
        {
            $e404 = TRUE;
        }
    }
}

// Load the notfound controller
if ($e404)
{
    $controller = 'Notfound';
    $method = 'index';
    $directory = ROOTPATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
    
    require_once($directory.strtolower($controller).'.php');
}

// Construct the class controller
$CORE = new $controller();
$CORE->controller = $controllerPrefix.$controller;
$CORE->method = $method;

// Initilaize the system
require_once ROOTPATH . '/engine/initialize.php';

// Check for notifications
if ($controller != 'Notification' && $NOTIFICATIONS->Check())
{
    // Append the current page URL to the first notification for return
    if ($NOTIFICATIONS->AppendUrlToFirst())
    {
        // Go to the notifications page
        $NOTIFICATIONS->Launch();
    }
}

// Call the controller method
//call_user_func_array(array(&$CORE, $method), array());
$CORE->{$method}();

// Run the visitor tracker
if (!$CORE->IsACP())
{
    $tracker = new VisitorTracker();
    $tracker->track();
    unset($tracker);
}

// Run a complete shutdown
Shutdown::Execute();
exit;