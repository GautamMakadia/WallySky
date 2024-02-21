<?php
session_start();


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$method = $_SERVER['REQUEST_METHOD'];


if ($method == "GET" && isset($_GET['unset'])) {
    if (session_unset()){
        http_response_code(202);
    } else {
        http_response_code(404);
    }
    
    exit;
}

if ($method === "GET" && sizeof($_GET) == 0) {
    if (isset($_SESSION['uid'])) {
        http_response_code(200);
        echo json_encode([
            'uid' => $_SESSION['uid'] 
        ]);
        exit;
    } else {
        http_response_code(204);
        exit;
    }
}



if ($method === "POST") {
    if (!isset($_SESSION['uid'])) {
        http_response_code(200);
        $_SESSION['uid'] = $_POST['uid'];
        exit;

    } else {
        http_response_code(303);
        exit;
    }
}
?>