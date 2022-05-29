<?php
    include("connexion.php");

    session_start();

    if (isset($_SESSION['uname'])) {
        header("location: index.php");
    }

    if (!isset($_SESSION['srt'])) {
        $data = "";
        for ($i=0; $i<32; $i++) $data .= rand(0, 9);
        $_SESSION['srt'] = hash_hmac("sha256", $data, "key@1337..00++22");
    }

    if (isset($_POST['email']) && isset($_POST['pwd']) && isset($_POST['srt'])) {

        $srt = $_POST['srt'];
        if (!strcmp($_SESSION['srt'], $srt)) {
            $pwd_hash = sha1($_POST['pwd']);
            $email = $_POST['email'];

            $req = $conn->prepare("select * from account where email=? and pwd_hash=?");
            $req->execute(array($email, $pwd_hash));

            $q = $req->fetch(PDO::FETCH_ASSOC);
            if ($q === false) {
                $errno = -1;
            } else {
                $_SESSION['firstname'] = $q['firstname'];
                $_SESSION['lastname'] = $q['lastname'];
                $_SESSION['email'] = $email;
                $_SESSION['uname'] = $q['username'];
                $_SESSION['reg_date'] = $q['reg_date'];
                header("location: index.php");
            }
        } else {
            $errno = -2;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content">
        <?php
        if (isset($errno)) {
            $errorMessage = "Unknown error";
            switch ($errno) {
                case -1:
                    $errorMessage = "Wrong email or password";
                    break;
                case -2:
                    $errorMessage = "CSRF validation failed";
                    break;
            }
            echo "<p class=\"errno\">$errorMessage</p>";
        }
        ?>

        <h3>Login to your account</h3>

        <form action="" method="post">
            <div class="form-row">
                <label for="email">E-mail: </label>
                <input type="email" name="email" id="email">
            </div>
            <div class="form-row">
                <label for="pwd">Password: </label>
                <input type="password" name="pwd" id="pwd">
            </div>

            <div class="form-row">
                <input type="submit" value="Login">
            </div>

            <input type="hidden" name="srt" value="<?php echo $_SESSION['srt']; ?>">

            <div class="form-row">
                <small>new memeber? <a href="register.php">sign up</a></small>
            </div>
        </form>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>