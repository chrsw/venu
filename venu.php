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
_END;

// Read the database access info from a file in the form of
// host:username:password:database:table
$db_auth_file_name = "db_auth.txt";
$auth_info = file_get_contents("$db_auth_file_name") or
        die("Could not get database authorization info.\n<br>");

// Store the password info across some string vars
list($host, $username, $password, $database, $table) = split(":",$auth_info);

$table_length = 50;

// Eastern timezone
date_default_timezone_set('America/New_York');

// Body style banner message
//echo "<br>Welcome to Venu, a web based system for tracking music. ";

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

// Sub banner showing site stats. Size of database. Time.
$rows_junk = mysql_query("SELECT SQL_CALC_FOUND_ROWS * FROM releases LIMIT 1");
$rows_res = mysql_query("SELECT FOUND_ROWS()");
$rows_total = mysql_fetch_row($rows_res);
echo "<font style=\"font-size:smaller\"><b>$rows_total[0] releases in the database &middot ";
echo "Server time: ". date("g:i D\, M d\, Y") . " &middot ";
// Get db server, queries
// Might one want to do "in the past 24 hours" or something
$stats = explode('  ', mysql_stat($link));
$queries = preg_split("/ /", $stats[2]);
echo " $queries[1] queries";
echo "</b></font>";

// Main user input form
echo <<<_END
<p>Add to the database: <br>
<form method="post" action="add.php" class="form">
<p>
<label for="txtArtistName">Artist: </label> <input name="txtArtistName" class="input"/><br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName" class="input"/><br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl" class="input"/><br>
<!-- Need to fix the location of this add button -->
<br><input type="submit" class="button" value="Add" /></br>
</form>
_END;

// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$query_limit = 30;
$result = mysql_query("SELECT DISTINCT artist, release_name, time_added, link FROM releases ORDER BY time_added DESC LIMIT $query_limit");
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result
echo "<br>";

// Build an HTML table from the database and CSS in style.css
print("\n<table class=\"results\">");
print("\n<tr>");
print("<td align=\"left\"><b>Artist - Release</b></td>\n");
//echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"center\"><b>Added</b></td>\n";
echo "<td align=\"right\"><b>Host</b></td>\n";
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
    print("<td align=\"left\"><a href=\"{$row["link"]}\" target=\"_black\">{$row['artist']} - {$row['release_name']}</a></td>\n");
    //echo "<td align=\"left\">{$row['release_name']}</td>";
    $entry_age = findage($row['time_added']);
    print("<td align=\"center\">$entry_age</td>");
    // Open the link in a new window or tab
    // Todo: Show only the host instead of the full url
    $url = $row["link"];
    $domain = splitUrl($url, "domain");
    //preg_match("/[0-9a-zA-Z]+\.[0-9a-zA-Z]+$/", $url, $domain);
    print("<td align=\"right\">$domain</td>\n</tr>\n");
    //echo "<td align=\"right\">getDomain($url)</td>\n</tr>\n";
    //echo "getDomain($url)";
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

/*
 * Convert the time diff_unix_time_now between now and the time a link was submitted into a
 * short, user-friendly statement
 */ 
function findage($date)
{
    // The different type of units to be displayed.    
    $time_units = array("second", "minute", "hour", "day", "week", "month", "year");
    // Time divisors, rough estimates
    $lengths = array("60",      // 60 seconds
                     "60",      // 60 minutes
                     "24",      // 24 hours
                     "7",       // 7 days
                     "4.35",    // ~4 weeks
                     "12",      // 12 months
                     "10");     // 10 years
    // The current time
    $now = time();
    // Convert to unix format, # of secs since Jan 1, 1970
    $unix_time = strtotime($date);
    $diff_unix_time_now = $now - $unix_time;
    // Find the appropriate time unit to print
    for($units_idx = 0; $diff_unix_time_now >= $lengths[$units_idx] && $units_idx < count($lengths)-1; $units_idx++) {
        $diff_unix_time_now /= $lengths[$units_idx];
    }
    $diff_unix_time_now = round($diff_unix_time_now);
    // Turn the word to plural if there is more than one of a unit
    if($diff_unix_time_now != 1) {
        $time_units[$units_idx] .= "s";
    }
    // Print a short statement in the table instead of the acutal time if the
    // entry has just been added
    if(($diff_unix_time_now < 10) && ($units_idx == 0)){
        return "just now";
    } else {
        return "$diff_unix_time_now {$time_units[$units_idx]} ago";
    }
}

/*
 * Get the components of a url
 * */
function splitUrl($url, $component){
    // regex to split the url into an array
    preg_match('/^(ftp|https?):\/\/([^\/:]+)(?: :(\d+))?/x', $url, $matches);
    $protocol = $matches[1];
    $host = $matches[2];
    $port = $matches[3];
    // return a piece of the url depending on what the user wanted
    switch($component):
        case "protocol":
            $url_component = $protocol;
            break;
        case "host":
            $url_component = $host;
            break;
        case "port":
            $url_component = $port;
            break;
        case "domain":
            preg_match('/([0-9a-zA-Z]+\.[0-9a-zA-Z]+)$/', $host, $domain);
            $url_component = $domain[1];
            break;
        default:
            $url_component = "";
    endswitch;
    return $url_component;
}

// Main footer
print <<<_END
<p><p>&nbsp;<p>
<div class = "footer">venu &copy; <a href="mailto:chrisbw@gmail.com">Chris Williams</a> 2012</div>
<p>
</body>
</html>
_END;

?>
