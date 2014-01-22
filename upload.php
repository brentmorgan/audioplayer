<?php
session_start();
($_SESSION['isloggedin'] && $_SESSION['can_upload']) ? 1 : header('Location: index.php');

if ($_POST) {	/* ******************** PROECESS FORM ************************** */
	
	$notice = "<div class='important'>"; // for displaying error messages and/or upload status
	
	$info_is_good = true;
	//					Validate form info here.....
	if ($_POST['title'] == '' || $_POST['title'] == 'Tune Title') {
		$error_title = "<span class='important'>ERROR: You must enter a Title for the tune.</span>";
		$info_is_good = false;
	}
	if (strlen($_FILES['audio1']['name']) == 0 ) {
		$error_audio1 = "<span class='important'>ERROR: You must select at least one audio file to upload.</span>";
		$info_is_good = false;
	}
	
	/* *************************** Upload audio files to temp directory, while checking for errors ***************************** */
	$saved_audio = array();
	
	foreach ($_FILES as $file) {
		if (strlen($file['name']) > 0 && $file['error'] > 0) {
			$info_is_good = false;
			$notice .= "<p>There was an error uploading one of your audio files. " . $file['error'] . "</p>";
		} else if (strlen($file['name']) > 0) {
			$notice .= "<p>File ''" . $file['name'] . "'' was uploaded successfully.</p>";
			$ext = end(explode('.', $file['name']));
			$loc = $file['tmp_name'];
			$tmp_array = array('ext' => $ext, 'loc' => $loc);
			array_push($saved_audio, $tmp_array);
		}
	}
	
	if ($info_is_good) {

		// ***************** Update the DB here *************************
		include('db_connect.php');
	
		if ($_POST['player'] == 'Player') { $_POST['player'] = ''; }
		if ($_POST['instrument'] == 'Instrument') { $_POST['instrument'] = ''; }
		if ($_POST['type'] == 'Tune Type') { $_POST['type'] = ''; }
		if ($_POST['date'] == 'Recorded Date' || $_POST['date'] == '') {
			$date = date('Y-d-m');
		} else {						// mySQL dates go YYYY-MM-DD, but somehow it seems the MM and DD need to be reversed????
			$date = $_POST['date'];
			$date_parts = explode("/", $date);
			$date = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		}
		
		$sql = "INSERT INTO tp_tunes (tune_title, tune_player, tune_instrument, tune_type, tune_date) VALUES ('" . $_POST['title'] . "', '" . $_POST['player'] . "', '". $_POST['instrument'] . "', '" . $_POST['type'] . "', '" . $date . "')";
		$ins = mysql_query($sql, $conn) or print "Oh boy something went wrong with inserting tune info! " . mysql_error() . "<br/>";
		
		$tune_id = mysql_insert_id();
		
		foreach ($saved_audio as $file) {
			move_uploaded_file($file['loc'], "audio/" . $tune_id . "." . $file['ext']) or print "Balls. Error moving temp audio file to audio directory. Sad face. " . $file;
		}
		
		/* Tags */
		
		$tags = $_POST['title'];
		if ($_POST['player'] != '') { $tags .= "," . $_POST['player']; }
		if ($_POST['instrument'] != '') { $tags .= "," . $_POST['instrument']; }
		if ($_POST['type'] != '') { $tags .= "," . $_POST['type']; }
		$tags .= "," . $_POST['tags'];
		
		$sql = "INSERT INTO tp_tags (tags_tune_id, tags_user_id, tags_tag) VALUES ( " . $tune_id . ", " . $_SESSION['user_id'] . ", '" . $tags . "')";
		$ins = mysql_query($sql,$conn) or print "Aw man something went wrong with tha muthafuckin tags. " . mysql_error();
		
		include('db_close.php');
	}
	
	$notice .="</div>";
}
?>
<!doctype html>
<html>
<head>
	<title>Brent Tune Player Thingy - File Upload</title>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="tuneplayer.js"></script>	<!-- Not sure we'll need this on this page???? -- WE DO, there are some we need -------->

	<link href="tuneplayer.css" rel="stylesheet" type="text/css" />
	
	<!-- jQuery UI -->
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<!-- <link rel="stylesheet" href="/resources/demos/style.css" /> -->
    <script>
    $(function() {
      $( "#datepicker" ).datepicker();
    });
    </script>
	<!-- END JQuery UI -->

</head>
<body onLoad="$('#instructions_text').hide()">
	
	<?php echo $notice; ?>
	
	<h2>File Upload</h2>
	<div id="instructions">
		<a href="#" onClick="$('#instructions_text').toggle('slow')">Instructions</a>
		<div id="instructions_text">
			<p class="italic">For best compatibility across browsers and devices it is advisable to upload several different versions of each audio file in different codecs. Simple transcoding can be done via the command line in a Terminal window using FFMPEG as follows:</p>
			<div class="code">ffmpeg -i [filename].[extension] [filename].[newextension]</div>
				Where [filename] is the name of your original file, [extension] is its file extension, and [newextension] is the file extension of the format you are converting to. For example if you have an existing AIFF file called myTune.aiff you could use
				<div class="code">ffmpeg -i myTune.aiff myTune.m4a</div>
				to create a new file in M4A format.
				<p>For best results upload files in these formats:</p>
				<p>aiff<br/>wav<br/>ogg<br/>webm<br/>m4a</p>
				<p class="important">Note that all versions of a particular recording must be uploaded at once, or else they will be considred a different tune, not different formats of the same recording.</p>
		</div>
	</div>
	
	<h3>metadata</h3>
	<form method="POST" enctype="multipart/form-data" action="upload.php">
		<p>
			<input type="text" name="title" id="title" value="Tune Title" class="userInput" />
				<?php echo $error_title; ?><br />
			<input type="text" name="player" id="player" value="Player" class="userInput" /><br />
			<input type="text" name="instrument" id="instrument" value="Instrument" class="userInput" /><br />
			<input type="text" name="type" id="type" value="Tune Type" class="userInput" /><br />
			<input type="text" name="date" id="datepicker" value="Recorded Date" class="userInput" /><br />
		</p>
		
		<h3>tags</h3>
		<p>
			<input type="text" name="tags" id="tags" value="Comma-delimited please!" class="userInput" size="50" />
		</p>
	
		<h3>audio files</h3>
		<p>
			<input type="file" name="audio1" id="audio1" onChange="$('#audio2').show(); $('#upload_submit').attr('disabled', false);" /> 
				<?php echo $error_audio1; ?><br />
			<input type="file" name="audio2"  id="audio2" onChange="$('#audio3').show()" style="display:none" /> <br />
			<input type="file" name="audio3" id="audio3" onChange="$('#audio4').show()" style="display:none" /> <br />
			<input type="file" name="audio4" id="audio4" onChange="$('#audio5').show()" style="display:none" /> <br />
			<input type="file" name="audio5"  id="audio5" style="display:none" /> <br />
			<input type="submit" id="upload_submit" disbled='disabled' />
		</p>
	</form>
	
	<a href="index.php">HOME</a>
	
	<script>
	sharedFunctions();
	</script>
</body>
</html>