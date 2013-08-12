<?
include("common/dbconnect.php");
mysql_select_db("samdb");

for ($x = 0; $x < sizeof($_POST['SongID']); $x++)
{
	$sql = "INSERT INTO `categorylist` (`songID`, `categoryID`) VALUES ('" . addslashes($_POST['SongID'][$x]) . "', '" . addslashes($_POST['CategoryID']) . "')";
	mysql_query($sql);
}

mysql_close();
header("Location: songs.php?ID=" . $_POST["ReturnID"]);
?>