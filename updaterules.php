<?
include("common/dbconnect.php");

$keys = array_keys($_POST["RuleValue"]);
for ($x = 0; $x < sizeof($keys); $x++)
{
	$value = $_POST['RuleValue'][$keys[$x]];
	if ($_POST['RuleType'][$keys[$x]] != 'EIIP' &&
		$_POST['RuleType'][$keys[$x]] != 'EditedOnly')
	{
		$value *= 60;
	}
	$value = addslashes($value);
	$sql = "UPDATE `clockwheelrules` SET `RuleValue`='$value' WHERE `ID`='" . addslashes($keys[$x]) . "'";
	mysql_query($sql);
}

mysql_close();
header("Location: chooserules.php?ID=" . $_POST['ID']);
?>