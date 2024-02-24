<?php
// set for cross domains

header("Access-Control-Allow-Origin: http://localhost:8080");

// Ensure that the certificate is allowed
header("Access-Control-Allow-Credentials: true");

// Specify the allowed request header
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, platform, token");

// Specify the allowed HTTP method
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');

header('Content-Type: application/json');


$home = '/home/'.get_current_user();

$f3 = require($home.'/AboveWebRoot/fatfree-master/lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD','autoload/;'.$home.'/AboveWebRoot/autoload/');

$db = DatabaseConnection::connect(); // defined as autoloaded class in AboveWebRoot/autoload/

$f3->set('DB', $db);

//login
$f3->route('POST /login',
    function($f3)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $password = $data['password'];
        $controller = new UserController('user');
        $controller->login($username, $password);
    }
);

//register
$f3->route('POST /register',
    function($f3)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['user']['username'];
        $password = $data['user']['password'];
        $controller = new UserController('user');
        $result = $controller->register($username, $password);
        echo json_encode($result);
    }

);

$f3->route('GET /getuser',
    function ($f3)
    {
        $controller = new SimpleController('user');
        $data = $controller->getData();
        echo json_encode($data);
    }
);

// get information of the glaciers from database
$f3->route('GET /getlocation',
    function ()
    {
        $controller = new SimpleController('glacierEu');
        $data = $controller->getData();
        echo json_encode($data);
    }
);

$f3->run();
