<?php
session_start();

$un = $_SESSION['isloggedin'] ? $_SESSION['nickname'] : "Guest";
date_default_timezone_set('America/New_York');

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Brent&#39;s Tune Player Thingy</title>

<!-- jQuery -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- jQuery UI -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<!-- mine -->
<script src="tuneplayer.js"></script>
<link href="tuneplayer.css" rel="stylesheet" type="text/css" />

</head>
<body>
	
	<div id="center_dat_shit">
		
		<h2 id="top_title">Brent's Tune Player Thingy</h2>
	
		<div id="welcome">
			Welcome, 
			<?php
			echo $un;
			if ($_SESSION['isloggedin']) {
				echo " &bull; <a href='kill.php'>logout</a> ";
				if ($_SESSION['can_upload']) {
					echo " &bull; <a href='upload.php'>upload</a> "; 
				}
			} else {
				echo " &bull; <a href='login.php'>login</a>";
			}
			?>
		</div>
	
		<div id="the_whole_player">
			<!-- ******************************* Audio player and controlls ************************* -->
			<div id="div_audio">
				<div id="now_playing">Now Playing: <span id="now_playing_text"></span><span id="countdown"> </span></div>
				<div>
					<audio id="the_audio" controls>

					</audio>
	
					<div id="the_audio_controls">
						Playback Speed: <input type="range" id="playbackSpeedRange" min="50" max="100" step="1" value="100" /> <input type="text" id="displayPlaybackSpeed" value="100" size="2" onClick="this.value=''" />%	<br/>
						<!--
						<a href="#" onClick="var bull = { keyCode: 105 }; keyWasPressed(bull);" title="shortcut: I"> IN</a> -->
							Inpoint: <input type="text" id="inpoint" value="0" size="2" onClick="this.value=''">
						<!--	
						<a href="#" onClick="var bull = { keyCode: 111 }; keyWasPressed(bull);" title="shortcut: O"> OUT</a> -->
							Outpoint: <input type="text" id="outpoint" value="" size="2" onClick="this.value=''"> 
						Loop? <input type="checkbox" id="loop" onClick="var bull = { keyCode: 108 }; keyWasPressed(bull); this.blur();"> <br/>
						Delay: <input type="range" id="delayRange" min="0" max="10" step="1" value="0" /> <input type="text" id="displayDelay" value="0" size="1" onClick="this.value=''" /> sec.
					</div>
				</div>
			</div>

			<!-- *************************** End Audio Player ************************************ -->
	

	
			<!-- *************************** Tunes by Tags Here **************************** -->
	
			<div id="div_tunes_by_tags" class="ui-widget-content ui-corner-all">
			
				<h3 title="wut?" style="cursor:help" onClick="$('#wut_tags').toggle('slow')" class="ui-widget-header ui-corner-all">Tunes by Tags: </span></h3>
		
				<div id="wut_tags" class="no-shadow" style="display:none">
					Enter 'tags' or 'keywords' here to search for particular tunes or types of tunes. If you are logged in you can also add your own custom tags to tunes. Examples of tags are things like 'fiddle', 'jig', 'jeanne freeman', 'variations', or pretty much anything else you can think of that might apply to a tune. You can combine multiple tags to create more refined search results; for instance using 'fiddle' and 'reel' should get you more focussed results than just 'fiddle' or 'reel' alone.
				</div>
		
				<input type="text" id="input_tag" value="Enter Tags Here" class="userInput" onKeyUp="typing_in_tags(this)" /> 
				<button id="add_tag" onClick="add_tag()">Add</button> <button id="search_tags" onClick="search_tags()">Search</button>
				<span id="tags_list" class="no-shadow"></span>
				<div id="search_results" class="no-shadow"><!-- wheeee --></div>
		
			</div>

			<!-- **************************** Recent Tunes Here ********************************* -->
	
			<div id="div_recent_tunes" class="ui-widget-content ui-corner-all">
		
				<h3 title="wut?" style="cursor:help" onClick="$('#wut_recent').toggle('slow')" class="ui-widget-header ui-corner-all">Recent Tunes:</h3>
				<div id="wut_recent" class="no-shadow" style="display:none">
					The most recent tune uploaded to the database will show up here by default. You can move backwards (and forwards when available) in time by clicking the "Prev" and "Next" buttons. The date displayed is the the date the recording was made, or if unavailable is the date the recording was uploaded to the database.
				</div>
		
				<span id="recent_tunes">
					<button id="prev" onClick="alert('oops this doesn\'t work yet!')">prev</button> <span id="tune_title" class="no-shadow"> </span> <span id="tune_date" class="no-shadow"> </span> <button id="next" onClick="#">next</button>
				</span>
			</div>
		
		</div>

	</div>

<script>
$(function() {
	tuneplayer(); // Loads the player and control functions without loading a tune
	
	tags_listener = document.getElementById('input_tag').addEventListener('keypress', tagBeingTyped, false);
	
	function tagBeingTyped(k) {
		console.log("tags_listener: " + k.keyCode);
		if (k.keyCode == 13){	// Enter key
			$('#input_tag').val() == '' ? search_tags() : add_tag();
		}
	}
	
	sharedFunctions();
	
	tune_by_date();	// load most recent tune into Recent Tunes thing
	
	$('#search_tags').attr('disabled', 'disbled');
	$('#next').attr('disabled', 'disabled');
	$('#add_tag').attr('disabled', 'disabled');
});
</script>
		
</body>
</html>