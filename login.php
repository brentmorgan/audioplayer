<?php
session_start();

if ($_POST){
	$un = $_POST['un'];
	$pw = $_POST['pw'];
	
	include('db_connect.php');
	$sql = "SELECT name, email, can_upload FROM tp_users WHERE email = '" . $un . "' AND password = '" . $pw . "'";
	$result = mysql_query($sql,$conn) or print "Oh No something went wrong with the query. " . mysql_error();
	include('db_close');
	
	if (mysql_num_rows($result)) {
		$_SESSION['isloggedin'] = true;
		$row = mysql_fetch_assoc($result);
		$_SESSION['nickname'] = $row['name'];
		$_SESSION['email'] = $row['email'];
		$_SESSION['can_upload'] = $row['can_upload'];
		header('Location: ' . $_SESSION['loc']);
	} else {
		$_SESSION['isloggedin'] = false;
		echo "<h1>Login Failure</h1> <p><a href='index.php'>Continue as Guest</a></p>";
	}
}

$_SESSION['loc'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

?>

<!doctype html>
<html>
<head>
	<title>Brent Tune Player Thingy</title>

	<style type="text/css">
	#box {
		border: 1px solid #000;
		padding: 10px;
	}
	</style>
	
	<link href="tuneplayer.css" rel="stylesheet" type="text/css" />
	
</head>
<body onLoad="document.getElementById('un').focus()">
	
<div id='box'>
	<form method="POST">
	<p>&nbsp; &nbsp; &nbsp; email: <input type='text' name='un' id='un' /></p> <!-- sorry this is REALLY LAME but i wanted them to line up and I am lazy -->
	<p>password: <input type='password' name='pw' /></p>
	<input type='submit' />
</form>
</div>

</body>
</html>