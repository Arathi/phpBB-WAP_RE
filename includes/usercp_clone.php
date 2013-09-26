<?php
/***************************************************************************
 *                           usercp_clone.php
 *                            -------------------
 *      Разработка: Фёдор Кулаков
 *      Модификация: Гутник Игорь
 *		2010 год
 *		简体中文：爱疯的云
 *		说明：类似用户
 ***************************************************************************/
 
if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ($userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_ERROR, '您不是管理员！');
}

if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

include($phpbb_root_path . 'includes/page_header.' . $phpEx);

$template->set_filenames(array(
	'body' => 'profile_password.tpl')
);

$user_id = intval($HTTP_GET_VARS[POST_USERS_URL]);

$sql = "SELECT * FROM " . USERS_TABLE . " 
	WHERE user_id = $user_id";
	
if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not obtain user information');
}
$userinfo = $db->sql_fetchrow($result);
$user_password = $userinfo['user_password'];
$username = $userinfo['username'];
$user_icq = $userinfo['user_icq'];
$user_website = $userinfo['user_website'];

$template->assign_vars(array(
	'USERNAME' => $username)
);

$sql = "SELECT * FROM " . USERS_TABLE . " 
	WHERE user_password = '$user_password'";
	
if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not obtain user password information');
}
if ( $row = $db->sql_fetchrow($result) )
{
	$i = 0;
	$link_passwords = '';
	do
	{
		$link_passwords .= '- <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$row['user_id']) . '">' . $row['username'] . '</a><br>';
		$i++;
	}
	while ( $row = $db->sql_fetchrow($result) );
	$db->sql_freeresult($result);
} else {
	$link_passwords = '没有密码相同的会员';
}

$template->assign_vars(array('LINK_PASSWORD' => $link_passwords));

if ( !empty($user_icq) )
{
	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_icq = '$user_icq'";
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain user password information');
	}
	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		$link_icq = '';
		do
		{
			$link_icq .= '- <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$row['user_id']) . '">' . $row['username'] . '</a><br>';
			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	} else {
		$link_icq = '没有相同 ICQ 的会员';
	}
} else {
	$link_icq = '该会员没有在个人资料中填写 ICQ 信息';
}
	
$template->assign_vars(array('LINK_ICQ' => $link_icq));

if ( !empty($user_website) )
{
	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_website = '$user_website'";
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain user password information');
	}
	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		$link_website = '';
		do
		{
			$link_website .= '- <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;u=".$row['user_id']) . '">' . $row['username'] . '</a><br>';
			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	} else {
		$link_website = '没有相同博客的会员';
	}
} else {
	$link_website = '该会员没有在个人资料中填写博客信息';
}
	
$template->assign_vars(array(
	'LINK_WEBSITE' => $link_website)
);

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.' . $phpEx);

?>