<?php

// Main page HTML header
echo <<<_END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<link rel="stylesheet" href="style.css" type="text/css" media="all"/>
<head>

<title>Venu Music Database</title>

</head>

<body>

<div class="header">Venu Music Database</div>
<div class="wrapper">
_END;

// Read the database access info from a file in the form of
// host:username:password:database:table
$db_auth_file_name = "db_auth.txt";
$auth_info = file_get_contents("$db_auth_file_name") or
        die("Could not get database authorization info.\n<br>");

// Store the password info across some string vars
list($host, $username, $password, $database, $table) = split(":",$auth_info);

// Eastern timezone
date_default_timezone_set('America/New_York');

// Connect to the mysql server
$link = mysql_connect("$host", "$username", "$password");
if (!$link){
    die('Could not connect to the server: ' . mysql_error());
}

// Try to connect to a database
if (!mysql_select_db($database, $link)){
    mysql_close($link);
    die('Could not connect to the database: ' . mysql_error());
    exit;
}

// Get the release # passed in from the url
$rid = $_GET["rid"];

// Build a SQL query based off the rid passed via URL
//$query
$result = mysql_query("SELECT DISTINCT artist, release_name, link, rid, time_added FROM releases WHERE rid=$rid");
$row = mysql_fetch_assoc($result);
if(!$result){
  // Maybe have a clean exit function here
  echo "\n<br>Bad query: " . mysql_error();
} else {
    // Print release info
    print("<font size=\"larger\"><b>Release info:</b></font><br>");
    print("<p>");
    if(isset($row['release_name'])){
        print("<b>Artist:</b> {$row['artist']}<br>\n");
        print("<b>Release:</b> {$row['release_name']}<br>\n");
        print("<b>Link:</b> {$row['link']}<br>\n");
        print("<b>Added:</b>{$row['time_added']}<br>\n");
        print("<b>Archive password:</b><br>\n");
        print("<b>Comment:</b><br>\n");
    }
}

echo "<br><a href=\"venu.php\">Return</a>";
// Main footer
print <<<_END
<div class="push"></div>
</div> <!-- End of wrapper -->
<div class="footer">venu &copy; 2012 <a href="mailto:chrisbw@gmail.com">Chris Williams</a></div>
<p>
</body>
</html>
_END;
mysql_close($link);
exit();
?>
