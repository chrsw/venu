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

require_once 'db_login.php';    // In the future store MySQL DB login info in seperate file
$table_length = 50;

// Eastern timezone
date_default_timezone_set(EST);
echo "<br>Welcome to Venu, a web based system for tracking music. ";
//echo "\n<p>This is very early software going through heavy development.";
//echo "\n<br>It is ".date("l\, jS \of F\, Y") . ".<p>";

// One of the default databases on the MySQL system
// real authentication

// TODO: see if the database already exists or if we have to create it.
// Right now the system assumes it exists and dies if it doesn't

// Connect to the mysql server
$link = mysql_connect("$host", "$user", "$password");
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
<label for="txtArtistName">Artist: </label> <input name="txtArtistName" class="input"/></br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName" class="input"/></br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl" class="input"/></br>
<br><input type="submit" class="button" value="Add" /></br>
</form>
_END;

// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$result = mysql_query('SELECT DISTINCT artist, release_name, link FROM releases');
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result
echo "<br>";
// Build a table from the CSS defined above
echo "\n<table class=\"results\">";
echo "\n<tr>";
echo "<td align=\"left\"><b>Artist</b></td>\n";
echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"right\"><b>Link</b></td>\n";
echo "</tr>\n";
// While there's data to be fetched from the table, store it in the associative array $row 
$table_idx = 0;       // Alternate row styles
while ($row = mysql_fetch_assoc($result)){
    // Alternate row formatting
    if (($table_idx++ % 2) == 1){
        echo "\n<tr>";
    } else {
        echo "\n<tr class=\"alt\">";
    } 
    echo "<td align=\"left\">{$row['artist']}</td>\n";
    echo "<td align=\"left\">{$row['release_name']}</td>";
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

?> <!-- End of db PHP block -->

<p><p>
<p><p>&nbsp;<p>
<div class = "footer">venu &copy; <a href="mailto:chrisbw@gmail.com">Chris Williams</a> 2012</div>
<p>
</body>
</html>
