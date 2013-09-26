<?php
/*********************************
 *		usercp_lock.php
 *		-------------------
 *      Разработка: Фёдор Кулаков
 *      Модификация: Гутник Игорь
 *		2010 год
 *		简体中文：爱疯的云
 *		说明：锁定用户
 *********************************/
 
if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ($userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_MESSAGE, '您不是管理员！');
}

if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

$user_id = intval($HTTP_GET_VARS[POST_USERS_URL]);
$profiledata = get_userdata($HTTP_GET_VARS[POST_USERS_URL]);

if ($profiledata['user_active'] == 1)
{
	if ($profiledata['user_level'] != USER)
	{
		message_die(GENERAL_MESSAGE, '您不能停用该会员！');
	}
	$sql = "UPDATE " . USERS_TABLE . " SET user_active = 0 WHERE user_id = $user_id";
	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, '无法禁用会员');
	} else {
		redirect(append_sid("profile.$phpEx?mode=viewprofile&u=$user_id", true));
	}
} elseif ($profiledata['user_active'] == 0) {
	$sql = "UPDATE " . USERS_TABLE . " SET user_active = 1 WHERE user_id = $user_id";
	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, '无法激活用户');
	} else {
		redirect(append_sid("profile.$phpEx?mode=viewprofile&u=$user_id", true));
	}
}

?>