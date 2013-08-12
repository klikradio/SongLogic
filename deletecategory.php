<?
include("common/dbconnect.php");

$sql = "DELETE FROM `clockwheelcategories` WHERE `ElementID`='" . addslashes($_GET["ElementID"]) . "' AND `CategoryID`='" . addslashes($_GET["CategoryID"]) . "'";
mysql_query($sql);

mysql_close();

header("Location: choosecategories.php?ID=" . $_GET["ElementID"]);
?>