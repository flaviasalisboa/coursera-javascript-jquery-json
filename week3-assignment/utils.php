<?php

function pdoDB() 
{
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc2','flavia','php200-2020');
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function validateProfile() {
	if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 ||
	     strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 ||
	     strlen($_POST['summary']) == 0 ) {
		return "All fields are required";
	}

	if ( strpos($_POST['email'], '@') === false ) {
		return "Email address must contain @";
	}
	return true;
}

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function loadPos($pdo, $profile_id) {
	$stmt = $pdo->prepare('SELECT * FROM Position
		WHERE profile_id = :prof ORDER BY rank');
	$stmt->execute(array( ':prof' => $profile_id));
	$positions = array();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$positions[] = $row;
	}
	return $positions;
}

function validationEdit()
{
	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
    {
        $_SESSION['status'] = "All fields are required";
        return false;
    }

    for ($i=1; $i<=9; $i++) 
    {
    	if ( ! isset($_POST['year'.$i]) ) continue;
		if ( ! isset($_POST['desc'.$i]) ) continue;

		$year = htmlentities($_POST['year'.$i]);
		$desc = htmlentities($_POST['desc'.$i]);

    	if (strlen($year) < 1)
    	{
    		$_SESSION['status'] = "All fields are required";
    		return false;
    	}

	    if (strlen($desc) < 1)
	    {
			$_SESSION['status'] = "All fields are required";
        	return false;
	    }

	    if(!is_numeric($year))
    	{
    		$_SESSION['status'] = "Position year must be numeric";
    		return false;
    	}
    }

    if (strpos($_POST['email'], '@') === false)
    {
        $_SESSION['status'] = "Email address must contain @";
        return false;
    }

    return true;
}