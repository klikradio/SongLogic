<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?
$get = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=track.getinfo&api_key=02ebc4801d6302410cd413154050b02a&artist=cher&track=believe");
$xml = new SimpleXMLElement($get);
print_r($xml);
$xml = new SimpleXMLElement(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=02ebc4801d6302410cd413154050b02a&mbid=" . $xml->track->album->mbid));
print_r($xml->album);
?>
</body>
</html>