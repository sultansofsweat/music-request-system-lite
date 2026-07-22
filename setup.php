<?php
	require("errorhandler.php");
	require("password_compat.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	if(!file_exists("firstuse.txt"))
	{
		echo("<script type=\"text/javascript\">window.location = 'index.php'</script>");
		die("Setup has already been completed. You should be automatically redirected. If you are not, please <a href=\"index.php\">click here</a>.");
	}
	
	$done=false;
	
	$filename=sys_get_temp_dir() . DIRECTORY_SEPARATOR . "mrs_setup_key.txt";
	if(!file_exists($filename))
	{
		$characters="123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$length=strlen($characters);
		$key = "";
		for ($i=0;$i<16;$i++)
		{
			$key.=$characters[rand(0,$length-1)];
		}
		unset($characters,$length,$i);
		$fh=fopen($filename,'w');
		if($fh)
		{
			fwrite($fh,$key);
			fclose($fh);
			unset($fh,$key);
		}
		else
		{
			unset($fh,$key);
			die(trigger_error("No way to confirm user is administrator! Halting execution.",E_USER_ERROR));
		}
	}
?>
<html>
<head>
<title>Music Request System LITE - Initial Setup</title>
<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<h1>Music Request System LITE</h1>
<h2>Initial Setup</h2>
<?php
	if(!empty($_POST['s']))
	{
		echo("<p>STARTING MRS SETUP...<br>");
		$good=true;
		echo("Checking submitted data...");
		$password="";
		$confirm="";
		$key="";
		if(!empty($_POST['password']))
		{
			$password=$_POST['password'];
		}
		if(!empty($_POST['confirm']))
		{
			$confirm=$_POST['confirm'];
		}
		if(!empty($_POST['key']))
		{
			$key=preg_replace("/[^A-Za-z0-9]/","",$_POST['key']);
		}
		if(empty($password) || empty($confirm) || empty($key))
		{
			echo("FAILED!<br>");
			$good=false;
		}
		else
		{
			echo("DONE.<br>");
		}
		if($good === true)
		{
			echo("Validating submitted data...");
			$actual_key="";
			if(file_exists($filename))
			{
				$actual_key=file_get_contents($filename);
			}
			if($password != $confirm || empty($actual_key) || $key != $actual_key)
			{
				echo("FAILED!<br>");
				$good=false;
			}
			else
			{
				echo("DONE.<br>");
			}
		}
		unset($key,$actual_key,$confirm);
		if($good === true)
		{
			echo("Checking installed PHP version...");
			if(PHP_VERSION_ID >= 50307)
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
				$good=false;
			}
		}
		if($good === true)
		{
			echo("Checking existence of database folder...");
			if(file_exists($database_folder) && is_dir($database_folder))
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
				$good=false;
			}
		}
		if($good === true)
		{
			echo("Checking writability of database folder...");
			$fh=@fopen($database_folder . "canary.txt",'w');
			@fclose($fh);
			if(file_exists($database_folder . "canary.txt"))
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
				$good=false;
			}
		}
		if($good === true)
		{
			echo("Setting default settings...");
			$fh=@fopen($database_folder . "open.txt",'w');
			if($fh)
			{
				@fwrite($fh,"n");
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			$fh=@fopen($database_folder . "date_format.txt",'w');
			if($fh)
			{
				@fwrite($fh,"n");
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			$fh=@fopen($database_folder . "stale_requests.txt",'w');
			if($fh)
			{
				@fwrite($fh,"l F jS, Y, g:i A T");
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			$fh=@fopen($database_folder . "timezone.txt",'w');
			if($fh)
			{
				@fwrite($fh,date_default_timezone_get());
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			$fh=@fopen($database_folder . "requests.txt",'w');
			if($fh)
			{
				@fwrite($fh,serialize(array("New"=>array(),"Queued"=>array(),"Done"=>array())));
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			$fh=@fopen($database_folder . "banned.txt",'w');
			if($fh)
			{
				@fwrite($fh,serialize(array("Usernames"=>array(),"IPs"=>array())));
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			
			if($good === true)
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
			}
		}
		if($good === true)
		{
			echo("Setting system password...");
			
			$time=0.350;
			$cost=5;
			do
			{
				$cost++;
				$start=microtime(true);
				password_hash($password,PASSWORD_BCRYPT,array("cost"=>$cost));
				$end=microtime(true);
			}
			while(($end-$start) < $time);
			$cost--;
			unset($time,$end,$start);
			
			$hash=password_hash($password,PASSWORD_BCRYPT,array("cost"=>$cost));
			unset($password);
			
			$fh=@fopen($database_folder . "password.txt",'w');
			if($fh)
			{
				@fwrite($fh,$hash);
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			
			if($good === true && $hash !== false)
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
			}
		}
		unset($hash,$cost);
		if($good === true)
		{
			echo("Setting version information...");
			$fh=@fopen($database_folder . "version.txt",'w');
			if($fh)
			{
				@fwrite($fh,serialize(array("Build"=>202607221233,"Major"=>1,"Minor"=>0,"Revision"=>0)));
				@fclose($fh);
			}
			else
			{
				$good=false;
			}
			
			if($good === true)
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
			}
		}
		if($good === true)
		{
			echo("Cleaning up temporary files...");
			$debug1=@unlink($database_folder . "canary.txt");
			$debug2=@unlink($filename);
			if($debug1 === true && $debug2 === true)
			{
				echo("DONE.<br>");
			}
			else
			{
				echo("FAILED!<br>");
				$good=false;
			}
		}
		echo("</p>");
		unset($debug1,$debug2);
		
		if($good === true)
		{
			echo("<p><span style=\"color:#00FF00;\"><b>SETUP COMPLETE!!!</b></span><br>Please remember to delete the \"firstuse.txt\" file AND this script (\"setup.php\") from the root of the MRS folder as this is not done automatically!<br>You should be redirected automatically. If you are not, please <a href=\"index.php\">click here</a>.</p>");
			$done=true;
			echo("<script type=\"text/javascript\">setTimeout(()=>{window.location = 'index.php';},5000)</script>");
		}
		else
		{
			echo("<p><span style=\"color:#FF0000;\"><b>SETUP FAILED!!!</b></span><br>Please check the output above and retry. If the problem persists, please send a bug report to the developers.</p>");
		}
		unset($good,$fh);
	}
?>
<div <?php if($done === true) { echo "style=\"visibility:hidden;\""; } ?>>
<p>Welcome to the Music Request System LITE initial setup script.<br><br>
You are seeing this as this MRS instance appears not to have been set up yet.<br><br>
<b>IF YOU <u>ARE NOT</u> THE SYSTEM ADMINISTRATOR (BOFH)</b>: please leave this site and inform the BOFH of this problem.<br>
<b>IF YOU <u>ARE</u> THE BOFH</b>: please fill in the form and follow the directions below.</p>
<p><b>BEFORE RUNNING THIS SCRIPT</b>, you must have two things: a <b>database folder</b> and the <b>setup key</b>.<br>
The <b>setup key</b> has been written to "mrs_setup_key.txt" the system temporary directory (e.g. "C:\Windows\Temp\mrs_setup_key.txt" or "/tmp/mrs_setup_key.txt").<br>
The <b>database folder</b>, for simplicity, has been hard-coded to "../../mrs/"<br>
The system assumes that the MRS system files are in a subdirectory on the server root, and that thus the database folder is one level below the server root!
A valid example would be the MRS system in "/var/www/html/mrs/", and the database folder in "/var/www/mrs/".<br>
Please note that this directory MUST be created manually and MUST be writable by the web server user (e.g. www-data on Apache2)!!!</p>
<p>For more information, please see the setup instructions.</p>
<form method="post" action="setup.php">
<input type="hidden" name="s" value="y">
Please enter your administrator password: <input type="password" name="password" maxlength="40" required="required"> (maximum length of 40 characters)<br>
Please confirm your administrator password: <input type="password" name="confirm" maxlength="40" required="required"><br><br>
Please enter the MRS setup key: <input type="password" name="key" size="40" required="required"><br><br>
<input type="submit" value="Start setup">
</form>
</div>
</body>
</html>
<?php
	unset($filename,$done,$database_folder);
?>