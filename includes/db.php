<?php
/***************************************************************************
 *                                 db.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************************************/

if ( !defined('IN_PHPBB') )// define() 函数定义一个常量
{
	// die() 函数输出一条消息,并退出当前脚本
	die("ERROR!!! THIS FILE PROTECTED. IF YOU SAW THIS REPORT, MEANS HACKERS HERE IS NOTHING TO DO ");
}

switch($dbms)
{
	case 'mysql':
		include($phpbb_root_path . 'db/mysql.'.$phpEx);// 包含文件
		break;

	case 'mysql4':
		include($phpbb_root_path . 'db/mysql4.'.$phpEx);
		break;
}

// 创建对象
$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
if(!$db->db_connect_id)
{
	message_die(CRITICAL_ERROR, "Could not connect to the database");// 内建函数
}

if($db_charset_utf)
{
	$db->sql_query("SET NAMES utf8");// 设置为格式 UTF-8
}

?>