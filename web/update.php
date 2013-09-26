<?php

include("../config.php");
$connect = mysql_connect($dbhost,$dbuser,$dbpasswd);
mysql_select_db($dbname,$connect);

echo '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Обновление WEB-версии</title>
<style type="text/css">
body { background-color : #FFF; color: #494949; font-family: sans-serif; font-size: 12px; }
a:link,a:active,a:visited { color: #105289; text-decoration: none; }
a:hover { text-decoration: none; color: black; position: relative; top: 1px; }
div.navbar { background-color:#cadceb;margin-bottom:4px;border-radius:6px;padding:5px }
</style>
</head>
<body>
<div class="navbar">
<a href="index.php">首页</a>|升级
</div>';

if($db_charset_utf)
{
	mysql_query("SET NAMES utf8");
}

function mysql_table_seek($tablename, $dbname)
{
	$table_list = mysql_query("SHOW TABLES FROM " . $dbname);
	while ($row = mysql_fetch_row($table_list))
	{
		if ($tablename==$row[0])
		{
			return true;
		}
	}
	return false;
}

$sql = array();
if ( !mysql_table_seek($table_prefix . "themes", $dbname) )
{
	$sql[] = "CREATE TABLE " . $table_prefix . "themes (themes_id mediumint(8) UNSIGNED NOT NULL auto_increment, template_name varchar(30) NOT NULL default '', style_name varchar(30) NOT NULL default '', head_stylesheet varchar(100) default NULL, body_background varchar(100) default NULL, body_bgcolor varchar(6) default NULL, body_text varchar(6) default NULL, body_link varchar(6) default NULL, body_vlink varchar(6) default NULL, body_alink varchar(6) default NULL, body_hlink varchar(6) default NULL, tr_color1 varchar(6) default NULL, tr_color2 varchar(6) default NULL, tr_color3 varchar(6) default NULL, tr_class1 varchar(25) default NULL, tr_class2 varchar(25) default NULL, tr_class3 varchar(25) default NULL, th_color1 varchar(6) default NULL, th_color2 varchar(6) default NULL, th_color3 varchar(6) default NULL, th_class1 varchar(25) default NULL, th_class2 varchar(25) default NULL, th_class3 varchar(25) default NULL, td_color1 varchar(6) default NULL, td_color2 varchar(6) default NULL, td_color3 varchar(6) default NULL, td_class1 varchar(25) default NULL, td_class2 varchar(25) default NULL, td_class3 varchar(25) default NULL, fontface1 varchar(50) default NULL, fontface2 varchar(50) default NULL, fontface3 varchar(50) default NULL, fontsize1 tinyint(4) default NULL, fontsize2 tinyint(4) default NULL,fontsize3 tinyint(4) default NULL, fontcolor1 varchar(6) default NULL, fontcolor2 varchar(6) default NULL, fontcolor3 varchar(6) default NULL, span_class1 varchar(25) default NULL, span_class2 varchar(25) default NULL, span_class3 varchar(25) default NULL, img_size_poll smallint(5) UNSIGNED, img_size_privmsg smallint(5) UNSIGNED, PRIMARY KEY (themes_id))";
}
else
{
	$sql[] = "TRUNCATE " . $table_prefix . "themes";
}
if ( !mysql_table_seek($table_prefix . "themes_name", $dbname) )
{
	$sql[] = "CREATE TABLE " . $table_prefix . "themes_name (themes_id smallint(5) UNSIGNED DEFAULT '0' NOT NULL, tr_color1_name char(50), tr_color2_name char(50), tr_color3_name char(50), tr_class1_name char(50), tr_class2_name char(50), tr_class3_name char(50), th_color1_name char(50), th_color2_name char(50), th_color3_name char(50), th_class1_name char(50), th_class2_name char(50), th_class3_name char(50), td_color1_name char(50), td_color2_name char(50), td_color3_name char(50), td_class1_name char(50), td_class2_name char(50), td_class3_name char(50), fontface1_name char(50), fontface2_name char(50), fontface3_name char(50), fontsize1_name char(50), fontsize2_name char(50), fontsize3_name char(50), fontcolor1_name char(50), fontcolor2_name char(50), fontcolor3_name char(50), span_class1_name char(50), span_class2_name char(50), span_class3_name char(50), PRIMARY KEY (themes_id))";
}
else
{
	$sql[] = "TRUNCATE " . $table_prefix . "themes_name";
}
$sql[] = "INSERT INTO " . $table_prefix . "themes VALUES (1, 'prosilver', 'prosilver', 'prosilver.css', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'bg2', 'bg1', '', '', '', '', 0, 0, 0, '', '00AA00', 'AA0000', '', '', '', 0, 0)";
$sql[] = "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('default_web_style','1')";
$sql[] = "ALTER TABLE " . $table_prefix . "users ADD user_web_style tinyint(4)";

$errormsg = '';

for( $i = 0; $i < count($sql); $i++ )
{
	if( !$result = mysql_query($sql[$i]) )
	{
		$errorsql = true;
	}
	else
	{
		$errorsql = false;
	}
}

if($errorsql)
{
	echo '<span style="color:#FF1493">Обновление выполнено, но с ошибками.<br/>Проверьте, корректно ли работает веб-версия. Если всё нормально, значит OK.</span><br/>';
	echo $errormsg;
} else {
	echo 'Обновление выполнено<br/>';
}
echo '<p>(c) Игорь Гутник, 2010</p>
</body>
</html>';
mysql_close($connect);

?>