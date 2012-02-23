<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!-- Test system: -->
<!-- Mac OS X 10.7.2 Darwin Kernel Version 11.2.0: Tue Aug  9 20:54:00 PDT 2011; root:xnu-1699.24.8~1/RELEASE_X86_64 x86_64 -->
<!-- PHP PHP 5.3.6 with Suhosin-Patch (cli) (built: Sep  8 2011 19:34:00) -->
<!--  -->
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
</style>
<div class="header">Venu Music Database</div>
<p>
<!-- Start the main PHP script... -->
<?php
// Eastern timezone
date_default_timezone_set(EST);
echo "<br>Welcome to Venu, a web based system for tracking music. It is ".date("l\, jS \of F\, Y") . ".<p>";

// One of the default databases on the MySQL system
// Use this for database connectivity test
$test_database = "mysql";
$database = "venu";

$host = "localhost";     // hostname of the mysql server
$user = "admin";         // New MySQL user with privs for accessing the db
$password = "password";    // DB access password, eventually there will be
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
// A simple query -- Send a simple SQL query to the MySQL server and
// print the results into an HTML table.
$result = mysql_query('SELECT artist, release_name, link FROM releases');
if(!$result){
    die('<p>Invalid query: ' . mysql_error());
}
// The query results are stored in the PHP var $result
echo "<br>Query results:<br>";
echo "\n<table border=\"2\">";
echo "\n<tr>";
echo "<td><b>Artist</b></td>\n";
echo "<td><b>Release</td></b>\n";
echo "<td><b>Link</b></td>\n";
echo "</tr>\n";
while ($row = mysql_fetch_assoc($result)){
    // try to create a table from these results
    echo "\n<tr>";
    echo "<td>{$row['artist']}</td>\n";
    echo "<td>{$row['release_name']}</td>";
    echo "<td><a href=\"{$fow['link']}\">{$row['link']}</a></td>";
    echo "\n</tr>";
}
echo "</table><br>";
// Not sure if this is necessary yet
mysql_free_result($result);

// Close the connection to the server
mysql_close($link);
?> <!-- End of db PHP block -->

<p><p>&nbsp;<p>
<div class="sect1">Add an entry to the database:</div><p>
<div class="block">
  <label>Artist:</label>
  <input class="input" type="text" id="txtArtist"/>
</div>
<div class="block">
  <label>Release:</label>
  <input class="input" type="text" id="txtRelease"/>
</div>
<div class="block">
  <label>Link:</label>
  <input class="input" type="text" id="txtLink">
</div>
<p><p>&nbsp;<p>
<div class="sect1">Search the database:</div><p>
<div class = "block">
  <label>Search:</label>
  <input class="input" type="text" id="txtSearch">
</div>

<p><p>
<p><p>&nbsp;<p>
<p><p>&nbsp;<p>

<div class = "footer">&copy; <a href="mailto:chrisbw@gmail.com">Chris Williams</a> 2012</div>
</body>
</html>
