<?
function writeCats($category, $level, $selectedID)
{
	$sql = "SELECT `ID`, `name` FROM `category` WHERE `parentID`='" . addslashes($category) . "' ORDER BY `name`";
	$query = mysql_query($sql);
	for ($x = 0; $x  < mysql_num_rows($query); $x++)
	{
		echo "<option value=\"" . mysql_result($query, $x, "ID") . "\"";
		if ($selectedID == mysql_result($query, $x, "ID"))
		{
			echo " selected=\"selected\"";
		}
		echo ">";
		for ($y = 0; $y < $level; $y++)
		{
			echo "&nbsp;&nbsp;&nbsp;";
		}
		echo mysql_result($query, $x, "name");
		echo "</option>";
		writeCats(mysql_result($query, $x, "ID"), $level + 1, $selectedID);
	}
}
function writeCategories($category, $selectedID)
{
	writeCats($category, 0, $selectedID);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rules</title>
</head>

<body>
<p>Rules help you manage when songs play, and what songs get picked.  Use rules for artist and album separation, blacklisting songs, etc.</p>
<form action="updaterules.php" method="post">
<input name="ID" value="<? echo $_GET['ID']; ?>" type="hidden" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>Rule Type</strong></td>
    <td><strong>Rule Value</strong></td>
    <td>&nbsp;</td>
  </tr>
  <?
  include("common/dbconnect.php");
  
  $sql = "SELECT * FROM `clockwheelrules` WHERE `ElementID`='" . addslashes($_GET["ID"]) . "'";
  $query = mysql_query($sql);
  
  for ($x = 0; $x < mysql_num_rows($query); $x++)
  {
  ?>
  <tr>
    <td>
	<?
	switch (mysql_result($query, $x, "RuleType"))
	{
		case 'EIIP':
			echo "Exclude if in Playlist";
			break;
		case 'ArtistSep':
			echo "Artist Separation";
			break;
		case 'TrackSep':
			echo 'Track Separation';
			break;
		case 'AlbumSep':
			echo "Album Separation";
			break;
		case 'EditedOnly':
			echo "Edited Songs Only";
			break;
		case 'Year':
			echo "Year is >=";
			break;
	}
    ?><input type="hidden" name="RuleType[<? echo mysql_result($query, $x, "ID"); ?>]" value="<? echo mysql_result($query, $x, "RuleType"); ?>" /></td>
    <td><?
	if (mysql_result($query, $x, "RuleType") == 'EIIP')
	{
		// Add a drop down box for categories...
		?>
        <select name="RuleValue[<? echo mysql_result($query, $x, "ID"); ?>]">
        	<?
			mysql_select_db("samdb");
            echo writeCategories(-2, mysql_result($query, $x, "RuleValue")); ?>
        </select>
        <?
	}
	else if (mysql_result($query, $x, "RuleType") == 'EditedOnly')
	{
		?>
        <input name="RuleValue[<? echo mysql_result($query, $x, "ID"); ?>]" type="checkbox" value="1" <?
		if (mysql_result($query, $x, 'RuleValue') == '1')
		{
			echo "checked=\"checked\"";
		}
        ?> />
        <?
	}
	else
	{
		?>
		<input name="RuleValue[<? echo mysql_result($query, $x, "ID"); ?>]" type="text" value="<? echo (mysql_result($query, $x, "RuleValue") / 60); ?>" /> minutes
	<?
	}
    ?></td>
    <td><a href="javascript:deleteRule(<? echo mysql_result($query, $x, "ID"); ?>)">Delete</a></td>
  </tr>
  <?
  }
  mysql_close();
  ?>
</table>
<input type='submit' value='Update Rules' />
</form>
<hr />
<h3>Add a New Rule</h3>
<p>&nbsp;</p>
</body>
</html>