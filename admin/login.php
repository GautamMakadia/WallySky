<?php
session_start();

if (isset($_SESSION['admin'])) {
    header("Location:admin.php");
    exit;
}

if (isset($_POST['login'])) {
    do_login($_POST);
}



function do_login($login_data) {
    include_once './connection.php';

    $email = $login_data['email'];
    $password = md5($login_data['password']);
    
    $con = conn();

    $user = $con->query("select id, username, email from admin where email = '$email' and password = '$password'");

    if ($user->num_rows == 0) {
        echo "<h1>User Detailes Are Wrong! <a href='admin_login.php'>Retry</a></h1>";
        $con->close();
        exit;
    }
    $_SESSION['admin'] = $con->insert_id;
    $con->close();
    header("Location:index.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="http://localhost:3000/api/cyclone-svgrepo-com.png" type="image/png">
    <link rel="stylesheet" href="./style/form.css">
</head>


<body>

    <h1 id="page-heading">🌀 Welcom WallySky Admin</h1>

    <div class="container">
        <h1>Login</h1>
        <form id="login-form" class="auth_form" method="post" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input class="form-btn-main" id="login" type="submit" name="login" value="Login">
            <div class="form-group ext-link">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </form>
    </div>
</body>

</html>