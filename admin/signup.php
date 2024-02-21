<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['admin'])) {
  header("Location:admin.php");
  exit;
}

if (isset($_POST['signup'])) {
  do_SignUp($_POST);
}



function do_SignUp($login_data)
{
  include_once './connection.php';
  $email = $login_data['email'];
  $password = md5($login_data['password']);
  $username = $login_data['username'];


  $con = conn();

  $user = $con->query("select id from admin where email = '$email' and password = '$password' and username='$username'");

  if ($user->num_rows != 0) {
    echo "<h1>User Alreadey Exist! <a href='admin_signup.php'>Retry</a></h1>";
    $con->close();
    die();
  } else {
    $con->query("INSERT INTO `admin`(username, email, password) VALUES('$username','$email', '$password')");
    $con->close();
    header('Location:admin.php');
    exit;
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="shortcut icon" href="http://localhost:3000/api/cyclone-svgrepo-com.png" type="image/png">
  <link rel="stylesheet" href="./style/form.css">
</head>

<body class="disabled">

  <h1 id="page-heading">🌀 Welcom To WallySky</h1>

  <div class="container">
    <h1>Sign Up</h1>
    <form id="signup-form" class="auth_form" method="post" action="signup.php">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
      </div>

      <div class="form-group">
        <label for="conf_password">Confirm Password:</label>
        <input type="password" id="conf_password" name="conf_password" required><br>
      </div>


      <input id="signup" class="form-btn-main" type="submit" name="signup" value="Sign Up">


      <div class="form-group ext-link">
        <p>Already Registered <a href="login.php">Login Here</a></p>
      </div>

    </form>
  </div>
</body>

</html>