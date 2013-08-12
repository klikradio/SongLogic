<?
if (!isset($_GET['page']))
{
	$_GET['page'] = 0;
}
function writeCats($category, $level)
{
	$sql = "SELECT `ID`, `name` FROM `category` WHERE `parentID`='" . addslashes($category) . "' ORDER BY `name`";
	$query = mysql_query($sql);
	for ($x = 0; $x  < mysql_num_rows($query); $x++)
	{
		for ($y = 0; $y < $level; $y++)
		{
			echo "&nbsp;&nbsp;&nbsp;";
		}
		echo '<a href="songs.php?ID=' . mysql_result($query, $x, "ID") . '">';
		if ($_GET["ID"] == mysql_result($query, $x, "ID"))
		{
			echo '<b>';
		}
		
		echo mysql_result($query, $x, "name") . '</a><br />';
		
		if ($_GET["ID"] == mysql_result($query, $x, "ID"))
		{
			echo '</b>';
		}
		
		writeCats(mysql_result($query, $x, "ID"), $level + 1);
	}
}
function writeCategories($category)
{
	writeCats($category, 0);
}

function writeCatsToDropDown($category, $level)
{
	$sql = "SELECT `ID`, `name` FROM `category` WHERE `parentID`='" . addslashes($category) . "' ORDER BY `name`";
	$query = mysql_query($sql);
	for ($x = 0; $x  < mysql_num_rows($query); $x++)
	{
		echo '<option value="' . mysql_result($query, $x, "ID") . '">';
		for ($y = 0; $y < $level; $y++)
		{
			echo "&nbsp;&nbsp;&nbsp;";
		}
		echo mysql_result($query, $x, "name");
		echo '</option>';
		
		writeCatsToDropDown(mysql_result($query, $x, "ID"), $level + 1);
	}
}
function writeCategoriesToDropDown()
{
	writeCatsToDropDown(-2, 0);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>KLIK Songs</title>
<script language="javascript">
function deleteCategory()
{
	if (confirm('Are you sure you want to delete this category?'))
	{
		window.location='deletesamcategory.php?ID=<? echo $_GET["ID"]; ?>';
	}
}
</script>
</head>

<body vlink="#0000FF">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="200" valign="top">
    <p style="margin-top: 0px;"><a href="songs.php?ID=-2">Music (All)</a></p>
	<p><?
	include("common/dbconnect.php");
	
	mysql_select_db("samdb");
	
	writeCategories(-2);
	
    ?></p>
    <p><a href="songs.php?ID=u">Uncategorized Songs</a></p>
    <form action="createcategory.php" style="margin-top: 0px; margin-bottom: 0px;" method="post">
    <label for="name">Create Category: </label><br /><input name="name" type="text" /><br />
    <input type="submit" name="button" id="button" value="Create" />
    <input type="hidden" value="-2" name="parentID" />
    <input type="hidden" value="<? echo $_GET['ID']; ?>" name="returnID" />
    </form>
    </td>
    <td valign="top"><h1 style="margin-top: 0px;"><?
		echo "Bulk Categorize Songs With Years";
    ?></h1>

    <h2>Songs</h2>
        
    <form action="bulkcatsongswithyear.php" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <?
	if ($_GET['ID'] == 'u' || !isset($_GET['ID']))
	{
		$sql = "SELECT DISTINCT(`songID`) FROM `categorylist`";
		$query = mysql_query($sql);
		
		$sql = 'SELECT * FROM `songlist` WHERE ';
		for ($x = 0; $x < mysql_num_rows($query); $x++)
		{
			$sql .= "`ID`!='" . mysql_result($query, $x) . "' AND ";
		}
		$sql .= "`songtype`='S' ORDER BY `date_added` DESC";
	}
	else
	{
		$sql = "SELECT `songlist`.* FROM `categorylist` INNER JOIN `songlist` ON `songlist`.`ID`=`categorylist`.`songID` WHERE `categorylist`.`categoryID`='" . addslashes($_GET['ID']) . "' AND `songtype`='S' ORDER BY `date_added` DESC";
	}
	
	$query = mysql_query("SELECT * FROM `category` WHERE `parentID`='-2' ORDER BY `name`");
	for ($x = 0; $x < mysql_num_rows($query); $x++)
	{
		$cat['ID'] = mysql_result($query, $x, "ID");
		$cat['name'] = mysql_result($query, $x, "name");
		$categories[] = $cat;
	}
	
	if ($_GET['ID'] != 'u' || (isset($_GET['ID']) && $_GET['ID'] != 'u'))
	{
		$bigsql = "SELECT * FROM `songlist` WHERE ";
		$query = mysql_query($sql);
		for ($x = 0; $x < mysql_num_rows($query); $x++)
		{
			$sql = "SELECT COUNT(*) FROM `categorylist` WHERE `songID`='" . mysql_result($query, $x, "ID") . "' AND `categoryID`!='" . addslashes($_GET['ID']) . "'";
			$subquery = mysql_query($sql);
			if (mysql_result($subquery, 0) == 0)
			{
				$bigsql .= "`ID`='" . mysql_result($query, $x, "ID") . "' OR ";
			}
		}
		if (substr($bigsql, strlen($bigsql) - 3) == 'OR ')
		{
			$bigsql = substr($bigsql, 0, strlen($bigsql) - 3);
		}
		$bigsql .= "ORDER BY `artist`, `title` DESC LIMIT ";
		if (isset($_GET['page']))
		{
			$bigsql .= ($_GET['page'] * 10) . ", 10";
		}
		else
		{
			$bigsql .= "10";
		}
		
		$query = mysql_query($bigsql);
	}
	else
	{
		$sql .= " LIMIT ";
		if (isset($_GET['page']))
		{
			$sql .= ($_GET['page'] * 10) . ", 10";
		}
		else
		{
			$sql .= "10";
		}
		$query = mysql_query($sql);
	}
		for ($x = 0; $x < mysql_num_rows($query); $x++)
		{
			$song = mysql_fetch_assoc($query);
			?>
      <tr<? if ($x % 2) { echo ' bgcolor="#DDDDDD"'; } ?>>
        <td width="65"><input type="hidden" name="ID[]" value="<? echo $song['ID']; ?>" />
          <a href="/music/<? echo str_replace("C:\\Users\\KLIK Volunteer\\Music\\", "", $song['filename']); ?>">Play</a></td>
        <td><? echo $song['artist']; ?></td>
        <td><? echo $song['title']; ?></td>
        <td><select name="genre[]" id="select">
        	<option value="-1">Skip for Now</option>
    	<?
		foreach ($categories as $category)
		{
			?>
            <option value="<? echo $category["ID"]; ?>"><? echo $category["name"]; ?></option>
            <?
		}
        ?>
    </select> <input name="albumyear[]" type="text" value="<? echo $song['albumyear']; ?>" size="5" /></td>
      </tr>      
            <?
		}
	?>
    </table>
    
    <div style="width: 50%; float: left;">
    <input type="hidden" name="ReturnID" value="<? echo $_GET["ID"]; ?>" />
    <input type="submit" value="Categorize" />
    </div>
    
    <div style="width: 50%; float: right;" align="right">
    <?
	$keys = array_keys($_GET);
	if ($_GET["page"] > 0)
	{
		echo '<a href="?';
		foreach ($keys as $key)
		{
			if (strtolower($key) == 'page')
			{
				echo "page=" . ($_GET['page'] - 1) . "&";
			}
			else
			{
				echo "$key=" . $_GET[$key] . "&";
			}
		}
		echo '">Previous</a>';
	}
	
	if (mysql_num_rows($query) == 10)
	{
		if ($_GET['page'] > 0)
		{
			echo ' | ';
		}
		
		echo '<a href="?';
		foreach ($keys as $key)
		{
			if (strtolower($key) == 'page')
			{
				echo "page=" . ($_GET['page'] + 1) . "&";
			}
			else
			{
				echo "$key=" . $_GET[$key] . "&";
			}
		}
		echo '">Next</a>';
	}
    ?>
    </div>
    
    </form>
    
    </td>
  </tr>
</table>
</body>
</html>