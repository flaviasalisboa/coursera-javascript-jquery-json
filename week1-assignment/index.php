<?php
// Initial file index.php

session_start();

$login = false;

if (isset($_SESSION['name']) ) {
	$login = true;
	$status = false;

	if ( isset($_SESSION['status']) ) {
		$status = htmlentities($_SESSION['status']);
		$status_color = htmlentities($_SESSION['color']);

		unset($_SESSION['status']);
		unset($_SESSION['color']);
	}

	require_once "pdo.php";

	$profiles = [];
	$all_profiles = $pdo->query("SELECT * FROM profile");

	while ( $row = $all_profiles->fetch(PDO::FETCH_OBJ) )
	{
    	$profiles[] = $row;
	}
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
			<h1>Flavia Lisboa´s Profiles Registry</h1>
			<?php if (!$login) : ?>
				<p>
					<a href="login.php">Please log in</a>
				</p>
				<p>
					Note: Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.
				</p>
			</div>
			<?php else : ?>
				<?php
	                if ( $status !== false )
	                {
	                    echo(
	                        '<p style="color: ' .$status_color. ';" class="col-sm-10">'.
	                            $status.
	                        "</p>\n"
	                    );
	                }
	         ?>

		<div class="container">
            <?php if(empty($profiles)) { ?>
            		<p>No rows found</p>
 			<?php }  else { ?>

                  <table class="table-condensed table-bordered">
                  <tr><th>Name</th><th>Headline</th><th>Action</th><tr>
                    <?php
                        $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr><td>";
                            echo '<a href="view.php?profile_id='.$row['profile_id'].'">';
                            echo(htmlspecialchars($row['first_name'].' '.$row['last_name']));
                            echo '</a>';
                            echo "</td><td>";
                            echo(htmlspecialchars($row['headline']));
                            echo "</td><td>";
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
			echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                            echo "</td></tr>\n";
                        }
                    ?>
                  </table><br>
            <?php } ?>
 	            <p>
				<a href="add.php" class="btn btn-primary">Add New Entry</a>
				<a href="logout.php" class="btn btn-default">Logout</a>
            </p>

            <?php endif; ?>
        </div>

	</body>
</html>