sed 's,?>,[/insert_php],g' games.php > games-temp.php
sed 's,<?php,[insert_php],g' games-temp.php > games-temp2.php
sed 's,$wip = true;,$wip = false;,g' games-temp2.php > games-wp.php
rm games-temp.php
rm games-temp2.php

sed 's,?>,[/insert_php],g' games.php > wip-temp.php
sed 's,<?php,[insert_php],g' wip-temp.php > wip-temp2.php
sed 's,$wip = false;,$wip = true;,g' wip-temp2.php > wip-wp.php
rm wip-temp.php
rm wip-temp2.php