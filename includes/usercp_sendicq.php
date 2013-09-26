<?php
/**************************************************************
 *		usercp_sendicq.php
 *		-------------------
 *		Разработка и оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ( !$board_config['send_user_icq'] )
{
	message_die(GENERAL_MESSAGE, $lang['Icq_disable']);
}
if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

$current_time = time();

if (($current_time - intval($board_config['last_check_icq_time'])) < intval($board_config['flood_icq_interval']))
{
	message_die(GENERAL_MESSAGE, $lang['Flood_Icq_Error']);
}

$user = intval($HTTP_GET_VARS[POST_USERS_URL]);

$sql = "SELECT username, user_icq, user_icq_send 
	FROM " . USERS_TABLE . " 
	WHERE user_id = '$user'";
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, 'Could not obtain user information for sendpassword', '', __LINE__, __FILE__, $sql);
}
if ( !$row = $db->sql_fetchrow($result) )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
if ( !$row['user_icq_send'] )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_icq_send']);
}
if ( empty($row['user_icq']) )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_icq_specified']);
}
$user_icq = intval($row['user_icq']);
$username = $row['username'];

if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['message']) )
{
	$message = htmlspecialchars(trim($HTTP_POST_VARS['message']));
	$bot = sprintf($lang['Icq_msg_bot'], 'http://'.$board_config['server_name'].$board_config['script_path'], $userdata['username']);
	$msg = $message.$bot;
	$msg = u2w($msg);

	include($phpbb_root_path . 'includes/functions_icq.'.$phpEx);
	define('UIN', $board_config['default_icq']);
	define('PASSWORD', $board_config['default_icq_pass']);

	$icq = new WebIcqLite();
	if ( $icq->connect(UIN, PASSWORD) )
	{
		if ( !$icq->send_message($user_icq, $msg) )
		{
			$icq->disconnect();

			$sql = "UPDATE " . CONFIG_TABLE . " SET
				config_value = " . $current_time . "
				WHERE config_name = 'last_check_icq_time'";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update last check icq time information', '', __LINE__, __FILE__, $sql);
			}

			message_die(GENERAL_MESSAGE, w2u($icq->error));
		} else {
			$icq->disconnect();

			$sql = "UPDATE " . CONFIG_TABLE . " SET
				config_value = " . $current_time . "
				WHERE config_name = 'last_check_icq_time'";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update last check icq time information', '', __LINE__, __FILE__, $sql);
			}

			message_die(GENERAL_MESSAGE, $lang['Message_icq_send']);
		}
	}
	else
	{
		message_die(GENERAL_MESSAGE, w2u($icq->error));
	}
}
else
{
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'profile_send_icq.tpl')
	);

	if ( isset($HTTP_POST_VARS['submit']) && empty($HTTP_POST_VARS['message']) )
	{
		$template->set_filenames(array(
			'reg_header' => 'error_body.tpl')
		);
		$template->assign_vars(array(
			'ERROR_MESSAGE' => $lang['No_message_icq'])
		);
		$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
	}

	$template->assign_vars(array(
		'USERNAME' => $username,
		'ICQ' => $user_icq,
		'L_SUBMIT' => $lang['Submit'],
		'S_POST_ACTION' => append_sid("profile.$phpEx?mode=sendicq&amp;u=$user"))
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}

?>