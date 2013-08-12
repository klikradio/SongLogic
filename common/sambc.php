<?
function CreateCategory($name, $parentID)
{
	$sql = "SELECT `levelindex` FROM `category` WHERE `ID`='" . addslashes($parentID) . "'";
	$query = mysql_query($sql);
	if (mysql_num_rows($query) > 0)
	{
		$levelID = mysql_result($query, 0) + 1;
		
		$sql = "SELECT MAX(`itemindex`) FROM `category` WHERE `parentID`='" . addslashes($parentID) . "'";
		$query = mysql_query($sql);
		if (mysql_num_rows($query) > 0)
		{
			$itemindex = mysql_result($query, 0) + 1;
			$sql = "INSERT INTO `category` (`name`, `parentID`, `levelindex`, `itemindex`) VALUES ('" . addslashes($name) . "', '" . addslashes($parentID) . "', '$levelID', '$itemindex')";
		}
		else
		{
			$sql = "INSERT INTO `category` (`name`, `parentID`, `levelindex`) VALUES ('" . addslashes($name) . "', '" . addslashes($parentID) . "', '$levelID')";
		}
	}
	else
	{
		$sql = "INSERT INTO `category` (`name`, `parentID`) VALUES ('" . addslashes($name) . "', '" . addslashes($parentID) . "')";
	}
	mysql_query($sql);
	return mysql_insert_id();
}
?>