<?
function writeCats($category, $categories)
{
	$sql = "SELECT `ID`, `name` FROM `category` WHERE `parentID`='" . addslashes($category) . "' ORDER BY `name`";
	$query = mysql_query($sql);
	if (mysql_num_rows($query) > 0)
	{
		echo '<ul style="list-style-type:none">';
		
		for ($x = 0; $x  < mysql_num_rows($query); $x++)
		{
			echo '<label><input type="checkbox" name="CategoryID[]" value="' . mysql_result($query, $x, "ID") . '" ';
			if (in_array(mysql_result($query, $x, "ID"), $categories))
			{
				echo 'checked="checked"';
			}
			echo '/>';
			echo mysql_result($query, $x, "name") . '</label><br />';
			writeCats(mysql_result($query, $x, "ID"), $categories);
		}
		echo '</ul>';
	}
}
function writeCategories($category, $categories)
{
	writeCats($category,  $categories);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>KLIK Song</title>
</head>

<body>
<h1>Song Categorization</h1>
<h2><?
include("common/dbconnect.php");

mysql_select_db('samdb');

$sql = "SELECT * FROM `songlist` WHERE `ID`='" . addslashes($_GET['ID']) . "'";
$query = mysql_query($sql);

if (mysql_num_rows($query) > 0)
{
	echo mysql_result($query, 0, "artist") . " - " . mysql_result($query, 0, "title");
}
?></h2>
<form id="form1" name="form1" method="post" action="updatesongcats.php">
<input type="hidden" name="SongID" value="<? echo $_GET['ID']; ?>" />
<input type="hidden" name="ReturnID" value="<? echo $_GET['CategoryID']; ?>" />
<?
$sql = "SELECT `categoryID` FROM `categorylist` WHERE `songID`='" . addslashes($_GET['ID']) . "'";
$query = mysql_query($sql);
for ($x = 0; $x < mysql_num_rows($query); $x++)
{
	$CategoryID[] = mysql_result($query, $x);
}

writeCategories(-2, $CategoryID);
?>
<input type="submit" value="Update Categories" />
</form>
<?
mysql_close();
?>
</body>
</html>