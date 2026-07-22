<?php
	require("errorhandler.php");
	require("password_compat.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$disabled=false;
?>
<html>
<head>
<title>Music Request System LITE - Hold The Line</title>
<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php
	if(!file_exists($database_folder . "version.txt"))
	{
		if(file_exists("firstuse.txt"))
		{
			echo("<script type=\"text/javascript\">window.location = 'setup.php'</script>");
			die("This MRS instance is either not set up yet or is broken. You should be automatically redirected. If you are not, please <a href=\"setup.php\">click here</a>.");
		}
		else
		{
			die(trigger_error("Could not find required database files",E_USER_ERROR));
		}
	}
	if($admin !== true)
	{
		echo("<script type=\"text/javascript\">window.location = 'login.php?ref=close_system'</script>");
		echo("You are not signed in. You should be automatically redirected. If you are not, please <a href=\"login.php?ref=close_system\">click here</a>.");
		$disabled=true;
	}
	if(!empty($_POST['s']))
	{
		$fh=@fopen($database_folder . "open.txt",'w');
		if($fh)
		{
			@fwrite($fh,"n");
			@fclose($fh);
		}
		$disabled=true;
		echo("The system is now CLOSED. Please <a href=\"index.php\">click here</a> to leave this page.");
	}
?>
<h1>Music Request System LITE</h1>
<h2>Close System</h2>
<form method="post" action="close_system.php">
<input type="hidden" name="s" value="y">
<input type="submit" value="Confirm close" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Abscond</a></p>
</body>
</html>