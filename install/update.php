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
	<link rel="stylesheet" href="http://phpbb-wap.com/style.css" type="text/css" />	
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

	//
	// 友情链接
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "linkexchange_config";

	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "linkexchange";
	$sql[] = "CREATE TABLE `" . $table_prefix . "linkexchange` (
	  `link_id` smallint(8) unsigned NOT NULL default '0',
	  `link_name` varchar(255) NOT NULL default '',
	  `link_email` varchar(255) NOT NULL default '',  
	  `link_website` varchar(255) NOT NULL default '',
	  `link_img` varchar(255) NOT NULL default '',    
	  `link_desc` text NOT NULL,
	  `link_url` varchar(255) NOT NULL default '',
	  `link_active` tinyint(3) unsigned NOT NULL default '1',
	  `link_out` int(11) NOT NULL default '0',
	  `link_in` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`link_id`)
	) TYPE=MyISAM DEFAULT CHARSET=utf8";
	$sql[] = "INSERT INTO `" . $table_prefix . "linkexchange` VALUES ('0', '爱疯的云', 'support@phpbb-wap.com', '中文phpBB-WAP', 'http://phpbb-wap.com/images/logo.png', '中文phpBB-WAP', 'phpbb-wap.com', '0', '0', '0')";

	//
	// 聊天室
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "shout";
	$sql[] = "CREATE TABLE `" . $table_prefix . "shout` (
	  `shout_id` mediumint(8) unsigned NOT NULL auto_increment,
	  `shout_username` varchar(25) NOT NULL,
	  `shout_user_id` mediumint(8) NOT NULL,
	  `shout_session_time` int(11) NOT NULL,
	  `shout_ip` char(8) NOT NULL,
	  `shout_text` text NOT NULL,
	  `enable_bbcode` tinyint(1) NOT NULL,
	  `enable_html` tinyint(1) NOT NULL,
	  `enable_smilies` tinyint(1) NOT NULL,
	  `shout_bbcode_uid` varchar(10) NOT NULL,
	  KEY `shout_id` (`shout_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	//
	//精华帖子
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "specials";
	$sql[] = "CREATE TABLE `" . $table_prefix . "specials` (
	  `special_id` mediumint(8) unsigned NOT NULL auto_increment,
	  `special_name` varchar(255) NOT NULL,
	  `special_forum` mediumint(8) NOT NULL,
	  PRIMARY KEY  (`special_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$sql[] = "ALTER TABLE " . $table_prefix . "topics ADD topic_special tinyint(8) default '0'";

	//
	//签名
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "sign";
	$sql[] = "CREATE TABLE `" . $table_prefix . "sign` (
	  `sign_id` mediumint(8) unsigned NOT NULL auto_increment,
	  `sign_user_id` mediumint(8) NOT NULL default '-1',
	  `sign_time` int(11) NOT NULL default '0',
	  `sign_talk` text,
	  PRIMARY KEY  (`sign_id`),
	  KEY `sign_user_id` (`sign_user_id`),
	  KEY `sign_time` (`sign_time`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	//
	// 虚拟彩票
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "lottery";
	$sql[] = "CREATE TABLE `" . $table_prefix . "lottery` (
	  `id` int(10) unsigned NOT NULL auto_increment,
	  `user_id` int(10) NOT NULL,
	  PRIMARY KEY  (`id`),
	  KEY `user_id` (`user_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "lottery_history";
	$sql[] = "CREATE TABLE `" . $table_prefix . "lottery_history` (
	  `id` int(10) unsigned NOT NULL auto_increment,
	  `user_id` int(10) NOT NULL,
	  `amount` int(10) NOT NULL,
	  `currency` char(32) NOT NULL,
	  `time` int(10) NOT NULL,
	  PRIMARY KEY  (`id`),
	  KEY `user_id` (`user_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_cost', '1')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_ticktype', 'single')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_length', '500000')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_name', '虚拟彩票')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_base', '50')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_start', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_reset', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_status', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_items', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_win_items', '')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_show_entries', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_mb', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_mb_amount', '1')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_history', '1')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_currency', '')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_item_mcost', '1')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_item_xcost', '500')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('lottery_random_shop', '')";

	//
	// 虚拟银行
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "bank";
	$sql[] = "CREATE TABLE `" . $table_prefix . "bank` (
	  `id` int(10) unsigned NOT NULL auto_increment,
	  `user_id` int(10) NOT NULL,
	  `holding` int(10) unsigned default '0',
	  `totalwithdrew` int(10) unsigned default '0',
	  `totaldeposit` int(10) unsigned default '0',
	  `opentime` int(10) unsigned NOT NULL,
	  `fees` char(5) NOT NULL default 'on',
	  PRIMARY KEY  (`user_id`),
	  KEY `id` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankinterest', '2')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankfees', '2')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankpayouttime', '84600')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankname', '虚拟银行')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankopened', 'off')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bankholdings', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('banktotaldeposits', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('banktotalwithdrew', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('banklastrestocked', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bank_minwithdraw', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bank_mindeposit', '0')";
	$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('bank_interestcut', '0')";

	//
	// 友情链接
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "linkexchange";
	$sql[] = "CREATE TABLE `" . $table_prefix . "linkexchange` (
	  `link_id` smallint(8) unsigned NOT NULL default '0',
	  `link_name` varchar(255) character set utf8 NOT NULL,
	  `link_email` varchar(255) character set utf8 NOT NULL,
	  `link_website` varchar(255) character set utf8 NOT NULL,
	  `link_img` varchar(255) character set utf8 NOT NULL,
	  `link_desc` text character set utf8 NOT NULL,
	  `link_url` varchar(255) character set utf8 NOT NULL,
	  `link_active` tinyint(3) unsigned NOT NULL default '1',
	  `link_out` int(11) NOT NULL default '0',
	  `link_in` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`link_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	//
	// 红包
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "deliver";
	$sql[] = "CREATE TABLE `" . $table_prefix . "deliver` (
	  `deliver_id` mediumint(8) unsigned NOT NULL auto_increment,
	  `deliver_poster` mediumint(8) NOT NULL default '0',
	  `deliver_point` int(11) NOT NULL default '0',
	  `deliver_cut_point` int(11) NOT NULL default '0',
	  `deliver_title` char(255) NOT NULL,
	  `deliver_reason` text,
	  PRIMARY KEY (`deliver_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "partake_deliver";
	$sql[] = "CREATE TABLE `" . $table_prefix . "partake_deliver` (
	  `partake_id` mediumint(8) unsigned NOT NULL auto_increment,
	  `partake_user_id` mediumint(8) NOT NULL default '0',
	  `deliver_id` mediumint(8) NOT NULL default '0',
	  PRIMARY KEY (`partake_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	
	//
	// 邀请
	//
	$sql[] = "DROP TABLE IF EXISTS " . $table_prefix . "invite";
	$sql[] = "CREATE TABLE `" . $table_prefix . "invite` (
	  `id` int(6) NOT NULL auto_increment,
	  `user_id` int(8) NOT NULL,
	  `ip` text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$sql[] = "ALTER TABLE " . $table_prefix . "users ADD invite_num int(5) NOT NULL default '0'";

	//
	// 帖子颜色
	//
	$sql[] = "ALTER TABLE `" . $table_prefix . "topics` ADD `topic_color` varchar(6) default NULL";

	//
	//新年风格
	//
	$sql[] = "INSERT INTO " . $table_prefix . "styles (style_name, style_path) VALUES ('新年快乐', 'NewYear')";

	//
	// 更新附件的一些设置
	//
	$sql[] = "UPDATE `" . $table_prefix . "attachments_config` SET `config_value` =  'download' WHERE `config_name` = 'upload_dir'";
	$sql[] = "UPDATE `" . $table_prefix . "attachments_config` SET `config_value` =  'upload_img' WHERE `config_name` = 'images/icons/icon_clip.gif'";
	$sql[] = "UPDATE `" . $table_prefix . "attachments_config` SET `config_value` =  'topic_icon' WHERE `config_name` = 'images/icons/icon_clip.gif'";
	
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
	<link rel="stylesheet" href="http://phpbb-wap.com/style.css" type="text/css" />	
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
2、本人（即 “爱疯的云” 、中文phpBB-WAP） 没有义务解答您的的问题！
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