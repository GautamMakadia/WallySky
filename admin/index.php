<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location:login.php");
    exit;
}

include_once './connection.php';


$con = conn();
$pending = $con->query("SELECT * FROM wallpaper_pending");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="shortcut icon" href="http://localhost:3000/api/cyclone-svgrepo-com.png" type="image/png">
    <link rel="stylesheet" href="./style/index.css">
</head>

<style>
    .row {
        grid-template-columns: 5fr 5fr 15em 20em 5fr 3fr 3fr 3fr 3fr;
    }
</style>

<body>

    <div class="navigation">
        <h3><a href="payment.php">Payment</a></h3>
        <h3><a href="wallpaper.php">Wallpapers</a></h3>
        <h3><a href="logout.php">Logout</a></h3>
    </div>

    <h1>Pending Request For Wallpaper Upload</h1>
    <section id="pending-walls">
        <div id="table">
            <div class="header row">
                <div class="wall-id attribute">wallpaper_id</div>
                <div class="wall-artist attribute">artist</div>
                <div class="wall-title attribute">title</div>
                <div class="wall-category attribute">category</div>
                <div class="wall-size attribute">size</div>
                <div class="wall-mime attribute">mime_type</div>
                <div class="add-to-wallpaper attribute">approve</div>
                <div class="decdivne-request attribute">decline</div>
                <div class="show-img attribute">show</div>
            </div>

            <?php while ($wall = $pending->fetch_assoc()) {
                $artist = $con->query("SELECT * FROM artist WHERE `user_id`=" . $wall['artist_id'])->fetch_assoc();
            ?>

                <div class="row">
                    <div class="wall-id"><?= $wall['id'] ?></div>
                    <div class="wall-artist"><?= $artist['username'] ?></div>
                    <div class="wall-title"><?= $wall['title'] ?></div>
                    <div class="wall-cat"><?= $wall['category'] ?></div>
                    <div class="wall-size"><?= $wall['size'] ?></div>
                    <div class="wall-mime"><?= $wall['mime_type'] ?></div>
                    <div class="wall-aprove">
                        <button class="aprove-btn btn" onclick="approve(<?= $wall['id'] ?>)">
                            Aprove
                        </button>
                    </div>
                    <div class="decline-request">
                        <button class="decline-btn btn" onclick="decline(<?= $wall['id'] ?>)">
                            Decline
                        </button>
                    </div>
                    <div class="show-img">
                        <button onclick="openDialog('<?= $wall['url'] ?>')">
                            Show Image
                        </button>
                    </div>
                </div>

            <?php } ?>
        </div>
    </section>


    <dialog aria-labelledby="dialog_title" aria-describedby="dialog_description">
        <img id="wall-img"></img>
        <button id="close-dialog">Close</button>
    </dialog>


    <script type='text/javascript'>
        function test() {
            alert('Index.')
        }

        async function approve(id) {
            const approve_res = await fetch('http://localhost:3000/api/?approve=' + id)

            if (approve_res.status == 500) {
                const res = await approve_res.json()
                alert(res.msg)
                return
            }

            if (approve_res.status == 204) {
                alert('Somthing Went Wrong!')
                return
            }

            if (approve_res.status == 201) {
                const res = await approve_res.json()
                alert(res.msg)
                window.location.reload()
                return
            }
        }


        async function decline(id) {
            const approve_res = await fetch('http://localhost:3000/api/?decline=' + id)

            if (approve_res.status == 500) {
                const res = await approve_res.json()
                alert(res)
                return
            }

            if (approve_res.status == 204) {
                alert('Somthing Went Wrong!')
                return
            }

            if (approve_res.status == 201) {
                const res = await approve_res.json()
                alert(res.msg)
                window.location.reload()
                return
            }
        }
    </script>
</body>

<script type="text/javascript">
    const close_dialog = document.getElementById('close-dialog');
    const dialog = document.querySelector("dialog");
    const img = document.getElementById('wall-img')

    close_dialog.onclick = (event) => {
        event.stopPropagation()
        closeDialog(event)
    }

    const openDialog = (url) => {
        img.src = 'http://localhost:3000/pending/' + url
        dialog.showModal();
        dialog.addEventListener("keydown", trapFocus);
    };

    const closeDialog = (e) => {
        e.preventDefault();
        dialog.close();
        img.src = '';
        dialog.removeEventListener("keydown", trapFocus);
        openDialogBtn.focus();
    };


    const trapFocus = (e) => {
        if (e.key === "Tab") {
            const tabForwards = !e.shiftKey && document.activeElement === lastElement;
            const tabBackwards = e.shiftKey && document.activeElement === firstElement;
            if (tabForwards) {
                // only TAB is pressed, not SHIFT simultaneously
                // Prevent default behavior of keydown on TAB (i.e. focus next element)
                e.preventDefault();
                firstElement.focus();
            } else if (tabBackwards) {
                // TAB and SHIFT are pressed simultaneously
                e.preventDefault();
                lastElement.focus();
            }
        }
    };
</script>


</html>