<?
include("common/dbconnect.php");

$keys = array_keys($_POST["Selection"]);
for ($x = 0; $x < sizeof($keys); $x++)
{
	$sql = "UPDATE `clockwheelcategories` SET `Recursive`='";
	if ($_POST["Selection"][$keys[$x]] == 'recursive')
	{
		$sql .= "1";
	}
	else if ($_POST["Selection"][$keys[$x]] == 'recursiveonly')
	{
		$sql .= "1";
	}
	else
	{
		$sql .= "0";
	}
	$sql .= "', `IncludeRoot`='";
	if ($_POST["Selection"][$keys[$x]] == 'recursive')
	{
		$sql .= "1";
	}
	else if ($_POST["Selection"][$keys[$x]] == 'recursiveonly')
	{
		$sql .= "0";
	}
	else
	{
		$sql .= "1";
	}
	$sql .= "' WHERE `ElementID`='" . addslashes($_POST["ElementID"]) . "' AND `CategoryID`='";
	$sql .= addslashes($keys[$x]) . "'";
	mysql_query($sql);
}

mysql_close();
header("Location: choosecategories.php?ID=" . $_POST["ElementID"]);
?>