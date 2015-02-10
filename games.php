<?php
$docId = "1OkeugKNdbYvkqztkKoW4Y2SNE1h84hMFZdA3jZSSpUI";
$url = "http://spreadsheets.google.com/feeds/list/" . $docId . "/od6/public/values?alt=json&amp;callback=displayContent";
$json = file_get_contents( $url );
$data = json_decode( $json, TRUE );
$Games = $data['feed']['entry'];
$wip = false;

function displayGames( $Games, $badgeData, $wip ) {
	$GamesHTML = "";
	foreach ( $Games as $game ) {
		if ( $game['gsx$showonsite']['$t'] == "TRUE" ) {
			$newGame = "<div class='game'>"
				. "<a href='" . addLink( $game ) . "' target='_blank'>"
				. "<div class='photoHolder'>"
				. addPhoto( $game )
				. "</div>"
				. addName( $game )
				. "</a></div>";
			if ( $wip ) {
				if ( empty( $game['gsx$releasedate']['$t'] ) ) {
					$GamesHTML .= $newGame;
				}
			}else {
				if ( !empty( $game['gsx$releasedate']['$t'] ) ) {
					$GamesHTML .= $newGame;
				}
			}
		}
	}
	return $GamesHTML;
}

function countGames( $Games, $wip ) {
	$count = 0;
	foreach ( $Games as $game ) {
		if ( $game['gsx$showonsite']['$t'] == "TRUE" ) {
			if ( $wip ) {
				if ( empty( $game['gsx$releasedate']['$t'] ) ) {
					$count ++;
				}
			}else {
				if ( !empty( $game['gsx$releasedate']['$t'] ) ) {
					$count ++;
				}
			}
		}
	}
	return $count;
}

function addAnchor( $game ) {
	$name = "<a name='";
	if ( !empty( $game['gsx$firstname']['$t'] ) ) {
		$name .= strtolower( htmlspecialchars( $game['gsx$firstname']['$t'] ) );
	}
	if ( !empty( $game['gsx$lastname']['$t'] ) ) {
		$name .= strtolower( htmlspecialchars( $game['gsx$lastname']['$t'] ) );
	}
	return $name . "'></a>";
}

function addName( $game ) {
	$name = "<h3>";
	if ( !empty( $game['gsx$gamename']['$t'] ) ) {
		$name .= $game['gsx$gamename']['$t'];
	}
	return $name . "</h3>";
}

function addLink( $game ) {
	if ( empty( $game['gsx$link']['$t'] ) ) {
		return "";
	}
	return htmlspecialchars( $game['gsx$link']['$t'] );
}

function addPhoto( $game ) {
	$photoURL = $game['gsx$photourl']['$t'];
	if ( empty( $photoURL ) ) {
		return "<img class='directoryPhoto' src='http://gamedevlou.org/wp-content/uploads/2015/02/needs-image.png'></img>";
	}
	$name = "";
	if ( !empty( $game['gsx$firstname']['$t'] ) ) {
		$name .= $game['gsx$firstname']['$t'] . " ";
	}
	if ( !empty( $game['gsx$lastname']['$t'] ) ) {
		$name .= $game['gsx$lastname']['$t'];
	}
	$location = "";
	if ( !empty( $game['gsx$location']['$t'] ) ) {
		$location .= $game['gsx$location']['$t'];
	}
	return "<img class='directoryPhoto' src='" . htmlspecialchars( $photoURL ) . "' alt='". htmlspecialchars( $name ) ." - independant game developer - " . htmlspecialchars( $location ) . "'></img>";
}

$badgeData = (object) array(
	'ggj14' => (object) array(
		'name' => 'ggj14',
		'link' => 'ggj14link',
		'description' => ' - Global Game Jam - 2014 - We dont see things as they are, we see them as we are.',
		'image' => 'http://gamedevlou.org/wp-content/uploads/2015/02/badge-ggj14.png'
	),
	'ld29' => (object) array(
		'name' => 'ld29',
		'link' => 'ld29link',
		'description' => ' - Ludum Dare 29 - April 2014	- Beneath the surface',
		'image' => 'http://gamedevlou.org/wp-content/uploads/2015/02/badge-ld29.png'
	),
	'ld30' => (object) array(
		'name' => 'ld30',
		'link' => 'ld30link',
		'description' => ' - Ludum Dare 30 - August 2014 - Connected Worlds',
		'image' => 'http://gamedevlou.org/wp-content/uploads/2015/02/badge-ld30.png'
	),
	'ld31' => (object) array(
		'name' => 'ld31',
		'link' => 'ld31link',
		'description' => ' - Ludum Dare 31 - December 2014 - Entire Game on One Screen!',
		'image' => 'http://gamedevlou.org/wp-content/uploads/2015/02/badge-ld31.png'
	),
	'ggj15' => (object) array(
		'name' => 'ggj15',
		'link' => 'ggj15link',
		'description' => ' - Global Game Jam - 2015 - What do we do now?',
		'image' => 'http://gamedevlou.org/wp-content/uploads/2015/02/badge-ggj15.png'
	)
);

function addBadges( $game, $data ) {
	$badges = "<div class='badges'>";

	foreach ( $data as $badge ) {
		$gameName = $game['gsx$' . $badge->name]['$t'];
		$gameName = str_replace( "'", "&rsquo;", $gameName );
		$gameLink = $game['gsx$' . $badge->link]['$t'];
		if ( !empty( $game['gsx$' . $badge->name]['$t'] ) ) {
			$badgeHTML = "<img class='badge' src='" . $badge->image . "' alt='" . htmlspecialchars( $gameName ) . $badge->description . "' title='" . $gameName . $badge->description . "'/>";
			if ( !empty( $gameLink ) ) {
				$badges .= "<a href='". htmlspecialchars( $gameLink ) . "' target='_blank'>";
				$badges .= $badgeHTML;
				$badges .= "</a>";
			}else {
				$badges .= $badgeHTML;
			}
		}
	};

	return $badges .= "</div>";
}
?>

<h3><?php echo countGames( $Games, $wip ); ?>  indie games made in Louisville!</h3>

<div class="gamesList">
	<?php echo displayGames( $Games, $badgeData, $wip ); ?>
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

.photoHolder img{
	margin: 0 auto;
	max-width: 100%;
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
