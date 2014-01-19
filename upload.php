<?php
session_start();
($_SESSION['isloggedin'] && $_SESSION['can_upload']) ? 1 : header('Location: index.php');
?>
<!doctype html>
<html>
<head>
	<title>Brent Tune Player Thingy - File Upload</title>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="tuneplayer.js"></script>	<!-- ****************** Not sure we'll need this on this page???? ****** -------->

	<link href="tuneplayer.css" rel="stylesheet" type="text/css" />
	
</head>
<body onLoad="$('#instructions_text').hide()">
	
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

</body>
</html>