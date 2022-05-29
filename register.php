<?php
    include("connexion.php");

    session_start();

    if (isset($_SESSION['uname'])) {
        header("location: index.php");
    }

    if (!isset($_SESSION['reg_srt'])) {
        $data = "";
        for ($i=0; $i<32; $i++) $data .= rand(0, 9);
        $_SESSION['reg_srt'] = hash_hmac("sha256", $data, "key@1337..00++22");
    }

    function verify_reg_info($conn) {
        if (!isset($_POST['firstname']) or !isset($_POST['lastname'])
            or !isset($_POST['email']) or !isset($_POST['pwd']) or !isset($_POST['reg_srt'])) {
                return -1; // missing required params
        }

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $pwd = $_POST['pwd'];
        $email = $_POST['email'];

        if (strlen($firstname) < 3 || strlen($lastname) < 3) {
            return -2; // invalid name
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return -3; // invalid email address
        }

        if (str_contains($pwd, $firstname) || str_contains($pwd, $lastname) || strlen($pwd) < 6) {
            return -4; // invalid password
        }

        $req = $conn->prepare("select id from account where email=?");
        $req->execute(array($email));
        if ($req->fetch() !== false) {
            return -5; // email already in use
        }

        return 0;
    }

    function gen_username($conn, $firstname, $lastname) {
        for ($i=0; $i<20; $i++) {
            $uname = $firstname . $lastname . rand(1000, 9999);
            $req = $conn->prepare("select id from account where username=?");
            $req->execute(array($uname));
            if ($req->fetch() === false) {
                return $uname;
            }
            return null;
        }
    }

    $code = verify_reg_info($conn);

    if ($code == 0) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $pwd = $_POST['pwd'];
        $email = $_POST['email'];
        $reg_srt = $_POST['reg_srt'];

        if (!strcmp($reg_srt, $_SESSION['reg_srt'])) {
            $uname = gen_username($conn, $firstname, $lastname);
            if (!$uname) {
                $errno = -17;
            } else {
                $pwd_hash = sha1($pwd);
                $req = $conn->prepare("insert into account (firstname, lastname, email, pwd_hash, username) values (?,?,?,?,?)");
                $req->execute(array($firstname, $lastname, $email, $pwd_hash, $uname));

                $_SESSION['uname'] = $uname;
            }
        } else {
            $errno = -12;
        }
    }
    else if ($code < -1) {
        $errno = $code;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>create new account</title>
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
                case -2:
                    $errorMessage = "Invalid name";
                    break;
                case -3:
                    $errorMessage = "Invalid email address";
                    break;
                case -4:
                    $errorMessage = "Invalid password";
                    break;
                case -5:
                    $errorMessage = "email already in use";
                    break;
                case -12:
                    $errorMessage = "CSRF validation failed";
                    break;
            }
            echo "<p class=\"errno\">$errorMessage</p>";
        }
        ?>
        
        <h3>Create new account</h3>

        <form action="" method="post">
            <div class="form-row">
                <label for="firstname">Firstname: </label>
                <input type="text" name="firstname" id="firstname" required>
            </div>
            <div class="form-row">
                <label for="lastname">Lastname: </label>
                <input type="text" name="lastname" id="lastname" required>
            </div>
            <div class="form-row">
                <label for="email">E-mail: </label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-row">
                <label for="pwd">Password: </label>
                <input type="password" name="pwd" id="pwd" required>
            </div>

            <input type="hidden" name="reg_srt" value="<?php echo $_SESSION['reg_srt']; ?>">

            <div class="form-row">
                <input type="submit" value="Register">
            </div>

            <div class="form-row">
                <small>already have an account? <a href="login.php">Login</a></small>
            </div>
        </form>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>