<?
include("common/dbconnect.php");

$sql = "INSERT INTO `clockwheelcategories` (`ElementID`, `CategoryID`, `Recursive`, `IncludeRoot`) VALUES ";
$sql .= "('" . addslashes($_POST["ElementID"]) . "', '" . addslashes($_POST["CategoryID"]) . "', ";

switch ($_POST["Selection"])
{
	case 'recursive':
		$sql .= "'1', '1'";
		break;
	case 'standard':
		$sql .= "'0', '1'";
		break;
	case 'recursiveonly':
		$sql .= "'1', '0'";
		break;
}

$sql .= ")";
mysql_query($sql);

mysql_close();

header("Location: choosecategories.php?ID=" . $_POST["ElementID"]);
?>