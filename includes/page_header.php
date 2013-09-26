<?php
/*************************************************
 *		page_header.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

define('HEADER_INC', TRUE);

// Gzip 压缩
$do_gzip_compress = FALSE;
if ( $board_config['gzip_compress'] )
{
	$phpver = phpversion();

	$useragent = (isset($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) ? $HTTP_SERVER_VARS['HTTP_USER_AGENT'] : getenv('HTTP_USER_AGENT');

	if ( $phpver >= '4.0.4pl1' && ( strstr($useragent,'compatible') || strstr($useragent,'Gecko') ) )
	{
		if ( extension_loaded('zlib') )
		{
			ob_start('ob_gzhandler');
		}
	}
	else if ( $phpver > '4.0' )
	{
		if ( strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') )
		{
			if ( extension_loaded('zlib') )
			{
				$do_gzip_compress = TRUE;
				ob_start();
				ob_implicit_flush(0);

				header('Content-Encoding: gzip');
			}
		}
	}
}

// 全部 mian 页面的头部
$template->set_filenames(array(
	'overall_header' => 'overall_header.tpl')
);

// 当前时间
$current_time = time();

if ($board_config['reputation_last_check_time'] + $board_config['reputation_check_rate'] * 60 < $current_time)
{
	if ($board_config['reputation_delete_expired'] != -1)
	{
		$delete_time = $current_time - $board_config['reputation_delete_expired'] * 86400;
		$result = db_query('SELECT id
			FROM {REPUTATION_TABLE}
			WHERE (modification = {REPUTATION_WARNING_EXPIRED} OR modification = {REPUTATION_BAN_EXPIRED})
				AND expire < %d', $delete_time);

		$in_sql = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$in_sql .= ($in_sql ? ',' : '') . $row['id'];
		}
		if ($in_sql)
		{
			db_query('DELETE FROM {REPUTATION_TABLE}
				WHERE id IN (' . $in_sql . ')');
			db_query('DELETE FROM {REPUTATION_TEXT_TABLE}
				WHERE id IN (' . $in_sql . ')');
		}
	}
	else
	{
		$delete_time = -1;
	}

	$result = db_query('SELECT id, user_id, modification FROM {REPUTATION_TABLE}
			WHERE (modification = {REPUTATION_WARNING} OR modification = {REPUTATION_BAN})
				AND expire < %d', $current_time);

	while ($row = $db->sql_fetchrow($result))
	{
		if ($delete_time != -1 && $row['expire'] < $delete_time)
		{
			db_query('DELETE FROM {REPUTATION_TABLE} WHERE id = %d', $row['id']);
			db_query('DELETE FROM {REPUTATION_TEXT_TABLE} WHERE id = %d', $row['id']);
		}
		else
		{
			$set = ($row['modification'] == REPUTATION_WARNING) ? REPUTATION_WARNING_EXPIRED : REPUTATION_BAN_EXPIRED;
			db_query('UPDATE {REPUTATION_TABLE} SET modification = %d WHERE id = %d', $set, $row['id']);
		}
		if ($row['modification'] == REPUTATION_BAN)
		{
			db_query('DELETE FROM {BANLIST_TABLE} WHERE ban_userid = %d', $row['user_id']);
			db_query('UPDATE {USERS_TABLE} SET user_allow_pm = 1 WHERE user_id = %d', $row['user_id']);
		}
		db_query('UPDATE {USERS_TABLE} SET user_warnings = IF(user_warnings > 0, user_warnings - 1, 0) WHERE user_id = %d', $row['user_id']);
	}

	db_query('UPDATE {CONFIG_TABLE} SET config_value = %d WHERE config_name = \'reputation_last_check_time\'', $current_time);
}

// 生成登录、退出状态
if ( $userdata['session_logged_in'] )
{
	$u_login_logout = 'login.'.$phpEx.'?logout=true&amp;sid=' . $userdata['session_id'];
	$l_login_logout = $lang['Logout'] . ' [ ' . $userdata['username'] . ' ]';

	$sql_count = "SELECT COUNT(user_id) AS total
			FROM " . BOOKMARK_TABLE . "
			WHERE user_id = " . $userdata['user_id'];
	$result_count = $db->sql_query($sql_count);
	$row_count = $db->sql_fetchrow($result_count);
	$total_bookmarks = $row_count['total'];
}
else
{
	$u_login_logout = 'login.'.$phpEx;
	$l_login_logout = $lang['Login'];
}

$s_last_visit = ( $userdata['session_logged_in'] ) ? create_date($board_config['default_dateformat'], $userdata['user_lastvisit'], $board_config['board_timezone']) : '';

// 在线状态
$logged_visible_online = 0;
$logged_hidden_online = 0;
$guests_online = 0;
$online_userlist = '';
$l_online_users = '';

if (defined('SHOW_ONLINE'))
{

	$user_forum_sql = ( !empty($forum_id) ) ? "AND s.session_page = " . intval($forum_id) : '';
	$sql = "SELECT u.username, u.user_id, u.user_allow_viewonline, u.user_level, s.session_logged_in, s.session_ip
		FROM ".USERS_TABLE." u, ".SESSIONS_TABLE." s
		WHERE u.user_id = s.session_user_id
			AND s.session_time >= ".( time() - 300 ) . "
			$user_forum_sql
		ORDER BY u.username ASC, s.session_ip ASC";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain user/online information', '', __LINE__, __FILE__, $sql);
	}

	$userlist_ary = array();
	$userlist_visible = array();

	$prev_user_id = 0;
	$prev_user_ip = $prev_session_ip = '';

	while( $row = $db->sql_fetchrow($result) )
	{
		if ( $row['session_logged_in'] )
		{
			if ( $row['user_id'] != $prev_user_id )
			{

				if ( $row['user_allow_viewonline'] )
				{
					$user_online_link = '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id']) . '"' . $style_color .'>' . $row['username'] . '</a>';
					$logged_visible_online++;
				}
				else
				{
					$user_online_link = '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id']) . '"' . $style_color .'><i>' . $row['username'] . '</i></a>';
					$logged_hidden_online++;
				}

				if ( $row['user_allow_viewonline'] || $userdata['user_level'] == ADMIN )
				{
					$online_userlist .= ( $online_userlist != '' ) ? ', ' . $user_online_link : $user_online_link;
				}
			}

			$prev_user_id = $row['user_id'];
		}
		else
		{
			if ( $row['session_ip'] != $prev_session_ip )
			{
				$guests_online++;
			}
		}

		$prev_session_ip = $row['session_ip'];
	}
	$db->sql_freeresult($result);

	if ( empty($online_userlist) )
	{
		$online_userlist = $lang['None'];
	}
	$online_userlist = ( ( isset($forum_id) ) ? $lang['Browsing_forum'] : $lang['Registered_users'] ) . ' ' . $online_userlist;

	$total_online_users = $logged_visible_online + $logged_hidden_online + $guests_online;

	if ( $total_online_users > $board_config['record_online_users'])
	{
		$board_config['record_online_users'] = $total_online_users;
		$board_config['record_online_date'] = time();

		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = '$total_online_users'
			WHERE config_name = 'record_online_users'";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update online user record (nr of users)', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = '" . $board_config['record_online_date'] . "'
			WHERE config_name = 'record_online_date'";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update online user record (date)', '', __LINE__, __FILE__, $sql);
		}
	}

	if ( $total_online_users == 0 )
	{
		$l_t_user_s = $lang['Online_users_zero_total1'];
	}
	else if ( $total_online_users == 1 )
	{
		$l_t_user_s = $lang['Online_user_total'];
	}
	else
	{
		$l_t_user_s = $lang['Online_users_total1'];
	}

	if ( $logged_visible_online == 0 )
	{
		$l_r_user_s = $lang['Reg_users_zero_total1'];
	}
	else if ( $logged_visible_online == 1 )
	{
		$l_r_user_s = $lang['Reg_user_total1'];
	}
	else
	{
		$l_r_user_s = $lang['Reg_users_total1'];
	}

	if ( $logged_hidden_online == 0 )
	{
		$l_h_user_s = $lang['Hidden_users_zero_total1'];
	}
	else if ( $logged_hidden_online == 1 )
	{
		$l_h_user_s = $lang['Hidden_user_total1'];
	}
	else
	{
		$l_h_user_s = $lang['Hidden_users_total1'];
	}

	if ( $guests_online == 0 )
	{
		$l_g_user_s = $lang['Guest_users_zero_total1'];
	}
	else if ( $guests_online == 1 )
	{
		$l_g_user_s = $lang['Guest_user_total1'];
	}
	else
	{
		$l_g_user_s = $lang['Guest_users_total1'];
	}

	$l_online_users = sprintf($l_t_user_s, $total_online_users);
	
	/****************************************************************
	* $l_online_users .= sprintf($l_r_user_s, $logged_visible_online);
	* $l_online_users .= sprintf($l_h_user_s, $logged_hidden_online);
	* $l_online_users .= sprintf($l_g_user_s, $guests_online);
	*****************************************************************/
}


// 如果用户在线，更新信箱的信息
if ( ($userdata['session_logged_in']) && (empty($gen_simple_header)) )
{
	if ( $userdata['user_new_privmsg'] )
	{
		$l_message_new = ( $userdata['user_new_privmsg'] == 1 ) ? $lang['New_pm'] : $lang['New_pms'];
		$l_message_neww = ( $userdata['user_new_privmsg'] == 1 ) ? $lang['You_new_pm'] : $lang['You_new_pms'];
		$l_privmsgs_text = sprintf($l_message_new, $userdata['user_new_privmsg']);
		$l_privmsgs_text1 = '<a href="'.append_sid('privmsg.'.$phpEx.'?folder=inbox').'">'.sprintf($l_message_neww, $userdata['user_new_privmsg']).'</a>';

		if ( $userdata['user_last_privmsg'] > $userdata['user_lastvisit'] )
		{
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_last_privmsg = " . $userdata['user_lastvisit'] . "
				WHERE user_id = " . $userdata['user_id'];
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update private message new/read time for user', '', __LINE__, __FILE__, $sql);
			}

			$s_privmsg_new = 1;
			$icon_pm = $images['pm_new_msg'];
		}
		else
		{
			$s_privmsg_new = 0;
			$icon_pm = $images['pm_new_msg'];
		}
	}
	else
	{
		$l_privmsgs_text = $lang['No_new_pm'];

		$s_privmsg_new = 0;
		$icon_pm = $images['pm_no_new_msg'];
	}

	if ( $userdata['user_unread_privmsg'] )
	{
		$l_message_unread = ( $userdata['user_unread_privmsg'] == 1 ) ? $lang['Unread_pm'] : $lang['Unread_pms'];
		$l_privmsgs_text_unread = sprintf($l_message_unread, $userdata['user_unread_privmsg']);
	}
	else
	{
		$l_privmsgs_text_unread = $lang['No_unread_pm'];
	}
}
else
{
	$icon_pm = $images['pm_no_new_msg'];
	$l_privmsgs_text = $lang['Login_check_pm'];
	$l_privmsgs_text1 = '';
	$l_privmsgs_text_unread = '';
	$s_privmsg_new = 0;
}

// 当权限是管理员时获取投诉信息
if ( $userdata['user_level'] >= ADMIN )
{
	$open_reports = reports_count();
	if ( $open_reports > 0 )
	{
		$open_reports = sprintf(( ($open_reports == 1) ? $lang['Post_reports_one_cp'] : $lang['Post_reports_many_cp']), $open_reports);
		$open_reports = '<span style="color: red">' . $open_reports . '</span>';
		$l_privmsgs_text1 .= ( $userdata['user_new_privmsg'] ) ? '<br/>' : '';
		$l_privmsgs_text1 .= '<a href="' . append_sid($phpbb_root_path . 'modcp.'.$phpEx.'?mode=reportpost') . '">' . $open_reports . '</a>';
	}
}

// 在线颜色
$online_color = ' style="color: #0fff0f"';
$offline_color = ' style="color: #b40000"';
$hidden_color = ' style="color: #888888"';

// 
$advertisment_head = ($this_file == 'index.php') ? $board_config['forums_index_top'] : $board_config['forums_other_top'];

// 时间
$l_timezone = explode('.', $board_config['board_timezone']);
$l_timezone = (count($l_timezone) > 1 && $l_timezone[count($l_timezone)-1] != 0) ? $lang[sprintf('%.1f', $board_config['board_timezone'])] : $lang[number_format($board_config['board_timezone'])];

$template->assign_vars(array(
	'SITENAME' 			=> $board_config['sitename'],
	'SITE_DESCRIPTION' 	=> $board_config['site_desc'],
	
	'ADVERTISMENT' 	=> $advertisment_head,
	'PAGE_TITLE' 	=> $page_title,
	
	'LAST_VISIT_DATE' 	=> sprintf($lang['You_last_visit'], $s_last_visit),
	'CURRENT_TIME' 		=> sprintf($lang['Current_time'], create_date($board_config['default_dateformat'], time(), $board_config['board_timezone'])),
	
	'TOTAL_USERS_ONLINE' 	=> $l_online_users,
	'LOGGED_IN_USER_LIST' 	=> $online_userlist,
	
	'RECORD_USERS' 	=> sprintf($lang['Record_online_users'], $board_config['record_online_users'], create_date($board_config['default_dateformat'], $board_config['record_online_date'], $board_config['board_timezone'])),
	
	'PRIVATE_MESSAGE_INFO' 			=> $l_privmsgs_text,
	'PRIVATE_MESSAGE_INFO_UNREAD'	=> $l_privmsgs_text_unread,
	'PRIVATE_MESSAGE_NEW_FLAG' 		=> $s_privmsg_new,
	'PRIVMSG_IMG' 					=> $icon_pm,
	'HOBOE' 						=> $l_privmsgs_text1,

	'L_USERNAME' 		=> $lang['Username'],
	'L_PASSWORD' 		=> $lang['Password'],
	
	'L_LOGIN_LOGOUT' 	=> $l_login_logout,
	'L_LOGIN' 			=> $lang['Login'],
	'L_LOG_ME_IN' 		=> $lang['Log_me_in'],
	'L_AUTO_LOGIN' 		=> $lang['Log_me_in'],
	
	'L_INDEX' 			=> sprintf($lang['Index'], $board_config['sitename']),
	'L_REGISTER' 		=> $lang['Register'],
	'L_PROFILE' 		=> $lang['Profile'],
	'L_SEARCH' 			=> $lang['Search'],
	'L_BOOKMARKS' 		=> $lang['Bookmarks'] . ' (' . $total_bookmarks . ')',
	
	'L_PRIVATEMSGS' 	=> $lang['Private_Messages'],
	'L_WHO_IS_ONLINE' 	=> $lang['Who_is_Online'],
	'L_MEMBERLIST' 		=> $lang['Memberlist'],
	
	'L_FAQ' 			=> $lang['FAQ'],
	'L_RULES' 			=> $lang['Rules'],
	'L_BANK' 			=> $lang['bank'],
	
	'L_USERGROUPS' 		=> $lang['Usergroups'],
	'L_SEARCH_NEW' 		=> $lang['Search_new'],
	
	'L_SEARCH_UNANSWERED' 	=> $lang['Search_unanswered'],
	'L_SEARCH_SELF' 		=> $lang['Search_your_posts'],
	'L_MEDALS' 				=> $lang['Medals'],
	'L_SHOP' 				=> $lang['Shop'],
	'L_STAFF' 				=> $lang['Staff'],
	'L_ALBUM' 				=> $lang['Album'],
	
	'S_CONTENT_DIRECTION' 	=> $lang['DIRECTION'],
	'S_CONTENT_ENCODING' 	=> $lang['ENCODING'],
	'S_CONTENT_DIR_LEFT' 	=> $lang['LEFT'],
	'S_CONTENT_DIR_RIGHT' 	=> $lang['RIGHT'],
	
	'U_SEARCH_UNANSWERED' 	=> append_sid('search.'.$phpEx.'?search_id=unanswered'),
	'U_SEARCH_SELF' 		=> append_sid('search.'.$phpEx.'?search_id=egosearch'),
	'U_SEARCH_NEW' 			=> append_sid('search.'.$phpEx.'?search_id=newposts'),
	
	'U_INDEX' 				=> append_sid("{$phpbb_root_path}index.$phpEx"),
	'U_FORUM' 				=> append_sid("{$phpbb_root_path}forum.$phpEx"),
	'U_REGISTER' 			=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=register"),
	'U_PROFILE' 			=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $userdata['user_id']),
	'U_BACK_PROFILE'		=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $profiledata['user_id']),
	'U_PRIVATEMSGS' 		=> append_sid("{$phpbb_root_path}privmsg.$phpEx?folder=inbox"),
	'U_PRIVATEMSGS_POPUP' 	=> append_sid("{$phpbb_root_path}privmsg.$phpEx?mode=newpm"),
	'U_SEARCH' 				=> append_sid("{$phpbb_root_path}search.$phpEx"),
	'U_BOOKMARKS' 			=> append_sid("{$phpbb_root_path}search.$phpEx?search_id=bookmarks"),
	'U_MEMBERLIST' 			=> append_sid("{$phpbb_root_path}memberlist.$phpEx"),
	'U_BANLIST' 			=> append_sid("{$phpbb_root_path}memberlist.$phpEx?ban"),
	'U_MODCP' 				=> append_sid("{$phpbb_root_path}modcp.$phpEx"),
	
	'U_VIEWONLINE' 			=> append_sid("{$phpbb_root_path}viewonline.$phpEx"),
	'U_LOGIN_LOGOUT' 		=> append_sid($phpbb_root_path . $u_login_logout),
	'U_GROUP_CP' 			=> append_sid("{$phpbb_root_path}groupcp.$phpEx"),
	'U_RULES' 				=> append_sid("{$phpbb_root_path}rules.$phpEx"),
	'U_STAFF' 				=> append_sid("{$phpbb_root_path}memberlist.$phpEx?admin"),
	'U_MEDALS' 				=> append_sid("{$phpbb_root_path}medals.$phpEx"),
	'U_ALBUM' 				=> append_sid("{$phpbb_root_path}album.$phpEx"),
	
	// MODS
	'U_SHOP' 				=> append_sid("{$phpbb_root_path}mods/shop/index.$phpEx"),
	'U_CHAT'				=> append_sid("{$phpbb_root_path}chat.$phpEx"),
	'U_BANK' 				=> append_sid("{$phpbb_root_path}mods/bank/index.".$phpEx),
	
	'T_ROOT_PATH'			=> $phpbb_root_path,
	
	'S_TIMEZONE' 			=> sprintf($lang['All_times'], $l_timezone),
	'S_LOGIN_ACTION' 		=> append_sid("{$phpbb_root_path}login.$phpEx"))
);

// Login box?
if ( !$userdata['session_logged_in'] )
{
	$template->assign_block_vars('switch_user_logged_out', array());

	if (!isset($board_config['allow_autologin']) || $board_config['allow_autologin'] )
	{
		$template->assign_block_vars('switch_allow_autologin', array());
		$template->assign_block_vars('switch_user_logged_out.switch_allow_autologin', array());
	}
}
else
{
	$template->assign_block_vars('switch_user_logged_in', array());

	if ( !empty($userdata['user_popup_pm']) )
	{
		$template->assign_block_vars('switch_enable_pm_popup', array());
	}
}

if ( $board_config['shop'] )
{
	$template->assign_block_vars('shop_on', array());
}
// Add no-cache control for cookies if they are set
//$c_no_cache = (isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_sid']) || isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_data'])) ? 'no-cache="set-cookie", ' : '';

if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2'))
{
	header ('Cache-Control: no-cache, pre-check=0, post-check=0');
}
else
{
	header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
}
header ('Expires: 0');
header ('Pragma: no-cache');

$template->pparse('overall_header');

?>