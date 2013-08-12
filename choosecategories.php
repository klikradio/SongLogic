<?
function writeCats($category, $level)
{
	$sql = "SELECT `ID`, `name` FROM `category` WHERE `parentID`='" . addslashes($category) . "' ORDER BY `name`";
	$query = mysql_query($sql);
	for ($x = 0; $x  < mysql_num_rows($query); $x++)
	{
		echo "<option value=\"" . mysql_result($query, $x, "ID") . "\">";
		for ($y = 0; $y < $level; $y++)
		{
			echo "&nbsp;&nbsp;&nbsp;";
		}
		echo mysql_result($query, $x, "name");
		echo "</option>";
		writeCats(mysql_result($query, $x, "ID"), $level + 1);
	}
}
function writeCategories($category)
{
	writeCats($category, 1);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Categories</title>
<script language="javascript">
function deleteCategory(id)
{
	if (confirm("Are you sure you want to delete this category?"))
	{
		window.location='deletecategory.php?ElementID=<? echo $_GET["ID"]; ?>&CategoryID=' + id;
	}
}
</script>
</head>

<body>
<?
include("common/dbconnect.php");

$sql = "SELECT * FROM `songlogic`.`clockwheelcategories` LEFT OUTER JOIN `samdb`.`category` ON `category`.`ID`=`clockwheelcategories`.`CategoryID` WHERE `ElementID`='" . addslashes($_GET["ID"]) . "'";
$query = mysql_query($sql);
?>
<h3>Current Categories</h3>
<form action="updatecategories.php" method="post">
<input type="hidden" name="ElementID" value="<? echo $_GET["ID"]; ?>" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>Category Name</strong></td>
    <td><strong>Selection Parameters</strong></td>
    <td>&nbsp;</td>
  </tr>
<?
for ($x = 0; $x < mysql_num_rows($query); $x++)
{
?>

  <tr>
    <td><?
	if (mysql_result($query, $x, "CategoryID") >= 0)
	{
	    echo mysql_result($query, $x, "name");
	}
	else
	{
		switch (mysql_result($query, $x, "CategoryID"))
		{
			case -2:
				echo "Music (All)";
				break;
			case -4:
				echo "Station ID's (All)";
				break;
		}
	}?></td>
    <td>
      <select name="Selection[<? echo mysql_result($query, $x, "ID"); ?>]" id="Selection[]">
        <option value="recursive"<? if (mysql_result($query, $x, "Recursive") == '1' && mysql_result($query, $x, "IncludeRoot") == '1') { echo ' selected="selected" '; } ?>>I want to include songs in both this category as well as its subcategories</option>
        <option value="standard"<? if (mysql_result($query, $x, "Recursive") == '0' && mysql_result($query, $x, "IncludeRoot") == '1') { echo ' selected="selected" '; } ?>>I want to include songs in this category, but not its subcategories</option>
        <option value="recursiveonly"<? if (mysql_result($query, $x, "Recursive") == '1' && mysql_result($query, $x, "IncludeRoot") == '0') { echo ' selected="selected" '; } ?>>I do not want to include songs in this category, but only its subcategories</option>
      </select></td>
    <td><a href="javascript:deleteCategory(<? echo mysql_result($query, $x, "CategoryID"); ?>)">Delete</a></td>
  </tr>
  <?
}
?>
</table>
<input type="submit" value="Update Categories" />
</form>
<hr />
<h3>Add a New Category</h3>
<form id="form1" name="form1" method="post" action="addcategory.php">
<input type="hidden" name="ElementID" value="<? echo $_GET["ID"]; ?>" />
  <p>
    <select name="CategoryID" id="select">
    	<option value="-2">Music (All)</option>
    	<?
		mysql_select_db("samdb");
		writeCategories(-2);
        ?>
        <option value="-4">Station IDs (All)</option>
        <?
		writeCategories(-4);
		?>
    </select>
  </p>
  <p>
    <input type="radio" name="Selection" id="radio" value="recursive" />
    <label for="radio">I want to include songs in both this category as well as its subcategories</label><br />
    <input type="radio" name="Selection" id="radio2" value="standard" />
    <label for="radio2">I want to include songs in this category, but not its subcategories</label> <br/>
    <input type="radio" name="Selection" id="radio3" value="recursiveonly" />
    <label for="radio3">I do not want to include songs in this category, but only its subcategories</label>
  </p>
  <blockquote><strong>NOTE: </strong>If things don't seem to be working correctly, remember that a rule you create to apply to this clockwheel element (or, possibly, a master rule) may be interfering with the selection process for this element.</blockquote>
  <p>
    <input type="submit" name="button" id="button" value="Add Category" />
  </p>
</form>
<p>&nbsp;</p>
<?
mysql_close();
?>
</body>
</html>