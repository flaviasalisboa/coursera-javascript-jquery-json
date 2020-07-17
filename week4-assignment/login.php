<?php
// Login page

session_start();

if ( isset($_POST['logout'] ) ) {
    header("Location: index.php");
    return;
}

require_once "pdo.php";

$salt = 'XyZzy12*_';

$failure = false;  // If we have no POST data

if ( isset($_SESSION['failure']) ) {
    $failure = $_SESSION['failure'];

    unset($_SESSION['failure']);
}

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) )
{
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 )
    {
        $_SESSION['failure'] = "Email and password are required";
        header("Location: login.php");
        return;
    }
    else
    {
        $pass= htmlentities($_POST['pass']);
        $email = htmlentities($_POST['email']);

        if ((strpos($email, '@') === false))
        {
            $_SESSION['failure'] = "Email must have an at-sign (@)";
            header("Location: login.php");
            return;
        }
        else
        {
            $check = hash('md5', $salt.$_POST['pass']);

            $stmt = $pdo->prepare('SELECT user_id, name FROM users             WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $email, ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo ($row['password']);

            if ( $row !== false )
            {
                error_log("Login success ".$email);

                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                header("Location: index.php");
                return;
            }
            else
            {
                error_log("Login fail ".$pass." $check");
                $_SESSION['failure'] = "Incorrect password";

                header("Location: login.php");
                return;
            }
        }
    }
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Flavia Oliveira Santos de Sa Lisboa a07e9aab - Login Page</title>
        <?php require_once "head.php"; ?>
    </head>
    <body>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('passw').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>

        <div class="container">
            <h1>Please Log In</h1>
                <?php
                     if ( $failure !== false )
                    {
                        echo(
                            '<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.
                                htmlentities($failure).
                            "</p>\n"
                        );
                    }
                ?>
            <form method="post" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Email:</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="email" id="email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass">Password:</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="password" name="pass" id="passw">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 col-sm-offset-2">
                        <input class="btn btn-primary" type="submit" onclick="return doValidate();" value="Log In">
                        <input class="btn" type="submit" name="logout" value="Cancel">
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>