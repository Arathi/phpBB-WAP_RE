<?php
/*************************************************************
 *		usercp_delete.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		
 *************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}
if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

if ( $userdata['user_level'] == ADMIN )
{

$confirm = isset($HTTP_POST_VARS['confirm']) ? true : false;
$user_id = intval($HTTP_GET_VARS[POST_USERS_URL]);

if (!($this_userdata = get_userdata($user_id)))
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified'] );
}
if ( $this_userdata['user_level'] != USER )
{
	message_die(GENERAL_MESSAGE, '您不能删除管理员！');
}
if( $userdata['user_id'] == $user_id )
{
	message_die(GENERAL_MESSAGE, '您不能删除您自己！');
}

if ( $confirm )
{
	$sql = "SELECT g.group_id 
		FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g  
		WHERE ug.user_id = $user_id 
			AND g.group_id = ug.group_id 
			AND g.group_single_user = 1";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain group information for this user', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);

	$sql = "UPDATE " . POSTS_TABLE . "
		SET poster_id = " . DELETED . ", post_username = '" . str_replace("\\'", "''", addslashes($this_userdata['username'])) . "' 
		WHERE poster_id = $user_id";
	if( !$db->sql_query($sql) )	
	{
		message_die(GENERAL_ERROR, 'Could not update posts for this user', '', __LINE__, __FILE__, $sql);
		}

	$sql = "UPDATE " . TOPICS_TABLE . "
		SET topic_poster = " . DELETED . " 
		WHERE topic_poster = $user_id";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update topics for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "UPDATE " . VOTE_USERS_TABLE . "
		SET vote_user_id = " . DELETED . "
		WHERE vote_user_id = $user_id";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update votes for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "UPDATE " . GROUPS_TABLE . "
		SET group_moderator = " . $userdata['user_id'] . "
		WHERE group_moderator = $user_id";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update group moderators', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . USERS_TABLE . "
		WHERE user_id = $user_id";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . USER_GROUP_TABLE . "
		WHERE user_id = $user_id";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete user from user_group table', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . GROUPS_TABLE . "
		WHERE group_id = " . $row['group_id'];
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete group for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
		WHERE group_id = " . $row['group_id'];
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete group for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
		WHERE user_id = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete user from topic watch table', '', __LINE__, __FILE__, $sql);
	}
			
	$sql = "DELETE FROM " . BANLIST_TABLE . "
		WHERE ban_userid = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete user from banlist table', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . SESSIONS_TABLE . "
		WHERE session_user_id = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete sessions for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM " . SESSIONS_KEYS_TABLE . "
		WHERE user_id = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete auto-login keys for this user', '', __LINE__, __FILE__, $sql);
	}

	$sql = "SELECT privmsgs_id
		FROM " . PRIVMSGS_TABLE . "
		WHERE privmsgs_from_userid = $user_id 
			OR privmsgs_to_userid = $user_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select all users private messages', '', __LINE__, __FILE__, $sql);
	}

	while ( $row_privmsgs = $db->sql_fetchrow($result) )
	{
		$mark_list[] = $row_privmsgs['privmsgs_id'];
	}
		
	if ( count($mark_list) )
	{
		$delete_sql_id = implode(', ', $mark_list);

		$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
			WHERE privmsgs_text_id IN ($delete_sql_id)";
		$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_id IN ($delete_sql_id)";
	
		if ( !$db->sql_query($delete_sql) )
		{
			message_die(GENERAL_ERROR, 'Could not delete private message info', '', __LINE__, __FILE__, $delete_sql);
		}
		
		if ( !$db->sql_query($delete_text_sql) )
		{
			message_die(GENERAL_ERROR, 'Could not delete private message text', '', __LINE__, __FILE__, $delete_text_sql);
		}
	}

	message_die(GENERAL_MESSAGE, '删除失败！');

} else {

	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'confirm_body' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' => '确认',
		'MESSAGE_TEXT' => '请问是否删除该用户？',

		'L_YES' => $lang['Yes'],
		'L_NO' => $lang['No'],

		'S_CONFIRM_ACTION' => append_sid("profile.$phpEx?mode=delete&amp;u=$user_id"))
	);

	$template->pparse('confirm_body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

}

} else {
	message_die(GENERAL_MESSAGE, 'Ты кто такой? Топай отсюда по-хорошему...');
}

?>
