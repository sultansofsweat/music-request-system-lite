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
?>
<html>
<head>
<title>Music Request System LITE - Change System Password</title>
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
		echo("<script type=\"text/javascript\">window.location = 'login.php?ref=change_passwd'</script>");
		die("You are not signed in. You should be automatically redirected. If you are not, please <a href=\"login.php?ref=change_passwd\">click here</a>.");
	}
	if(!empty($_POST['s']))
	{
		$password=file_get_contents($database_folder . "password.txt");
		if(password_verify($_POST['current'],$password) === true && $_POST['new'] == $_POST['confirm'])
		{
			$time=0.350;
			$cost=5;
			do
			{
				$cost++;
				$start=microtime(true);
				password_hash($_POST['new'],PASSWORD_BCRYPT,array("cost"=>$cost));
				$end=microtime(true);
			}
			while(($end-$start) < $time);
			$cost--;
			
			$fh=@fopen($database_folder . "password.txt",'w');
			if($fh)
			{
				@fwrite($fh,password_hash($_POST['new'],PASSWORD_BCRYPT,array("cost"=>$cost)));
				@fclose($fh);
			}
			echo("<p>Password has been changed.</p>");
		}
		else
		{
			echo("<p><b>FAILED</b> to change password. Current password incorrect or new password did not match. Please fix and retry.</p>");
		}
	}
?>
<h1>Music Request System LITE</h1>
<h2>Admin Console</h2>
<form method="post" action="change_passwd.php">
<input type="hidden" name="s" value="y">
Please enter the current administrator password: <input type="password" name="current" required="required"><br>
Please enter the new administrator password: <input type="password" name="new" required="required"><br>
Please confirm the new administrator password: <input type="password" name="confirm" required="required"><br><br>
<input type="submit" value="Change passwd">
</form>
<p><a href="admin.php">Abscond</a></p>
</body>
</html>