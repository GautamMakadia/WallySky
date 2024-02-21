<?php

$method = $_SERVER['REQUEST_METHOD'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($method == "POST" && isset($_FILES['wallpaper'], $_POST['upload'])) {
    header("Content-Type: application/json; charset=UTF-8");

    if ($_FILES['wallpaper']['size'] == 0) {
        http_response_code(500);
        echo json_encode(['msg' => 'file is not uploaded properly or file size is excceding.']);
        exit;
    }

    $response = upload_wallpaper($_FILES['wallpaper'], $_POST);

    echo $response;
    exit;
}


if ($method == "GET" && isset($_GET['down'])) {
    generateDownload($_GET['down']);
}

if ($method == "GET" && isset($_GET['approve'])) {
    echo json_encode(approve($_GET['approve']));
    exit;
}


if ($method == "GET" && isset($_GET['decline'])) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(decline($_GET['decline']));
    // echo "Hello";
    exit;
}

function conn()
{
    $con = new mysqli("localhost", "root", "", "wallysky");

    if ($con->error) {
        throw new Exception("sql error");
    }

    return $con;
}


function generatedownload($url)
{
    $conn = conn();

    $sql = "SELECT `url`, title FROM wallpaper WHERE `url`= '$url'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $original_filename = "../" . $row['url'];
        $ext = pathinfo("$original_filename", PATHINFO_EXTENSION);
        $down_filename = $row['title'].".".$ext;


        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$down_filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($original_filename));


        readfile($original_filename);
    } else {
        echo "Wallpaper not found.";
    }


    $conn->close();
}

function upload_wallpaper($file, $wall_data): string
{
    $con = conn();

    try {

        $filehash = md5_file($file['tmp_name']);
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $filehash . "." . $file_ext;

        $uid = $wall_data['uid'];

        $title = $wall_data['title'];

        $cat = $wall_data['cat'];

        $is_premium = $wall_data['is_prem'];
        $price = 0;

        $imgRes = getimagesize($file['tmp_name']);
        $resolution = "" . $imgRes[0] . "x" . $imgRes[1];

        $size = number_format($file['size'] / 1e+6, 3) . "MB";

        if ($is_premium != 0) {
            $price = $is_premium;
            $is_premium = 1;
        }

        $response = [];
        $res_code = 200;


        $res = $con->query("SELECT hashcode FROM wallpaper where hashcode='$filehash'");
        if ($res->num_rows != 0 || file_exists("../" . $filename)) {
            $res_code = 500;
            $response = [
                'status' => '500',
                'msg' => 'wallpaper alredy exist.'
            ];
        } else {

            $query = $con->query("SELECT id FROM artist where `user_id`=$uid");
            if ($query->num_rows == 0) {
                $con->query("INSERT INTO artist (`user_id`, username, email) SELECT `id`, username, email FROM users where `id`=$uid");
            }

            $image = $file['tmp_name'];

            if (!move_uploaded_file($image, "../pending/".$filename)) {
                $res_code = 500;
                $response = [
                    'status' => 500,
                    'msg' => 'either filesystem error or file size is exceding 50MB limit.'
                ];
            } else {
                $artist = $con->query("SELECT username from artist where `user_id`=$uid")->fetch_assoc()['username'];
                $con->query("INSERT INTO `wallpaper_pending` (artist_id, title, artist, url, mime_type, resolution, size, category, is_premium, price, hashcode) VALUES ($uid, '$title', '$artist' ,'$filename','$file_ext' ,'$resolution', '$size' ,'$cat', $is_premium, $price, '$filehash')");

                $res_code = 201;
                $response = [
                    'id' => "$con->insert_id",
                    'msg' => 'uploaded sucessfully'
                ];
            }
        }
    } catch (Exception $err) {
        $res_code = 500;
        $response = [
            "status" => 500,
            "error_msg" => "$err"
        ];
    } finally {
        $con->close();
    }


    http_response_code($res_code);

    return json_encode($response);
}


function decline($id)
{

    $con = conn();
    $res_code = 204;
    $response = [];

    try {
        $pending = $con->query("SELECT * FROM wallpaper_pending WHERE id = $id")->fetch_assoc();

        if (unlink("../pending/" . $pending['url'])) {
            $con->query("DELETE FROM wallpaper_pending WHERE id=$id");
            $res_code = 201;
            $response = [
                "status" => 201,
                "msg" => "Deleted Successfully."
            ];
        }
    } catch (Exception $err) {
        $res_code = 500;
        $response = [
            "status" => 500,
            "error_msg" => "$err"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $response;
}


function approve($id)
{
    $con = conn();
    $res_code = 204;
    $response = [];

    try {
        $pending = $con->query("SELECT * FROM wallpaper_pending WHERE id = $id")->fetch_assoc();
        $image_name = $pending['url'];

        $pending_image_path = "../pending/".$image_name;
        $compressed_img_path = "../compressed/".$image_name;
        
        $main_image_path = "../".$image_name;

        $image = $pending_image_path;
        $mime = getimagesize($image)['mime'];

        switch($mime){ 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($image); 
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($image); 
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($image); 
                break; 
            case 'image/webp':
                $image = imagecreatefromwebp($image);
                break;
            default: 
                $image = imagecreatefromjpeg($image); 
        } 


        if ($image && imagejpeg($image, $compressed_img_path, 40) && rename($pending_image_path, $main_image_path)) {
            $con->query("INSERT INTO wallpaper(artist_id, artist, title, mime_type, url, resolution, size, category, is_premium, price, date_added, hashcode) SELECT artist_id, artist, title, mime_type, url, resolution, size, category, is_premium, price, date_added, hashcode FROM wallpaper_pending WHERE id=$id");
            $res_code = 201;
            $response = [
                'id' => $con->insert_id,
                'msg' => "Approved."
            ];
            $con->query("DELETE FROM wallpaper_pending WHERE id=$id");
        } else {
            $res_code = 500;
            $response = [
                'msg' => "compress issue"
            ];
        }
    } catch (Exception $err) {
        $res_code = 500;
        $response = [
            "status" => 500,
            "error_msg" => "$err"
        ];
    } finally {
        $con->close();
    }

    http_response_code($res_code);
    return $response;
}
