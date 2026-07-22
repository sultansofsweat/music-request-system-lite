<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$date_format="l F jS, Y, g:i A T";
	if(file_exists($database_folder . "date_format.txt"))
	{
		$date_format=file_get_contents($database_folder . "date_format.txt");
	}
	
	$stale_marker=72;
	if(file_exists($database_folder . "stale_requests.txt"))
	{
		$stale_marker=file_get_contents($database_folder . "stale_requests.txt");
	}
	
	$timezone="America/Toronto";
	if(file_exists($database_folder . "timezone.txt"))
	{
		$timezone=file_get_contents($database_folder . "timezone.txt");
	}
	
	$open="n";
	if(file_exists($database_folder . "open.txt"))
	{
		$open=file_get_contents($database_folder . "open.txt");
	}
	
	$anon="n";
	if(file_exists($database_folder . "anon.txt"))
	{
		$anon=file_get_contents($database_folder . "anon.txt");
	}
	
	$restrict=1;
	if(file_exists($database_folder . "restrict.txt"))
	{
		$restrict=file_get_contents($database_folder . "restrict.txt");
	}
?>
<html>
<head>
<title>Music Request System LITE - System Settings</title>
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
		echo("<script type=\"text/javascript\">window.location = 'login.php?ref=admin'</script>");
		die("You are not signed in. You should be automatically redirected. If you are not, please <a href=\"login.php?ref=admin\">click here</a>.");
	}
	if(!empty($_POST['s']))
	{
		$open=preg_replace("/[^a-z]/","",$_POST['open']);
		$anon=preg_replace("/[^a-z]/","",$_POST['anon']);
		$restrict=preg_replace("/[^0-9]/","",$_POST['restrict']);
		$timezone=htmlspecialchars($_POST['timezone']);
		$date_format=htmlspecialchars($_POST['dateformat']);
		$stale_marker=preg_replace("/[^0-9]/","",$_POST['stale']);
		
		$fh=@fopen($database_folder . "open.txt",'w');
		if($fh)
		{
			@fwrite($fh,$open);
			@fclose($fh);
		}
		$fh=@fopen($database_folder . "anon.txt",'w');
		if($fh)
		{
			@fwrite($fh,$anon);
			@fclose($fh);
		}
		$fh=@fopen($database_folder . "restrict.txt",'w');
		if($fh)
		{
			@fwrite($fh,$restrict);
			@fclose($fh);
		}
		$fh=@fopen($database_folder . "stale_requests.txt",'w');
		if($fh)
		{
			@fwrite($fh,$stale_marker);
			@fclose($fh);
		}
		$fh=@fopen($database_folder . "date_format.txt",'w');
		if($fh)
		{
			@fwrite($fh,$date_format);
			@fclose($fh);
		}
		$fh=@fopen($database_folder . "timezone.txt",'w');
		if($fh)
		{
			@fwrite($fh,$timezone);
			@fclose($fh);
		}
		echo("<p>Updated settings. Please <a href=\"admin.php\">click here</a> to verify changes.</p>");
	}
?>
<h1>Music Request System LITE</h1>
<h2>Admin Console</h2>
<form method="post" action="admin.php">
<input type="hidden" name="s" value="y">
Accepting requests: <input type="radio" name="open" value="y" <?php if($open == "y") { echo "checked=\"checked\""; } ?>> Yes | <input type="radio" name="open" value="n" <?php if($open == "n") { echo "checked=\"checked\""; } ?>> No<br>
Allow anonymous requests: <input type="radio" name="anon" value="y" <?php if($anon == "y") { echo "checked=\"checked\""; } ?>> Yes | <input type="radio" name="anon" value="n" <?php if($anon == "n") { echo "checked=\"checked\""; } ?>> No<br>
Posting restrictions: <input type="radio" name="restrict" value="2" <?php if($restrict == 2) { echo "checked=\"checked\""; } ?>> One ACTIVE request | <input type="radio" name="restrict" value="1" <?php if($restrict == 1) { echo "checked=\"checked\""; } ?>> One NEW request | <input type="radio" name="restrict" value="0" <?php if($restrict == 0) { echo "checked=\"checked\""; } ?>> No restriction<br>
NOTE: "ACTIVE" refers to both NEW and QUEUED requests.<br>
Timezone: <input type="text" name="timezone" value="<?php echo $timezone; ?>" size="20"> (please see <a href="https://www.php.net/manual/en/timezones.php" target="_blank">list of supported timezones</a>)<br>
Date format: <input type="text" name="dateformat" value="<?php echo $date_format; ?>" size="25"> (please see <a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">date/time format parameters</a>)<br>
The current date format looks like: <?php echo date($date_format); ?><br>
Consider requests "stale" after: <input type="text" name="stale" value="<?php echo $stale_marker; ?>" size="2"> hours<br>
<input type="submit" value="Change settings">
</form>
<p>Additional settings:<br>
<a href="change_passwd.php">Change system password</a><br>
<a href="ban_list.php">Edit ban lists</a></p>
<p><a href="index.php">Abscond</a></p>
</body>
</html>