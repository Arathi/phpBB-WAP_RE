<?php
/*******************************************************
 *      Разработка: Carbofos
 *      Оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：评价
 *******************************************************/

if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

include($phpbb_root_path . 'includes/functions_reputation.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

$page_title = $lang['Reputation'];

$mode = input_var('mode', 'reputation');
$user_id = $voter_id = input_var(POST_USERS_URL, 0);
$post_id = input_var(POST_POST_URL, 0);
$review_id = input_var(POST_REVIEWS_URL, 0);
$post_order = input_var('postorder', array('desc', 'asc'));
$start = input_var('start', 0);

if ($post_id)
{
	$mode = 'post';
}
else
{
	if ($review_id)
	{
		$result = db_query('SELECT * FROM {REPUTATION_TABLE} WHERE id = %d', $review_id);
		if ($row = $db->sql_fetchrow($result))
		{
			$user_id = $row['user_id'];
			$voter_id = $row['voter_id'];
			$post_id = $row['post_id'];

			switch ($row['modification'])
			{
				case REPUTATION_WARNING:
				case REPUTATION_BAN:
					$mode = 'warnings';
					break;
				case REPUTATION_WARNING_EXPIRED:
				case REPUTATION_BAN_EXPIRED:
					$mode = 'expired';
					break;
				default:
					$mode = 'reputation';
			}
		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['reputation_no_review_spec']);
		}
	}
	elseif (!$user_id)
	{
		message_die(GENERAL_MESSAGE, $lang['reputation_no_user_spec']);
	}
}

switch ($mode)
{
	case 'given':
		$cond = "r.voter_id = $voter_id";
		$view_param = POST_USERS_URL . '=' . $voter_id;
		break;
	case 'post':
		$cond = "r.post_id = $post_id";
		$view_param = POST_POST_URL . '=' . $post_id;
		break;
	default:
		$cond = "r.user_id = $user_id";
		$view_param = POST_USERS_URL . '=' . $user_id;
}

switch ($mode)
{
	case 'warnings':
		$cond .= ' AND (r.modification = {REPUTATION_WARNING} OR r.modification = {REPUTATION_BAN})';
		break;
	case 'expired':
		$cond .= $board_config['reputation_delete_expried'] ? 'FALSE' : ' AND (r.modification = {REPUTATION_WARNING_EXPIRED} OR r.modification = {REPUTATION_BAN_EXPIRED})';
		break;
	default:
		$cond .= $board_config['reputation_positive_only'] ? ' AND r.modification = {REPUTATION_INC}' : ' AND (r.modification = {REPUTATION_INC} OR r.modification = {REPUTATION_DEC})';
}

$ban_expired = $warnings_expired = $ban = $warnings = $plus = $minus = 0;
$result = db_query("SELECT modification, COUNT(modification) cnt FROM {REPUTATION_TABLE} r
		WHERE $cond
		GROUP BY modification");
while ($row = $db->sql_fetchrow($result))
{
	switch ($row['modification'])
	{
		case REPUTATION_INC: $plus = $row['cnt']; break;
		case REPUTATION_DEC: $minus = $row['cnt']; break;
		case REPUTATION_WARNING: $warnings = $row['cnt']; break;
		case REPUTATION_WARNING_EXPIRED: $warnings_expired = $row['cnt']; break;
		case REPUTATION_BAN: $ban = $row['cnt']; break;
		case REPUTATION_BAN_EXPIRED: $ban_expired = $row['cnt']; break;
	}
}
$reputation = $plus - $minus;

include($phpbb_root_path . 'includes/page_header.' . $phpEx);
$template->set_filenames(array('body' => 'profile_view_reputation.tpl'));

if ($mode == 'post')
{

	$result = db_query('SELECT u.username, u.user_id, u.user_level, u.user_sig, u.user_sig_bbcode_uid, u.user_allowsmile, p.*, pt.post_text, pt.post_subject, pt.bbcode_uid, u.user_reputation, u.user_reputation_plus, u.user_warnings
		FROM {POSTS_TABLE} p, {USERS_TABLE} u, {POSTS_TEXT_TABLE} pt
		WHERE p.post_id = %d
			AND pt.post_id = p.post_id
			AND u.user_id = p.poster_id', $post_id);
	if (!$postrow = $db->sql_fetchrow($result))
	{
		message_die(GENERAL_MESSAGE, $lang['Topic_post_not_exist']);
	}
	if ($postrow['poster_id'] == ANONYMOUS)
	{
		message_die(GENERAL_MESSAGE, $lang['reputation_anonymous_no_reviews']);
	}
	if (!$plus && !$minus)
	{
		message_die(GENERAL_MESSAGE, $lang['reputation_no_reviews'] . '<br /><br />' . sprintf($lang['reputation_msg_back_to_topic'], "<a href=\"" . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id") . "#$post_id\">", "</a>"));
	}
	$num_reviews = $plus + $minus;

	$is_auth = reputation_auth($postrow['forum_id'], $userdata);
	$forums_auth = array($postrow['forum_id'] => $is_auth);

	if (!$is_auth['auth_view_rep'])
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=$mode&start=$start&postorder=$post_order&$view_param", true));
		}
		message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
	}

	$post_id = $postrow['post_id'];

	if ($userdata['session_logged_in'] && $postrow['post_time'] > $userdata['user_lastvisit'])
	{
		$mini_post_img = $images['icon_minipost_new'];
		$mini_post_alt = $lang['New_post'];
	}
	else
	{
		$mini_post_img = $images['icon_minipost'];
		$mini_post_alt = $lang['Post'];
	}
	$mini_post_url = append_sid("viewtopic.$phpEx?" . POST_POST_URL . '=' . $post_id) . '#' . $post_id;

	$bani_img = $bani = $warn_img = $warn = $edit_img = $edit = $delpost_img = $delpost = $ip_img = $ip = $reportpost_img = $reportpost = $user_sig = $l_edited_by = $user_warnings = $user_reputation = $temp_url = '';

	$is_auth = reputation_auth($is_auth, $userdata, $postrow, true);

	if ($board_config['reputation_enabled'])
	{
		$user_reputation = reputation_display($postrow, $is_auth, true);
	}
	if ($board_config['warnings_enabled'])
	{
		$result = db_query('SELECT *
			FROM {BANLIST_TABLE}
			WHERE ban_userid = %d', $postrow['user_id']);

		$user_warnings = reputation_warnings($postrow, (bool) $db->sql_fetchrow($result), $is_auth);
	}

	if ($board_config['warnings_enabled'])
	{
		if ($is_auth['auth_warn'])
		{
			$temp_url = "reputation.$phpEx?mode=warning&amp;" . POST_POST_URL . "=" . $post_id . "&amp;sid=" . $userdata['session_id'];
			$warn_img = '<a href="' . $temp_url . '">' . $lang['reputation_warn_user'] . '</a>';
			$warn = '<a href="' . $temp_url . '">' . $lang['reputation_warn_user'] . '</a>';
		}

		if ($is_auth['auth_ban'])
		{
			$temp_url = "reputation.$phpEx?mode=ban&amp;" . POST_POST_URL . "=" . $post_id . "&amp;sid=" . $userdata['session_id'];
			$bani_img = '<a href="' . $temp_url . '">' . $lang['reputation_ban_user'] . '</a>';
			$bani = '<a href="' . $temp_url . '">' . $lang['reputation_ban_user'] . '</a>';
		}
	}

	if (($userdata['user_id'] == $postrow['user_id'] && $is_auth['auth_edit']) || $is_auth['auth_mod'])
	{
		$temp_url = append_sid("posting.$phpEx?mode=editpost&amp;" . POST_POST_URL . "=" . $post_id);
		$edit = '<a href="' . $temp_url . '" title="' . $lang['Edit_delete_post'] . '">' . $lang['Edit'] . '</a>';
	}

	if (($userdata['user_id'] == $postrow['user_id'] && $is_auth['auth_delete'] && $forum_topic_data['topic_last_post_id'] == $post_id) || $is_auth['auth_mod'])
	{
		$temp_url = "posting.$phpEx?mode=delete&amp;" . POST_POST_URL . "=" . $post_id . "&amp;sid=" . $userdata['session_id'];
		$delpost = '<a href="' . $temp_url . '" title="' . $lang['Delete_post'] . '">' . $lang['Delete'] . '</a>';
	}

	if ($is_auth['auth_mod'])
	{
		$temp_url = "modcp.$phpEx?mode=ip&amp;" . POST_POST_URL . "=" . $post_id . "&amp;" . POST_TOPIC_URL . "=" . $postrow['topic_id'] . "&amp;sid=" . $userdata['session_id'];
		$ip = '<a href="' . $temp_url . '" title="' . $lang['View_IP'] . '">IP</a>';
	}
	else
	{
		if ($board_config['reports_enabled'] && $userdata['session_logged_in'] && $userdata['user_level'] == USER)
		{
			$temp_url = "post_report.$phpEx?mode=report&amp;" . POST_POST_URL . "=" . $post_id . "&amp;sid=" . $userdata['session_id'];
			$reportpost = '<a href="' . $temp_url . '" title="' . $lang['reputation_report_post'] . '">' . $lang['reputation_report'] . '</a>';
		}
	}

	$user_profile = $user_profile_img = '';

	$message = prepare_display($postrow['post_text'], $postrow['bbcode_uid'], $postrow['enable_html'], $postrow['enable_smilies']);
	$post_subject = censor($postrow['post_subject']);

	if ($postrow['enable_sig'] && $postrow['user_sig'] && $board_config['allow_sig'])
	{
		$user_sig = prepare_display($postrow['user_sig'], $postrow['user_sig_bbcode_uid'], true, true);
		$user_sig = '<br clear="all" />_________________<br />' . $user_sig;
	}

	if ($postrow['post_edit_count'])
	{
		$l_edit_time_total = ($postrow['post_edit_count'] == 1) ? $lang['Edited_time_total'] : $lang['Edited_times_total'];
		$l_edited_by = '<br /><br />' . sprintf($l_edit_time_total, $postrow['username'], create_date($board_config['default_dateformat'], $postrow['post_edit_time'], $board_config['board_timezone']), $postrow['post_edit_count']);
	}

	$template->assign_block_vars('postrow', array(
		'POSTER_NAME' 		=> $postrow['username'],
		'POST_DATE' 		=> $post_date,
		'POST_SUBJECT' 		=> $post_subject,
		'MESSAGE' 			=> $message,
		'SIGNATURE' 		=> $user_sig,
		'EDITED_MESSAGE' 	=> $l_edited_by,
		'POSTER_REPUTATION' => $user_reputation,
		'POSTER_WARNINGS' 	=> $user_warnings,

		'REPORTPOST' 		=> $reportpost,
		'REPORTPOST_IMG' 	=> $reportpost_img,
		'RED' 				=> $bani,
		'RED_IMG' 			=> $bani_img,
		'YELLOW' 			=> $warn,
		'YELLOW_IMG' 		=> $warn_img,
		'MINI_POST_IMG' 	=> $mini_post_img,
		'EDIT_IMG' 			=> $edit_img,
		'EDIT' 				=> $edit,
		'IP_IMG' 			=> $ip_img,
		'IP' 				=> $ip,
		'DELETE_IMG' 		=> $delpost_img,
		'DELETE' 			=> $delpost,

		'L_POST' 			=> $lang['Post'],
		'L_POST_SUBJECT' 	=> $lang['Post_subject'],
		'L_MINI_POST_ALT' 	=> $mini_post_alt,

		'U_RETURN_TOPIC' 	=> sprintf($lang['reputation_msg_back_to_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . '=' . $post_id) . '#' . $post_id . '">', '</a>'),
		'U_MINI_POST' 		=> $mini_post_url,
		'U_POST_ID' 		=> $post_id)
	);

	if ($board_config['warnings_enabled'])
	{
		$result = db_query('SELECT r.*, rt.*, u.username FROM {REPUTATION_TABLE} r, {REPUTATION_TEXT_TABLE} rt, {USERS_TABLE} u
			WHERE r.post_id = %d
				AND r.modification IN ({REPUTATION_WARNING}, {REPUTATION_BAN}, {REPUTATION_WARNING_EXPIRED}, {REPUTATION_BAN_EXPIRED})
				AND r.id = rt.id
				AND r.voter_id = u.user_id
				LIMIT 1', $post_id);
		if ($post_warning = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('postrow.warning', reputation_warning_tpl($post_warning));
		}
	}

	if ($board_config['reputation_positive_only'])
	{
		$block_title = $lang['reputation_post_reviews'] . ": &nbsp; $thumb_up_img $plus";
	}
	else
	{
		$block_title = $lang['reputation_post_reviews'] . ": &nbsp; $thumb_up_img $plus &nbsp; $thumb_dn_img $minus &nbsp; (" . $lang['reputation_total'] . ': ' . ($reputation > 0 ? '+' : '') . $reputation . ')';
	}
	$block = 'rep';
}
else
{
	$result = db_query('SELECT username, user_reputation, user_warnings FROM {USERS_TABLE} WHERE user_id = %d', $user_id);
	if (!($user = $db->sql_fetchrow($result)))
	{
		message_die(GENERAL_ERROR, $lang['reputation_no_user_spec']);
	}

	$forums_auth = reputation_auth(AUTH_LIST_ALL, $userdata);

	$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id");
	$user_profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';
	$user_link = '<a href="' . $temp_url . '">' . $user['username'] . '</a>';
	if ($mode == 'warnings')
	{
		if ($warnings || $ban)
		{
			$warnings_data = ($warnings ? $warned_img . $warnings : '') . ($ban ? ' &nbsp; ' . $banned_img : '');

			$num_reviews = $warnings + $ban;
			$block_title = sprintf($lang['reputation_warnings_to'], $user_link) . ': &nbsp; ' . $warnings_data;
		}
		else
		{
			$message = $lang['reputation_no_warnings'];
			if ($warnings_expired || $ban_expired)
			{
				$message .= '<br /><br /><a href="' . append_sid("profile.$phpEx?mode=expired&amp;m=" . 'expired' . '&amp;' . POST_USERS_URL . "=$user_id") . '">' . $lang['reputation_display_expired'] . '</a>';
			}
			else
			{
				$message .= '<br /><br />' . sprintf($lang['reputation_msg_view_profile'], '<a href="' . $temp_url . '">', '</a>');
			}
			message_die(GENERAL_MESSAGE, $message);
		}
		$block = 'warn';
	}
	elseif ($mode == 'expired')
	{
		if ($warnings_expired || $ban_expired)
		{
			$num_reviews = $warnings_expired + $ban_expired;
			$block_title = sprintf($lang['reputation_warnings_expired'], $user_link) . ': &nbsp; ' . $warnings_expired;
		}
		else
		{
			$message = $lang['reputation_no_warnings'] . '<br /><br />' . sprintf($lang['reputation_msg_view_profile'], '<a href="' . $temp_url . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
		$block = 'warn';
	}
	else
	{
		if ($plus || $minus)
		{
			$num_reviews = $plus + $minus;
			$block_title = sprintf($lang['reputation_of'], $user_link);

			if ($board_config['reputation_positive_only'])
			{
				$block_title .= ': +'.$plus;
			}
			else
			{
				$block_title .= ': +'.$plus.'/-'.$minus;
			}
		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['reputation_no_details'] . '<br /><br />' . sprintf($lang['reputation_msg_view_profile'], '<a href="' . $temp_url . '">', '</a>'));
		}
		$block = 'rep';
	}
}

$self = "profile.$phpEx?mode=$mode&amp;start=$start&amp;postorder=$post_order&amp;$view_param";
$pagination = generate_pagination($self, $num_reviews, $board_config['topics_per_page'], $start);

if ($mode == 'warnings' || $mode == 'expired')
{
	$l_expire = ($mode == 'warnings') ? $lang['reputation_expire'] : $lang['reputation_expired'];
	$l_read = $lang['Warning'];
	$l_unread = $lang['New_warning'];

	$auth_edit_key = 'auth_edit_warn';
	$auth_delete_key = 'auth_delete_warn';
}
else
{
	$l_expire = '';
	$l_read = $lang['Review'];
	$l_unread = $lang['New_review'];

	$auth_edit_key = 'auth_edit_rep';
	$auth_delete_key = 'auth_delete_rep';
}

$select_post_order = html_select('postorder', array('asc', 'desc'), array('Oldest_First', 'Newest_First'), $post_order);

$template->assign_vars(array(
	'L_DISPLAY_POSTS' 		=> $lang['reputation_display'],
	'L_GO' 					=> $lang['Go'],
	'L_BACK_TO_TOP' 		=> $lang['Back_to_top'],
	'L_REASON' 				=> $lang['reputation_reason'],
	'L_AUTHOR' 				=> $lang['Author'],
	'L_OFFICIAL'			=> $lang['Official'],
	'L_WARNING' 			=> $lang['Warning'],
	'L_REVIEW' 				=> $lang['Review'],
	'L_ISSUED' 				=> $lang['reputation_issued'],
	'L_POST_REF' 			=> $lang['reputation_post_ref'],
	'L_EXPIRE' 				=> $l_expire,
	'L_POSTED' 				=> $lang['Posted'],
	
	'U_REPUTATION_BACK_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $HTTP_GET_VARS['u']),

	'S_REVIEW_OREDR_ACTION' => append_sid($self),
	'S_SELECT_POST_ORDER' 	=> $select_post_order,

	'PAGINATION' 			=> $pagination
));

$template->assign_block_vars($block, array(
	'BLOCK_TITLE' 				=> $block_title,

	'U_VIEWPROFILE' 			=> $user_profile,
	'U_VIEWPROFILE_USERNAME' 	=> $user_link,
	'U_VIEWPROFILE_IMG' 		=> $user_profile_img,
));

$review_images = array(
	REPUTATION_INC 				=> '好评',
	REPUTATION_DEC 				=> '差评', 
	REPUTATION_WARNING 			=> '警告', 
	REPUTATION_WARNING_EXPIRED 	=> '警告', 
	REPUTATION_BAN 				=> '黑名单', 
	REPUTATION_BAN_EXPIRED 		=> '黑名单'
);

$result = db_query("SELECT r.*, rt.* FROM {REPUTATION_TABLE} r, {REPUTATION_TEXT_TABLE} rt
		WHERE $cond
			AND r.id = rt.id
		ORDER BY r.date %s
		LIMIT %d, %d",
		($post_order == 'desc') ? 'DESC' : 'ASC', $start, $board_config['topics_per_page'], 0, $board_config['topics_per_page']);

$report_button = $board_config['reports_enabled'] && $userdata['session_logged_in'] && $userdata['user_level'] == USER && ($mode == 'reputation' || $mode == 'given');

for ($i = true; $row = $db->sql_fetchrow($result); $i = !$i)
{
	$is_auth = reputation_auth($forums_auth[$row['forum_id']], $userdata, $row, true);

	if ($row['post_id'] != NO_ID)
	{
		$post_reference = '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . '=' . $row['post_id']) . '#' . $row['post_id'] . '">' . $lang['reputation_post_ref'] . '</a>';
	}
	elseif ($row['forum_id'] != NO_ID)
	{
		$post_reference = $lang['reputation_post_deleted'];
	}
	else
	{
		$post_reference = $lang['No'];
	}

	$review = $review_images[$row['modification']];
	$date_time = create_date($board_config['default_dateformat'], $row['date'], $board_config['board_timezone']);
	$expire = is_null($row['expire']) ? $lang['reputation_expire_never'] : create_date($board_config['default_dateformat'], $row['expire'], $board_config['board_timezone']);

	$ip_img = $ip = $delpost_img = $delpost = $edit_img = $edit = $reportpost_img = $reportpost = $l_edited_by = '';

	if ($is_auth['auth_mod'])
	{
		$temp_url = 'http://www.dnsstuff.com/tools/whois.ch?ip=' . decode_ip($row['poster_ip']);
		$ip = '<a href="' . $temp_url . '" title="' . $lang['View_IP'] . '">IP</a>';
	}

	if ($is_auth[$auth_delete_key])
	{
		$temp_url = "reputation.$phpEx?mode=delete&amp;" . POST_REVIEWS_URL . '=' . $row['id'] . '&amp;ret=' . $mode . '&amp;sid=' . $userdata['session_id'];
		$delpost = ' <a href="' . $temp_url . '" title="' . $lang['reputation_delete_review'] . '">删除</a>';
	}

	if ($is_auth[$auth_edit_key])
	{
		$temp_url = "reputation.$phpEx?mode=edit&amp;" . POST_REVIEWS_URL . '=' . $row['id'] . '&amp;ret=' . $mode . '&amp;sid=' . $userdata['session_id'];
		$edit = ' <a href="' . $temp_url . '" title="' . $lang['reputation_edit_review'] . '">修改</a>';
	}

	if ($report_button)
	{
		$temp_url = "post_report.$phpEx?mode=report&amp;" . POST_REVIEWS_URL . '=' . $row['id'] . '&amp;sid=' . $userdata['session_id'];
		$reportpost = '<a href="' . $temp_url . '" title="' . $lang['reputation_report_post'] . '">' . $lang['reputation_report_post'] . '</a>';
	}

	if ($userdata['session_logged_in'] && $row['date'] > $userdata['user_lastvisit'])
	{
		$mini_post_img = $images['icon_minipost_new'];
		$mini_post_alt = $l_unread;
	}
	else
	{
		$mini_post_img = $images['icon_minipost'];
		$mini_post_alt = $l_read;
	}

	$mini_post_url = append_sid("profile.$phpEx?mode=$mode&amp;" . POST_REVIEWS_URL . '=' . $row['id'] . "&amp;postorder=$post_order");

	if ($row['edit_count'])
	{
		$l_edit_time_total = ($row['edit_count'] == 1) ? $lang['Edited_time_total'] : $lang['Edited_times_total'];
		$l_edited_by = '<br />' . sprintf($l_edit_time_total, $row['username'], create_date($board_config['default_dateformat'], $row['edit_time'], $board_config['board_timezone']), $row['edit_count']);
	}

	$sql2 = "SELECT user_id, username 
		FROM " . USERS_TABLE . " 
		WHERE user_id = " . $row['voter_id'];
	if ( !($result2 = $db->sql_query($sql2)) )
	{
		message_die(GENERAL_ERROR, 'Tried obtaining data for a non-existent user', '', __LINE__, __FILE__, $sql2);
	}
	if ( $row2 = $db->sql_fetchrow($result2) )
	{
		$reviewer_name = '<a href="'.append_sid('profile.'.$phpEx.'?mode=viewprofile&amp;u='.$row2['user_id']).'">'.$row2['username'].'</a>';
	} else {
		$reviewer_name = '删除';
	}

	$message = prepare_display($row['text'], $row['bbcode_uid'], true, true, true);
	$message_text = ( $row['text_message'] ) ? '<br/>Текст на момент наказания:<br/>' . prepare_display($row['text_message'], $row['mess_bbcode_uid'], true, true, true) : '';
	$class = ( !($i % 2) ) ? 'row_hard' : 'row_easy';

	$template->assign_block_vars("$block.rows", array(
		'L_MINI_POST_ALT' 		=> $mini_post_alt,
		'U_MINI_POST' 			=> $mini_post_url,
		'COMMENTS' 				=> $message,
		'MESSAGE_TEXT' 			=> $message_text,
		'EDITED_COMMENTS' 		=> $l_edited_by,
		'REVIEWER_NAME' 		=> $reviewer_name,
		'REVIEW' 				=> $review,
		'ROW_CLASS' 			=> $class,
		'EXPIRE' 				=> $expire,
		'DATE_TIME' 			=> $date_time,
		'POST_REF' 				=> $post_reference,
		'POST_DATE' 			=> create_date($board_config['default_dateformat'], $row['date'], $board_config['board_timezone']),
		'REPORTPOST' 			=> $reportpost,
		
		'REPORTPOST_IMG' 		=> $reportpost_img,
		'EDIT_IMG' 				=> $edit_img,
		'EDIT' 					=> $edit,
		'IP_IMG' 				=> $ip_img,
		'IP' 					=> $ip,
		'DELETE_IMG' 			=> $delpost_img,
		'DELETE' 				=> $delpost,
		'MINI_POST_IMG' 		=> $mini_post_img
	));
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.' . $phpEx);

?>