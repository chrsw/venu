<?php
/*
 * add.php: Process input, insert into database and bounce back to the main venu page
 */

require_once 'db_login.php';
// Connect to the mysql server
$link = mysql_connect("$host", "$user", "$password");
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
    $insertStart = sprintf("INSERT INTO %s(artist, release_name, link) ", mysql_real_escape_string($table)); 
    $insertValues = sprintf("VALUES('%s', '%s', '%s');", mysql_real_escape_string($txtArtistName), mysql_real_escape_string($txtReleaseName), mysql_real_escape_string($txtLinkUrl));
    $insertQuery = $insertStart . $insertValues;
    $insertStatus = mysql_query($insertQuery);
    if (!$insertStatus){
        // Oops! something went wrong with the database insertion
        die("Bad insert: " . mysql_error());
    }
    unset($_POST);
}

header("Location: venu.php");
exit();
?>

