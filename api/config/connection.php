<?php

function conn() {
    $con = new mysqli("localhost", "root", "", "wallysky");

    if ($con->error) {
        die("sql error");
    }

    return $con;
}

?>