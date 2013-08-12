<?
include("common/dbconnect.php");

$sql = "SELECT MAX(`SortID`) FROM `clockwheelelements` WHERE `ClockID`='1'";
$query = mysql_query($sql);
$SortID = mysql_result($query, 0) + 1;

$sql = "INSERT INTO `clockwheelelements` (`ClockID`, `SortID`, `Comments`, `SelectionMethod`, `MasterRulesApply`) VALUES";

$sql .= " ('1', '$SortID', '" . addslashes($_POST["Comments"]) . "', '";
$sql .= addslashes($_POST["SelectionMethod"]) . "', '";

if ($_POST[""] == '1')
{
	$sql .= "0";
}
else
{
	$sql .= '1';
}

$sql .= "')";

mysql_query($sql);
$ID = mysql_insert_id();

mysql_close();

header("Location: choosecategories.php?ID=$ID");
?>