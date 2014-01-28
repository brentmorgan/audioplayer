/* ********************************* tuneplayer.js ************************************* */

function tuneplayer() {

	document.getElementById('the_audio').addEventListener('loadedmetadata', function() { 
		
		var dur = this.duration; 
		tune = {
			loop: false,
			inpoint: 0,
			outpoint: dur,
			speed: 100,
			delay: 2
		}
		
		updateView();

		function loopy(){				// this is inside here because it can only start once the audio is fully loaded and defined etc...
			setTimeout(loopy,20);
			var aud = document.getElementById('the_audio');
			ct = aud.currentTime;
			if (ct >= tune.outpoint) {
				aud.currentTime = tune.inpoint;
				aud.pause();
				tune.loop? playAfterPause(aud) : 1; 
			}
		}

		loopy();		// start checking the time for loopyness!!
	});

	function playAfterPause(aud){
		aud.pause();
		setTimeout(function(){aud.play()}, tune.delay*1000);
		var countdown = tune.delay;

		function tic() {
			document.getElementById('countdown').innerHTML = countdown;
			countdown--;
			countdown > -1 ? setTimeout(tic,1000) : document.getElementById('countdown').innerHTML = '';
		}

		tic();
	}

	function updateView(){
		$('#inpoint').val(tune.inpoint);
		$('#outpoint').val(tune.outpoint);
		$('#loop').prop("checked",tune.loop); // apparently you have to use prop() instead of attr() for some shit like form elements. awesome.
		$('#delayRange').val(tune.delay);
		$('#displayDelay').val(tune.delay);
	}
	
	var key_listener = window.addEventListener('keypress',keyWasPressed,false);
	var speed_listener = document.getElementById('playbackSpeedRange').addEventListener('change',rangeChanged, false);
	var range_listener = document.getElementById('delayRange').addEventListener('change',delayChanged, false);

	function keyWasPressed(k){
/*		
		if (k.target.id == "input_tag") {	// !!!!!! If they're typing in an input field we don't want to activate the audio
			console.log('RETURN cause we donot care what fucking key they pressed');
			if (k.keyCode == 13){	// Enter key
				$('#input_tag').val() == '' ? search_tags() : add_tag();
			}
			return;							// player controls! (like if they type fLute we don't want LOOP to turn on & off)
		}
*/
		var update = true;
	
		var aud = document.getElementById('the_audio');

		console.log("Key was pressed!!! " + k.keyCode + " " + k.srcElement + " " + Object.keys(k));
		switch (k.keyCode) {
		case 105:				// i ..... inpoint
		case 73: 				// I
			tune.inpoint = aud.currentTime;
			tune.outpoint < tune.inpoint ? tune.outpoint = aud.duration : 1;
			break;
		case 111:   			// o ..... outpoint
		case 79:    			// O
			tune.outpoint = aud.currentTime;
			tune.inpoint > tune.outpoint ? tune.inpoint = 0 : 1;
			break;
		case 32:  				// space bar
			aud.playing || !aud.paused ? aud.pause() : playAfterPause(aud); //aud.play();
			break;
		case 108:
		case 76:   				// loop
			tune.loop == false ? tune.loop = true : tune.loop = false;
			aud.loop = tune.loop;
			//console.log(tune.loop);
			break;
		case 13:    			// enter key
			console.log(k.target.id);
			if (k.target.id == "displayPlaybackSpeed") {
				var spood = $('#displayPlaybackSpeed').val();
				spood < 50 ? spood = 50 : spood = spood;
				console.log("spood" + spood);
				$('#displayPlaybackSpeed').val(spood);
				$('#playbackSpeedRange').val(spood);
				$('#displayPlaybackSpeed').blur();
			
				aud.playbackRate = spood/100;
			}
		
			else if (k.target.id == "inpoint") {
				tune.inpoint = $('#inpoint').val();
				aud.currentTime < tune.inpoint ? aud.currentTime = tune.inpoint : 1;
				$('#inpoint').blur();
			} else if (k.target.id == "outpoint") {
				tune.outpoint = $('#outpoint').val();
				if (aud.currentTime > tune.outpoint) {
					aud.pause();
					aud.currentTime = tune.outpoint;
				}
				$('#outpoint').blur();
				//aud.currentTime > tune.outpoint ? aud.pause(); aud.currentTime = tune.outpoint : 1;
			} else if (k.target.id == "displayDelay") {
				var spood = $('#displayDelay').val();
				$('#delayRange').val(spood);
				$('#displayDelay').blur();
				tune.delay = spood;
			}	
		
		
		
			break;
		
		default:
			console.log("not a key we care about");
			update = false;
		}

		if (update){
			updateView();
		}
	}

	function rangeChanged(){
		console.log('range changed');
		$('#displayPlaybackSpeed').val($('#playbackSpeedRange').val());
		document.getElementById('the_audio').playbackRate = $('#playbackSpeedRange').val()/100;
	}

	function delayChanged() {
		console.log('delay changed');
		$('#displayDelay').val($('#delayRange').val());
		tune.delay = $('#delayRange').val();
	}

}

function sharedFunctions() {
	$('.userInput').focus(function() { this.value=''; $(this).removeClass('userInput'); });
}

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
	$('#tags_list').html('');
}

function load_metadata(id) {
	console.log('load_metadata ' + id);
	$('#now_playing_text').load("ajax.php", "action=showTuneMetaData&id=" + id);
}

function load_tune(id) {
	console.log("Load Tuuuuuuuuuuuune: " + id);
	$('#the_audio').load("ajax.php", "action=loadTune&id=" + id, function() {
		delete window.tunePlayer;	// delete original instance of this so functions don't get called twice
		$('#the_audio').load();
	//	tuneplayer();					// don't do this, this fucks everything up by adding new event listeners
		load_metadata(id);
	});
}



