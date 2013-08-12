<?
include("common/sambc.php");
include("common/dbconnect.php");
mysql_select_db("samdb");

print_r($_POST);
// Go through each of them...
for ($x = 0; $x < sizeof($_POST['ID']); $x++)
{
	if ($_POST['genre'][$x] != -1)
	{
		// First, check to make sure the category even exists...
		$sql = "SELECT `name` FROM `category` WHERE `ID`='" . addslashes($_POST['genre'][$x]) . "'";
		$query = mysql_query($sql);
		if (mysql_num_rows($query) > 0)
		{
			$genre = mysql_result($query, 0);
			echo $genre;
			
			$sql = "SELECT `ID` FROM `category` WHERE `name`='$genre " . addslashes($_POST['albumyear'][$x]) . "'";
			$query = mysql_query($sql);
			if (mysql_num_rows($query) > 0)
			{
				$CategoryID = mysql_result($query, 0);
			}
			else
			{
				// Otherwise, we need to create it...
				$CategoryID = CreateCategory("$genre " . $_POST['albumyear'][$x], $_POST['genre'][$x]);
			}
		}
	//	echo $CategoryID;
		
		// Insert this song into this category...
		$sql = "INSERT INTO `categorylist` (`songID`, `categoryID`) VALUES ('" . addslashes($_POST['ID'][$x]) . "', '$CategoryID')";
		mysql_query($sql);
	//	echo mysql_error();
	}
	
	// Update the album year of this song...
	$sql = "UPDATE `songlist` SET `albumyear`='" . addslashes($_POST['albumyear'][$x]) . "' WHERE `ID`='" . addslashes($_POST['ID'][$x]) . "'";
	mysql_query($sql);
}

mysql_close();
if ($_POST['ReturnID'])
{
header("Location: catsongswithyear.php?ID=" . $_POST['ReturnID'], true);
}
else
{
	header("Location: catsongswithyear.php", true);
}
?>