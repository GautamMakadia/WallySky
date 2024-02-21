<?php
include_once './auth_routes.php';

$method = $_SERVER['REQUEST_METHOD'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($method === 'POST' && isset($_POST['login'])) {
    header('Content-Type: application/json');
    $responce = do_login($_POST);
    echo json_encode($responce, JSON_NUMERIC_CHECK);
    exit;
}

if ($method === "POST" && isset($_POST['signup'])) {
    header('Content-Type: application/json');
    $responce = do_SignUp($_POST);
    echo json_encode($responce, JSON_NUMERIC_CHECK);
    exit;
}



header("Status: 200");
echo json_encode([
    'msg'=>'outter'
]);

?>