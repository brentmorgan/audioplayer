<?php
/* ***** ajax.php ***** */
date_default_timezone_set('America/New_York');

if ($_GET['action'] == "search") {		// ********* TUNES BY TAGS section *********
	$tags =  $_GET['tags_list'];
	$crap = array(',', ';');
	$tags = str_replace($crap, " ", $tags);
	$tags = htmlspecialchars($tags, ENT_QUOTES);
	$tags = explode(" ", $tags);
	$tags = array_filter($tags); // remove empty elemnts left over from punctuation and spaces

	include('db_connect.php');
	$list = array();

	$total_tags = count($tags);
	$hits = array();

	foreach ($tags as $tag) {
		$sql = "SELECT tags_tune_id FROM tp_tags WHERE tags_tag LIKE '%" . $tag . "%'";
		$result = mysql_query($sql, $conn) or print "Oh no dude you fucked up. " . myqsl_error();
		while ($tune_id = mysql_fetch_array($result, MYSQL_NUM)) {
			array_push($list, $tune_id[0]);	
			$hits[$tune_id[0]]++;		// This will show if ALL tags fit a tune, or just one or more
		}
		$list = array_unique($list);
	}
	
	$out1 = "<div>Top Results:</div>";
	$out2 = "<div onClick='$(\"#more_results\").toggle(\"slow\")'>More Results</div><div id='more_results' style='display:none'>";
	$count1 = 0;
	$count2 = 0;
	
	foreach ($list as $id) {
		$sql = "SELECT * FROM tp_tunes WHERE tune_id = " . $id;
		$result = mysql_query($sql, $conn) or print "FUUUUUUCK " . mysql_error();
		$tune_info = mysql_fetch_assoc($result);
		
		$datetime = explode(" ", $tune_info['tune_date']);	// discard time information ($datetime[1])
		
		$html = "<div><a href='#' onClick='load_tune(" . $id . ")' title='" . $tune_info['tune_player'] . ": " . $datetime[0] . "'>" . $tune_info['tune_title'] . "</a></div>";
		if ($hits[$id] == $total_tags) {
			$out1 .= $html; 
			$count1++;
		} else {
			$out2 .= $html;
			$count2++;
		}
	}
	$count1 == 0 ? $out1 .="<div>No exact matches.</div>" : 1;
	$count2 == 0 ? $out2 .="<div>No more results.</div>" : 1;
	
	$out2 .= "</div>";
	
	echo $out1;
	echo $out2;
	
	include('db_close.php');
	
} else if ($_GET['action'] == 'loadTune') {		/* ************************************************** Load Tune (by id) ************ */
	include('db_connect.php');
	$sql = "SELECT tune_id FROM tp_tunes WHERE tune_id = '" . $_GET['id'] . "'";
	$result = mysql_query($sql,$conn) or print "Balls dude, problems. " . mysql_error();
	include('db_close.php');

	$row = mysql_fetch_assoc($result);

	$tune_id = $row['tune_id'];
	
	$exts = array("aiff", "webm", "ogg", "wav", "mp3");		// possible file extensions
	foreach ($exts as $ext) {
		if (file_exists("audio/" . $tune_id . "." . $ext)) {
			echo "<source src='audio/" . $tune_id . "." . $ext . "' type='audio/" . $ext . "' />";
		}
	}
} else if ($_GET['action'] == 'showTuneMetaData') {		/* ************************************** Show Tune Meta Shit (by id) ******* */
	include('db_connect.php');
	$sql = "SELECT tune_title, tune_date, tune_player, tune_instrument FROM tp_tunes WHERE tune_id = '" . $_GET['id'] . "'";
	$result = mysql_query($sql,$conn) or print "Balls dude, problems. " . mysql_error();
	include('db_close.php');

	$row = mysql_fetch_assoc($result);
	
	$tune_title = $row['tune_title'];
	$tune_datetime = $row['tune_date'];
	$tune_datetime = explode(" ", $tune_datetime);
	$tune_date = $tune_datetime[0];
	$tune_player = $row['tune_player'];
	$tune_instrument = $row['tune_instrument'];
	
	echo "<span title='" . $tune_date . "\n" . $tune_player . "\n" . $tune_instrument . "'>" . $tune_title . "</span>";
} else if ($_GET['action'] == 'tuneTitleByDate') {
	if (isset($_GET['d'])) {
		$d = $_GET['d'];
	} else {
		$d = date('Y-m-d H:i:m');
	}
	include('db_connect.php');
	$sql = "SELECT tune_title, tune_id, tune_date FROM tp_tunes WHERE tune_date <= '" . $d . "' ORDER BY tune_date DESC LIMIT 1";
	$res = mysql_query($sql, $conn) or print "OOOOOps problems. " . mysql_error();
	include('db_close.php');
	
	$row = mysql_fetch_row($res);
	
	echo "<a href='#' onClick='load_tune(" . $row[1] . ")'>" . $row[0] . "</a><script>document.getElementById('prev').onclick='tune_by_date(\"". $row[2] . "\")';</script>";
	
} else if ($_GET['action'] == 'tuneDateByDate') {
	include('db_connect.php');
	$sql = "SELECT tune_date FROM tp_tunes WHERE tune_date <= '" . date('Y-m-d') . "' ORDER BY tune_date DESC LIMIT 1";
	$res = mysql_query($sql, $conn) or print "Oh no problem with dates. " . mysql_error();
	include('db_close.php');
	
	$row= mysql_fetch_row($res);
	
	$datetime = $row[0];
	$datetime = explode(" ", $datetime);
	$date = $datetime[0];
	echo $date;
}










?>