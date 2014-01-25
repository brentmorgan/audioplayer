<?php
/* ***** ajax.php ***** */

if ($_GET['action'] == "search") {
	$tags =  $_GET['tags_list'];
	$crap = array(',', ';');
	$tags = str_replace($crap, " ", $tags);
	$tags = explode(" ", $tags);
	
	//print_r($tags);

	include('db_connect.php');
	$list = array();
	foreach ($tags as $tag) {
		$sql = "SELECT tags_tune_id FROM tp_tags WHERE tags_tag LIKE '%" . $tag . "%'";
		$result = mysql_query($sql, $conn) or print "Oh no dude you fucked up. " . myqsl_error();
		$tune_id = mysql_fetch_row($result);
		array_push($list, $tune_id[0]);
		$list = array_unique($list);
	}
	
	$out = "";
	foreach ($list as $id) {
		$sql = "SELECT * FROM tp_tunes WHERE tune_id = " . $id;
		$result = mysql_query($sql, $conn) or print "FUUUUUUCK " . mysql_error();
		$tune_info = mysql_fetch_assoc($result);
		$html = "<div>" . $tune_info['tune_title'] . "</div>";
		$out .= $html;
	}
	
	echo $out;
	
	include('db_close.php');
}



/*
echo "FFUFUFUFUF KCKCKCKCCKCKK YYYUYUYUYOOOOOO";
echo "<h1>" . $_GET['poop'] . "</h1>";
*/








?>