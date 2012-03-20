<?php
/*
 * submit.php
 * Displays a form to submit the entry to the database.
 * */


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

// Main user input form
echo <<<_END
<p><b>Add to the database:</b><p>
<form method="post" action="add.php" class="form">
<p>
<label for="txtArtistName">Artist: </label> <input name="txtArtistName" class="input"/><br>
<label for="txtReleaseName">Release: </label> <input name="txtReleaseName" class="input"/><br>
<label for="txtLinkUrl">Link Url: </label> <input name="txtLinkUrl" class="input"/><br>
<p>
&nbsp;<p>
<b>Optional:</b><p>
<label for="txtReleaseYear">Year: </label> <input name="txtReleaseYear" class="input"/><br>
<label for="txtMusicStyle">Style: </label> <input name="txtMusicStyle" class="input"/><br>
<br><label for="txtComments">Comments: </label> <input name="txtComments" class="input"/><br>

<!-- Need to fix the location of this add button -->
<br><input type="submit" class="button" value="Add" /></br>
</form>
_END;

echo "<a href=\"venu.php\">Return to database</a>";
echo "<p><p>";

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
