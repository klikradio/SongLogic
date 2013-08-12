<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>KLIK Clockwheel</title>
<script language="javascript">
function chooseCategories(id)
{
	window.open('choosecategories.php?ID=' + id, "categories", "width=800, height=600");
}
function chooseRules(id)
{
	window.open('chooserules.php?ID=' + id, "rules", "width=800, height=600");
}
function deleteElement(id)
{
	if (confirm('Are you sure you want to delete this element?'))
	{
		window.location='deleteclockelement.php?ID=' + id;
	}
}
function verifyForm()
{
//	document.forms[''].item('').value
	return false;
}
</script>
</head>

<body>
<form id="form1" name="form1" method="post" action="updateclock.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100"><strong>Sort Number</strong></td>
      <td><strong>Comments</strong></td>
      <td><strong>Song Selection Method</strong></td>
      <td><strong>Exempt from Master Rules</strong></td>
      <td><a href="javascript:chooseRules(-1)">Master Rules</a></td>
    </tr>
    <?
	include("common/dbconnect.php");
	
	if (!isset($_GET['ID']))
	{
		$_GET["ID"] = '1';
	}
	
	$sql = "SELECT * FROM `clockwheelelements` WHERE `ClockID`='" . addslashes($_GET["ID"]) . "' ORDER BY `SortID`";
	$query = mysql_query($sql);
	
	for ($x = 0; $x < mysql_num_rows($query); $x++)
	{
    ?>
    <tr>
      <td width="100">
      <input name="SortNumber[<? echo mysql_result($query, $x, "ID"); ?>]" type="text" id="SortNumber[]" size="6" value="<? echo mysql_result($query, $x, "SortID"); ?>" /></td>
      <td><input name="Comments[<? echo mysql_result($query, $x, "ID"); ?>]" type="text" id="Comments[]" size="50" value="<? echo mysql_result($query, $x, "Comments"); ?>" /></td>
      <td>
        <select name="SongSelection[<? echo mysql_result($query, $x, "ID"); ?>]" id="SongSelection">
          <option<? if (mysql_result($query, $x, "SelectionMethod") == 'Random') { echo ' selected="selected"'; } ?>>Random</option>
          <option<? if (mysql_result($query, $x, "SelectionMethod") == 'LRP') { echo ' selected="selected"'; } ?>>LRP</option>
          <option<? if (mysql_result($query, $x, "SelectionMethod") == 'LRPA') { echo ' selected="selected"'; } ?>>LRPA</option>
      </select></td>
      <td><input name="MasterExempt[<? echo mysql_result($query, $x, "ID"); ?>]" type="checkbox" id="MasterExempt[]" value="1"<? if (mysql_result($query, $x, "MasterRulesApply") == '0') { echo ' checked="checked" '; } ?>/></td>
      <td><a href="javascript:chooseCategories(<? echo mysql_result($query, $x, "ID"); ?>)">Categories</a> | <a href="javascript:chooseRules(<? echo mysql_result($query, $x, "ID"); ?>)">Rules</a> | <a href="javascript:deleteElement(<? echo mysql_result($query, $x, "ID"); ?>)">Delete</a></td>
    </tr>
    <?
	}
	
	mysql_close();
	?>
  </table>
  <input type="submit" value="Update Elements" />
</form>
<hr />
<h2>Add Element</h2>
<form action="addelement.php" method="post" target="categories" onsubmit="window.open('about:blank', 'categories', 'width=800, height=600')">
  <label for="Comments">Comment:</label>
  <input name="Comments" type="text" id="Comments" size="50" />
  <br />
  <label for="SelectionMethod">Song Selection Method:</label>
  <select name="SelectionMethod" id="SelectionMethod">
    <option>Random</option>
    <option>LRP</option>
    <option>LRPA</option>
  </select>
  <br />
  <input type="checkbox" name="checkbox" id="checkbox" />
  <label for="checkbox">Exempt from master rules</label>
<br />
  <input type="submit" name="button" id="button" value="Add Category" />
<br />
</form>
</body>
</html>
