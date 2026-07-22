<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$disabled=false;
	
	if(file_exists($database_folder . "timezone.txt"))
	{
		date_default_timezone_set(file_get_contents($database_folder . "timezone.txt"));
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
	
	$ban_list=array("Usernames"=>array(),"IPs"=>array());
	if(file_exists($database_folder . "banned.txt"))
	{
		$ban_list=unserialize(file_get_contents($database_folder . "banned.txt"));
	}
	
	$requests=array("New"=>array(),"Queued"=>array(),"Done"=>array());
	if(file_exists($database_folder . "requests.txt"))
	{
		$requests=unserialize(file_get_contents($database_folder . "requests.txt"));
	}
	
	$username="";
	if(!empty($_SESSION['past_username']))
	{
		$username=$_SESSION['past_username'];
	}
	if(!empty($_POST['username']))
	{
		$username=preg_replace("/[^A-Za-z0-9 ]/","",$_POST['username']);
	}
?>
<html>
<head>
<title>Music Request System LITE - Make Request</title>
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
	
	if($open != "y" && $admin !== true)
	{
		echo("<p><b>HOLD THE LINE</b>! Requests are not being accepted right now.</p>");
		$disabled=true;
	}
	if($restrict > 0)
	{
		$found=false;
		foreach($requests["New"] as $request)
		{
			if($request["User"] == $username || $request["IP"] == $_SERVER['REMOTE_ADDR'])
			{
				$found=true;
				break;
			}
		}
		if($found === false && $restrict == 2)
		{
			foreach($requests["Queued"] as $request)
			{
				if($request["User"] == $username || $request["IP"] == $_SERVER['REMOTE_ADDR'])
				{
					$found=true;
					break;
				}
			}
		}
		if($found === true)
		{
			echo("<p><b>HOLD THE LINE</b>! You have too many requests going at this time.</p>");
			$disabled=true;
		}
	}
	if(in_array($username,$ban_list["Usernames"]) || in_array($_SERVER['REMOTE_ADDR'],$ban_list["IPs"]))
	{
		echo("<p><b>HOLD THE LINE</b>! You have been BANNED from making requests.</p>");
		$disabled=true;
	}
	
	if(!empty($_POST['s']) && $disabled === false)
	{
		$req=htmlspecialchars($_POST['request']);
		
		$request=array("ID"=>false,"User"=>$username,"IP"=>$_SERVER['REMOTE_ADDR'],"Time"=>time(),"Text"=>$req,"Status"=>0);
		
		$id=0;
		foreach(array_keys($requests["New"]) as $r)
		{
			$id=max($id,$r);
		}
		foreach(array_keys($requests["Queued"]) as $r)
		{
			$id=max($id,$r);
		}
		foreach(array_keys($requests["Done"]) as $r)
		{
			$id=max($id,$r);
		}
		$id++;
		$request["ID"]=$id;
		
		$requests["New"][$id]=$request;
		
		$fh=@fopen($database_folder . "requests.txt",'w');
		if($fh)
		{
			@fwrite($fh,serialize($requests));
			@fclose($fh);
		}
		$_SESSION['past_username']=$username;
		echo("<p>Request has been made. Please <a href=\"index.php\">click here</a>.</p>");
		$disabled=true;
	}
?>
<h1>Music Request System LITE</h1>
<h2>Make A Request</h2>
<form method="post" action="post.php">
<input type="hidden" name="s" value="y">
Username: <input type="text" name="username" value="<?php echo $username; ?>" size="10" <?php if($anon == "n") { echo "required=\"required\""; } ?>> (letters numbers and spaces ONLY)<br>
IP address: <?php echo $_SERVER['REMOTE_ADDR']; ?> (WILL BE RECORDED by the server; visible only to the BOFH)<br>
Request: <input type="text" name="request" size="50" required="required"><br><br>
<input type="submit" value="Make request" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Never mind</a></p>
</body>
</html>