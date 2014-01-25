<?php
session_start();

$un = $_SESSION['isloggedin'] ? $_SESSION['nickname'] : "Guest";

$_GET['tune_date'] ? $tune_date = $_GET['tune_date'] : $tune_date = date('Y-d-m'); 	// why are d and m backwards???
include('db_connect.php');
$sql = "SELECT * FROM tp_tunes WHERE tune_date <= '" . $tune_date . "' ORDER BY tune_date DESC LIMIT 1";
$result = mysql_query($sql,$conn) or print "Balls dude, problems. " . mysql_error();
include('db_close');

$row = mysql_fetch_assoc($result);

$tune_id = $row['tune_id'];
$tune_title = $row['tune_title'];
$tune_date = $row['tune_date'];

?>
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
	
	<h2 id="top_title">Brent's Tune Player Thingy</h2>
	
	<div id="welcome">
		Welcome, 
		<?php
		echo $un;
		if ($_SESSION['isloggedin']) {
			echo " <a href='kill.php'>logout</a> ";
			if ($_SESSION['can_upload']) {
				echo " <a href='upload.php'>upload</a> "; 
			}
		} else {
			echo " <a href='login.php'>login</a>";
		}
		?>
	</div>
	
	<!-- **************************** Choose Tune Here ********************************* -->
	
	<div id="chooser">
		
		<h3 title="wut?" style="cursor:help" onClick="$('#wut_recent').toggle('slow')">Recent Tunes:</h3>
		<div id="wut_recent" style="display:none">
			The most recent tune uploaded to the database will show up here by default. You can move backwards (and forwards when available) in time by clicking the "Prev" and "Next" buttons. The date displayed is the the date the recording was made, or if unavailable is the date the recording was uploaded to the database.
		</div>
		
		<span id="recent_tunes">
			<button id="prev" onClick="#">prev</button> <span id="tune_title"><?php echo $tune_title; ?></span> <span id="tune_date"><?php echo $tune_date; ?></span> <button id="next" onClick="#">next</button>
		</span>

		
		<h3 title="wut?" style="cursor:help" onClick="$('#wut_tags').toggle('slow')">Tunes by Tags:</h3>
		
		<div id="wut_tags" style="display:none">
			Enter 'tags' or 'keywords' here to search for particular tunes or types of tunes. If you are logged in you can also add your own custom tags to tunes. Examples of tags are things like 'fiddle', 'jig', 'jeanne freeman', 'variations', or pretty much anything else you can think of that might apply to a tune. You can combine multiple tags to create more refined search results; for instance using 'fiddle' and 'reel' should get you more focussed results than just 'fiddle' or 'reel' alone.
		</div>
		
		<input type="text" id="input_tag" value="Enter Tags Here" class="userInput" onKeyUp="typing_in_tags()" /> 
		<button id="add_tag" onClick="add_tag()">Add</button> <button id="search_tags" onClick="search_tags()">Search</button>
		<span id="tags_list"></span>
		<div id="search_results"><!-- wheeee --></div>
		
	</div>

<!-- ******************************* Audio player and controlls ************************* -->

	<div id="div_audio">
		<audio id="the_audio" controls>
			<?php
/*			
			$_GET['tune_date'] ? $tune_date = $_GET['tune_date'] : $tune_date = date('Y-d-m'); 	// why are d and m backwards???
			include('db_connect.php');
			$sql = "SELECT * FROM tp_tunes WHERE tune_date <= '" . $tune_date . "' ORDER BY tune_date DESC LIMIT 1";
			$result = mysql_query($sql,$conn) or print "Balls dude, problems. " . mysql_error();
			include('db_close');
			
			$row = mysql_fetch_assoc($result);
			
			$tune_id = $row['tune_id'];
			$tune_title = $row['tune_title'];
*/
			//$tune_name = "track";	/* ************************************** this needs to come out and become dynamiccccc **** */
			$exts = array("aiff", "webm", "ogg", "wav", "mp3");		// possible file extensions
			foreach ($exts as $ext) {
				if (file_exists("audio/" . $tune_id . "." . $ext)) {
					echo "<source src='audio/" . $tune_id . "." . $ext . "' type='audio/" . $ext . "' />";
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
		sharedFunctions();
		$('#search_tags').attr('disabled', 'disbled');
		$('#next').attr('disabled', 'disabled');
		$('#add_tag').attr('disabled', 'disabled');
		
		function typing_in_tags() {
			var l = $('#input_tag').val().length;
			console.log(l);
			if (l > 1) {
				$('#add_tag').attr('disabled', false);
				$('#search_tags').attr('disabled', false);
			}
		}
		
		function add_tag() {
			var new_tag = $('#input_tag').val();
			var old_tags = $('#tags_list').html();
			$('#tags_list').html(old_tags + " " + new_tag);
			$('#input_tag').val('');
			$('#input_tag').focus();
			$('#add_tag').attr('disabled', 'disabled');
			
		}
		
		function search_tags() {
			var tags_list = $('#tags_list').html();
			console.log("AAAAFDSFADSFASDF " + tags_list);
			$('#search_results').load("ajax.php", "action=search&tags_list=" + tags_list);
		}
	</script>

</body>
</html>