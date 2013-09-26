<?php
/***************************************************************************
 *                            functions_report.php
 *                            --------------------
 *      Разработка: wGEric, chatasos.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *               2010 год
 ***************************************************************************/

function insert_report($post_id, $comments)
{
	global $db, $userdata;

	$sql = "INSERT INTO " . POST_REPORTS_TABLE . " (post_id, reporter_id, report_time, report_status, report_comments)
		VALUES ($post_id, " . $userdata['user_id'] . ", " . time() . ", " . REPORT_POST_NEW . ", '" . str_replace("\'", "''", $comments) . "')";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not insert report', '', __LINE__, __FILE__, $sql);
	}

	return;
}

function email_report($forum_id, $post_id, $topic_title, $comments)
{
	global $db, $phpbb_root_path, $phpEx, $userdata, $board_config, $lang;

	$sql = "SELECT u.user_email 
		FROM " . AUTH_ACCESS_TABLE . " aa, " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g, " . USERS_TABLE . " u
		WHERE aa.forum_id = $forum_id 
			AND aa.auth_mod = " . TRUE . " 
			AND g.group_single_user = 1
			AND ug.group_id = aa.group_id 
			AND g.group_id = aa.group_id 
			AND u.user_id = ug.user_id
			AND u.user_report_optout = 0
		GROUP BY u.user_id, u.username
		ORDER BY u.user_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query forum moderator information', '', __LINE__, __FILE__, $sql);
	}

	$moderators = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$moderators[] = $row['user_email'];
	}

	$sql = "SELECT g.group_id 
		FROM " . AUTH_ACCESS_TABLE . " aa, " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g 
		WHERE aa.forum_id = $forum_id
			AND aa.auth_mod = " . TRUE . " 
			AND g.group_single_user = 0
			AND g.group_type <> ". GROUP_HIDDEN ."
			AND ug.group_id = aa.group_id 
			AND g.group_id = aa.group_id 
		GROUP BY g.group_id, g.group_name
		ORDER BY g.group_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query forum group moderator information', '', __LINE__, __FILE__, $sql);
	}

	$groups = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$groups[] = $row['group_id'];
	}

	if ( sizeof($groups) )
	{
		$sql = "SELECT u.user_email
			FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g, " . USERS_TABLE . " u
			WHERE ug.group_id = g.group_id
				AND g.group_single_user = 0
				AND g.group_id IN (" . implode(',', $groups) . ")
				AND ug.user_id = u.user_id
				AND u.user_report_optout = 0";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query forum moderator information', '', __LINE__, __FILE__, $sql);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			if ( !in_array($row['user_email'], $moderators) )
			{
				$moderators[] = $row['user_email'];
			}
		}
	}

	$sql = "SELECT user_email FROM " . USERS_TABLE . "
		WHERE user_level = " . ADMIN . "
		AND user_report_optout = 0";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query forum admin information', '', __LINE__, __FILE__, $sql);
	}

	while( $row = $db->sql_fetchrow($result) )
	{
		if ( !in_array($row['user_email'], $moderators) )
		{
				$moderators[] = $row['user_email'];
		}
	}

	include($phpbb_root_path . 'includes/emailer.'.$phpEx);

	$emailer = new emailer($board_config['smtp_delivery']);

	$emailer->from($board_config['board_email']);
	$emailer->replyto($board_config['board_email']);

	foreach($moderators as $email)
	{
		$emailer->bcc($email);
	}

	$emailer->use_template('report_post');
	$emailer->email_address($board_config['board_email']);
	$emailer->set_subject('Жалоба - ' . $topic_title);

	$email_headers = 'X-AntiAbuse: Board servername - ' . $board_config['server_name'] . "\n";
	$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
	$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
	$email_headers .= 'X-AntiAbuse: User IP - ' . decode_ip($user_ip) . "\n";
	$emailer->extra_headers($email_headers);

	$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
	$script_name = ( $script_name != '' ) ? $script_name . '/viewtopic.'.$phpEx : 'viewtopic.'.$phpEx;
	$server_name = trim($board_config['server_name']);
	$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
	$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

	$server_url = $server_protocol . $server_name . $server_port . $script_name;

	$emailer->assign_vars(array(
		'SITENAME'		=> $board_config['sitename'],
		'USERNAME'		=> $userdata['username'],
		'POST_ID'		=> $post_id,
		'TOPIC_TITLE'	=> $topic_title,
		'COMMENTS'		=> $comments,
		'EMAIL_SIG'		=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

		'U_VIEW_POST'	=> urldecode($server_url . '?' . POST_POST_URL . '=' . $post_id . '#' . $post_id))
	);
	$emailer->send();
	$emailer->reset();

	return;
}

function show_reports($status = REPORT_POST_NEW)
{
	global $db, $board_config, $template, $lang, $phpbb_root_path, $phpEx, $userdata, $start;

	$forum_ids = array();
	$forum_ids = get_forums_auth_mod();
	
	if ( empty($forum_ids) )
	{
		return;
	}
	else
	{
		$where_sql2 = ' AND p.forum_id IN (' . implode(',', $forum_ids) . ')';
	}
	
	$where_sql = ( $status == 'all') ? '' : ' AND pr.report_status = ' . intval($status);

	$sql = "SELECT COUNT(*) AS total
		FROM " . POST_REPORTS_TABLE . " pr, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f
		WHERE u.user_id = pr.reporter_id
			AND pr.post_id = p.post_id
			AND p.topic_id = t.topic_id
			AND t.forum_id = f.forum_id
			$where_sql 
			$where_sql2
		ORDER BY report_time DESC";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query reports', '', __LINE__, __FILE__, $sql);
	}
	$total_reports = ( $row = $db->sql_fetchrow($result) ) ? intval($row['total']) : 0;

	$sql = "SELECT pr.*, u.username, t.topic_title, f.forum_id, f.forum_name 
		FROM " . POST_REPORTS_TABLE . " pr, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f
		WHERE u.user_id = pr.reporter_id
			AND pr.post_id = p.post_id
			AND p.topic_id = t.topic_id
			AND t.forum_id = f.forum_id
			$where_sql 
			$where_sql2
		ORDER BY report_time DESC
		LIMIT $start, ".$board_config['posts_per_page'];

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query reports', '', __LINE__, __FILE__, $sql);
	}

	$i = 0;

	while( $row = $db->sql_fetchrow($result) )
	{
		$comments_temp = array();
		$comments_temp = create_comments($row);
		
		$last_action 			= $comments_temp['last_action'];
		$comments 				= $comments_temp['comments'];
		$last_action_comments	= $comments_temp['last_action_comments'];
		
		$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';

		$template->assign_block_vars('postrow', array(
			'ROW_CLASS'			=> $row_class,

			'REPORT_ID'			=> $row['report_id'],
			'TOPIC_TITLE'		=> $row['topic_title'],
			'REPORTER'			=> '<a href="' . append_sid($phpbb_root_path . 'profile.'.$phpEx.'?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['reporter_id']) . '">' . $row['username'] . '</a>',
			'COMMENTS'			=> $comments,
			'DATE'				=> create_date($board_config['default_dateformat'], $row['report_time'], $board_config['board_timezone']),
			'FORUM'				=> $row['forum_name'],

			'LAST_ACTION'				=> $last_action,
			'LAST_ACTION_COMMENTS'  => $last_action_comments,

			'L_CLOSE_REPORT'	=> ( $row['report_status'] == REPORT_POST_NEW ) ? $lang['Close'] : $lang['Open'],

			'U_VIEW_POST'		=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?' . POST_POST_URL . '=' . $row['post_id'] . '#' . $row['post_id']),
			'U_CLOSE_REPORT'	=> ( $row['report_status'] == REPORT_POST_NEW ) ? append_sid($phpbb_root_path . 'modcp.'.$phpEx.'?mode=reportpost&amp;action=closereport&amp;report=' . $row['report_id']) : append_sid($phpbb_root_path . 'modcp.'.$phpEx.'?mode=reportpost&amp;action=openreport&amp;report=' . $row['report_id']))
		);
		
		$i++;
	}

	if ( $i == 0 )
	{
		$template->assign_block_vars('no_reports', array());
	}

	$delete_ids = array();
	$delete_ids = get_reports_with_no_posts();

	if ( !empty($delete_ids) )
	{
		$sql = "DELETE FROM " . POST_REPORTS_TABLE . "
			WHERE report_id IN (" . implode(',', $delete_ids) . ")";

		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not delete reports', '', __LINE__, __FILE__, $sql);
		}
		$deleted_reports =  '<br/>' . sprintf($lang['Non_existent_posts'], count($delete_ids));
	}
	else
	{
		$deleted_reports = '';
	}
	
	$template->assign_vars(array(
		'DELETED_REPORTS'	=> $deleted_reports,
		'PAGINATION' => ( $total_reports > $board_config['posts_per_page'] ) ? generate_pagination("modcp.$phpEx?mode=reportpost&amp;status=$status", $total_reports, $board_config['posts_per_page'], $start) : '')
	);

	return;
}

function report_flood()
{
	global $db, $board_config, $userdata;

	$sql = "SELECT MAX(report_time) AS latest_time FROM " . POST_REPORTS_TABLE . "
		WHERE reporter_id = " . $userdata['user_id'];
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get most recent report', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);

	$current_time = time();
	if ( ($current_time - $row['latest_time']) < $board_config['flood_interval'] )
	{
		return false;
	}
	else
	{
		return true;
	}
}

function reports_count($status = REPORT_POST_NEW)
{
	global $db;

	$forum_ids = array();
	$forum_ids = get_forums_auth_mod();

	if ( empty($forum_ids) )
	{
		return 0;
	}
	else
	{
		$where_sql = ' AND p.forum_id IN (' . implode(',', $forum_ids) . ')';
	}

	$sql = "SELECT COUNT(pr.report_id) as total
		FROM " . POST_REPORTS_TABLE . " pr, " . POSTS_TABLE . " p
		WHERE pr.report_status = " . intval($status) . "
			AND pr.post_id = p.post_id
			" . $where_sql;
			
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get reports count', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);

	return ( $row['total'] ) ? $row['total'] : 0;
}

function report_exists($post_id)
{
	global $db;

	$sql = "SELECT report_id FROM " . POST_REPORTS_TABLE . "
		WHERE post_id = $post_id
		AND report_status = " . REPORT_POST_NEW;
		
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get report', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);

	return ( $row ) ? TRUE : FALSE;
}

function get_report_comments($report_id)
{
	global $db;

	$sql = "SELECT last_action_comments FROM " . POST_REPORTS_TABLE . "
		WHERE report_id = " . $report_id;
		
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get report comments', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);

	return ( $row['last_action_comments'] && $row['last_action_comments'] != '' ) ? $row['last_action_comments'] : '';
}

function get_forums_auth_mod()
{
	global $userdata;

	$auth = auth(AUTH_MOD, AUTH_LIST_ALL, $userdata);
	$forums_auth = array();
	
	while ( list($forum) = each($auth) )
	{
		if ( $auth[$forum]['auth_mod'] )
		{
			$forums_auth[] = $forum;
		}
	}

	return $forums_auth;
}

function create_comments($row)
{
	global $db, $board_config, $lang, $phpbb_root_path, $phpEx;

	if ( $row['last_action_user_id'] != 0 && $row['last_action_time'] != 0 )
	{
		$sql2 = "SELECT username FROM " . USERS_TABLE . "
			WHERE user_id = " . $row['last_action_user_id'];

		if ( !($result2 = $db->sql_query($sql2)) )
		{
			message_die(GENERAL_ERROR, 'Could not get last action user id information', '', __LINE__, __FILE__, $sql2);
		}
		
		$row2 = $db->sql_fetchrow($result2);

		$last_action_user = '<a href="' . append_sid($phpbb_root_path . 'profile.'.$phpEx.'?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['last_action_user_id']) . '">' . $row2['username'] . '</a>';
		$last_action_date = create_date($board_config['default_dateformat'], $row['last_action_time'], $board_config['board_timezone']);

		if ( $row['report_status'] == REPORT_POST_NEW )
		{
			$last_action = '<b>' . sprintf($lang['Opened_by_user_on_date'], $last_action_user, $last_action_date) . '</b>';
		}
		else
		{
			$last_action = '<b>' . sprintf($lang['Closed_by_user_on_date'], $last_action_user, $last_action_date) . '</b>';
		}

		$last_action_comments = $row['last_action_comments'];
	}
	else
	{
		$last_action = ( $row['report_status'] == REPORT_POST_NEW ) ? '<b>' . $lang['Opened'] . '</b>' : '<b>' . $lang['Closed'] . '</b>';
		$last_action_comments = '';
	}

	$comments = str_replace("\n", "\n<br />\n", $row['report_comments']);
	$last_action_comments = str_replace("\n", "\n<br />\n", $last_action_comments);
	$comments_temp = array('last_action' => $last_action, 'comments' => $comments, 'last_action_comments' => $last_action_comments);

	return $comments_temp;
}

function get_reports_with_no_posts()
{
	global $db;

	$sql = "SELECT pr.post_id FROM " . POST_REPORTS_TABLE . ' pr, ' . POSTS_TABLE . " p
		WHERE pr.post_id = p.post_id";
	
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query reports', '', __LINE__, __FILE__, $sql);
	}

	$common_post_ids = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$common_post_ids[] = $row['post_id'];
	}

	$sql = "SELECT report_id, post_id
		FROM " . POST_REPORTS_TABLE ;

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query reports', '', __LINE__, __FILE__, $sql);
	}

	$delete_ids = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		if ( !in_array($row['post_id'], $common_post_ids) )
		{
			$delete_ids[] = $row['report_id'];
		}
	}

	return $delete_ids;
}

?>
