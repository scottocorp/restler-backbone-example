<?php
/*
 * This script is used to report unhandled errors in our
 * web application.  It is typically sent here when there
 * is an unhandled error or an unhandled exception.
 *
 * In both cases, the session data has hopefully been primed
 * with data that we can use to print a more helpful message
 * for the user.
 */
ob_start();

require_once 'lib/core.php';

if (isset($_SESSION['exception']))
{
  	$exc = $_SESSION['exception'];
  	$msg = $exc->getMessage();
}
else if (isset($_SESSION['errstr']))
{
  	$msg = $_SESSION['errstr'];
}
else if (!isset($_SESSION))
{
  	$msg = "Unable to initialise the session. Please verify that the session data directory exists.";
}
else
{
  $msg = "Unknown Error";
}

/*
 * Make sure that the next time an error occurs, we reset
 * these error data.
 */
unset($_SESSION['exception']);
unset($_SESSION['errstr']);

?>
<!DOCTYPE html>
<html>
  	<head>
  		<meta charset="utf-8">
    	<title>Error</title>   
    	<link rel="stylesheet" href="css/app.css"/> 
  	</head>
	<body>
		<h2>Unexpected Error</h2>
		<p>
			Please click <a href='index.php'>here</a> to go back to the main page and continue working with our system.
		</p>
		<p>
  			The error received was: <br/><br/>
  			<b><?php echo $msg ?></b>
		</p>
  	</body>
</html>	
<?php
ob_end_flush();
?>
