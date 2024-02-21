<?php
include_once "./user_routes.php";
include_once "../auth/auth_routes.php";
    
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];


if ($method == "POST" && isset($_POST['login'])) {
    header('Content-Type: application/json');
    echo json_encode(do_login($_POST));
    exit;
}

if ($method == "POST" && isset($_POST['payment']))
{
    echo json_encode(doPayment($_POST['upi_id'], $_POST['uid'], $_POST['wall_id'], $_POST['price'], $_POST['password']));
    exit;
}

if ($method == "GET" && isset($_GET['id'])) {

    header("Content-Type: application/json; charset=UTF-8");
    
    $responce = [];


    if (isset($_GET['mode'])) {
        $responce = get_user_detail($_GET['id'], "full");
    } else {
        $responce = get_user_detail($_GET['id']);
    }

    echo json_encode($responce, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);

    exit;
} 

if ($method == "POST" && isset($_POST['wall_id'], $_POST['uid'], $_POST['fav'])) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(add_to_favorite($_POST['wall_id'], $_POST['uid']));

    exit;
}


if ($method == "GET" && isset($_GET['paid'], $_GET['uid'])) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(getUserPaidWall($_GET['uid']), JSON_NUMERIC_CHECK);
    exit;
}
?>