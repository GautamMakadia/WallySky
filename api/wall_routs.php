<?php

function get()
{
    return [
        "name" => "gautam",
    ];
}

function getWallpaperById($id): array
{
    include './config/connection.php';
    $con = conn();

    $res_code = 204;
    $wallpaper = [];

    try {
        $query_result = $con->query("SELECT * FROM wallpaper WHERE id=$id");
        $data = $query_result->fetch_assoc();

        if ($data) {
            $wallpaper = $data;
            $res_code = 200;
        }
        
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $wallpaper = [
            "status" => "402",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $wallpaper;
}


function getWallpaperByCount($count): array
{
    include './config/connection.php';
    $con = conn();

    $res_code = 200;
    $wallpaper = [];

    try {
        $query_result = $con->query("SELECT * FROM wallpaper LIMIT $count");

        $wallpaper = $query_result->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $wallpaper = [
            "status" => "402",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $wallpaper;
}


function getRandomly(): array
{
    include_once './config/connection.php';
    $con = conn();

    $wallpaper = [];
    $res_code = 204;

    try {
        $query = $con->query("SELECT * FROM wallpaper ORDER BY RAND()");

        if ($query->num_rows != 0) {
            while ($data = $query->fetch_assoc()) {
                $data['category'] = explode(", ", $data['category']);
                $wallpaper["'" . $data['id'] . "'"] = $data;
            }
            $res_code = 200;
        }
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $wallpaper = [
            "status" => "402",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }


    http_response_code($res_code);
    return $wallpaper;
}


function getFavWalls($id): array
{
    include './config/connection.php';
    $con = conn();

    $wallpaper = [];
    $res_code = 204;

    try {
        $query_result = $con->query("SELECT * FROM favorite WHERE `user_id`=$id");

        if ($query_result->num_rows != 0) {
            $data = $query_result->fetch_all(MYSQLI_ASSOC);
            $wallpaper = [];
            foreach ($data as $img) {
                $wallpaper[$img['wall_id']] = $img;
            }
            $res_code = 200;
        }
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $wallpaper = [
            "status" => "500",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $wallpaper;
}

function getWallByCat($cat): array
{
    include_once "./config/connection.php";

    $wallpaper = [];
    $res_code = 204;

    try {
        $con = conn();
        $query = $con->query("SELECT * FROM wallpaper WHERE category LIKE '%$cat%'");


        if ($query->num_rows != 0) {
            while ($wall = $query->fetch_assoc()) {
                $wallpaper[$wall['id']] = $wall;
            }
            $res_code = 200;
        }
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $wallpaper = [
            "status" => "500",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
        http_response_code($res_code);
        return $wallpaper;
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $wallpaper;
}
