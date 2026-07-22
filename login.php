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
	
	$redirect_page="index.php";
	if(!empty($_GET['ref']))
	{
		$redirect_page=preg_replace("/[^a-z]/","",$_GET['ref']) . ".php";
	}
	if(!file_exists($redirect_page))
	{
		$redirect_page="index.php";
	}
	
	$disabled=false;
?>
<html>
<head>
<title>Music Request System LITE - Admin Login</title>
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
	if($admin === true)
	{
		echo("<script type=\"text/javascript\">window.location = '$redirect_page'</script>");
		echo("You are already signed in. You should be automatically redirected. If you are not, please <a href=\"$redirect_page\">click here</a>.");
		$disabled=true;
	}
	if(!empty($_POST['s']))
	{
		if(!empty($_POST['ref']))
		{
			$redirect_page=preg_replace("/[^a-z]/","",$_POST['ref']) . ".php";
		}
		if(!file_exists($redirect_page))
		{
			$redirect_page="index.php";
		}
		$password=file_get_contents($database_folder . "password.txt");
		if(password_verify($_POST['password'],$password) === true)
		{
			$_SESSION['mrsadmin']=dirname(__FILE__);
			$disabled=true;
			echo("<span style=\"color:#00FF00;\"><b>LOGON SUCCESSFUL</b></span>. Please <a href=\"$redirect_page\">click here</a> to leave this page.");
		}
		else
		{
			echo("<span style=\"color:#FF0000;\"><b>LOGON FAILED</b></span>. Please try again.");
		}
	}
?>
<h1>Music Request System LITE</h1>
<h2>Administrator Login</h2>
<form method="post" action="login.php">
<input type="hidden" name="s" value="y">
<input type="hidden" name="ref" value="<?php echo substr($redirect_page,0,-4); ?>">
Please enter the administrator password: <input type="password" name="password" required="required"><br><br>
<input type="submit" value="Log on" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Abscond</a></p>
</body>
</html>