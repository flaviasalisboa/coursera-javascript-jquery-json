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

$name = htmlentities($_SESSION['name']);

$_SESSION['color'] = 'red';

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['status'] = 'Bad value for profile_id';
    header('Location: edit.php');
    return; 
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];


$stmt = $pdo->prepare("SELECT * FROM position where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$position = [];
    while ( $row = $stmt->fetch(PDO::FETCH_OBJ) ) 
    {
        $position[] = $row;
    }
    $positionLen = count($position);

$stmt = $pdo->prepare("SELECT * FROM education where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$education = [];
    while ( $row = $stmt->fetch(PDO::FETCH_OBJ) ) 
    {
        $education[] = $row;
    }
    $educationLen = count($education);

?>

<!DOCTYPE html>
<html>
<head>
<title>Flavia Oliveira Santos de Sa Lisboa a07e9aab</title>
<?php require_once "head.php"; ?>
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
    <p>First Name: <?= $fn ?>
    <p>Last Name: <?= $ln ?>
    <p>Email: <?= $em ?>
    <p>Headline: <?= $he ?>
    <p>Summary: <?= $su ?>
    <?php if($positionLen > 0) : ?>
            <p>Positions:</p>
            <div>
                <ul>
                    <?php for($i=1; $i<=$positionLen; $i++) : ?>
                        <li><?php echo $position[$i-1]->year; ?>: <?php echo $position[$i-1]->description; ?></li>
                            <?php endfor; ?>
                </ul>
            </div>
    <?php endif; ?>
    <p><br />
    <?php if($educationLen > 0) : ?>
            <p>Education:</p>
            <div>
                <ul>
                    <?php for($i=1; $i<=$educationLen; $i++) : ?>
                                <?php 
                                $edu_school = $education[$i-1]->institution_id;
                                $stmt = $pdo->prepare("SELECT * FROM institution
                                                WHERE institution_id = :edu_school LIMIT 1");
                                $stmt->execute([':edu_school' => $edu_school]);
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                    <li><?php echo $education[$i-1]->year; ?>: <?php echo $row['name']; ?></li>
                    <?php endfor; ?>
                </ul>
            </div>
    <?php endif; ?>
    <p><br />
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <a href="index.php" class="btn btn-default">Done</a>    
    </form>

</div>
</body>
</html>