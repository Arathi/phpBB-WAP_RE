<?php
/*************************************************************
 *		admin_money_payment.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 ************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Shop']['Shop_pay'] = $file;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

if ( !$board_config['pay_money'] )
{
	message_die(GENERAL_MESSAGE, $lang['Shop_Error_Not_Open_Pay_money']);
}

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = intval($HTTP_POST_VARS['start1']);
	$start = (($start1 - 1) * $board_config['topics_per_page']);
} else {
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$template->set_filenames(array(
	'body' => 'admin/admin_users_payment.tpl')
);

if ( isset($HTTP_GET_VARS['cancel']) )
{
	if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}
	$user = intval($HTTP_GET_VARS[POST_USERS_URL]);

	$sql = "SELECT * 
		FROM " . USERS_TABLE . " 
		WHERE user_id = '$user'";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain user information', '', __LINE__, __FILE__, $sql);
	}
	if ( !$row = $db->sql_fetchrow($result) )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}
	$earned = $row['user_money_earned'];

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_money_earned = 0 
		WHERE user_id = $user";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
	}
	$sql = "UPDATE " . CONFIG_TABLE . "
		SET config_value = config_value - $earned
		WHERE config_name = 'money_earned'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update config table', '', __LINE__, __FILE__, $sql);
	}

	message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Cancel_User_Pay_money'], $row['username'], $earned));

} 
elseif ( isset($HTTP_GET_VARS['pay']) ) 
{
	if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}
	$user = intval($HTTP_GET_VARS[POST_USERS_URL]);

	$sql = "SELECT * 
		FROM " . USERS_TABLE . " 
		WHERE user_id = '$user'";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain user information', '', __LINE__, __FILE__, $sql);
	}
	if ( !$row = $db->sql_fetchrow($result) )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}
	$earned = $row['user_money_earned'];

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_money_payment = user_money_payment + $earned, user_money_earned = 0 
		WHERE user_id = $user";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
	}
	$sql = "UPDATE " . CONFIG_TABLE . "
		SET config_value = config_value + $earned
		WHERE config_name = 'money_payment'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update config table', '', __LINE__, __FILE__, $sql);
	}

	message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Message_User_Pay_money'], $earned, $row['username'], $row['user_purse']));

} 
else 
{

	$template->assign_vars(array(
		'L_TITLE'						=> $lang['Shop_Pay_Money'],
		'L_CANCEL'						=> $lang['Shop_Cancel'],
		'L_NOT_PAY_MONEY_USERS'			=> $lang['Shop_Not_Pay_Money_Users'],
		'L_NOTE_PAY_MONEY'				=> $lang['Shop_Note_Pay_Money'],
		'L_PAY_MONEY_EXPLAIN'			=> sprintf($lang['Shop_Pay_money_Explain'], $board_config['money_earned'], $board_config['money_payment']))
	);

	$sql = "SELECT username, user_id, user_posts, user_money_earned 
		FROM " . USERS_TABLE . "
		WHERE user_id <> " . ANONYMOUS . " AND user_money_earned > 0 
		ORDER BY user_money_earned ASC LIMIT $start, " . $board_config['topics_per_page'];
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$username = $row['username'];
			$user_money_earned = $row['user_money_earned'];
			$user_id = $row['user_id'];
			$posts = $row['user_posts'];

			$template->assign_block_vars('memberrow', array(
				'USERNAME' 		=> $username,
				'POSTS' 		=> $posts,
				'EARNED' 		=> $user_money_earned,
				'U_VIEWPROFILE'	=> append_sid("../profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"),
				'U_PAY' 		=> append_sid("admin_money_payment.$phpEx?pay&amp;" . POST_USERS_URL . "=$user_id"),
				'U_CANCEL' 		=> append_sid("admin_money_payment.$phpEx?cancel&amp;" . POST_USERS_URL . "=$user_id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	} 
	else 
	{
		$template->assign_block_vars('no_pay', array() );
	}

	$sql = "SELECT count(*) AS total
		FROM " . USERS_TABLE . "
		WHERE user_id <> " . ANONYMOUS . " AND user_money_earned > 0";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
	}

	if ( $total = $db->sql_fetchrow($result) )
	{
		$total_members = $total['total'];
		$pagination = generate_pagination("admin_money_payment.$phpEx", $total_members, $board_config['topics_per_page'], $start);
	}
	$db->sql_freeresult($result);

	if ( $total_members > $board_config['topics_per_page'] )
	{
		$template->assign_vars(array(
			'PAGINATION' => $pagination)
		);
	}
	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);
}

?>