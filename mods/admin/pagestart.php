<?php
/**************************************************
 *		pagestart.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************/

if (!defined('IN_PHPBB'))
{
	die("Hacking attempt");
}

define('IN_ADMIN', true);
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);

if (!$userdata['session_logged_in'])
{
	redirect(append_sid("login.$phpEx?redirect=mods/admin/index.$phpEx", true));
}
else if ($userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_MESSAGE, $lang['Not_admin']);
}

if ($HTTP_GET_VARS['sid'] != $userdata['session_id'])
{
	redirect("index.$phpEx?sid=" . $userdata['session_id']);
}

if (!$userdata['session_admin'])
{
	redirect(append_sid("login.$phpEx?redirect=mods/admin/index.$phpEx&admin=1", true));
}

if (empty($no_page_header))
{
	include('./page_header_mods.'.$phpEx);
}

?>