<?
include("common/dbconnect.php");
mysql_select_db("samdb");

$sql = "DELETE FROM `categorylist` WHERE `songID`='" . addslashes($_POST['SongID']) . "'";
mysql_query($sql);

for ($x = 0; $x < sizeof($_POST['CategoryID']); $x++)
{
	$sql = "INSERT INTO `categorylist` (`songID`, `categoryID`) VALUES ('" . addslashes($_POST['SongID']) . "', '" . addslashes($_POST['CategoryID'][$x]) . "')";
	mysql_query($sql);
}

mysql_close();
header("Location: songs.php?ID=" . $_POST['ReturnID']);
?>