<?php

/*
 * Create a table showing the latest entries into the database
 * Offer links for uers to search the database and view results,
 * sort the database, add links to the database, on a simple
 * page/interface.
 */

// Get the start time for page load/php processing
$time = microtime();
$time = explode(' ', $time);
$start = $time[1] + $time[0];

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

$table_length = 50;

// Eastern timezone
date_default_timezone_set('America/New_York');

// Assume the database is already setup

// Connect to the mysql server
$link = mysql_connect("$host", "$username", "$password");
if (!$link){
    die('Could not connect to the database: ' . mysql_error());
}

// Didn't die, print a status message
//echo "\n<br>Connected to database on $host succesfully<br>\n";

// Try to connect to a database
if (!mysql_select_db($database, $link)){
    // Couldn't connect to the MySQL dbase
    echo "\n<p>Could not select database" . $database;
    echo "\n<br>";
    printf("%s", mysql_error());
    mysql_close($link);
    exit;
} else {
  // successful connection
}

// Sub banner showing site stats. Size of database. Time.
// Switch to this command, should be faster:
$results_info = mysql_query("SELECT TABLE_NAME, TABLE_TYPE, TABLE_ROWS FROM information_schema.tables WHERE table_schema = DATABASE()");
//$rows_junk = mysql_query("SELECT SQL_CALC_FOUND_ROWS * FROM releases LIMIT 1");
//$rows_res = mysql_query("SELECT FOUND_ROWS()");
//$rows_total = mysql_fetch_row($rows_res);
$row_info = mysql_fetch_assoc($results_info);
echo "<font style=\"font-size:smaller\"><b>Releases in database: {$row_info['TABLE_ROWS']} &middot ";
echo "Page generated: ". date("g:i D\, M dS") . " &middot ";
// Get db server, queries
// Might one want to do "in the past 24 hours" or something
$stats = explode('  ', mysql_stat($link));
$queries = preg_split("/ /", $stats[2]);
echo " $stats[7]";
echo "</b></font>";


// Links to other features and parts of the system
echo "\n<p><a href=\"venu.php\">Latest</a>";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "|";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "<a href=\"submit.php\">Add</a>";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "|";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "<a href=\"search.php\">Search</a>\n";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "|";
echo "&nbsp";
echo "&nbsp";
echo "&nbsp";
echo "<a href=\"about.php\">About</a>\n";

// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$query_limit = 30;
$result = mysql_query("SELECT DISTINCT artist, release_name, time_added, link, rid FROM releases ORDER BY time_added DESC LIMIT $query_limit");
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result

// Build an HTML table from the database and CSS in style.css
print("\n<table class=\"results\">");
print("\n<tr>");
print("<td align=\"left\"><b>Artist - Release</b></td>\n");
//echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"center\"><b>Age</b></td>\n";
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
    print("<td align=\"left\"><a href=view.php?rid={$row['rid']}>{$row['artist']} - {$row['release_name']}</a></td>\n");
    //echo "<td align=\"left\">{$row['release_name']}</td>";
    $entry_age = findage($row['time_added']);
    print("<td align=\"center\">$entry_age</td>");
    // Open the link in a new window or tab
    // Todo: Show only the host instead of the full url
    $url = $row["link"];
    $domain = splitUrl($url, "domain");
    //preg_match("/[0-9a-zA-Z]+\.[0-9a-zA-Z]+$/", $url, $domain);
    print("<td align=\"right\"><a href=\"{$row["link"]}\" target=\"_blank\">$domain &raquo;</a></td>\n</tr>\n");
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
        return "$diff_unix_time_now {$time_units[$units_idx]}";
    }
}

/*
 * Get the components of a url
 * */
function splitUrl($url, $component){
        // regex to split the url into protocol, host and port.
        // calculate the domain after.
    preg_match('/^(ftp|https?):\/\/([^\/:]+)(?: :(\d+))?/x', $url, $matches);
    $protocol = $matches[1];
    $host = $matches[2];
    if ($matches[3]) {
        $port = $matches[3];
    } else {
        $port = NULL;
    }
    // return a piece of the url depending on what the user wanted
    switch($component):
        case "protocol":
            $url_component = $protocol;
            break;
        case "host":
            $url_component = $host;
            break;
        case "port":
            if($port) {
                $url_component = $port;
            } else {
                $url_component = NULL;
            }
            break;
        case "domain":
            // Pick out the domain from the URI (domain.tld)
            preg_match('/([0-9a-zA-Z]+\.[0-9a-zA-Z]+)$/', $host, $domain);
            $url_component = $domain[1];
            break;
        default:
            // Send nothing back without a second argument
            $url_component = "";
    endswitch;
    return $url_component;
}

/*
 * Something went wrong, exit gracefully by finishing out the html
 */
function early_exit() {

}

/* 
 * Start a timer
 */
function timer_start()
{

}

/*
 * Stop a timer
 */
function timer_stop()
{

}


// Now that the page is almost completely generated, get the time
$time = microtime();
$time = explode(' ', $time);
$finish = $time[1] + $time[0];
$process_time = round(($finish - $start), 10);

// Main footer
print <<<_END
<div class="push"></div>
</div> <!-- End of wrapper -->
<div class="footer"><p class="aboutleft">Venu &copy; 2012 <a href="mailto:chrisbw@gmail.com">Chris Williams</a></p><p class="perfright">Page generated in $process_time seconds</p></div>
<p>
</body>
</html>
_END;

?>
