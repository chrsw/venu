<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<link rel="stylesheet" href="style.css" type="text/css" media="all"/>
<head>

<title>Venu Music Database</title>

</head>

<body>
<!-- TODO: Clean up all this CSS and move it to seperate files -->

<div class="header">Venu Music Database <header class="right">A simple way to track music</header></div>
<p>

<!-- Start the main PHP script... -->
<?php
require_once 'db_login.php';    // In the future store MySQL DB login info in seperate file

// Eastern timezone
date_default_timezone_set(EST);
echo "<br>Welcome to Venu, a web based system for tracking music. ";
echo "<p>This is very early software going through heavy development.";
echo" <br>It is ".date("l\, jS \of F\, Y") . ".<p>";

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
echo "\n<br>Connected to database on $host succesfully<br>\n";

// Try to connect to a database
if (!mysql_select_db($database, $link)){
    echo "\n<p>Could not select database" . $database;
    mysql_close($link);
    exit;
} else {
    echo "<br>\nSelected database $database succesfully<br>\n";
}

// Save all the user input from the Artist entry form
if (isset($_POST['txtArtistName']) && isset($_POST['txtReleaseName']) && isset($_POST['txtLinkUrl'])) {
    // Grab the user input into PHP vars
    $txtArtistName = $_POST['txtArtistName'];
    $txtReleaseName = $_POST['txtReleaseName'];
    $txtLinkUrl = $_POST['txtLinkUrl'];
    // Sanitize some user input
    $txtArtistName = htmlentities($txtArtistName);
    $txtReleaseName = htmlentities($txtReleaseName);
    $txtLinkUrl = htmlentities($txtLinkUrl);
    // Display the user input -- DEBUG off
    //echo "Your artist is $txtArtistName.<br>\n";
    //echo "Your releasee is $txtReleaseName.<br>\n";
    //echo "Your link is $txtLinkUrl.<br>\n";
    //echo "<br>\n";
    // Add the user input to the database
    // MySQL command has the form:
    // INSERT INTO releases(artist,release_name,link) VALUES('New Group','Group Release','http://newgroup.bandcamp.com/');
    // Start building a query
    $insertStart = sprintf("INSERT INTO %s(artist, release_name, link) ", mysql_real_escape_string($table)); 
    $insertValues = sprintf("VALUES('%s', '%s', '%s');", mysql_real_escape_string($txtArtistName), mysql_real_escape_string($txtReleaseName), mysql_real_escape_string($txtLinkUrl));
    $insertQuery = $insertStart . $insertValues;
    $insertStatus = mysql_query($insertQuery);
    if (!$insertStatus){
        // Oops! something went wrong
        echo "<p><b>Couldn't update the table!</b><p>";
        echo "Tried to use: <pre>$insertQuery</pre><br>";
        die("Bad insert: " . mysql_error());
    }
    unset($_POST);
} else {
echo "Release info not yet enerted.<p>";
}
// Main user input form
echo <<<_END
Add to the database: <br>
<form method="post" action="venu.php" class="form">
<p>
<label for="txtArtistName">Artist: </label> <input name="txtArtistName"/></br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName"/></br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl"/></br>
<p class="submit"><input type="submit" value="Add" /></p>
</form>
_END;

// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$result = mysql_query('SELECT artist, release_name, link FROM releases');
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result
echo "<br>Current database<br>";
// Build a table from the CSS defined above
echo "\n<table class=\"results\">";
echo "\n<tr>";
echo "<td align=\"left\"><b>Artist</b></td>\n";
echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"right\"><b>Link</b></td>\n";
echo "</tr>\n";
// While there's data to be fetched from the table, store it in the array $row 
$table_idx = 0;       // Alternate row styles
while ($row = mysql_fetch_assoc($result)){
    // Alternate row formatting
    if ($table_idx++ % 2 == 1){
        echo "\n<tr>";
    } else {
        echo "\n<tr class=\"alt\">";
    } 
    echo "<td align=\"left\">{$row['artist']}</td>\n";
    echo "<td align=\"left\">{$row['release_name']}</td>";
    echo "<td align=\"right\"><a href=\"{$row['link']}\">{$row['link']}</a></td>";
    echo "\n</tr>";
    // Stop after a certain amount of results
    if ($table_idx == 20){
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
</body>
</html>
