<?
include("common/sambc.php");
include("common/dbconnect.php");

mysql_select_db("samdb");
CreateCategory($_POST['name'], $_POST['parentID']);
mysql_close();

header("Location: songs.php?ID=" . $_POST["returnID"]);
?>