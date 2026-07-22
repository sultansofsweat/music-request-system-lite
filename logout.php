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
<title>Music Request System LITE - Admin Logout</title>
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
		echo("<script type=\"text/javascript\">window.location = 'login.php'</script>");
		echo("You are not signed in. You should be automatically redirected. If you are not, please <a href=\"login.php\">click here</a>.");
		$disabled=true;
	}
	if(!empty($_POST['s']))
	{
		$_SESSION['mrsadmin']=false;
		unset($_SESSION['mrsadmin']);
		session_destroy();
		$disabled=true;
		echo("<span style=\"color:#00FF00;\"><b>LOGOFF SUCCESSFUL</b></span>. Please <a href=\"index.php\">click here</a> to leave this page.");
	}
?>
<h1>Music Request System LITE</h1>
<h2>Log Off</h2>
<form method="post" action="logout.php">
<input type="hidden" name="s" value="y">
<input type="submit" value="Confirm logout" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Abscond</a></p>
</body>
</html>