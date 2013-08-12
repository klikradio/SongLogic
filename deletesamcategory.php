<?
include("common/dbconnect.php");

$sql = "DELETE FROM `clockwheelcategories` WHERE `CategoryID`='" . addslashes($_GET["ID"]) . "'";
mysql_query($sql);

mysql_select_db("samdb");
$sql = "DELETE FROM `categorylist` WHERE `categoryID`='" . addslashes($_GET["ID"]) . "'";
mysql_query($sql);

$sql = "SELECT `parentID` FROM `category` WHERE `ID`='" . addslashes($_GET['ID']) . "'";
$query = mysql_query($sql);
$returnID = mysql_result($query, 0);

$sql = "DELETE FROM `category` WHERE `ID`='" . addslashes($_GET['ID']) . "'";
mysql_query($sql);

mysql_close();

header("Location: songs.php?ID=$returnID");
?>