<?php
/***************************************************************************
 *		medals.php
 *		------------
 *		Разработка: ycl6
 *		Модификация: Гутник Игорь ( чел )
 *			2010 год
 *		简体中文：爱疯的云
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.' . $phpEx);

$userdata = session_pagestart($user_ip, PAGE_MEDALS);
init_userprefs($userdata);

if ( isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']) )
{
	$action = ( isset($HTTP_POST_VARS['action']) ) ? htmlspecialchars($HTTP_POST_VARS['action']) : htmlspecialchars($HTTP_GET_VARS['action']);
}
else
{
	$action = '';
}

if ( $action == 'cp' )
{

$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
$script_name = ( $script_name != '' ) ? $script_name . '/medals.'.$phpEx : 'medals.'.$phpEx;
$server_name = trim($board_config['server_name']);
$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
$server_url = $server_protocol . $server_name . $server_port . $script_name;

if ( isset($HTTP_GET_VARS[POST_MEDAL_URL]) || isset($HTTP_POST_VARS[POST_MEDAL_URL]) )
{
	$medal_id = ( isset($HTTP_POST_VARS[POST_MEDAL_URL]) ) ? intval($HTTP_POST_VARS[POST_MEDAL_URL]) : intval($HTTP_GET_VARS[POST_MEDAL_URL]);
}
else
{
	$medal_id = '';
}

if ( isset($HTTP_POST_VARS['mode']) || isset($HTTP_GET_VARS['mode']) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
	$mode = htmlspecialchars($mode); 
}
else
{
	$mode = '';
}

if (!empty($HTTP_POST_VARS['sid']) || !empty($HTTP_GET_VARS['sid']))
{
	$sid = (!empty($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : $HTTP_GET_VARS['sid'];
}
else
{
	$sid = '';
}

if ($sid == '' || $sid != $userdata['session_id'])
{
	$message = $lang['Not_Authorised'] . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

	message_die(GENERAL_ERROR, $message);
}

$is_moderator = FALSE;

$sql = "SELECT *
	FROM " . MEDAL_TABLE . " 
	WHERE medal_id =" . $medal_id;
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain medal information', '', __LINE__, __FILE__, $sql);
}

if ( $medal_info = $db->sql_fetchrow($result) )
{
	$is_moderator = ($userdata['user_level'] != ADMIN) ? check_medal_mod($medal_id) : TRUE;

	if ( !$is_moderator )
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("index.$phpEx") . '">')
		);
			
		$message = $lang['Not_medal_moderator'] . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

		message_die(GENERAL_MESSAGE, $message);
	}

	if ( isset($HTTP_POST_VARS['submit']) )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx?redirect=medals.$phpEx&" . POST_MEDAL_URL . "=$medal_id", true));
		}

		if (!isset($HTTP_POST_VARS['sid']) || $HTTP_POST_VARS['sid'] != $userdata['session_id'])
		{
			message_die(GENERAL_ERROR, 'Invalid_session');
		}

		if ( !empty($HTTP_POST_VARS['username']) )
		{
			$username = phpbb_clean_username($HTTP_POST_VARS['username']);

			$sql = "SELECT user_id, user_email, user_lang
				FROM " . USERS_TABLE . " 
				WHERE username = '" . str_replace("\'", "''", $username) . "'";

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, "Could not get user information", $lang['Error'], __LINE__, __FILE__, $sql);
			}

			if ( !($user = $db->sql_fetchrow($result)) )
			{
				$template->assign_vars(array(
					'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']) . '">')
				);
				$message = $lang['Could_not_add_user'] . '<br /><br />' . sprintf($lang['Click_return_medal'], '<a href="' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']."") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');
	
				message_die(GENERAL_MESSAGE, $message);
			}

			$issue_reason = ( isset($HTTP_POST_VARS['issue_reason']) ) ? trim(htmlspecialchars($HTTP_POST_VARS['issue_reason'])) : "";

			if ( $user['user_id'] == ANONYMOUS )
			{
				$template->assign_vars(array(
					'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']) . '">')
				);

				$message = $lang['Could_not_anonymous_user'] . '<br /><br />' . sprintf($lang['Click_return_medal'], '<a href="' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']."") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

				message_die(GENERAL_MESSAGE, $message);
			}

			$sql = "INSERT INTO " . MEDAL_USER_TABLE . " (medal_id, user_id, issue_reason, issue_time) 
				VALUES ( $medal_id, " . $user['user_id'] . ", '$issue_reason', " . time() . ")";

			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not add medal to user', '', __LINE__, __FILE__, $sql);
			}

			$medal_sql = "SELECT medal_name 
					FROM " . MEDAL_TABLE . " 
					WHERE medal_id = $medal_id";
			if ( !($result = $db->sql_query($medal_sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get medal information', '', __LINE__, __FILE__, $medal_sql);
			}

			$medal_name_row = $db->sql_fetchrow($result);
		
			$medal_name = $medal_name_row['medal_name'];

			include($phpbb_root_path . 'includes/emailer.'.$phpEx);
			$emailer = new emailer($board_config['smtp_delivery']);
			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);

			$emailer->use_template('medal_added', stripslashes($user['user_lang']));
			$emailer->email_address($user['user_email']);
			$emailer->set_subject($lang['Medal_added']);

			$emailer->assign_vars(array(
				'SITENAME' => $board_config['sitename'], 
				'MEDAL_NAME' => $medal_name,
				'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

				'U_MEDAL' => $server_url)
			);
			$emailer->send();
			$emailer->reset();
			
			$message = $lang['Medal_update_sucessful'] . '<br /><br />' . sprintf($lang['Click_return_medal'], '<a href="' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']) . '">', '</a>');
		}
		else if ( !empty($HTTP_POST_VARS['unmedal_user']) )
		{
			$where_sql = '';

			if ( isset($HTTP_POST_VARS['unmedal_user']) )
			{
				$user_list = $HTTP_POST_VARS['unmedal_user'];

				for($i = 0; $i < count($user_list); $i++)
				{
					if ( $user_list[$i] != -1 )
					{	
						$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . intval($user_list[$i]);
					}
				}
			}
			if ( $where_sql != '' )
			{
				$sql = "DELETE FROM " . MEDAL_USER_TABLE . "
					WHERE issue_id IN ($where_sql)";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Couldn't delete medal info from user", "", __LINE__, __FILE__, $sql);
				}
			}
			$message = $lang['Medal_update_sucessful'] . '<br /><br />' . sprintf($lang['Click_return_medal'], '<a href="' . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']."") . '">', '</a>');
		}
		else if (empty($HTTP_POST_VARS['username']) || empty($HTTP_POST_VARS['unmedal_user']))
		{
			message_die(GENERAL_MESSAGE, $lang['No_username_specified'] );
		}

		message_die(GENERAL_MESSAGE, $message);
	}

	$sql = "SELECT *
		FROM " . MEDAL_TABLE . "
		WHERE medal_id =" . $medal_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting medal information', '', __LINE__, __FILE__, $sql);
	}

	if ( !($medal_info = $db->sql_fetchrow($result)) )
	{
		message_die(GENERAL_MESSAGE, $lang['Medal_not_exist']); 
	}

	$sql = "SELECT u.username, u.user_id
		FROM " . USERS_TABLE . " u, " . MEDAL_MOD_TABLE . " mm
		WHERE mm.medal_id = $medal_id
		AND u.user_id = mm.user_id
		ORDER BY u.username";

	if ( !($result = $db->sql_query($sql)) ) 
	{
		message_die(GENERAL_ERROR, 'Error getting medal moderator information', '', __LINE__, __FILE__, $sql);
	}

	$medal_moderator = '';
	while ( $row = $db->sql_fetchrow($result) )
	{
		$medal_moderator .= ( $medal_moderator != '' ) ? ', ' . '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id'] ) . '">' . $row['username'] . '</a>' : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id'] ) . '">' . $row['username'] . '</a>';
	}

	$sql = "SELECT u.username, u.user_id, mu.issue_id, mu.issue_time
		FROM " . USERS_TABLE . " u, " . MEDAL_USER_TABLE . " mu
		WHERE mu.medal_id = $medal_id
		AND u.user_id = mu.user_id
		ORDER BY u.username"; 

	if ($result = $db->sql_query($sql))
	{
		$medal_member = '';
		$rowset = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$rowset[$row['user_id']]['username'] = $row['username'];
		}
	}

	while (list($user_id, $medal) = @each($rowset))
	{
		$medal_member .= ( $medal_member != '' ) ? ', ' . '<a href="' . append_sid("medals.$phpEx?action=edit&amp;" . POST_MEDAL_URL . "=$medal_id&amp;" . POST_USERS_URL . "=" . $user_id."&amp;sid=".$userdata['session_id'] ) . '">' . $medal['username'] . '</a>' : '<a href="' . append_sid("medals.$phpEx?action=edit&amp;" . POST_MEDAL_URL . "=$medal_id&amp;" . POST_USERS_URL . "=" . $user_id."&amp;sid=".$userdata['session_id'] ) . '">' . $medal['username'] . '</a>';
	}

	$sql = "SELECT u.username, u.user_id, mu.issue_id, mu.issue_time
		FROM " . USERS_TABLE . " u, " . MEDAL_USER_TABLE . " mu
		WHERE mu.medal_id = $medal_id
		AND u.user_id = mu.user_id
		ORDER BY u.username"; 
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting user list for medal', '', __LINE__, __FILE__, $sql);
	}

	$medal_members = array();
	$medal_members = $db->sql_fetchrowset($result); 
	$members_count = count($medal_members);
	$db->sql_freeresult($result);

	$select_userlist = '';
	for($i = 0; $i < $members_count; $i++)
	{
		$issue_time = create_date($board_config['default_dateformat'], $medal_members[$i]['issue_time'], $board_config['board_timezone']);
		$select_userlist .= '<input type="checkbox" name="unmedal_user[]" value="' . $medal_members[$i]['issue_id'] . '" /> ' . $medal_members[$i]['username'] . ' [' . $issue_time . ']<br/>';
	}

	if( $select_userlist == '' )
	{
		$select_userlist = $lang['No_medal_members'] . '<input type="hidden" name="unmedal_user[]" value="-1" />';
	}

	$s_hidden_fields .= '<input type="hidden" name="mode" value="submit" />';

	$page_title = $lang['Medal_Control_Panel'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'info' => 'medalcp_body.tpl')
	);

	$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
	$s_hidden_fields .= '<input type="hidden" name="' . POST_MEDAL_URL . '" value="' . $medal_id . '" />';

	$template->assign_vars(array(
		'L_MEDAL_INFORMATION' => $lang['Medal_Information'],
		'L_MEDAL_NAME' => $lang['Medal_name'],
		'L_MEDAL_DESC' => $lang['Medal_description'],
		'L_MEDAL_IMAGE' => $lang['Medal_image'],
		'L_MEDAL_MODERATOR' => $lang['Medal_moderator'], 
		'L_MEDAL_MEMBERS' => $lang['Medal_Members'], 
		'L_MEDAL_MEMBERS_EXPLAIN' => $lang['Medal_Members_explain'], 
		'L_MEDAL_USER' => $lang['Medal_user_username'],
		'L_MEDAL_REASON' => $lang['Medal_reason'],
		'L_MEDAL_REASON_EXPLAIN' => $lang['Medal_reason_explain'],
		'L_UNMEDAL_USER' => $lang['Medal_unmedal_username'],
		'L_UNMEDAL_USER_EXPLAIN' => $lang['Medal_unmedal_username_explain'],
		'L_USERNAME' => $lang['Username'], 
		'L_LOOK_UP' => $lang['Look_up_User'],
		'L_FIND_USERNAME' => $lang['Find_username'],
		'L_SUBMIT' => $lang['Submit'],
		'L_RESET' => $lang['Reset'],

		'MEDAL_NAME' => $medal_info['medal_name'],
		'MEDAL_DESC' => $medal_info['medal_description'],
		'MEDAL_IMAGE' => $medal_info['medal_image'],
		'MEDAL_IMAGE_DISPLAY' => ( !empty($medal_info['medal_image']) ) ? '<img src="' . $medal_info['medal_image'] . '" alt="" />' : "",
		'MEDAL_MODERATOR' => ( $medal_moderator ) ? $medal_moderator : $lang['No_medal_mod'],
		'MEDAL_MEMBER' => ( $medal_member ) ? $medal_member : $lang['No_medal_members'],

		'U_SEARCH_USER' => append_sid("search.$phpEx?mode=searchuser"),
		'S_UNMEDAL_USERLIST_SELECT' => $select_userlist,
		'S_MEDALCP_ACTION' => append_sid("medals.$phpEx?action=cp"),
		'S_HIDDEN_FIELDS' => $s_hidden_fields)
	);

}
else
{
	message_die(GENERAL_MESSAGE, $lang['No_medals_exist']);
}

$template->pparse('info');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} elseif ( $action == 'edit' ) {

if ( isset($HTTP_GET_VARS[POST_USERS_URL]) || isset($HTTP_POST_VARS[POST_USERS_URL]) )
{
	$user_id = ( isset($HTTP_POST_VARS[POST_USERS_URL]) ) ? intval($HTTP_POST_VARS[POST_USERS_URL]) : intval($HTTP_GET_VARS[POST_USERS_URL]);
}
else
{
	$user_id = '';
}
$profiledata = get_userdata($user_id);

if ( isset($HTTP_GET_VARS[POST_MEDAL_URL]) || isset($HTTP_POST_VARS[POST_MEDAL_URL]) )
{
	$medal_id = ( isset($HTTP_POST_VARS[POST_MEDAL_URL]) ) ? intval($HTTP_POST_VARS[POST_MEDAL_URL]) : intval($HTTP_GET_VARS[POST_MEDAL_URL]);
}
else
{
	$medal_id = '';
}

if (!empty($HTTP_POST_VARS['sid']) || !empty($HTTP_GET_VARS['sid']))
{
	$sid = (!empty($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : $HTTP_GET_VARS['sid'];
}
else
{
	$sid = '';
}

if ($sid == '' || $sid != $userdata['session_id'])
{
	$message = $lang['Not_Authorised'] . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

	message_die(GENERAL_ERROR, $message);
}

$is_moderator = FALSE;

$is_moderator = ($userdata['user_level'] != ADMIN) ? check_medal_mod($medal_id) : TRUE;

if ( !$is_moderator )
{
	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("index.$phpEx") . '">')
	);
		
	$message = $lang['Not_medal_moderator'] . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

	message_die(GENERAL_MESSAGE, $message);
}

if ( empty($user_id) )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified'] );
}

if ( empty($medal_id) )
{
	message_die(GENERAL_MESSAGE, $lang['No_medal_id_specified']);
}

$sql = "SELECT *
	FROM " . MEDAL_USER_TABLE . "
	WHERE medal_id = $medal_id
	AND user_id = '" . $profiledata['user_id'] . "'
	ORDER BY issue_id";

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Error getting user medal information', '', __LINE__, __FILE__, $sql);
}
else
{
	$row = array();
	$issue = array();
	$default_config = array();
	$row = $db->sql_fetchrowset($result);
	$rows = count($row);

	for($i = 0; $i < $rows; $i++)
	{
		$issue[$i]['issue_id'] = $row[$i]['issue_id'];
		$issue[$i]['issue_time'] = $row[$i]['issue_time'];
		$issue_reason = $row[$i]['issue_reason'];
		$default_config[$i] = str_replace("'", "\'", $issue_reason);

		$issue[$i]['issue_reason'] = ( isset($HTTP_POST_VARS['issue_reason'.$row[$i]['issue_id']]) ) ? trim($HTTP_POST_VARS['issue_reason'.$row[$i]['issue_id']]) : $default_config[$i];

		if( isset($HTTP_POST_VARS['submit']) )
		{
			$sql = "UPDATE " . MEDAL_USER_TABLE . " 
				SET issue_reason = '" . str_replace("\'", "''", $issue[$i]['issue_reason']) . "'
				WHERE issue_id =" . $row[$i]['issue_id'];

			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update medal info', '', __LINE__, __FILE__, $sql);
			}
		}

	}
		
	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = $lang['Medal_update_sucessful'] . "<br /><br />" . sprintf($lang['Click_return_medal'], "<a href=\"" . append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=$medal_id&amp;sid=".$userdata['session_id']) . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_index'], "<a href=\"" . append_sid("index.$phpEx") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}

$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
$s_hidden_fields .= '<input type="hidden" name="' . POST_USERS_URL . '" value="' . $profiledata['user_id'] . '" />';
$s_hidden_fields .= '<input type="hidden" name="' . POST_MEDAL_URL . '" value="' . $medal_id . '" />';

$page_title = $lang['Medal_Control_Panel'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'medalcp_edit_body.tpl')
);

$sql = "SELECT *
	FROM " . MEDAL_TABLE . " 
	WHERE medal_id =" . $medal_id;
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain medal information', '', __LINE__, __FILE__, $sql);
}
$medal_info = $db->sql_fetchrow($result);

$template->assign_vars(array(
	'USERNAME'				=> $profiledata['username'],
	'L_SUBMIT' 				=> $lang['Submit'],
	'L_RESET'				=> $lang['Reset'],

	'MEDAL_NAME' 			=> $medal_info['medal_name'],
	'MEDAL_DESCRIPTION' 	=> $medal_info['medal_description'],
	
	'U_MEDALS_CP'			=> append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=" . $medal_id . "&amp;sid=" . $userdata['session_id']),
	
	'S_MEDAL_ACTION' 		=> append_sid("medals.$phpEx?action=edit"),
	'S_HIDDEN_FIELDS' 		=> $s_hidden_fields)
);

for($i = 0; $i < $rows; $i++)
{
	$template->assign_block_vars("medaledit", array(
		'L_MEDAL_TIME' 				=> $lang['Medal_time'],
		'L_MEDAL_REASON' 			=> $lang['Medal_reason'],
		'L_MEDAL_REASON_EXPLAIN' 	=> $lang['Medal_reason_explain'],
		'L_ISSUE_REASON' 			=> 'issue_reason'. $issue[$i]['issue_id'],
		
		"ISSUE_REASON" 				=> $issue[$i]['issue_reason'],
		"ISSUE_TIME" 				=> create_date($board_config['default_dateformat'], $issue[$i]['issue_time'], $board_config['board_timezone'])
		)
	);
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} else {

if ( isset($HTTP_GET_VARS[POST_MEDAL_URL]) || isset($HTTP_POST_VARS[POST_MEDAL_URL]) )
{
	$medal_id = ( isset($HTTP_POST_VARS[POST_MEDAL_URL]) ) ? intval($HTTP_POST_VARS[POST_MEDAL_URL]) : intval($HTTP_GET_VARS[POST_MEDAL_URL]);
}
else
{
	$medal_id = '';
}

if ( isset($HTTP_POST_VARS['mode']) || isset($HTTP_GET_VARS['mode']) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
	$mode = htmlspecialchars($mode); 
}
else
{
	$mode = '';
}

$sql = "SELECT cat_id, cat_title, cat_order
		FROM " . MEDAL_CAT_TABLE . "
		ORDER BY cat_order";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query medal categories list', '', __LINE__, __FILE__, $sql);
}

$category_rows = array();
while ($row = $db->sql_fetchrow($result) ) 
{
	$category_rows[] = $row;
}
$db->sql_freeresult($result);

if( ( $total_categories = count($category_rows) ) )
{
	$sql = "SELECT * FROM " . MEDAL_TABLE . "
			ORDER BY medal_name";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain medal information', '', __LINE__, __FILE__, $sql);
	}

	$medal_data = array();
	while ($row = $db->sql_fetchrow($result) ) 
	{
		$medal_data[] = $row;
	}
	$db->sql_freeresult($result);

	if ( !($total_medals = count($medal_data)) )
	{
		message_die(GENERAL_MESSAGE, $lang['No_medal']);
	}

	$sql = "SELECT u.user_id, u.username, mm.medal_id
			FROM " . USERS_TABLE . " u, " . MEDAL_MOD_TABLE . " mm
			WHERE u.user_id = mm.user_id
			GROUP BY u.user_id, u.username, mm.medal_id
			ORDER BY mm.medal_id, u.user_id";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query medal moderator information', '', __LINE__, __FILE__, $sql);
	}

	$medal_moderators = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$medal_moderators[$row['medal_id']][] = '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id']) . '">' . $row['username'] . '</a>';
	}
	$db->sql_freeresult($result);

	$sql = "SELECT u.user_id, u.username, mu.medal_id
			FROM " . USERS_TABLE . " u, " . MEDAL_USER_TABLE . " mu
			WHERE u.user_id = mu.user_id
			GROUP BY u.user_id, u.username, mu.medal_id
			ORDER BY u.user_id";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query medal userlist information', '', __LINE__, __FILE__, $sql);
	}

	$medal_users = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$medal_users[$row['medal_id']][] = '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id']) . '">' . $row['username'] . '</a>';
	}
	$db->sql_freeresult($result);

	$page_title = 'Награды';
	include ($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'medals_body.tpl')
	);

	$template->assign_vars(array(
		'L_USERS_LIST' 			=> $lang['Medal_userlist'],
		'L_MEDAL_INFORMATION' 	=> $lang['Medal_Information'],
		'L_MEDAL_NAME' 			=> $lang['Medal_name'],
		'L_MEDAL_DESCRIPTION' 	=> $lang['Medal_description'],
		'L_MEDAL_MODERATOR' 	=> $lang['Medal_moderator'],
		'L_MEDAL_IMAGE' 		=> $lang['Medal_image'],
		'L_LINK_TO_CP' 			=> $lang['Link_to_cp']
		)
	);

	for($i = 0; $i < $total_categories; $i++)
	{
		$cat_id = $category_rows[$i]['cat_id'];
		$display_medal = FALSE;
		for($k = 0; $k < $total_medals; $k++)
		{
			if ( $medal_data[$k]['cat_id'] == $cat_id )
			{
				$display_medal = TRUE;
			}
		}

		if ( $display_medal )
		{
			$template->assign_block_vars('catrow', array(
				'CAT_ID' 	=> $cat_id,
				'CAT_DESC' 	=> $category_rows[$i]['cat_title'])
			);
	
			for($j = 0; $j < $total_medals; $j++)
			{
				if ( $medal_data[$j]['cat_id'] == $cat_id )
				{
					$medal_id = $medal_data[$j]['medal_id'];

					if ( count($medal_moderators[$medal_id]) > 0 )
					{
						$moderator_list = implode(', ', $medal_moderators[$medal_id]);
					}
					else
					{
						$moderator_list = $lang['No_medal_mod'];
					}
					if ( count($medal_users[$medal_id]) > 0 )
					{
						$user_list = implode(', ', $medal_users[$medal_id]);
					}
					else
					{
						$user_list = $lang['No_medal_members'];
					}

					$template->assign_block_vars('catrow.medals', array(
						'MEDAL_ID' 				=> $medal_data[$j]['medal_name'],
						'MEDAL_NAME' 			=> $medal_data[$j]['medal_name'],
						'MEDAL_DESCRIPTION'  	=> ( !empty($medal_data[$j]['medal_description']) ) ? $medal_data[$j]['medal_description'] . '<br/>' : '',
						'MEDAL_IMAGE' 			=> ($medal_data[$j]['medal_image'] == '') ? '' : '<img src="'. $medal_data[$j]['medal_image'] . '" alt="">',
						'MEDAL_MOD' 			=> $moderator_list, 
						'USERS_LIST' 			=> $user_list,
						'U_MEDAL_CP' 			=> append_sid("medals.$phpEx?action=cp&amp;" . POST_MEDAL_URL . "=".$medal_data[$j]['medal_id']."&amp;sid=".$userdata['session_id']))
					);

					$is_moderator = check_medal_mod($medal_id);

					if ( $is_moderator || $userdata['user_level'] == ADMIN )
					{
						$template->assign_block_vars('catrow.medals.switch_mod_option', array());
					}
				}
			}
		}
	}

}
else
{
	message_die(GENERAL_MESSAGE, $lang['No_medal']);
}

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

}

?>
