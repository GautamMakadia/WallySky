<?php

function get_user_detail($id, $mode = 'user_detail'): array
{
    include_once "../config/connection.php";
    $con = conn();

    $res_code = 204;
    $user = [];

    try {
        $query_result = $con->query("SELECT id, username, email FROM users WHERE id=$id");
        $data = $query_result->fetch_assoc();


        if ($data != null && $data != false) {
            $user = $data;

            if ($mode == "full") {
                include "../wall_routs.php";
                $fav_walls = getFavWalls($id);

                $user['fav_list'] = $fav_walls;
            }

            $res_code = 200;
        }
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $user = [
            "status" => "500",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }


    http_response_code($res_code);

    return $user;
}

function add_to_favorite($wall_id, $uid)
{
    include_once "../config/connection.php";
    $con = conn();

    $response = [];
    $res_code = 204;

    try {

        $query = $con->query("SELECT id FROM favorite WHERE wall_id = $wall_id and `user_id` = $uid");

        if ($query->num_rows != 0) {
            $data = $query->fetch_assoc();
            $id = $data['id'];

            $con->query("DELETE FROM favorite WHERE id = $id");

            $res_code = 201;
            $response = [
                'id' => $id,
                'isFav' => false
            ];
        } else {

            $query = $con->query("SELECT * FROM wallpaper WHERE id = $wall_id");
            $wall = $query->fetch_assoc();

            if ($wall != null && $wall != false) {
                $wallpaper_id = $wall['id'];

                $wall = $con->query("INSERT INTO favorite(`user_id`, wall_id) VALUES($uid,$wallpaper_id)");

                $res_code = 201;
                $response = [
                    'id' => $con->insert_id,
                    'isFav' => true
                ];
            }
        }
    } catch (mysqli_sql_exception $err) {
        $res_code = 500;
        $response = [
            "status" => "500",
            "msg" => "mysql_error",
            "error" => "$err"
        ];
    } finally {
        $con->close();
    }


    http_response_code($res_code);

    return $response;
}


function doPayment($upi_id, $uid, $wall_id, $price, $pass)
{
    include_once "../config/connection.php";

    $con = conn();
    $res_code = 200;

    $password = md5($pass);

    try {

        $user = $con->query("SELECT * FROM users WHERE id=$uid AND password='$password'");

        if ($user->num_rows == 0) {
            http_response_code(204);
            $con->close();
            return [
                "status" => 204,
                "msg" => "Passowrd is Wrong"
            ];
        }
        $check = $con->query("SELECT * FROM payment where `wall_id`=$wall_id AND `user_id`=$uid AND `amount`=$price");


        if ($check->num_rows == 0) {
            $con->query("INSERT INTO payment (amount, artist_id, `user_id`, wall_id, gateway, status) SELECT `price`, `artist_id`, $uid, $wall_id, '$upi_id', 'paid' FROM `wallpaper` WHERE `id`=$wall_id");
            $res_code = 201;
            $response = [
                'payment_id' => $con->insert_id,
                'status' => 'paid',
            ];
        } else {
            $oldPayment = $check->fetch_assoc();
            $response = 403;
            $response = [
                'payment_id' => $oldPayment['id'],
                'status' => 'already paid.',
                'details' => $oldPayment
            ];
        }
    } catch (Exception $e) {
        $res_code = 500;
        $response = [
            "status" => "500",
            "error_msg" => "$e"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $response;
}

function getUserPaidWall($id)
{
    include '../config/connection.php';
    $con = conn();

    $wallpaper = [];
    $res_code = 204;

    try {
        $query_result = $con->query("SELECT * FROM payment WHERE `user_id`=$id");

        if ($query_result->num_rows != 0) {
            $data = $query_result->fetch_all(MYSQLI_ASSOC);
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
