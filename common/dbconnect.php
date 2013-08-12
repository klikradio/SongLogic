<?
if (mysql_connect("localhost", "songlogic", "..."))
{
	if (!mysql_select_db("songlogic"))
	{
		die("Couldn't change database");
	}
}
else
{
	die("Couldn't connect to database");
}
?>