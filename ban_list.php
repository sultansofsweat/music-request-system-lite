<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$ban_list=array("Usernames"=>array(),"IPs"=>array());
	if(file_exists($database_folder . "banned.txt"))
	{
		$ban_list=unserialize(file_get_contents($database_folder . "banned.txt"));
	}
?>
<html>
<head>
<title>Music Request System LITE - Edit Bans</title>
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
		echo("<script type=\"text/javascript\">window.location = 'login.php?ref=ban_list'</script>");
		die("You are not signed in. You should be automatically redirected. If you are not, please <a href=\"login.php?ref=ban_list\">click here</a>.");
	}
	if(!empty($_POST['s']))
	{
		$ips=array();
		$usernames=array();
		$rawips=array();
		$rawusers=array();
		
		if(!empty($_POST['ips']))
		{
			$rawips=explode("\r\n",$_POST['ips']);
			foreach($rawips as $ip)
			{
				if(filter_var($ip,FILTER_VALIDATE_IP) !== false)
				{
					$ips[]=$ip;
				}
			}
		}
		if(!empty($_POST['users']))
		{
			$rawusers=explode("\r\n",$_POST['users']);
			foreach($rawusers as $name)
			{
				$usernames[]=preg_replace("/[^A-Za-z0-9 ]/","",$name);
			}
		}
		
		$ban_list=array("Usernames"=>array(),"IPs"=>array());
		if(!empty($ips))
		{
			foreach($ips as $ip)
			{
				$ban_list["IPs"][]=$ip;
			}
		}
		if(!empty($usernames))
		{
			foreach($usernames as $uname)
			{
				$ban_list["Usernames"][]=$uname;
			}
		}
		
		$fh=@fopen($database_folder . "banned.txt",'w');
		if($fh)
		{
			@fwrite($fh,serialize($ban_list));
			@fclose($fh);
		}
		echo("<p>The Hammer of Banination&trade; has swung.</p>");
	}
?>
<h1>Music Request System LITE</h1>
<h2>The Banhammer</h2>
<form method="post" action="ban_list.php">
<input type="hidden" name="s" value="y">
Usernames that should be banned into the outer bands of the Kuiper Belt (one per line):<br>
<textarea name="users" rows="10" cols="50"><?php echo implode("\r\n",$ban_list["Usernames"]); ?></textarea><br>
IP addresses that should be banned into the outer bands of the Kuiper Belt (one per line):<br>
<textarea name="ips" rows="10" cols="50"><?php echo implode("\r\n",$ban_list["IPs"]); ?></textarea><br><br>
<input type="submit" value="Ban these people">
</form>
<p><a href="admin.php">Abscond</a></p>
</body>
</html>