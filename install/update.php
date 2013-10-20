<?php
/******************
 * v4升级正式版
 *****************/
header("Content-Type: text/html; charset=UTF-8");
if ( isset($_POST['agree']) )
{
	if ( !file_exists("./../config.php") )
	{
		die("升级时必须要有 config.php 文件，请检查根目录的 config.php 文件是否存在！");
	}
	include("./../config.php");
	$connect = mysql_connect($dbhost,$dbuser,$dbpasswd);
	if ( !$connect )
	{
		die("请检查 config.php 内容是否正确！");
	}
	mysql_select_db($dbname,$connect);
?>
	<!DOCTYPE html PUBLIC " -//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />			
	<title>安装_协议</title>
	<link rel="shortcut icon" href="http://phpbb-wap.com/favicon.ico" />
	<link rel="stylesheet" href="../styles/prosilver/theme/prosilver.css" type="text/css" />	
	</head>
	<body>
		<div class="wrap">
			<div class="cat" align="center"><a href="http://phpbb-wap.com"><img src="http://phpbb-wap.com/images/logo.png" /></a></div>
			<div class="row1" align="center"><h2>升级</h2></div>
			<div class="catSides" align="center">升级...</div>
<?php
	if($db_charset_utf)
	{
		mysql_query("SET NAMES utf8");// 设置为 UTF-8 编码
	}

	$sql = array();

    //在此处将升级用的SQL的每一句插入到sql数组中
    
    //升级脚本执行框架
	$errormsg = '';
	for( $i = 0; $i < count($sql); $i++ )
	{
		if( !$result = mysql_query($sql[$i]) )
		{
			$error = mysql_error();
			$errorsql = true;
			$errormsg .= $error['message'].'<br/>';
		}
		else
		{
			$errorsql = false;
		}
	}

	if($errorsql)
	{
		echo $errormsg.'<br/>';
		print_r($error);
	}
	else 
	{
		echo '程序升级成功！<br/>';
	}
	
?>
			<div class="row1">点击<a href="../">这里</a>返回首页</div>
			<div class="copy">(c) 爱疯的云, 2013</div>
			<div class="copy">(c) Arathi, 2013</div>
		</div>
	</body>
	</html>
<?php
	mysql_close($connect);
	if ( is_dir("../install") ) 
	{ 
		rename("../install", "../install_finish"); 
	}
}
else
{
?>
	<!DOCTYPE html PUBLIC " -//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />			
	<title>升级_协议</title>
	<link rel="shortcut icon" href="http://phpbb-wap.com/favicon.ico" />
	<link rel="stylesheet" href="../styles/prosilver/theme/prosilver.css" type="text/css" />	
	</head>
	<body>
		<div class="wrap">
			<div class="cat" align="center"><a href="http://phpbb-wap.com"><img src="http://phpbb-wap.com/images/logo.png" /></a></div>
			<div class="row1" align="center"><h2>升级协议</h2></div>
			<div class="navbar">欢迎使用中文phpBB-WAP，phpBB-WAP是免费开源的移动终端网页程序，它可以在 GPL 协议约束的前提下，自由修改、发布。</div>
			<div class="catSides" align="center">协议</div>
			<form name="agree" action="<?php $_SERVER[PHP_SELF] ?>" method="post">
				<div class="row1">
					<textarea rows="20" style="width:99%;">1、本软件为自由软件，您可以遵守 GPL 协议的前提下自由使用！
2、本人（即"Arathi"、"中文phpBB-WAP开发团队"等）没有义务解答您的的问题！
3、如果您发现程序的 bug，您可以将 bug 以帖子的形式发表到 http://zisuw.com/viewforum.php?f=32 进行交流！
					</textarea>
				</div>
				<div class="row1">我 <input type="submit" name="agree" value="同意"/> 并遵守以上协议并升级！</div>
				<div class="row1">我 <a href="./">不同意</a> 以上协议！</div>
			</form>
		</div>
	</body>
	</html>

<?php 
} 
?>