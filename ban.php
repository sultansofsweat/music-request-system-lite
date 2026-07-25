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
	
	$requests=array("New"=>array(),"Queued"=>array(),"Done"=>array());
	if(file_exists($database_folder . "requests.txt"))
	{
		$requests=unserialize(file_get_contents($database_folder . "requests.txt"));
	}
	
	$request_id=0;
	if(!empty($_GET['req']))
	{
		$request_id=preg_replace("/[^0-9]/","",$_GET['req']);
	}
	
	$request=array("ID"=>false,"User"=>false,"IP"=>false,"Time"=>false,"Text"=>false,"Status"=>false);
	if(in_array($request_id,array_keys($requests["New"])))
	{
		$request=$requests["New"][$request_id];
	}
	if(in_array($request_id,array_keys($requests["Queued"])))
	{
		$request=$requests["Queued"][$request_id];
	}
	if(in_array($request_id,array_keys($requests["Done"])))
	{
		$request=$requests["Done"][$request_id];
	}
?>
<html>
<head>
<title>Music Request System LITE - Ban User</title>
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
		echo("<script type=\"text/javascript\">window.location = 'index.php'</script>");
		die("You are not authorized to ban users. You should be automatically redirected. If you are not, please <a href=\"index.php\">click here</a>.");
	}
	
	if(!empty($_GET['s']) && $disabled === false)
	{
		$ban_list=array("Usernames"=>array(),"IPs"=>array());
		if(file_exists($database_folder . "banned.txt"))
		{
			$ban_list=unserialize(file_get_contents($database_folder . "banned.txt"));
		}
		
		if(!in_array($request["User"],$ban_list["Usernames"]))
		{
			$ban_list["Usernames"][]=$request["User"];
		}
		if(!in_array($request["IP"],$ban_list["IPs"]))
		{
			$ban_list["IPs"][]=$request["IP"];
		}
		
		$fh=@fopen($database_folder . "banned.txt",'w');
		if($fh)
		{
			@fwrite($fh,serialize($ban_list));
			@fclose($fh);
		}
		echo("<p>User has been banned. Please <a href=\"index.php\">click here</a>.</p>");
		$disabled=true;
	}
?>
<h1>Music Request System LITE</h1>
<h2>Ban User</h2>
<p>This will add <u>both</u> <b><?php echo $request["User"]; ?></b> and <b><?php echo $request["IP"]; ?></b> to the ban list.</p>
<form method="get" action="ban.php">
<input type="hidden" name="s" value="y">
<input type="hidden" name="req" value="<?php echo $request_id; ?>">
<input type="submit" value="Swing the banhammer" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Never mind</a></p>
</body>
</html>