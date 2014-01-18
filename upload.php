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
	
	<?php
		
	if ($_POST){
		$un = $_POST['un'];
		$pw = $_POST['pw'];
		
		include('db_connect.php');
		$sql = "SELECT * FROM tp_users WHERE email = '" . $un . "' AND password = '" . $pw . "' AND can_upload";
		$result = mysql_query($sql,$conn) or print "Oh No something went wrong with the query. " . mysql_error();
		include('db_close');
		
		if (mysql_num_rows($result)) {
			$isloggedin = true;
		} else {
			$isloggedin = false;
		}
	}
		
	?>

</head>
<body>
	
<?php
if (!$loggedin) {
	echo "<div id='box'>
		<p>email: <input type='text' name='un' /></p>
		<p>password: <input type='text' name='pw' /></p>
		<input type='submit' />
	</div>";
} else {
	;
}
?>
<div>
<a href="login.php">login</a>
</div>


</body>
</html>