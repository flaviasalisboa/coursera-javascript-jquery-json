<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
	die('NOT Logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

$status = false;

if ( isset($_SESSION['status']) ) {
	$status = $_SESSION['status'];
	$status_color = $_SESSION['color'];

	unset($_SESSION['status']);
	unset($_SESSION['color']);
}

require_once "pdo.php";

$name = htmlentities($_SESSION['name']);

$_SESSION['color'] = 'red';

if (isset($_REQUEST['profile_id']))
{

    $profile_id = htmlentities($_REQUEST['profile_id']);

    // Check to see if we have some POST data, if we do process it
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) 
    {
        if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
        {
            $_SESSION['status'] = "All fields are required";
            header("Location: edit.php?profile_id=" . htmlentities($_REQUEST['profile_id']));
            return;
        }

        if (strpos($_POST['email'], '@') === false)
        {
            $_SESSION['status'] = "Email address must contain @";
            header("Location: edit.php?profile_id=" . htmlentities($_REQUEST['profile_id']));
            return;
        }

        $first_name = htmlentities($_POST['first_name']);
        $last_name = htmlentities($_POST['last_name']);
        $email = htmlentities($_POST['email']);
        $headline = htmlentities($_POST['headline']);
        $summary = htmlentities($_POST['summary']);

        $stmt = $pdo->prepare("
            UPDATE profile
            SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :profile_id
        ");

        $stmt->execute([
            ':first_name' => $first_name, 
            ':last_name' => $last_name, 
            ':email' => $email,
            ':headline' => $headline,
            ':summary' => $summary,
            ':profile_id' => $profile_id,
        ]);

        $_SESSION['status'] = 'Profile edited';
        $_SESSION['color'] = 'green';

        header('Location: index.php');
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['status'] = 'Bad value for profile_id';
        header("Location: edit.php?profile_id=" . htmlentities($_REQUEST['profile_id']));
        return; 
    }
    $fn = htmlentities($row['first_name']);
    $ln = htmlentities($row['last_name']);
    $em = htmlentities($row['email']);
    $he = htmlentities($row['headline']);
    $su = htmlentities($row['summary']);
    $profile_id = $row['profile_id'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Flavia Oliveira Santos de Sa Lisboa 92fe0f14</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
</head>
<body>
<div class="container">
    <h1>Editing profile for - <?php echo $name; ?></h1>
    <?php
        if ( $status !== false ) {
            echo('<p style="color: ' .$status_color. ';" class="col-sm-10 col-sm-offset-2">'.
                htmlentities($status).
                "</p>\n"
                );
        }
    ?>

    <form method="post" action="edit.php">
    <p>First Name:
    <input type="text" name="first_name" size="60" value="<?= $fn ?>" /></p>
    <p>Last Name:
    <input type="text" name="last_name" size="60" value="<?= $ln ?>" /></p>
    <p>Email:
    <input type="text" name="email" size="30" value="<?= $em ?>" /></p>
    <p>Headline:<br/>
    <input type="text" name="headline" size="80" value="<?= $he ?>" /></p>
    <p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea>
    <p>
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <input class="btn btn-primary" type="submit" value="Save">
    <a href="index.php" class="btn btn-default">Cancel</a>    
    </form>

</div>
</body>
</html>