<?
include("common/dbconnect.php");

$sql = "DELETE FROM `clockwheelelements` WHERE `ID`='" . addslashes($_GET["ID"]) . "'";
mysql_query($sql);

mysql_close();

header("Location: clockwheel.php");
?>