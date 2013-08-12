<?
if (!isset($_GET["ID"]))
{
	$_GET["ID"] = -2;
}
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
    <p style="margin-top: 0px;"><a href="songs.php?ID=-2"><? if ($_GET['ID'] == -2) { echo '<b>'; } ?>Music (All)<? if ($_GET['ID'] == -2) { echo '</b>'; } ?></a></p>
	<p><?
	include("common/dbconnect.php");
	
	mysql_select_db("samdb");
	
	writeCategories(-2);
	
    ?></p>
    <p><a href="songs.php?ID=u"><? if (strtolower($_GET['ID']) == 'u') { echo '<b>'; } ?>Uncategorized Songs<? if (strtolower($_GET['ID']) == 'u') { echo '</b>'; } ?></a><br />
    <a href="catsongswithyear.php">Assign Year/Genre to Uncat Songs</a></p>
    <form action="createcategory.php" style="margin-top: 0px; margin-bottom: 0px;" method="post">
    <label for="name">Create Category: </label><br /><input name="name" type="text" /><br />
    <input type="submit" name="button" id="button" value="Create" />
    <input type="hidden" value="-2" name="parentID" />
    <input type="hidden" value="<? echo $_GET['ID']; ?>" name="returnID" />
    </form>
    </td>
    <td valign="top"><h1 style="margin-top: 0px;"><?
	if ($_GET["ID"] == -2)
	{
		echo "Music (All)";
	}
	else if (strtolower($_GET['ID']) == 'u')
	{
		echo "Uncategorized Songs";
	}
	else
	{
		$sql = "SELECT `name` FROM `category` WHERE `ID`='" . addslashes($_GET['ID']) . "'";
		$query = mysql_query($sql);
		echo mysql_result($query, 0);
	}
    ?></h1>
    <?
	if (strtolower($_GET["ID"]) != 'u' && (!isset($_GET['page']) || $_GET['page'] == 0))
	{
		?>
    <h2 style="margin-bottom: 0px;">Subcategories</h2>
    <form action="createcategory.php" style="margin-top: 0px; margin-bottom: 0px;" method="post">
    <label for="name">Create Subcategory: </label><input name="name" type="text" />
    <input type="submit" name="button" id="button" value="Create" />
    <input type="hidden" value="<? echo $_GET["ID"]; ?>" name="parentID" />
    <input type="hidden" value="<? echo $_GET['ID']; ?>" name="returnID" />
    </form>
    <?  //if ($_GET["ID"] > 0)
		//{
			$sql = "SELECT * FROM `category` WHERE `parentID`='" . addslashes($_GET['ID']) . "' ORDER BY `name`";
			$query = mysql_query($sql);
			if (mysql_num_rows($query) > 0)
			{
				?>
				<ul style="margin-top: 0px;">
				<?
				for ($x = 0; $x < mysql_num_rows($query); $x++)
				{
					echo "<li><a href=\"songs.php?ID=" . mysql_result($query, $x, "ID") . "\">" . mysql_result($query, $x, "name") . "</a></li>";
				}
				?>
				</ul>
				<?
			}
		//}
	}
	?>
    <h2>Songs</h2>
    
    <form action="bulkcategorize.php" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <?
	if (strtolower($_GET["ID"]) == 'u')
	{
		$sql = "SELECT DISTINCT(`songID`) FROM `categorylist`";
		$query = mysql_query($sql);
		
		$sql = 'SELECT * FROM `songlist` WHERE ';
		for ($x = 0; $x < mysql_num_rows($query); $x++)
		{
			$sql .= "`ID`!='" . mysql_result($query, $x) . "' AND ";
		}
		$sql .= "`songtype`='S'";
	}
	else if ($_GET["ID"] > 0)
	{
		$sql = "SELECT `songlist`.* FROM `categorylist` INNER JOIN `songlist` ON `songlist`.`ID`=`categorylist`.`songID` WHERE `categorylist`.`categoryID`='";
		$sql .= addslashes($_GET['ID']) . "' AND `songlist`.`songtype`='S'";
	}
	else if ($_GET['ID'] == -2)
	{
		$sql = "SELECT * FROM `songlist` WHERE `songtype`='S'";
	}
	if ($_GET['ID'] == 'u')
	{
		$sql .= " ORDER BY `date_added` DESC LIMIT ";
	}
	else
	{
		$sql .= " ORDER BY `artist`, `title` LIMIT ";
	}
	if (isset($_GET['page']))
	{
		$sql .= ($_GET['page'] * 50) . ", 50";
	}
	else
	{
		$sql .= "50";
	}
	$query = mysql_query($sql);
		for ($x = 0; $x < mysql_num_rows($query); $x++)
		{
			$song = mysql_fetch_assoc($query);
			?>
      <tr<? if ($x % 2) { echo ' bgcolor="#DDDDDD"'; } ?>>
        <td width="65">
          <input type="checkbox" name="SongID[]" id="checkbox" value="<? echo $song['ID']; ?>" />&nbsp; 
          <a href="/music/<? echo str_replace("C:\\Users\\KLIK Volunteer\\Music\\", "", $song['filename']); ?>">Play</a></td>
        <td><? echo $song['artist']; ?></td>
        <td><? echo $song['title']; ?></td>
        <td width="100"><a href="songcategory.php?ID=<? echo $song['ID']; ?>&CategoryID=<? echo $_GET['ID']; ?>">Categories</a></td>
      </tr>      
            <?
		}
	?>
    </table>
    
    <div style="width: 50%; float: left;">
    <select name="CategoryID" id="select">
    	<?
		mysql_select_db("samdb");
		writeCategoriesToDropDown();
        ?>
    </select>
    <input type="hidden" name="ReturnID" value="<? echo $_GET["ID"]; ?>" />
    <input type="submit" value="Categorize" />
    </div>
    
    <div style="width: 50%; float: right;" align="right">
    <?
	$keys = array_keys($_GET);
	if ($_GET["page"] > 0)
	{
		echo '<a href="songs.php?';
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
	
	if (mysql_num_rows($query) == 50)
	{
		if ($_GET['page'] > 0)
		{
			echo ' | ';
		}
		
		echo '<a href="songs.php?';
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

<?
if ($_GET["ID"] >= 0)
{
	?>
    <div>
    <p align="left"><a href="catsongswithyears.php?ID=<? echo $_GET['ID']; ?>">Categorize the Songs in this Category by Year and Genre</a><br /><a href="javascript:deleteCategory()">Delete Category</a></p>
    </div>
    <?
}
?>
    
    </td>
  </tr>
</table>
</body>
</html>