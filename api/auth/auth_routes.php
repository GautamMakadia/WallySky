<?php 

function do_login($login_data) {
    include_once '../config/connection.php';

    $email = $login_data['email'];
    $password = md5($login_data['password']);

    $responce = [];
    $res_code = 204;
    

    try {
        $con = conn();

        $user = $con->query("select id, username, email from users where email = '$email' and password = '$password'");

        if ($user->num_rows != 0) {
            $res_code = 200;
            $responce = [
                'user' => $user->fetch_assoc()
            ];
        } 

    } catch (mysqli_sql_exception $e) {
        $res_code = 500;
        $responce = [
            'status'=> '500',
            'error'=> 'mysql',
            'msg' => "$e"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $responce;
}


function do_SignUp($login_data) {
    include_once '../config/connection.php';

    $email = $login_data['email'];
    $password = md5($login_data['password']);
    $username = $login_data['username'];
    
    $responce = [];
    $res_code = 422;
    

    try {
        $con = conn();

        $user = $con->query("select id from users where username='$username'");
        $email_quer = 0;
        
        $email_query = $con->query("select id from users where email = '$email'")->num_rows;


        if ($user->num_rows != 0 && $email_query != 0) {
            $responce = [
                'status' => 422,
                'message' => "User & Email Alredy Exist, Please Try Another", 
                "email" => $email,
                "username" => $username  
            ];
        } else if ($user->num_rows != 0) {
            $responce = [
                'status' => 422,
                'message' => "User Alredy Exist, Please Try Another", 
                "email" => $email,
                "username" => $username  
            ];
        } else if ($email_query != 0) {
            $responce = [
                'status' => 422,
                'message' => "Email Alredy Exist, Please Try Another", 
                "email" => $email,
                "username" => $username  
            ];
        } else {
            $con->query("INSERT INTO users(username, email, password) VALUES('$username','$email', '$password')");
            $res_code = 201;
        }

    } catch (mysqli_sql_exception $e) {
        $res_code = 500;
        $responce = [
            'status'=> '500',
            'error'=> 'mysql',
            'msg' => "$e"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $responce;
}

?>