<?php

include "./wall_routs.php";

$method = $_SERVER['REQUEST_METHOD'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



if ($method == "GET" && sizeof($_GET) == 0) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(get());
    exit;
}

if ($method == "GET" && isset($_GET['id'])) {
    header("Content-Type: application/json; charset=UTF-8");
    $responce = json_encode(getWallpaperById($_GET['id']), JSON_NUMERIC_CHECK);
    echo $responce;
    exit;
}

if ($method == "GET" && isset($_GET['count'])) {
    header("Content-Type: application/json; charset=UTF-8");
    $responce = json_encode(getWallpaperByCount($_GET['count']), JSON_FORCE_OBJECT);
    echo $responce;
    exit;
}

// what if we get request to wallpaper with car catagory;
if ($method == "GET" && isset($_GET['cat'])) {
    header("Content-Type: application/json");
    $responce = json_encode(getWallByCat($_GET['cat']), JSON_NUMERIC_CHECK); 
    echo $responce;
    exit;
}

// what if we want randome wallpaper.
if ($method == "GET" && isset($_GET['rand'])) {
    header("Content-Type: application/json");
    $responce = json_encode(getRandomly(), JSON_NUMERIC_CHECK); 
    echo $responce;
    exit;
}

if ($method == "GET" && isset($_GET['fav'], $_GET['uid'])) {
    header("Content-Type: application/json");
    $responce = json_encode(getFavWalls($_GET['uid']), JSON_NUMERIC_CHECK); 
    echo $responce;
    exit;
}

?>