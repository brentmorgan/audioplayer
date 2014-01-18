
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Brent&#39;s Tune Player Thingy</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="tuneplayer.js"></script>

<link href="tuneplayer.css" rel="stylesheet" type="text/css" />

</head>
<body>

<!-- ******************************* Audio player and controlls ************************* -->

	<div id="div_audio">
		<audio id="the_audio" controls>
			<?php
			$tune_name = "track";	/* ************************************** this needs to come out and become dynamiccccc **** */
			$exts = array("aiff", "webm", "ogg", "wav", "mp3");		// possible file extensions
			foreach ($exts as $ext) {
				if (file_exists("audio/" . $tune_name . "." . $ext)) {
					echo "<source src='audio/" . $tune_name . "." . $ext . "' type='audio/" . $ext . "' />";
				}
			}
			?>
			<!--
			<source src="audio/track.webm" type="audio/webm" />
			<source src="audio/track.ogg" type="audio/ogg" />
			<source src="audio/track.wav" type="audio/wav" />
				-->
		</audio>
	
		<div id="the_audio_controls">Playback Speed: <input type="range" id="playbackSpeedRange" min="50" max="100" step="1" value="100" /> <input type="text" id="displayPlaybackSpeed" value="100" size="2" onClick="this.value=''" />%
			<a href="#" onClick="var bull = { keyCode: 105 }; keyWasPressed(bull);" title="shortcut: I"> IN</a> <input type="text" id="inpoint" value="0" size="2" onClick="this.value=''"> 
			<a href="#" onClick="var bull = { keyCode: 111 }; keyWasPressed(bull);" title="shortcut: O"> OUT</a> <input type="text" id="outpoint" value="" size="2" onClick="this.value=''"> 
			Loop <input type="checkbox" id="loop" onClick="var bull = { keyCode: 108 }; keyWasPressed(bull); this.blur();">
			Delay <input type="range" id="delayRange" min="0" max="10" step="1" value="0" /> <input type="text" id="displayDelay" value="0" size="1" onClick="this.value=''" /> sec.
			<div id="countdown"></div>
		</div>
	</div>




	<script>
		tuneplayer();
	</script>

</body>
</html>