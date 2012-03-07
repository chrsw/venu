<?php

// Main page HTML header
echo <<<_END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<link rel="stylesheet" href="style.css" type="text/css" media="all"/>
<head>

<title>Venu Music Database</title>

</head>

<body>

<div class="header">Venu Music Database <header class="right">A simple way to track music</header></div>
<p>
_END;

// Read the database access info from a file in the form of
// host:username:password:database:table
$db_auth_file_name = "db_auth.txt";
$auth_info = file_get_contents("$db_auth_file_name") or die("Could not get database authorization info.\n<br>");
// Store the password info across some string vars
list($host, $username, $password, $database, $table) = split(":",$auth_info);

$table_length = 50;

// Eastern timezone
date_default_timezone_set(EST);
echo "<br>Welcome to Venu, a web based system for tracking music. ";
echo " It is ".date("l\, jS \of F\, Y") . ".<p>";

// TODO: see if the database already exists or if we have to create it.
// Right now the system assumes it exists and dies if it doesn't

// Connect to the mysql server
$link = mysql_connect("$host", "$username", "$password");
if (!$link){
    die('Could not connect to the database: ' . mysql_error());
}

// Didn't die, print a status message
//echo "\n<br>Connected to database on $host succesfully<br>\n";

// Try to connect to a database
if (!mysql_select_db($database, $link)){
    echo "\n<p>Could not select database" . $database;
    echo "\n<br>";
    printf("%s", mysql_error());
    mysql_close($link);
    exit;
} else {
//    echo "<br>\nSelected database $database succesfully<br>\n";
}

// Main user input form
echo <<<_END
<p>Add to the database: <br>
<form method="post" action="add.php" class="form">
<p>
<label for="txtArtistName">Artist: </label> <input name="txtArtistName" class="input"/><br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName" class="input"/><br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl" class="input"/><br>
<br><input type="submit" class="button" value="Add" /></br>
</form>
_END;

// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$result = mysql_query('SELECT DISTINCT artist, release_name, time_added, link FROM releases ORDER BY time_added DESC');
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result
echo "<br>";

// Build an HTML table from the CSS defined above
echo "\n<table class=\"results\">";
echo "\n<tr>";
echo "<td align=\"left\"><b>Artist</b></td>\n";
echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"center\"><b>Added</b></td>\n";
echo "<td align=\"right\"><b>Link</b></td>\n";
echo "</tr>\n";
// While there's data to be fetched from the table, store it in the associative array $row 
$table_idx = 0;       // Alternate row styles
while ($row = mysql_fetch_assoc($result)){
    // Alternate row formatting for visibility
    if (($table_idx++ % 2) == 1){
        echo "\n<tr>";
    } else {
        echo "\n<tr class=\"alt\">";
    } 
    echo "<td align=\"left\">{$row['artist']}</td>\n";
    echo "<td align=\"left\">{$row['release_name']}</td>";
    $entry_age = findage($row['time_added']);
    echo "<td align=\"center\">$entry_age</td>";
    // Open the link in a new window or tab
    echo "<td align=\"right\"><a href=\"{$row['link']}\" target=\"_blank\">{$row['link']}</a></td>";
    echo "\n</tr>";
    // Stop after a certain amount of results
    if ($table_idx == $table_length){
        break;
    }
}
echo "</table><br>";

// Not sure if this is necessary yet
mysql_free_result($result);

// Close the connection to the server
mysql_close($link);

// Convert the time diff_unix_time_now between now and the time a link was submitted into a
// short, user-friendly statement 
function findage($date)
{
    $time_units = array("second", "minute", "hour", "day", "week", "month", "year");
    // Time divisors, rough estimates
    $lengths = array("60","60","24","7","4.35","12","10");
    // Rhe current time
    $now = time();
    // Convert to unix format, # of secs since Jan 1, 1970
    $unix_time  = strtotime($date);
    $diff_unix_time_now = $now - $unix_time;
    // Find the appropriate time unit to print
    for($j = 0; $diff_unix_time_now >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $diff_unix_time_now /= $lengths[$j];
    }
    $diff_unix_time_now = round($diff_unix_time_now);
    if($diff_unix_time_now != 1) {
        $time_units[$j].= "s";
    }
    // Print a short statement in the table instead of the acutal time if the
    // entry has just been added
    if(($diff_unix_time_now < 30) && ($j == 0)){
        return "just now";
    } else {
        return "$diff_unix_time_now $time_units[$j] ago";
    }
}




?>

<p><p>
<p><p>&nbsp;<p>
<div class = "footer">venu &copy; <a href="mailto:chrisbw@gmail.com">Chris Williams</a> 2012</div>
<p>
</body>
</html>
