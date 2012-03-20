<?php
/*
 * add.php: Process input, insert into database and bounce back to the main venu page
 */


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

// Save all the user input from the Artist entry form
if (isset($_POST['txtArtistName']) && isset($_POST['txtReleaseName']) && isset($_POST['txtLinkUrl'])) {
    // Grab the user input into PHP vars
    $txtArtistName = $_POST['txtArtistName'];
    $txtReleaseName = $_POST['txtReleaseName'];
    $txtLinkUrl = $_POST['txtLinkUrl'];
    $txtReleaseYear = $_POST['txtReleaseYear'];
    // Sanitize user input
    // Might need to do a better job of extracting dangerous user input.
    // The docs on htmlentities() make it seem like it may not be sufficient.
    $txtArtistName = htmlentities($txtArtistName);
    $txtReleaseName = htmlentities($txtReleaseName);
    $txtLinkUrl = htmlentities($txtLinkUrl);
    // Don't enter blank input
    if((preg_match('/^$/',$txtArtistName)) || (preg_match('/^$/',$txtReleaseName))){
        // Return to the main page without inserting the data
        // Because either the artist name or the release name was blank.
        header("Location: venu.php");
        exit();
    }
    // Make sure the link entry is some type of URL looking thing
    if(!preg_match('/((ftp|http)s?\:\/\/)?[a-z0-9-.]*\.[a-z{2,4}]/',$txtLinkUrl)){
        header("Location: venu.php");
        exit();
    }
    // Add the user input to the database
    // MySQL command has the form:
    // INSERT INTO releases(artist,release_name,link) VALUES('New Group','Group Release','http://newgroup.bandcamp.com/');
    // Start building a query
    //$insertStart = sprintf("INSERT INTO %s(artist, release_name, link) ", mysql_real_escape_string($table)); 
    $insertStart = sprintf("INSERT INTO releases(artist, release_name, link) "); 
    $insertValues = sprintf("VALUES('%s', '%s', '%s');", mysql_real_escape_string($txtArtistName), mysql_real_escape_string($txtReleaseName), mysql_real_escape_string($txtLinkUrl));
    $insertQuery = $insertStart . $insertValues;
    $insertStatus = mysql_query($insertQuery);
    if (!$insertStatus){
        // Oops! something went wrong with the database insertion
        die("Bad insert: " . mysql_error());
    }
    unset($_POST);
}

mysql_close($link);
header("Location: venu.php");
exit();
?>

