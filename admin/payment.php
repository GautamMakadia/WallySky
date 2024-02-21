<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location:login.php");
    exit;
}

include_once "./connection.php";

$con = conn();
$query = "select payment.*, users.username as username, artist.username as artist_name, wallpaper.url, wallpaper.title FROM payment CROSS JOIN users CROSS JOIN artist CROSS JOIN wallpaper WHERE users.id=payment.user_id and wallpaper.id=payment.wall_id and artist.user_id=payment.artist_id LIMIT 0,100";

$payment = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="shortcut icon" href="http://localhost:3000/api/cyclone-svgrepo-com.png" type="image/png">
    <link rel="stylesheet" href="./style/index.css">
    <style>
        .row {
            grid-template-columns: 2fr 5fr 5fr 5fr 5fr 5fr 5fr 2fr 5fr;
        }
    </style>
</head>

<body>
    <div class="navigation">
        <h3><a href="index.php">Home</a></h3>
        <h3><a href="wallpaper.php">Wallpapers</a></h3>
        <h3><a href="logout.php">Logout</a></h3>
    </div>
    <h1>Payment Records Of WallySky</h1>
    <section id="pending-walls">
        <div id="table">
            <div class="header row">
                <div class="attribute">id</div>
                <div class="attribute">title</div>
                <div class="attribute">user</div>
                <div id="artist" class="attribute">artist</div>
                <div class="attribute">gateway</div>
                <div class="attribute">amount</div>
                <div class="attribute">payment_date</div>
                <div class="attribute">status</div>
                <div class="show-img attribute">show</div>
            </div>

            <?php while ($data = $payment->fetch_assoc()) { ?>

                <div class="row">
                    <div><?= $data['id'] ?></div>
                    <div><?= $data['title'] ?></div>
                    <div><?= $data['username'] ?></div>
                    <div><?= $data['artist_name'] ?></div>
                    <div><?= $data['gateway'] ?></div>
                    <div><?= $data['amount'] ?>$ </div>
                    <div><?= $data['payment_date'] ?></div>
                    <div><?= $data['status'] ?></div>
                    <div id="show-img">
                        <button onclick="openDialog('<?= $data['url'] ?>')">
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
</body>

<script type="text/javascript">
    const close_dialog = document.getElementById('close-dialog')
    const dialog = document.querySelector("dialog")
    const img = document.getElementById('wall-img')

    close_dialog.onclick = (event) => {
        event.stopPropagation()
        closeDialog(event)
    }

    const openDialog = (url) => {
        img.src = 'http://localhost:3000/' + url
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