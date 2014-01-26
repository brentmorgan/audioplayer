<?php
/* ***** ajax.php ***** */

if ($_GET['action'] == "search") {		// ********* TUNES BY TAGS section *********
	$tags =  $_GET['tags_list'];
	$crap = array(',', ';');
	$tags = str_replace($crap, " ", $tags);
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
		
		$html = "<div><a href='#' onClick='load_tune(" . $id . ")' title='" . $tune_info['tune_player'] . ": " . $tune_info['tune_date'] . "'>" . $tune_info['tune_title'] . "</a></div>";
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
}



/*
echo "FFUFUFUFUF KCKCKCKCCKCKK YYYUYUYUYOOOOOO";
echo "<h1>" . $_GET['poop'] . "</h1>";
*/








?>