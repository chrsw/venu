<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!-- Dev system: -->
<!-- Mac OS X 10.7.2 Darwin Kernel Version 11.2.0: Tue Aug  9 20:54:00 PDT 2011; root:xnu-1699.24.8~1/RELEASE_X86_64 x86_64 -->
<!-- PHP 5.3.6 with Suhosin-Patch (cli) (built: Sep  8 2011 19:34:00) -->
<!-- Apache/2.2.20 (Unix) DAV/2 PHP/5.3.6 with Suhosin-Patch -->

<head>
<title>Venu Music Database</title>
</head>

<body>

<style>
div.block{
  overflow:hidden;
}
div.block label{
  font-family: Verdana, Arial; 
  width: 120px;
  display: block;
  float: left;
  text-align: left;
}
div.block .input{
  font-family: Verdana, Arial;
  width: 400px;
  margin-left: 4px;
  float: left;
}
div.header{
  background-color: #b0c4de;
  color: #292980;
  font-family: Verdana, Arial;
  font-size: x-large;
  font-style: normal;
  font-weight: bolder;
  margin: 2px;
  padding-bottom: 2px;
  padding-left: 12px;
  padding-right: 2px;
  padding-top: 2px;
  text-align: left;
  width: 90%
} header.right {
  font-size: small;
  font-family: Verdana, Arial;
  font-style: italic;
  font-weight: lighter;
}
div.footer{
  background-color: #bbbbbb;
  color: #292980;
  font-family: Verdana, Arial;
  font-size: smaller;
  font-style: italic;
  font-weight: lighter;
  padding-bottom: 1px;
  padding-left: 12px;
  padding-right: 1px;
  padding-top: 1px;
  text-align: left;
  width: 90%
}
div.sect1{
  font-size: larger;
  font-family: Verdana, Arial;
}
table.results {
  border-width: 4px;
  border-spacing: 2px;
  border-style: none;
  border-color: gray;
  border-collapse: collapse;
  background-color: white;
  font-family: Verdana, Arial;
  font-size: smaller;
}
table.results th {
  border-width: 1px;
  padding: 4px;
  border-style: inset;
  border-color: white;
  background-color: rgb(194, 196, 238);
}
table.results td {
  border-width: 1px;
  padding: 4px;
  border-style: inset;
  border-color: white;
  background-color: rgb(194, 196, 238);
}
form label {
  width: 80px;
  float: left;
  text-align: right;
  margin-right: 0.5em;
  font-family: Verdana, Arial;
  font-size: smaller;
  display: block
} .submit input {
  margin-left: 432px;
  width: 60px;
} input {
  width: 400px;
  font-family: Verdana, Arial;
}

</style>
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
// Use this for database connectivity test
$test_database = "mysql";
$database = "venu";
$table = "releases";        // Name of table where actual release data is stored
$host = "localhost";        // hostname of the mysql server
$user = "admin";            // New MySQL user with privs for accessing the db
$password = "password";     // DB access password, eventually there will be
                            // real authentication

// Connect to the mysql server
$link = mysql_connect('localhost', 'admin', 'password');
if (!$link){
    die('Could not connect to the database: ' . mysql_error());
}

// Didn't die, print a status message
echo "\n<br>Test connected succesfully<br>\n";

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
    $txtArtistName = $_POST['txtArtistName'];
    $txtReleaseName = $_POST['txtReleaseName'];
    $txtLinkUrl = $_POST['txtLinkUrl'];
    // Sanitize some user input
    $txtArtistName = htmlentities($txtArtistName);
    $txtReleaseName = htmlentities($txtReleaseName);
    $txtLinkUrl = htmlentities($txtLinkUrl);
    // Display the user input
    echo "Your artist is $txtArtistName.<br>\n";
    echo "Your releasee is $txtReleaseName.<br>\n";
    echo "Your link is $txtLinkUrl.<br>\n";
    echo "<br>\n";
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
} else {
echo "Release info not yet enerted.<p>";
}
echo <<<_END
<form method="post" action="venu.php" />
Add to the database: <br>
<form action="venu.php" class="form">
<p><label for="txtArtistName">Artist: </label> <input name="txtArtistName" /></br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName" /></br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl" /></br>
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
echo "<br>Query results:<br>";
// Build a table from the CSS defined above
echo "\n<table class=\"results\">";
echo "\n<tr>";
echo "<td align=\"left\"><b>Artist</b></td>\n";
echo "<td alignt=\"left\"><b>Release</td></b>\n";
echo "<td align=\"right\"><b>Link</b></td>\n";
echo "</tr>\n";
while ($row = mysql_fetch_assoc($result)){
    // create a table from these results
    echo "\n<tr>";
    echo "<td align=\"left\">{$row['artist']}</td>\n";
    echo "<td align=\"left\">{$row['release_name']}</td>";
    echo "<td align=\"right\"><a href=\"{$row['link']}\">{$row['link']}</a></td>";
    echo "\n</tr>";
}
echo "</table><br>";
// Not sure if this is necessary yet
mysql_free_result($result);
// Close the connection to the server
mysql_close($link);
?> <!-- End of db PHP block -->
<p><p>
<p><p>&nbsp;<p>
<div class = "footer">&copy; <a href="mailto:chrisbw@gmail.com">Chris Williams</a> 2012</div>
</body>
</html>
