<?
date_default_timezone_set("US/Mountain");

function buildCategoriesToSearch($catID, $includeRoot, $includeKids)
{
	$sql = "";
	if ($includeRoot)
	{
		$sql .= "`categoryID`='$catID' OR ";
	}
	if ($includeKids)
	{
		$subsql = "SELECT * FROM `category` WHERE `parentID`='$catID'";
		$subquery = mysql_query($subsql);
		for ($x = 0; $x < mysql_num_rows($subquery); $x++)
		{
			$sql .= buildCategoriesToSearch(mysql_result($subquery, $x, "ID"), true, true) . " OR ";
		}
	}
	if (substr($sql, strlen($sql) - 4) == " OR ")
	{
		$sql = substr($sql, 0, strlen($sql) - 4);
	}
	return $sql;
}

function parentCategory($categoryID)
{
	if ($categoryID <= 0)
	{
		return $categoryID;
	}
	else
	{
		$sql = "SELECT `parentID` FROM `category` WHERE `ID`='" . addslashes($categoryID) . "'";
		$query = mysql_query($sql);
		return parentCategory(mysql_result($query, 0));
	}
}

function ContainsEIIP($query)
{
	for ($x = 0; $x < mysql_num_rows($query); $x++)
	{
		if (mysql_result($query, $x, "RuleType") == "EIIP")
		{
			return true;
		}
	}
	return false;
}

function ContainsEditedOnly($query)
{
	for ($x = 0; $x < mysql_num_rows($query); $x++)
	{
		if (mysql_result($query, $x, "RuleType") == 'EditedOnly')
		{
			return true;
		}
	}
	return false;
}

function HasCategories($query)
{
//	mysql_num_rows($ClockwheelCategories) > 0
	for ($x = 0; $x < mysql_num_rows($query); $x++)
	{
		if (mysql_result($query, $x, "CategoryID") >= 0)
		{
			return true;
		}
	}
	return false;
}

include("common/dbconnect.php");

// STEP 1: GET THE NEXT CLOCKWHEEL ITEM...
$sql = "SELECT * FROM `clockwheelelements` ORDER BY `SortID`";
$clockwheel = mysql_query($sql);
echo mysql_error();
for ($x = 0; $x < mysql_num_rows($clockwheel); $x++)
{
	if (mysql_result($clockwheel, $x, "LastPlayed") == 1)
	{
		// If we add one, will we exceed our available indices?
		if ($x + 1 > mysql_num_rows($clockwheel) - 1)
		{
			// Break the loop so we can start the clockwheel over...
			break;
		}
		else
		{
			// Get the next element in the clockwheel
			mysql_data_seek($clockwheel, $x + 1);
			$ClockwheelElement = mysql_fetch_assoc($clockwheel);
			break;
		}
	}
}

// If there isn't a clockwheel element in place yet, we need to start from the beginning of the clockwheel...
if (!isset($ClockwheelElement))
{
	mysql_data_seek($clockwheel, 0);
	$ClockwheelElement = mysql_fetch_assoc($clockwheel);
}

// STEP 2: UPDATE THE CLOCKWHEEL TO UPDATE THE CURRENT POSITION...
$sql = "UPDATE `clockwheelelements` SET `LastPlayed`='1' WHERE `ID`='" . $ClockwheelElement['ID'] . "'";
mysql_query($sql);
$sql = "UPDATE `clockwheelelements` SET `LastPlayed`='0' WHERE `ID`!='" . $ClockwheelElement['ID'] . "'";
mysql_query($sql);

// STEP 3: START FORMLUATING OUR SQL TO HIT AGAINST THE SAM BROADCASTER DATABASE...

$sql = "SELECT * FROM `clockwheelcategories` WHERE `ElementID`='" . $ClockwheelElement['ID'] . "'";
$ClockwheelCategories = mysql_query($sql);

$sql = "SELECT * FROM `clockwheelrules` WHERE `ElementID`='" . $ClockwheelElement['ID'] . "'";
if ($ClockwheelElement['MasterRulesApply'] == '1')
{
	$sql .= " OR `ElementID`='-1'";
}
$ClockwheelRules = mysql_query($sql);

mysql_select_db('samdb');

$sql = "SELECT `songlist`.`ID`, `artist`, `title`, `album`, `duration`, `filename` FROM ";
if (HasCategories($ClockwheelCategories))
{
	// It's more efficient tihs way...
	$sql .= "`categorylist` INNER JOIN `songlist` ON `songlist`.`ID`=`categorylist`.`songID` ";
}
else
{
	// But if there are no category restrictions, then just pick everything...
	$sql .= "`songlist` ";
}

if (ContainsEditedOnly($ClockwheelRules))
{
	$sql .= "LEFT OUTER JOIN `songlogic`.`extendedsonginfo` ON `extendedsonginfo`.`SongID`=`songlist`.`ID` ";
}

/*if (ContainsEIIP($ClockwheelRules) && !HasCategories($ClockwheelCategories))
{
	$sql .= "LEFT OUTER JOIN `categorylist` ON `categorylist`.`songID`=`songlist`.`ID` ";
}*/

// First, let's pull in our categories...
$sql .= "WHERE (";
for ($x = 0; $x < mysql_num_rows($ClockwheelCategories); $x++)
{
	if (mysql_result($ClockwheelCategories, $x, "CategoryID") > 0)
	{
		$sql .= "(" . buildCategoriesToSearch(mysql_result($ClockwheelCategories, $x, "CategoryID"),
												mysql_result($ClockwheelCategories, $x, "IncludeRoot"),
												mysql_result($ClockwheelCategories, $x, "Recursive")) . ") OR ";
	}
	else
	{
		$sql .= "`songtype`='";
		switch (mysql_result($ClockwheelCategories, $x, "CategoryID"))
		{
			case -2:
				$sql .= "S";
				break;
			case -4:
				$sql .= "I";
				break;
		}
		$sql .= "' OR ";
	}
}
if (substr($sql, strlen($sql) - 4) == " OR ")
{
	$sql = substr($sql, 0, strlen($sql) - 4);
}
$sql .= ")";

// Secondly, let's pull in our rules...
if (mysql_num_rows($ClockwheelRules) > 0)
{
	$sql .= " AND ";
	mysql_data_seek($ClockwheelRules, 0);
	for ($x = 0; $x < mysql_num_rows($ClockwheelRules); $x++)
	{
		$Rule = mysql_fetch_assoc($ClockwheelRules);
		if ($Rule['RuleType'] == 'EIIP') // Exclude if in playlist...
		{
			// This means we need to exclude any songs that match a certain categoryID...
//			$sql .= "`categoryID`!='" . addslashes($Rule['RuleValue']) . "' AND ";
		}
		else if ($Rule['RuleType'] == 'ArtistSep') // Minimum of X seconds since date_artist_played...
		{
			$sql .= "`date_artist_played` < '" . date("Y-m-d H:i:s", time() - $Rule['RuleValue']) . "' AND ";
		}
		else if ($Rule['RuleType'] == 'TrackSep') // Minimum of X seconds since date_track_played...
		{
			$sql .= "`date_played` < '" . date("Y-m-d H:i:s", time() - $Rule['RuleValue']) . "' AND ";
		}
		else if ($Rule['RuleType'] == 'TitleSep') // Minimum of X seconds since date_title_played...
		{
			$sql .= "`date_title_played` < '" . date("Y-m-d H:i:s", time() -$Rule['RuleValue']) . "' AND ";
		}
		else if ($Rule['RuleType'] == 'AlbumSep') // Minimum of X seconds since date_album_played...
		{
			$sql .= "`date_album_played` < '" . date("Y-m-d H:i:s", time() - $Rule['RuleValue']) . "' AND ";
		}
		else if ($Rule['RuleType'] == 'Year')
		{
			$sql .= "`albumyear` >= " . $Rule['RuleValue'] . " AND ";
		}
		else if ($Rule['RuleType'] == 'EditedOnly') // This one will be tricky...
		{
			$sql .= "`Censored`='1' AND ";
		}
//		print_r($Rule);
	}
	if (substr($sql, strlen($sql) - 4) == "AND ")
	{
		$sql = substr($sql, 0, strlen($sql) - 4);
	}
	else if (substr($sql, strlen($sql) - 3) == "OR ")
	{
		$sql = substr($sql, 0, strlen($sql) - 3);
	}
	
	if (strstr($_SERVER['HTTP_USER_AGENT'], "Chrome"))
	{
		echo $sql;
	}
}

// Thirdly, let's determine our selection method.  If it's random, this doesn't matter, but
// if the logic is for "least recently played" or something where order matters, it'd be
// better to have SQL sort the data than having to do it ourselves...
if ($ClockwheelElement['SelectionMethod'] == 'LRP')
{
	$sql .= " ORDER BY `date_played`";
}
else if ($ClockwheelElement['SelectionMethod'] == 'LRPA')
{
	$sql .= " ORDER BY `date_artist_played`";
}

// STEP 4: PICK A SONG...
$Songs = mysql_query($sql);
//echo mysql_error();
//echo $sql;
// TEMPORARY::
if (!$Songs)
{
	$ClockwheelElement['SelectionMethod'] = 'Random';
	$Songs = mysql_query("SELECT * FROM `songlist` WHERE `songtype`='S'");
}

if ($ClockwheelElement['SelectionMethod'] == 'Random')
{
	mysql_data_seek($Songs, rand(0, mysql_num_rows($Songs) - 1));
}

$Song = mysql_fetch_assoc($Songs);

// Fill in our XML response
// htmlspecialchars($Song["filename"])
$xml = "<?xml version=\"1.0\"?>
<LOGIC>
   <song>
    <songID>" . htmlspecialchars($Song["ID"]) . "</songID>
	<artist>" . htmlspecialchars($Song["artist"]) . "</artist>
	<title>" . htmlspecialchars($Song["title"]) . "</title>
	<album>" . htmlspecialchars($Song["album"]) . "</album>
	<duration>" . $Song["duration"] . "</duration>
   </song>
</LOGIC>";

header("Content-type: text/xml", true);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

echo $xml;

mysql_close();
?>