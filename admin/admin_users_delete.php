<?php
/**************************************************************
 *		admin_users_delete.php
 *		-------------------
 *  	Разработка и оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Users']['User_delete'] = $filename;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

if (!isset($HTTP_GET_VARS['delete']))
{
$users_per_page = 25;

if ( isset($HTTP_POST_VARS['start1']) )
{
$start1 = intval($HTTP_POST_VARS['start1']);
$start = (($start1 - 1) * $board_config['topics_per_page']);
} else {
$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
$start = ($start < 0) ? 0 : $start;
}

if( isset($HTTP_POST_VARS['sort']) )
{
	$sort_method = $HTTP_POST_VARS['sort'];
}
else if( isset($HTTP_GET_VARS['sort']) )
{
	$sort_method = $HTTP_GET_VARS['sort'];
}
else
{
	$sort_method = 'user_posts';
}

if( isset($HTTP_POST_VARS['order']) )
{
	$sort_order = $HTTP_POST_VARS['order'];
}
else if( isset($HTTP_GET_VARS['order']) )
{
	$sort_order = $HTTP_GET_VARS['order'];
}
else
{
	$sort_order = 'DESC';
}


$template->set_filenames(array(
	'body' => 'admin/admin_users_delete_body.tpl')
);

$sql = "SELECT count(user_id) as total FROM ".USERS_TABLE." WHERE user_id > 0";
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not count users", "", __LINE__, __FILE__, $sql);
}
$row = $db->sql_fetchrow($result);
$total_users = $row['total'];

$template->assign_vars(array(
	'L_SELECT_SORT_METHOD' 	=> $lang['Select_sort_method'],
	'U_LIST_ACTION' 		=> append_sid("admin_users_delete.$phpEx"),
	'U_DELETE_ACTION' 		=> append_sid("admin_users_delete.$phpEx?delete"),
	'L_SORT' 				=> $lang['Sort'],
	'L_ORDER' 				=> $lang['Order'],
	'L_SORT_DESCENDING' 	=> $lang['Sort_Descending'],
	'L_SORT_ASCENDING'	 	=> $lang['Sort_Ascending'],
	'ID_SELECTED' 			=> ($sort_method == 'user_id') ? 'selected="selected"' : '',
	'USERNAME_SELECTED' 	=> ($sort_method == 'username') ? 'selected="selected"' : '',
	'POSTS_SELECTED' 		=> ($sort_method == 'user_posts') ? 'selected="selected"' : '',
	'LASTVISIT_SELECTED' 	=> ($sort_method == 'user_lastvisit') ? 'selected="selected"' : '',
	'ASC_SELECTED' 			=> ($sort_order != 'DESC') ? 'selected="selected"' : '',
	'DESC_SELECTED' 		=> ($sort_order == 'DESC') ? 'selected="selected"' : '',
	'TOTAL_USERS' 			=> $total_users)
);

$sql = "SELECT user_id, username, user_regdate, user_lastvisit, user_posts, user_active
	FROM ".USERS_TABLE."
	WHERE user_id > 0
	ORDER BY " . $sort_method . " " . $sort_order . "
	LIMIT ".$start.",".$users_per_page;
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not query Users information", "", __LINE__, __FILE__, $sql);
}

while( $row = $db->sql_fetchrow($result) )
{
	$userrow[] = $row;
}

for ($i = 0; $i < $users_per_page; $i++)
{
	if (empty($userrow[$i]))
	{
		break;
	}

	$number	= $i + 1 + $start;
	$row_color = (($i % 2) == 0) ? "row_easy" : "row_hard";
	
	$template->assign_block_vars('userrow', array(
		'L_NUMBER'		=> $number,
		'COLOR' 		=> $row_color,
		'NUMBER' 		=> $userrow[$i]['user_id'],
		'USERNAME' 		=> ( $userrow[$i]['user_active'] ) ? '<b>' . $userrow[$i]['username'] . '</b>' : $userrow[$i]['username'],
		'U_ADMIN_USER' 	=> append_sid("admin_users.$phpEx?mode=edit&amp;" . POST_USERS_URL . "=" . $userrow[$i]['user_id']),
		'JOINED'	 	=> create_date($lang['DATE_FORMAT'], $userrow[$i]['user_regdate'], $board_config['board_timezone']),
		'LAST_VISIT' 	=> (!$userrow[$i]['user_lastvisit']) ? '' : create_date($lang['DATE_FORMAT'], $userrow[$i]['user_lastvisit'], $board_config['board_timezone']),
		'POSTS' 		=> $userrow[$i]['user_posts']) 
	);
} 

$template->assign_vars(array(
	'PAGINATION' => generate_pagination(append_sid("admin_users_delete.$phpEx?sort=$sort_method&amp;order=$sort_order"), $total_users, $users_per_page, $start)) 
);

$template->pparse('body');

include('./page_footer_admin.'.$phpEx);

} else {
	if ( isset($HTTP_POST_VARS['user_id_list']) )
	{
		$users = $HTTP_POST_VARS['user_id_list'];
	} else {
		message_die(GENERAL_MESSAGE, 'Ни один юзер не выбран. Шутим?');
	}

	for($i = 0; $i < count($users); $i++)
	{
		$user_id = intval($users[$i]);
		if( $userdata['user_id'] == $user_id )
		{
			message_die(GENERAL_MESSAGE, 'Ты больной? Зачем тебе удалять самого себя??? о_0');
		}
		$this_userdata = get_userdata($user_id);

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

		$sql = "DELETE FROM " . BOOKMARK_TABLE . "
			WHERE user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not delete user\'s bookmarks', '', __LINE__, __FILE__, $sql);
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
	}
	message_die(GENERAL_MESSAGE, 'Юзеры отправились фтопку');
}

?>