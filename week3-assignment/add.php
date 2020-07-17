<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
	die('ACCESS DENIED');
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
require_once "utils.php";

$name = htmlentities($_SESSION['name']);

$_SESSION['color'] = 'red';

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) 
{
    $msg = validateProfile();
    if ( is_string($msg) ) 
    {
        $_SESSION['status'] = $msg;
        header("Location: add.php");
        return;
    }

    $msg = validatePos();
    if ( is_string($msg) ) 
    {
        $_SESSION['status'] = $msg;
        header("Location: add.php");
        return;
    }    

    $first_name = htmlentities($_POST['first_name']);
    $last_name = htmlentities($_POST['last_name']);
    $email = htmlentities($_POST['email']);
    $headline = htmlentities($_POST['headline']);
    $summary = htmlentities($_POST['summary']);

    $stmt = $pdo->prepare("
        INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
        VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)
    ");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':first_name' => $first_name, 
        ':last_name' => $last_name, 
        ':email' => $email,
        ':headline' => $headline,
        ':summary' => $summary,
    ]);

    $profile_id = $pdo->lastInsertId();

    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
    
        $stmt2 = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

        $stmt2->execute(array(
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc)
        );
        $rank++;
    }
    $_SESSION['status'] = 'Profile added';
    $_SESSION['color'] = 'green';

    header('Location: index.php');
    return;
    
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Flavia Oliveira Santos de Sa Lisboa e88ea887</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
    <h1>Adding Profile for <?php echo $name; ?></h1>
    <?php
        if ( $status !== false ) {
            echo('<p style="color: ' .$status_color. ';" class="col-sm-10 col-sm-offset-2">'.
                htmlentities($status).
                "</p>\n"
                );
        }
    ?>

    <form method="post">
    <p>First Name:
    <input type="text" name="first_name" size="60"/></p>
    <p>Last Name:
    <input type="text" name="last_name" size="60"/></p>
    <p>Email:
    <input type="text" name="email" size="30"/></p>
    <p>Headline:<br/>
    <input type="text" name="headline" size="80"/></p>
    <p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"></textarea>
    <p>
        Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
        </div>
    </p>
    <p>
    <input class="btn btn-primary" type="submit" value="Add">
    <a href="index.php" class="btn btn-default">Cancel</a>
    </p>
    </form>

<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>

</div>
</body>
</html>