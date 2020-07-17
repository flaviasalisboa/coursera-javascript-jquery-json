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


if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
	$sql = "DELETE FROM profile WHERE profile_id = :zip"; 
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':zip'=>$_POST['profile_id']));
    $_SESSION['status'] = 'Profile deleted';
    $_SESSION['color'] = 'green';
    header('Location: index.php');
    return;
}
$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['error'] = 'Bad value for profile_id';
	header('Location: index.php');
	return;	
}
?>
<html>
<head>
	<title>Flavia Oliveira Santos de Sa Lisboa 92fe0f14</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
</head>
<body>
<div class="container">
  	<H2>Deleting Profile for <?php echo $name; ?></H2><br>
  		First Name: <?= htmlentities($row['first_name']) ?> 
  		<br> 
  		Last Name: <?= htmlentities($row['last_name']) ?>
  	</p>
	
	<form method="post">
		<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
		<input type="submit" value="Delete" name="delete" class="btn btn-primary">
		<a href="index.php" class="btn btn-default">Cancel</a>
	</form>
</div>
</body>
</html>		