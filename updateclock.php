<?
include("common/dbconnect.php");

$keys = array_keys($_POST['SortNumber']);
for ($x = 0; $x < sizeof($_POST['SortNumber']); $x++)
{
	$sql = "UPDATE `clockwheelelements` SET `SortID`='" . addslashes($_POST["SortNumber"][$keys[$x]]) . "'";
	$sql .= ", `Comments`='" . addslashes($_POST['Comments'][$keys[$x]]) . "', `SelectionMethod`='";
	$sql .= addslashes($_POST['SongSelection'][$keys[$x]]) . "', `MasterRulesApply`='";
	
	if ($_POST['MasterExempt'][$keys[$x]] == '1')
	{
		$sql .= '0';
	}
	else
	{
		$sql .= '1';
	}
	
	$sql .= "' WHERE `ID`='" . $keys[$x] . "'";
	mysql_query($sql);
}

mysql_close();
header("Location: clockwheel.php", true);
?>