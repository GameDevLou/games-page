<?php
$wip = false;

$gameDevLouGames = "1OkeugKNdbYvkqztkKoW4Y2SNE1h84hMFZdA3jZSSpUI";
$gameDevLouJams = "1yAFycceiw7hi3uIGfPof49V7jGJ0MzTlh0C-2YtS80Q";

$games = googleSheetData($gameDevLouGames);
$jams = googleSheetData($gameDevLouJams);

function googleSheetData($docId){
	$url = "http://spreadsheets.google.com/feeds/list/" . $docId . "/od6/public/values?alt=json&amp;callback=displayContent";
	$json = file_get_contents( $url );
	$data = json_decode( $json, TRUE );
	return $data['feed']['entry'];
}

$gamesModel = createModel($games);
$jamsModel = createModel($jams);

function createModel($table){
	$model = [];
	foreach ( $table as $row ) {
		$newItem = new stdClass;
		foreach ($row as $key => $value) {
			if( strpos($key, 'gsx$') !== FALSE){
				$fieldName = str_replace('gsx$', '', $key );
				$content = htmlspecialchars( str_replace("'", "", $value['$t']) );
				$newItem->$fieldName = $content;
			}
		}
		array_push($model, $newItem);
	}
	return $model;
}

function displayGames( $games, $jams, $wip ) {
	$gamesHTML = "";
	foreach ( $games as $game ) {
		if ( $game->showonsite == "TRUE" ) {
			$newGame = "<div class='game'>"
				. addAnchor( $game )
				. "<a href='" . addLink( $game ) . "' target='_blank'>"
				. "<div class='photoHolder'>"
				. addPhoto( $game ) . addBadge( $game, $jams )
				. "</div><h3>"
				. addName( $game )
				. "</h3></a></div>";
			if ( $wip ) {
				if ( empty( $game->releasedate ) ) {
					$gamesHTML .= $newGame;
				}
			}else {
				if ( !empty( $game->releasedate ) ) {
					$gamesHTML .= $newGame;
				}
			}
		}
	}
	return $gamesHTML;
}

function countGames( $games, $wip ) {
	$count = 0;
	foreach ( $games as $game ) {
		if ( $game->showonsite == "TRUE" ) {
			if ( $wip ) {
				if ( empty( $game->releasedate ) ) {
					$count ++;
				}
			}else {
				if ( !empty( $game->releasedate ) ) {
					$count ++;
				}
			}
		}
	}
	return $count;
}

function addAnchor( $game ) {
	return "<a name='" . strtolower( str_replace([" ", ".", ",", "!", "&rsquo;"], "", addName( $game ) ) ). "'></a>";
}

function addName( $game ) {
	if ( !empty( $game->gamename ) ) {
		return $game->gamename;
	}
}

function addAuthor( $game ) {
	if ( !empty( $game->studio ) ) {
		return $game->studio;
	}else if (!empty( $game->people ) ){
		return $game->people;
	}else{
		return "unknown";
	}
}

function addLink( $game ) {
	if ( empty( $game->link ) ) {
		return "";
	}
	return $game->link;
}

function addPhoto( $game ) {
	$photoURL = $game->photourl;
	if ( empty( $photoURL ) ) {
		return "<img class='gamePhoto' src='http://gamedevlou.org/wp-content/uploads/2015/02/needs-image.png'></img>";
	}
	return "<img class='gamePhoto' src='" . $photoURL . "' alt='". addName( $game ) ." by  " . addAuthor( $game ) . "' title='". addName( $game ) ." by  " . addAuthor( $game ) . "'></img>";
}

function addBadge( $game, $jams ) {
	foreach ( $jams as $jam ) {
		if( $jam->hashtag == $game->jam){
			$description = $jam->name . " - " . $jam->month . " - " . $jam->year . " - " . $jam->theme;
			return "<img class='badge' src='" . $jam->image . "'  alt='" . $description . "' title='" . $description . "'/>";
		}
	}
	return "";
}
?>

<h3><?php echo countGames( $gamesModel, $wip ); ?>  indie games made in Louisville!</h3>

<div class="gamesList">
	<?php echo displayGames( $gamesModel, $jamsModel, $wip ); ?>
</div>

<style>
* {-webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box; }

.gamesList{
  	text-align: center;
}

.game {
  display: inline-block;

  margin: 5px;
  text-align: center;
  vertical-align: top;
}

@media (min-width: 1025px) {
  .game {
  	height: 220px;
    width: 275px;
    margin: 5px;
  }
}
@media (min-width: 0px) and (max-width: 1024px) {
  .game {
    width: 100%;
    margin: 5px 0 15px 0;
  }
}

.game a, .game a:hover, .game a:visited{
	color: #000;
	text-decoration: none !important;
}

.game h3{
	height: 70px;
	margin: 2px auto;
}
.game h3 span{
	margin-left: 5px;
	font-size: 12px;
	font-weight: 200;
}

.photoHolder{
	display: block;
	position: relative;
	text-align: center;
	width: 100%;
	margin: 0 auto;
}

.gamePhoto{
	margin: 0 auto;
	max-width: 100%;
	position: relative;
}

.badge{
	width: 45px;
	position: absolute;
	top: 5px;
	right: 5px;
	border-radius: 0 !important;
	box-shadow: none !important;
}

@media (min-width: 1025px) {
  .photoHolder img {
	max-height: 150px;
  }
}
@media (min-width: 0px) and (max-width: 1024px) {
  .photoHolder img {
    max-height: 300px;
  }
}
</style>
