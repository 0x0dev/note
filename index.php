<?php

    include("connexion.php");
    session_start();

    if (!isset($_SESSION['uname'])) {
        header("location: login.php");
    }

    if (!isset($_SESSION['logout_srt'])) {
        $data = "";
        for ($i=0; $i<32; $i++) $data .= rand(0, 9);
        $_SESSION['logout_srt'] = hash_hmac("sha256", $data, "key@1337..00++22");
    }

    if (isset($_POST['op']) && isset($_POST['logout_srt'])) {
        if (!strcmp($_SESSION['logout_srt'], $_POST['logout_srt'])) {
            unset($_SESSION['uname']);
            session_destroy();
            header("location: login.php");
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="content">

        <div class="n-c">
            <div class="notes">
                <div class="note">
                    <small>#1</small>
                    <p>note 1</p>
                </div>
                <div class="note">
                    <small>#2</small>
                    <p>note 2</p>
                </div>
            </div>
            <div class="c">
                <p>
                    click on a NOTE to display its content.
                </p>
            </div>
        </div>


        <form action="" method="post">
            <input type="hidden" name="logout_srt" value="<?php echo $_SESSION['logout_srt']; ?>">
            <input type="hidden" name="op" value="1">
            <input type="submit" value="logout">
        </form>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>