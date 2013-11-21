<?php
/***************************************************
 *		viewtopic.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 ***************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);// Bbcode 处理
include($phpbb_root_path . 'includes/functions_bookmark.'.$phpEx);// 书签
include($phpbb_root_path . 'includes/functions_post.'.$phpEx);// 表单处理
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);// 表单处理
include($phpbb_root_path . 'mods/includes/functions_selects.'.$phpEx);

$topic_id = $post_id = 0;

if ( isset($HTTP_GET_VARS[POST_TOPIC_URL]) )
{
	$topic_id = intval($HTTP_GET_VARS[POST_TOPIC_URL]);
}
else if ( isset($HTTP_GET_VARS['topic']) )
{
	$topic_id = intval($HTTP_GET_VARS['topic']);
}
if ( isset($HTTP_GET_VARS[POST_POST_URL]))
{
	$post_id = intval($HTTP_GET_VARS[POST_POST_URL]);
}

$download = ( isset($HTTP_GET_VARS['download']) ) ? $HTTP_GET_VARS['download'] : '';

if ( !$topic_id && !$post_id )
{
	message_die(GENERAL_MESSAGE, '主题或帖子不存在！');
}

if ( isset($HTTP_GET_VARS['view']) && empty($HTTP_GET_VARS[POST_POST_URL]) )
{
	if ( $HTTP_GET_VARS['view'] == 'newest' )
	{
		if ( isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_sid']) || isset($HTTP_GET_VARS['sid']) )
		{
			$session_id = isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_sid']) ? $HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_sid'] : $HTTP_GET_VARS['sid'];

			if (!preg_match('/^[A-Za-z0-9]*$/', $session_id)) 
			{
				$session_id = '';
			}

			if ( $session_id )
			{
				$sql = "SELECT p.post_id
					FROM " . POSTS_TABLE . " p, " . SESSIONS_TABLE . " s,  " . USERS_TABLE . " u
					WHERE s.session_id = '$session_id'
						AND u.user_id = s.session_user_id
						AND p.topic_id = $topic_id
						AND p.post_time >= u.user_lastvisit
					ORDER BY p.post_time ASC
					LIMIT 1";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not obtain newer/older topic information', '', __LINE__, __FILE__, $sql);
				}

				if ( !($row = $db->sql_fetchrow($result)) )
				{
					message_die(GENERAL_MESSAGE, 'No_new_posts_last_visit');
				}

				$post_id = $row['post_id'];

				if (isset($HTTP_GET_VARS['sid']))
				{
					redirect("viewtopic.$phpEx?sid=$session_id&" . POST_POST_URL . "=$post_id#$post_id");
				}
				else
				{
					redirect("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id#$post_id");
				}
			}
		}

		redirect(append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id", true));
	}
	else if ( $HTTP_GET_VARS['view'] == 'next' || $HTTP_GET_VARS['view'] == 'previous' )
	{
		$sql_condition = ( $HTTP_GET_VARS['view'] == 'next' ) ? '>' : '<';
		$sql_ordering = ( $HTTP_GET_VARS['view'] == 'next' ) ? 'ASC' : 'DESC';

		$sql = "SELECT t.topic_id
			FROM " . TOPICS_TABLE . " t, " . TOPICS_TABLE . " t2
			WHERE t2.topic_id = $topic_id
				AND t.forum_id = t2.forum_id
				AND t.topic_moved_id = 0
				AND t.topic_last_post_id $sql_condition t2.topic_last_post_id
			ORDER BY t.topic_last_post_id $sql_ordering
			LIMIT 1";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not obtain newer/older topic information", '', __LINE__, __FILE__, $sql);
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
			$topic_id = intval($row['topic_id']);
		}
		else
		{
			$message = ( $HTTP_GET_VARS['view'] == 'next' ) ? 'No_newer_topics' : 'No_older_topics';
			message_die(GENERAL_MESSAGE, $message);
		}
	}
}

$join_sql_table = (!$post_id) ? '' : ", " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2 ";
$join_sql = (!$post_id) ? "t.topic_id = $topic_id" : "p.post_id = $post_id AND t.topic_id = p.topic_id AND p2.topic_id = p.topic_id AND p2.post_id <= $post_id";
$count_sql = (!$post_id) ? '' : ", COUNT(p2.post_id) AS prev_posts";

$order_sql = (!$post_id) ? '' : "GROUP BY p.post_id, t.topic_id, t.topic_title, t.topic_status, t.topic_replies, t.topic_time, t.topic_type, t.topic_vote, t.topic_last_post_id, f.forum_name, f.forum_status, f.forum_id, f.auth_view, f.auth_read, f.auth_post, f.auth_reply, f.auth_edit, f.auth_delete, f.auth_sticky, f.auth_announce, f.auth_pollcreate, f.auth_vote, f.auth_attachments ORDER BY p.post_id ASC";

$sql = "SELECT t.topic_id, t.topic_title, t.topic_poster, t.topic_status, t.topic_replies, t.topic_views, t.topic_time, t.topic_type, t.topic_vote, t.topic_last_post_id, t.topic_closed, f.forum_name, f.forum_status, f.forum_id, f.auth_view, f.auth_read, f.auth_post, f.auth_reply, f.auth_edit, f.auth_delete, f.auth_sticky, f.auth_announce, f.auth_pollcreate, f.auth_vote, f.auth_attachments" . $count_sql . "
	FROM " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f" . $join_sql_table . "
	WHERE $join_sql
		AND f.forum_id = t.forum_id
		$order_sql";
attach_setup_viewtopic_auth($order_sql, $sql);
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "Could not obtain topic information", '', __LINE__, __FILE__, $sql);
}

if ( !($forum_topic_data = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_MESSAGE, 'Topic_post_not_exist');
}

$forum_id = intval($forum_topic_data['forum_id']);

$userdata = session_pagestart($user_ip, $forum_id);
init_userprefs($userdata);

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['posts_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_topic_data);

if ( $userdata['user_level'] == MODCP )
{
	$is_auth_mod = true;
}
else
{
	$is_auth_mod = $is_auth['auth_mod'];
}

if ( $download )
{
	$sql_download = ( $download != -1 ) ? " AND p.post_id = " . intval($download) . " " : '';

	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);

	$sql = "SELECT u.*, p.*,  pt.post_text, pt.post_subject, pt.bbcode_uid
		FROM " . POSTS_TABLE . " p, " . USERS_TABLE . " u, " . POSTS_TEXT_TABLE . " pt
		WHERE p.topic_id = $topic_id
			$sql_download
			AND pt.post_id = p.post_id
			AND u.user_id = p.poster_id
			ORDER BY p.post_time ASC, p.post_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not create download stream for post.", '', __LINE__, __FILE__, $sql);
	}

	$download_file = '';

	$is_auth_read = array();

	while ( $row = $db->sql_fetchrow($result) )
	{
		$is_auth_read = auth(AUTH_ALL, $row['forum_id'], $userdata);

		$poster_id = $row['user_id'];
		$poster = ( $poster_id == ANONYMOUS ) ? $lang['Guest'] : $row['username'];

		$post_date = create_date($board_config['default_dateformat'], $row['post_time'], $board_config['board_timezone']);

		$post_subject = ( $row['post_subject'] != '' ) ? $row['post_subject'] : '';

		$bbcode_uid = $row['bbcode_uid'];
		$message = $row['post_text'];
		$message = strip_tags($message);
		$message = preg_replace("/\[.*?:$bbcode_uid:?.*?\]/si", '', $message);
		$message = preg_replace('/\[url\]|\[\/url\]/si', '', $message);
		$message = preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);

		$message = unprepare_message($message);
		$message = preg_replace('/&#40;/', '(', $message);
		$message = preg_replace('/&#41;/', ')', $message);
		$message = preg_replace('/&#58;/', ':', $message);
		$message = preg_replace('/&#91;/', '[', $message);
		$message = preg_replace('/&#93;/', ']', $message);
		$message = preg_replace('/&#123;/', '{', $message);
		$message = preg_replace('/&#125;/', '}', $message);

		if (count($orig_word))
		{
			$post_subject = str_replace($orig_word, $replacement_word, $post_subject);

			$message = str_replace($orig_word, $replacement_word, $message);
		}

		$break = "\n";
		$line = '---------------';
		$download_file .= $post_subject . $break.$poster . $break.$post_date . $break . $message . $break . $line;
	}

	$disp_folder = ( $download == -1 ) ? 'topic_'.$topic_id : 'post_'.$download;

	if (!$is_auth_read['auth_read'])
	{
		$download_file = sprintf($lang['Sorry_auth_read'], $is_auth_read['auth_read_type']);
		$disp_folder = 'Download';
	}

	$filename = $board_config['sitename'] . '_' . $disp_folder . '_' . date("Ymd",time()) . '.txt';
	header('Content-Type: text/plain; name="'.$filename.'"');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Content-Transfer-Encoding: plain/text');
	header('Content-Length: '.strlen($download_file));
	print $download_file;

	exit;
}

if( !$is_auth['auth_view'] || !$is_auth['auth_read'] )
{
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = ($post_id) ? POST_POST_URL . "=$post_id" : POST_TOPIC_URL . "=$topic_id";
		$redirect .= ($start) ? "&start=$start" : '';
		redirect(append_sid("login.$phpEx?redirect=viewtopic.$phpEx&$redirect", true));
	}

	$message = ( !$is_auth['auth_view'] ) ? $lang['Topic_post_not_exist'] : sprintf($lang['Sorry_auth_read'], $is_auth['auth_read_type']);

	message_die(GENERAL_MESSAGE, $message);
}

$forum_name = $forum_topic_data['forum_name'];
$topic_title = $forum_topic_data['topic_title'];
$topic_id = intval($forum_topic_data['topic_id']);
$topic_time = $forum_topic_data['topic_time'];

if ( $forum_topic_data['topic_closed'] > 0 )
{
	if ( $forum_topic_data['topic_closed'] == $forum_topic_data['topic_poster'] )
	{
		$topic_closed = '线程关闭<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $forum_topic_data['topic_closed']) . '">作者</a><br/>';
	}
	else
	{
		$user_closed = get_userdata($forum_topic_data['topic_closed']);
		$topic_closed = '线程关闭 <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $forum_topic_data['topic_closed']) . '">' . $user_closed['username'] . '</a><br/>';
	}
}
else
{
	$topic_closed = '';
}

if ($post_id)
{
	$start = floor(($forum_topic_data['prev_posts'] - 1) / intval($board_config['posts_per_page'])) * intval($board_config['posts_per_page']);
}

if( $userdata['session_logged_in'] )
{
	if ( isset($HTTP_GET_VARS['report']) || isset($HTTP_POST_VARS['report']) )
	{
		if ( report_exists($post_id) )
		{
			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id#$post_id") . '">')
			);
			
			$message = $lang['Post_already_reported'] . '<br /><br />' . sprintf($lang['Click_return_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id#$post_id") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}

		$comments = ( !empty($HTTP_POST_VARS['comments']) ) ? htmlspecialchars(trim($HTTP_POST_VARS['comments'])) : '';

		if ( empty($comments) )
		{
			$page_title = '举报帖子';
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);

			$template->set_filenames(array(
				'report_post' => 'report_post.tpl')
			);

			$template->assign_vars(array(
				'TOPIC_TITLE'	=> $topic_title,
				'POST_ID'		=> $post_id,
				'FORUM_NAME' 	=> $forum_name,
				
				'U_VIEW_TOPIC'	=> append_sid($phpbb_root_path . 'viewtopic.'.$phpEx.'?' . POST_TOPIC_URL . '=' . $topic_id),
				'U_VIEW_FORUM'	=> append_sid("viewforum.$phpEx?" . POST_FORUM_URL . "=$forum_id"),

				'L_REPORT_POST'	=> $lang['Report_post'],
				'L_COMMENTS'	=> $lang['Comments'],
				'L_COMMENTS_EXPLAIN'	=> $lang['Comments_explain'],

				'L_SUBMIT'		=> $lang['Submit'],

				'S_ACTION'		=> append_sid($phpbb_root_path . 'viewtopic.'.$phpEx.'?report=true&amp;' . POST_POST_URL . '=' . $post_id))
			);

			$template->pparse('report_post');

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
			exit;
		}
		else
		{
			if ( !report_flood() )
			{
				message_die(GENERAL_MESSAGE, $lang['Flood_Error']);
			}
			insert_report($post_id, $comments);

			if ( $board_config['report_email'] )
			{
				email_report($forum_id, $post_id, $topic_title, $comments);
			}
	
			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id#$post_id") . '">')
			);
			$message = $lang['Post_reported'] . '<br /><br />' . sprintf($lang['Click_return_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id#$post_id") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
	}

	$can_watch_topic = TRUE;

	$sql = "SELECT notify_status
		FROM " . TOPICS_WATCH_TABLE . "
		WHERE topic_id = $topic_id
			AND user_id = " . $userdata['user_id'];
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not obtain topic watch information", '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		if ( isset($HTTP_GET_VARS['unwatch']) )
		{
			if ( $HTTP_GET_VARS['unwatch'] == 'topic' )
			{
				$is_watching_topic = 0;

				$sql_priority = (SQL_LAYER == "mysql") ? "LOW_PRIORITY" : '';
				$sql = "DELETE $sql_priority FROM " . TOPICS_WATCH_TABLE . "
					WHERE topic_id = $topic_id
						AND user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not delete topic watch information", '', __LINE__, __FILE__, $sql);
				}
			}

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="0;url=' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">')
			);

			$message = $lang['No_longer_watching'] . '<br /><br />' . sprintf($lang['Click_return_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			$is_watching_topic = TRUE;

			if ( $row['notify_status'] )
			{
				$sql_priority = (SQL_LAYER == "mysql") ? "LOW_PRIORITY" : '';
				$sql = "UPDATE $sql_priority " . TOPICS_WATCH_TABLE . "
					SET notify_status = 0
					WHERE topic_id = $topic_id
						AND user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not update topic watch information", '', __LINE__, __FILE__, $sql);
				}
			}
		}
	}
	else
	{
		if ( isset($HTTP_GET_VARS['watch']) )
		{
			if ( $HTTP_GET_VARS['watch'] == 'topic' )
			{
				$is_watching_topic = TRUE;

				$sql_priority = (SQL_LAYER == "mysql") ? "LOW_PRIORITY" : '';
				$sql = "INSERT $sql_priority INTO " . TOPICS_WATCH_TABLE . " (user_id, topic_id, notify_status)
					VALUES (" . $userdata['user_id'] . ", $topic_id, 0)";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not insert topic watch information", '', __LINE__, __FILE__, $sql);
				}
			}

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">')
			);

			$message = $lang['You_are_watching'] . '<br /><br />' . sprintf($lang['Click_return_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			$is_watching_topic = 0;
		}
	}
}
else
{
	if ( isset($HTTP_GET_VARS['unwatch']) )
	{
		if ( $HTTP_GET_VARS['unwatch'] == 'topic' )
		{
			redirect(append_sid("login.$phpEx?redirect=viewtopic.$phpEx&" . POST_TOPIC_URL . "=$topic_id&unwatch=topic", true));
		}
	}
	else
	{
		$can_watch_topic = 0;
		$is_watching_topic = 0;
	}
}

$previous_days = array(0, 1, 7, 14, 30, 90, 180, 364);
$previous_days_text = array($lang['All_Posts'], $lang['1_Day'], $lang['7_Days'], $lang['2_Weeks'], $lang['1_Month'], $lang['3_Months'], $lang['6_Months'], $lang['1_Year']);

if( !empty($HTTP_POST_VARS['postdays']) || !empty($HTTP_GET_VARS['postdays']) )
{
	$post_days = ( !empty($HTTP_POST_VARS['postdays']) ) ? intval($HTTP_POST_VARS['postdays']) : intval($HTTP_GET_VARS['postdays']);
	$min_post_time = time() - (intval($post_days) * 86400);

	$sql = "SELECT COUNT(p.post_id) AS num_posts
		FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
		WHERE t.topic_id = $topic_id
			AND p.topic_id = t.topic_id
			AND p.post_time >= $min_post_time";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not obtain limited topics count information", '', __LINE__, __FILE__, $sql);
	}

	$total_replies = ( $row = $db->sql_fetchrow($result) ) ? intval($row['num_posts']) : 0;

	$limit_posts_time = "AND p.post_time >= $min_post_time ";

	if ( !empty($HTTP_POST_VARS['postdays']))
	{
		$start = 0;
	}
}
else
{
	$total_replies = intval($forum_topic_data['topic_replies']) + 1;

	$limit_posts_time = '';
	$post_days = 0;
}

$select_post_days = '<select name="postdays">';
for($i = 0; $i < count($previous_days); $i++)
{
	$selected = ($post_days == $previous_days[$i]) ? ' selected="selected"' : '';
	$select_post_days .= '<option value="' . $previous_days[$i] . '"' . $selected . '>' . $previous_days_text[$i] . '</option>';
}
$select_post_days .= '</select>';

if ( !empty($HTTP_POST_VARS['postorder']) || !empty($HTTP_GET_VARS['postorder']) )
{
	$post_order = (!empty($HTTP_POST_VARS['postorder'])) ? htmlspecialchars($HTTP_POST_VARS['postorder']) : htmlspecialchars($HTTP_GET_VARS['postorder']);
	$post_time_order = ($post_order == "asc") ? "ASC" : "DESC";
}
else
{
	$post_order = 'asc'; 
	$post_time_order = 'ASC';
}

$select_post_order = '<select name="postorder">';
if ( $post_time_order == 'ASC' )
{
	$select_post_order .= '<option value="asc" selected="selected">' . $lang['Oldest_First'] . '</option><option value="desc">' . $lang['Newest_First'] . '</option>';
}
else
{
	$select_post_order .= '<option value="asc">' . $lang['Oldest_First'] . '</option><option value="desc" selected="selected">' . $lang['Newest_First'] . '</option>';
}
$select_post_order .= '</select>';

if ( isset($HTTP_GET_VARS['setbm']) || isset($HTTP_GET_VARS['removebm']) )
{
	$redirect = "viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&start=$start&postdays=$post_days&postorder=$post_order";
	if ( $userdata['session_logged_in'] )
	{
		if (isset($HTTP_GET_VARS['setbm']))
		{
			set_bookmark($topic_id, $start);
		}
		else if (isset($HTTP_GET_VARS['removebm']))
		{
			remove_bookmark($topic_id);
		}
	}
	else
	{
		if (isset($HTTP_GET_VARS['setbm']))
		{
			$redirect .= '&setbm=true';
		}
		else if (isset($HTTP_GET_VARS['removebm']))
		{
			$redirect .= '&removebm=true';
		}
		redirect(append_sid("login.$phpEx?redirect=$redirect", true));
	}
	redirect(append_sid($redirect, true));
}

$sql = "SELECT u.username, u.user_id, u.user_level, u.user_posts, u.user_gender, u.user_post_leng, u.user_nic_color, u.user_allowsmile, u.user_allow_viewonline, u.user_session_time, p.*,  pt.post_text, pt.post_subject, pt.bbcode_uid, u.user_reputation, u.user_reputation_plus, u.user_warnings ,u.user_avatar_type, u.user_allowavatar, u.user_avatar, u.user_zvanie, user_sig
	FROM " . POSTS_TABLE . " p, " . USERS_TABLE . " u, " . POSTS_TEXT_TABLE . " pt
	WHERE p.topic_id = $topic_id
		$limit_posts_time
		AND pt.post_id = p.post_id
		AND u.user_id = p.poster_id
	ORDER BY p.post_time $post_time_order
	LIMIT $start, ".$board_config['posts_per_page'];
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "Could not obtain post/user information.", '', __LINE__, __FILE__, $sql);
}

$postrow = array();
if ($row = $db->sql_fetchrow($result))
{
	$post_ids = $row['post_id'];
	do
	{
		$postrow[] = $row;
		$post_ids .= ',' . $row['post_id'];
	}
	while ($row = $db->sql_fetchrow($result));
	$db->sql_freeresult($result);

if ($board_config['warnings_enabled'] || $board_config['reputation_enabled'])
{
	include($phpbb_root_path . 'includes/functions_reputation.' . $phpEx);

	$is_auth = reputation_auth($is_auth, $userdata);

	if ($board_config['warnings_enabled'])
	{
		$result = db_query('SELECT ban_userid FROM {BANLIST_TABLE}');
		while ($row = $db->sql_fetchrow($result))
		{
			$banned[$row['ban_userid']] = true;
		}

		$result = db_query('SELECT r.*, rt.*, u.username FROM {REPUTATION_TABLE} r, {REPUTATION_TEXT_TABLE} rt, {USERS_TABLE} u
			WHERE r.post_id IN (%s)
				AND r.modification IN ({REPUTATION_WARNING},{REPUTATION_BAN},{REPUTATION_WARNING_EXPIRED},{REPUTATION_BAN_EXPIRED})
				AND r.id = rt.id
				AND r.voter_id = u.user_id', $post_ids);
		while ($row = $db->sql_fetchrow($result))
		{
			$post_warnings[$row['post_id']] = $row;
		}
	}
}

	$total_posts = count($postrow);
}
else 
{ 
	include($phpbb_root_path . 'includes/functions_admin.' . $phpEx); 
	sync('topic', $topic_id); 

	message_die(GENERAL_MESSAGE, $lang['No_posts_topic']); 
} 

$resync = FALSE; 
if ($forum_topic_data['topic_replies'] + 1 < $start + count($postrow)) 
{ 
	$resync = TRUE; 
} 
elseif ($start + $board_config['posts_per_page'] > $forum_topic_data['topic_replies']) 
{ 
	$row_id = intval($forum_topic_data['topic_replies']) % intval($board_config['posts_per_page']); 
	if ($postrow[$row_id]['post_id'] != $forum_topic_data['topic_last_post_id'] || $start + count($postrow) < $forum_topic_data['topic_replies']) 
	{ 
		$resync = TRUE; 
	} 
} 
elseif (count($postrow) < $board_config['posts_per_page']) 
{ 
	$resync = TRUE; 
} 

if ($resync) 
{ 
	include($phpbb_root_path . 'includes/functions_admin.' . $phpEx); 
	sync('topic', $topic_id); 

	$result = $db->sql_query('SELECT COUNT(post_id) AS total FROM ' . POSTS_TABLE . ' WHERE topic_id = ' . $topic_id); 
	$row = $db->sql_fetchrow($result); 
	$total_replies = $row['total'];
}

// BEGIN Ranks Info
$sql = "SELECT *
	FROM " . RANKS_TABLE . "
	ORDER BY rank_special, rank_min";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "获取用户头街等级信息失败！", '', __LINE__, __FILE__, $sql);
}

$ranksrow = array();
while ( $row = $db->sql_fetchrow($result) )
{
	$ranksrow[] = $row;
}
$db->sql_freeresult($result);

// END Ranks Info

$orig_word = array();
$replacement_word = array();
obtain_word_list($orig_word, $replacement_word);

if ( count($orig_word) )
{
	$topic_title = str_replace($orig_word, $replacement_word, $topic_title);
}

$highlight_match = $highlight = '';
if (isset($HTTP_GET_VARS['highlight']))
{
	$words = explode(' ', trim(htmlspecialchars($HTTP_GET_VARS['highlight'])));

	for($i = 0; $i < sizeof($words); $i++)
	{
		if (trim($words[$i]) != '')
		{
			$highlight_match .= (($highlight_match != '') ? '|' : '') . str_replace('*', '\w*', preg_quote($words[$i], '#'));
		}
	}
	unset($words);

	$highlight = urlencode($HTTP_GET_VARS['highlight']);
	$highlight_match = phpbb_rtrim($highlight_match, "\\");
}

$reply_topic_url = append_sid("posting.$phpEx?mode=reply&amp;" . POST_TOPIC_URL . "=$topic_id");
$view_forum_url = append_sid("viewforum.$phpEx?" . POST_FORUM_URL . "=$forum_id");

$reply_alt = ( $forum_topic_data['forum_status'] == FORUM_LOCKED || $forum_topic_data['topic_status'] == TOPIC_LOCKED ) ? $lang['Topic_locked'] : $lang['Reply_to_topic'];
$post_alt = ( $forum_topic_data['forum_status'] == FORUM_LOCKED ) ? $lang['Forum_locked'] : $lang['Post_new_topic'];

if ( $userdata['session_logged_in'] )
{
	$tracking_topics = ( isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) : array();
	$tracking_forums = ( isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) : array();

	if ( !empty($tracking_topics[$topic_id]) && !empty($tracking_forums[$forum_id]) )
	{
		$topic_last_read = ( $tracking_topics[$topic_id] > $tracking_forums[$forum_id] ) ? $tracking_topics[$topic_id] : $tracking_forums[$forum_id];
	}
	else if ( !empty($tracking_topics[$topic_id]) || !empty($tracking_forums[$forum_id]) )
	{
		$topic_last_read = ( !empty($tracking_topics[$topic_id]) ) ? $tracking_topics[$topic_id] : $tracking_forums[$forum_id];
	}
	else
	{
		$topic_last_read = $userdata['user_lastvisit'];
	}

	if ( count($tracking_topics) >= 150 && empty($tracking_topics[$topic_id]) )
	{
		asort($tracking_topics);
		unset($tracking_topics[key($tracking_topics)]);
	}

	$tracking_topics[$topic_id] = time();

	setcookie($board_config['cookie_name'] . '_t', serialize($tracking_topics), 0, $board_config['cookie_path'], $board_config['cookie_domain'], $board_config['cookie_secure']);
}

$hidden_form_fields = '<input type="hidden" name="mode" value="reply" />';
$hidden_form_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
$hidden_form_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';

$page_title = $topic_title;
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'viewtopic_body.tpl',
	'posttopic' => 'viewtopic_post.tpl')
);

$topic_mod = '';

if ( $is_auth_mod )
{
	$topic_mod .= "【<a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=delete&amp;sid=" . $userdata['session_id'] . '">' . $lang['Delete_topic'] . '</a>';
	$topic_mod .= "- <a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=move&amp;sid=" . $userdata['session_id'] . '">' . $lang['Move_topic'] . '</a>';
	$topic_mod .= ( $forum_topic_data['topic_status'] == TOPIC_UNLOCKED ) ? "- <a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=lock&amp;sid=" . $userdata['session_id'] . '">' . $lang['Lock_topic'] . '</a>' : "- <a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=unlock&amp;sid=" . $userdata['session_id'] . '">' . $lang['Unlock_topic'] . '</a><br/>';
	$topic_mod .= "- <a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=split&amp;sid=" . $userdata['session_id'] . '">' . $lang['Split_topic'] . '</a>】';
}
if ( !$is_auth_mod && ($forum_topic_data['topic_poster'] == $userdata['user_id'] && $userdata['user_id'] != ANONYMOUS) && $forum_topic_data['topic_status'] == TOPIC_UNLOCKED )
{
	$topic_mod .= "- <a href=\"modcp.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;mode=lock&amp;sid=" . $userdata['session_id'] . '">' . $lang['Lock_topic'] . '</a>';
}

$s_watching_topic = '';
if ( $can_watch_topic && ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm']) )
{
	if ( $is_watching_topic )
	{
		$s_watching_topic = '<a href="' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;unwatch=topic&amp;start=$start") . '" class="buttom">' . $lang['Stop_watching_topic'] . '</a><br/>';
		$notify_user = true;
	}
	else
	{
		$s_watching_topic = '<a href="' . append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;watch=topic&amp;start=$start") . '" class="buttom">' . $lang['Start_watching_topic'] . '</a><br/>';
		$notify_user = false;
	}
}

//表情选择
$smiles_select = smiles_select();

// 分页处理
$pagination = ( $highlight != '' ) ? generate_pagination("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;postdays=$post_days&amp;postorder=$post_order&amp;highlight=$highlight", $total_replies, $board_config['posts_per_page'], $start) : generate_pagination("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;postdays=$post_days&amp;postorder=$post_order", $total_replies, $board_config['posts_per_page'], $start);

$template->assign_vars(array(
	'L_DOWNLOAD_TOPIC' 	=> $lang['Download_topic'],
	'DOWNLOAD_TOPIC' 	=> append_sid("viewtopic.$phpEx?download=-1&amp;".POST_TOPIC_URL."=".$topic_id),
	'FORUM_ID' 			=> $forum_id,
	'FORUM_NAME' 		=> $forum_name,
	'TOPIC_ID' 			=> $topic_id,
	'TOPIC_TITLE' 		=> $topic_title,
	'TOPIC_REPLIES' 	=> $forum_topic_data['topic_replies'],
	'TOPIC_VIEWS' 		=> $forum_topic_data['topic_views'] + 1,
	
	'TOPIC_CLOSED' 			=> $topic_closed,
	'PAGINATION' 			=> $pagination,
	'PAGE_NUMBER' 			=> sprintf($lang['Page_of'], ( floor( $start / intval($board_config['posts_per_page']) ) + 1 ), ceil( $total_replies / intval($board_config['posts_per_page']) )),

	'L_POST_REPLY_TOPIC' 	=> $reply_alt,
	'L_NOTIFY_ON_REPLY' 	=> $lang['Notify'],
	'SMILES_SELECT' 		=> $smiles_select,
	'L_SUBMIT' 				=> $lang['Submit'],

	'S_TOPIC_LINK' 			=> POST_TOPIC_URL,
	'S_SELECT_POST_DAYS' 	=> $select_post_days,
	'S_SELECT_POST_ORDER' 	=> $select_post_order,
	'S_POST_DAYS_ACTION' 	=> append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . '=' . $topic_id . "&amp;start=$start"),
	'S_AUTH_LIST' 			=> $s_auth_can,
	'S_TOPIC_ADMIN' 		=> $topic_mod,
	'S_WATCH_TOPIC' 		=> $s_watching_topic,
 	'S_NOTIFY_CHECKED' 		=> ( $notify_user ) ? 'checked="checked"' : '',
	'S_POST_ACTION' 		=> append_sid("posting.$phpEx"),
	'S_HIDDEN_FORM_FIELDS' 	=> $hidden_form_fields,

	'U_VIEW_TOPIC' 			=> append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start&amp;postdays=$post_days&amp;postorder=$post_order&amp;highlight=$highlight"),
	'U_VIEW_FORUM' 			=> $view_forum_url,
	'U_POST_REPLY_TOPIC' 	=> $reply_topic_url)
);

$viewtopic_posting_true = false;
if ( $is_auth['auth_reply'] )
{
	if ( ($userdata['session_logged_in'] && $userdata['user_quick_answer'] && !$forum_topic_data['topic_status'] == TOPIC_LOCKED) || !$userdata['session_logged_in'] )
	{
		if (!$userdata['session_logged_in'])
		{
			$template->assign_block_vars('switch_username_select', array());
		}
		if ($userdata['session_logged_in'] && $userdata['user_bb_panel'])
		{
			$template->assign_block_vars('bb_panel', array());
		}
		if ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm'])
		{
			$template->assign_block_vars('switch_notify_checkbox', array());
		}
		$template->assign_var_from_handle('POSTTOPIC', 'posttopic');
		if ($userdata['user_java_otv'])
		{
			$viewtopic_posting_true = true;
			$template->assign_block_vars('user_otv', array());
		}		
	}
}

if ( !empty($forum_topic_data['topic_vote']) )
{
	$s_hidden_fields = '';

	$sql = "SELECT vd.vote_id, vd.vote_text, vd.vote_start, vd.vote_length, vr.vote_option_id, vr.vote_option_text, vr.vote_result
		FROM " . VOTE_DESC_TABLE . " vd, " . VOTE_RESULTS_TABLE . " vr
		WHERE vd.topic_id = $topic_id
			AND vr.vote_id = vd.vote_id
		ORDER BY vr.vote_option_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Could not obtain vote data for this topic", '', __LINE__, __FILE__, $sql);
	}

	if ( $vote_info = $db->sql_fetchrowset($result) )
	{
		$db->sql_freeresult($result);
		$vote_options = count($vote_info);

		$vote_id = $vote_info[0]['vote_id'];
		$vote_title = $vote_info[0]['vote_text'];

		$sql = "SELECT vote_id
			FROM " . VOTE_USERS_TABLE . "
			WHERE vote_id = $vote_id
				AND vote_user_id = " . intval($userdata['user_id']);
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not obtain user vote data for this topic", '', __LINE__, __FILE__, $sql);
		}

		$user_voted = ( $row = $db->sql_fetchrow($result) ) ? TRUE : 0;
		$db->sql_freeresult($result);

		if ( isset($HTTP_GET_VARS['vote']) || isset($HTTP_POST_VARS['vote']) )
		{
			$view_result = ( ( ( isset($HTTP_GET_VARS['vote']) ) ? $HTTP_GET_VARS['vote'] : $HTTP_POST_VARS['vote'] ) == 'viewresult' ) ? TRUE : 0;
		}
		else
		{
			$view_result = 0;
		}

		$poll_expired = ( $vote_info[0]['vote_length'] ) ? ( ( $vote_info[0]['vote_start'] + $vote_info[0]['vote_length'] < time() ) ? TRUE : 0 ) : 0;

		if ( $user_voted || $view_result || $poll_expired || !$is_auth['auth_vote'] || $forum_topic_data['topic_status'] == TOPIC_LOCKED )
		{
			$template->set_filenames(array(
				'pollbox' => 'viewtopic_poll_result.tpl')
			);

			$vote_results_sum = 0;

			for($i = 0; $i < $vote_options; $i++)
			{
				$vote_results_sum += $vote_info[$i]['vote_result'];
			}

			$vote_graphic = 0;
			$vote_graphic_max = count($images['voting_graphic']);

			for($i = 0; $i < $vote_options; $i++)
			{
				$vote_percent = ( $vote_results_sum > 0 ) ? $vote_info[$i]['vote_result'] / $vote_results_sum : 0;
				$vote_graphic_length = round($vote_percent * $board_config['vote_graphic_length']);

				$vote_graphic_img = $images['voting_graphic'][$vote_graphic];
				$vote_graphic = ($vote_graphic < $vote_graphic_max - 1) ? $vote_graphic + 1 : 0;

				if ( count($orig_word) )
				{
					$vote_info[$i]['vote_option_text'] = str_replace($orig_word, $replacement_word, $vote_info[$i]['vote_option_text']);
				}

				$template->assign_block_vars("poll_option", array(
					'POLL_OPTION_CAPTION' 	=> $vote_info[$i]['vote_option_text'],
					'POLL_OPTION_RESULT' 	=> $vote_info[$i]['vote_result'],
					'POLL_OPTION_PERCENT' 	=> sprintf("%.1d%%", ($vote_percent * 100)),

					'POLL_OPTION_IMG' 		=> $vote_graphic_img,
					'POLL_OPTION_IMG_WIDTH' => $vote_graphic_length)
				);
			}

			$template->assign_vars(array(
				'L_TOTAL_VOTES' => $lang['Total_votes'],
				'TOTAL_VOTES' => $vote_results_sum)
			);

		}
		else
		{
			$template->set_filenames(array(
				'pollbox' => 'viewtopic_poll_ballot.tpl')
			);

			for($i = 0; $i < $vote_options; $i++)
			{
				if ( count($orig_word) )
				{
					$vote_info[$i]['vote_option_text'] = str_replace($orig_word, $replacement_word, $vote_info[$i]['vote_option_text']);
				}

				$template->assign_block_vars("poll_option", array(
					'POLL_OPTION_ID' 		=> $vote_info[$i]['vote_option_id'],
					'POLL_OPTION_CAPTION' 	=> $vote_info[$i]['vote_option_text'])
				);
			}

			$template->assign_vars(array(
				'L_SUBMIT_VOTE' 	=> $lang['Submit_vote'],
				'L_VIEW_RESULTS' 	=> $lang['View_results'],

				'U_VIEW_RESULTS' 	=> append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id&amp;postdays=$post_days&amp;postorder=$post_order&amp;vote=viewresult"))
			);

			$s_hidden_fields = '<input type="hidden" name="topic_id" value="' . $topic_id . '" /><input type="hidden" name="mode" value="vote" />';
		}

		if ( count($orig_word) )
		{
			$vote_title = str_replace($orig_word, $replacement_word, $vote_title);
		}

		$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

		$template->assign_vars(array(
			'POLL_QUESTION' 	=> $vote_title,

			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields,
			'S_POLL_ACTION' 	=> append_sid("posting.$phpEx?mode=vote&amp;" . POST_TOPIC_URL . "=$topic_id"))
		);

		$template->assign_var_from_handle('POLL_DISPLAY', 'pollbox');
	}
}
init_display_post_attachments($forum_topic_data['topic_attachment']);

if ( $userdata['session_logged_in'] )
{
	$template->assign_block_vars('bookmark_state', array());

	$bm_action = (is_bookmark_set($topic_id)) ? ("&amp;removebm=true") : ("&amp;setbm=true");
	$template->assign_vars(array(
		'L_BOOKMARK_ACTION' => (is_bookmark_set($topic_id)) ? ($lang['Remove_Bookmark']) : ($lang['Set_Bookmark']),
		'U_BOOKMARK_ACTION' => append_sid("viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id$bm_action&amp;postdays=$post_days&amp;postorder=$post_order&amp;start=$start"))
	);
}

$sql = "UPDATE " . TOPICS_TABLE . "
	SET topic_views = topic_views + 1
	WHERE topic_id = $topic_id";
if ( !$db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, "Could not update topic views.", '', __LINE__, __FILE__, $sql);
}

// 专题帖子
$sql = "SELECT topic_special 
	FROM " . TOPICS_TABLE . " 
	WHERE topic_id = $topic_id";
	
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '获取专题信息失败！', '', __LINE__, __FILE__, $sql);
}
if ( !($topic_special = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_ERROR, '数据读取失败！', '', __LINE__, __FILE__, $sql);
}
$default_special = $topic_special['topic_special'];

$sql = "SELECT special_name
	FROM " . $table_prefix . "specials 
	WHERE special_id = $default_special";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
}
$special_data = $db->sql_fetchrow($result);
if (!$special_data)
{
	$special = '';
}
else
{
	$special = '<br />所属专题：<a href="' . append_sid("{$phpbb_root_path}mods/special/view.php?f=$forum_id&id=$default_special") . '">' . $special_data['special_name'] . '</a>';
}

for($i = 0; $i < $total_posts; $i++)
{
	
	// 帖子中的头像
	$avatar_img = ''; 
	if ( $postrow[$i]['user_avatar_type'] && $postrow[$i]['user_allowavatar'] ) 
	{ 
		switch( $postrow[$i]['user_avatar_type'] ) 
		{ 
			case USER_AVATAR_UPLOAD: 
				$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $postrow[$i]['user_avatar'] . '" alt="" border="0" width="32px" height="32px" />' : ''; 
			break; 
			case USER_AVATAR_REMOTE: 
				$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $postrow[$i]['user_avatar'] . '" alt="" border="0" width="32px" height="32px" />' : ''; 
			break; 
			case USER_AVATAR_GALLERY: 
			$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $postrow[$i]['user_avatar'] . '" alt="" border="0" width="32px" height="32px" />' : ''; 
			break; 
		} 
	}

	// 作者ID
	$poster_id = $postrow[$i]['user_id'];
	
	// 楼层
	$nomer_posta = $i + $start + 1;
	
	// 用户名颜色
	if ( $postrow[$i]['user_warnings'] == 0 )
	{
		if ( !empty($postrow[$i]['user_nic_color']) )
		{
			$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : $lang['Guest'] ) : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '" style="color: '.$postrow[$i]['user_nic_color'].'">' . $postrow[$i]['username'] . '</a>';
		} else {
			$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : $lang['Guest'] ) : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '">' . $postrow[$i]['username'] . '</a>';
		}
	} 
	else 
	{
		$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : $lang['Guest'] ) : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '" style="color:#000000">' . $postrow[$i]['username'] . '</a>';
	}

	// 发表日期
	$post_date = create_date($board_config['default_dateformat'], $postrow[$i]['post_time'], $board_config['board_timezone']);

	// 用户的帖子数
	$poster_posts = ( $postrow[$i]['user_id'] != ANONYMOUS ) ? '[' . $postrow[$i]['user_posts'] . ']' : ' ';

	// 性别
	if ( $postrow[$i]['user_gender'] == 1 )
	{
		$poster_gender = '<img src="images/gender/gender_m.gif" border="0" />';
	} 
	elseif ( $postrow[$i]['user_gender'] == 2) 
	{
		$poster_gender = '<img src="images/gender/gender_f.gif" border="0" />';
	}
	else
	{
		$poster_gender = '<img src="images/gender/gender_x.gif" border="0" />';
	}
	
	$temp_url = '';

	// 头街等级
	$poster_rank = '';
	$rank_image = '';
	if ( !empty($postrow[$i]['user_zvanie']) )
	{
		$poster_rank = $postrow[$i]['user_zvanie'];// 显示商店购买的个人等级
	}
	else
	{
		if ( $postrow[$i]['user_rank'] )
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_rank'] == $ranksrow[$j]['rank_id'] && $ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] ) 
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
			else
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_posts'] >= $ranksrow[$j]['rank_min'] && !$ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] )
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
	}
	
	// 权限
	if ( $postrow[$i]['user_level'] == ADMIN )
	{
		$user_level = '<span' . $font . '><img src="./images/admin.png"/></span>';
	}
	else if ( $postrow[$i]['user_level'] == MOD ) 
	{
		$user_level = '<img src="./images/mod.png"/>';
	} 
	else if ( $postrow[$i]['user_level'] == MODCP )
	{
		$user_level = '<img src="./images/modcp.png"/>';
	}
	else if ( $postrow[$i]['user_level'] == USER ) 
	{
		$user_level = '成员';
	}
	else
	{
		$user_level = '游客';
	}
	
	if ( $poster_id != ANONYMOUS )
	{
		// 警告、黑名单
		if ($board_config['warnings_enabled'])
		{
			$personal_auth = reputation_auth($is_auth, $userdata, $postrow[$i], true);

			if (!isset($post_warnings[$postrow[$i]['post_id']]))
			{
				if ( $personal_auth['auth_warn'] || $userdata['user_level'] == MODCP )
				{
					$temp_url = "reputation.$phpEx?mode=warning&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
					$warn = '<a href="' . $temp_url . '">' . $lang['reputation_warn'] . '</a>|';
				}

				if ( $personal_auth['auth_ban'] || $userdata['user_level'] == MODCP )
				{
					$temp_url = "reputation.$phpEx?mode=ban&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
					$ban = '<a href="' . $temp_url . '">' . $lang['reputation_ban1'] . '</a>|';
				}
			}
		}
		// 在线、隐身、离线
		if( $userdata['user_on_off'] == 1)
		{
			if ($postrow[$i]['user_session_time'] >= (time()-$board_config['online_time']))
			{
				if ($postrow[$i]['user_allow_viewonline'])
				{
					$online_status = '<span' . $online_color . '>' . $lang['Online'] . '</span>';
				}
				else if ( $is_auth_mod || $userdata['user_id'] == $poster_id )
				{
					$online_status = '<span' . $hidden_color . '>' . $lang['Hidden'] . '</span>';
				}
				else
				{
					$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
				}
			}
			else
			{
				$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
			}
		}
		else
		{
			$online_status = '';
		}
	}
	// 既然上面已经有了 if ( $poster_id != ANONYMOUS )
	// 为什么还要这句？？？搞不懂。。。还是留着它吧。
	$online_status = ( $postrow[$i]['user_id'] != ANONYMOUS ) ? $online_status : '';

	
	$temp_url = append_sid("posting.$phpEx?mode=otv&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
	
	//引用
	$quote = ( $viewtopic_posting_true ) ? '<a href="#text" onclick="otv(\'' . $postrow[$i]['username'] . '\');">' . $lang['Reply_to_topic'] . '</a>' : '<a href="' . $temp_url . '">' . $lang['Reply_to_topic'] . '</a>';
	if ( ($userdata['session_logged_in'] && $userdata['user_message_quote'] && $board_config['message_quote']) || (!$userdata['session_logged_in'] && $board_config['message_quote']) )
	{
		$temp_url = append_sid("posting.$phpEx?mode=quote&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
		$quote2 = '<a href="' . $temp_url . '">' . $lang['Reply_with_quote'] . '</a>|';
	}

	// 编辑
	if ( ( $userdata['user_id'] == $poster_id && $is_auth['auth_edit'] ) || $is_auth_mod )
	{
		$temp_url = append_sid("posting.$phpEx?mode=editpost&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
		$edit = '|<a href="' . $temp_url . '">' . $lang['Edit_delete_post'] . '</a>';
	}
	else
	{
		$edit_img = '';
		$edit = '';
	}

	// IP、删除
	if ( $is_auth_mod )
	{

		$temp_url = "modcp.$phpEx?mode=ip&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;" . POST_TOPIC_URL . "=" . $topic_id . "&amp;sid=" . $userdata['session_id'];
		$ip = '|<a href="' . $temp_url . '">' . $lang['View_IP'] . '</a>';

		$temp_url = "posting.$phpEx?mode=delete&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
		$delpost = '|<a href="' . $temp_url . '">' . $lang['Delete_post'] . '</a>';
	}
	else
	{
		$ip = '';

		if ( $userdata['user_id'] == $poster_id && $is_auth['auth_delete'] && $forum_topic_data['topic_last_post_id'] == $postrow[$i]['post_id'] )
		{
			$temp_url = "posting.$phpEx?mode=delete&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
			$delpost = '|<a href="' . $temp_url . '">' . $lang['Delete_post'] . '</a>';
		}
		else
		{
			$delpost = '';
		}
	}

	// ？？？phpBB-WAP 何来 post_subject
	$post_subject = ( $postrow[$i]['post_subject'] != '' ) ? $postrow[$i]['post_subject'] : '';

	// 内容
	$message = '<br />内容：<br />' . $postrow[$i]['post_text'];
	// bbcode 的 id
	$bbcode_uid = $postrow[$i]['bbcode_uid'];

	// 过于长的文件显示 -->
	if ( (!isset($HTTP_GET_VARS[POST_POST_URL]) || (($HTTP_GET_VARS[POST_POST_URL] != $postrow[$i]['post_id']) && isset($HTTP_GET_VARS[POST_POST_URL]))) && ($userdata['user_post_leng'] > 0) && $userdata['session_logged_in'] )
	{
		$message = u2w($message);
		if ( strlen($message) > $userdata['user_post_leng'] )
		{
			$obrez = strpos($message, '', $userdata['user_post_leng']);
			$message = substr($message, 0, $obrez);
			$message .= '...<a href="' . append_sid("viewtopic.$phpEx?" .POST_POST_URL . "=" . $postrow[$i]['post_id']) . '">--&gt</a>';
		}
		$message = w2u($message);
	}

	// 判断是否允许 HTML 标签
	if ( !$board_config['allow_html'] || !$userdata['user_allowhtml'])
	{
		if ( $postrow[$i]['enable_html'] )
		{
			$message = preg_replace('#(<)([\/]?.*?)(>)#is', "&lt;\\2&gt;", $message);
		}
	}

	// bbcode id
	if ($bbcode_uid != '')
	{
		$message = ($board_config['allow_bbcode']) ? bbencode_second_pass($message, $bbcode_uid) : preg_replace("/\:$bbcode_uid/si", '', $message);
	}

	$message = make_clickable($message);

	// 表情
	if ( $board_config['allow_smilies'] )
	{
		if ( $postrow[$i]['enable_smilies'] )
		{
			$message = smilies_pass($message);
		}
	}

	// 搜索匹配突出显示
	if ($highlight_match)
	{
		$message = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*>)#i', '<b style="color: red">\1</b>', $message);
	}

	// 敏感词
	if (count($orig_word))
	{
		$post_subject = str_replace($orig_word, $replacement_word, $post_subject);
		$message = str_replace($orig_word, $replacement_word, $message);
	}

	$message = str_replace("\n", "\n<br />\n", $message);

	// 最后编辑时间
	if ( $postrow[$i]['post_edit_count'] && (($userdata['session_logged_in'] && $userdata['user_posl_red']) || (!$userdata['session_logged_in'] && $board_config['posl_red'])) )
	{
		$l_edit_time_total = ( $postrow[$i]['post_edit_count'] == 1 ) ? $lang['Edited_time_total'] : $lang['Edited_times_total'];
		$l_edited_by = '<br/>__________<br/><span style="font-size: 10px;">' . sprintf($l_edit_time_total, $poster, create_date($board_config['default_dateformat'], $postrow[$i]['post_edit_time'], $board_config['board_timezone']), $postrow[$i]['post_edit_count']) . '</span>';
	}
	else
	{
		$l_edited_by = '';
	}

	// 警告、加黑的图标
	if (isset($post_warnings[$postrow[$i]['post_id']]))
	{
		$warning = $post_warnings[$postrow[$i]['post_id']];
		if ($warning['modification'] == REPUTATION_WARNING || $warning['modification'] == REPUTATION_WARNING_EXPIRED)
		{
			$icon = '<b>!</b>';
		}
		else
		{
			$icon = '<b>#</b>';
		}
	} else {
			$icon = '';
	}

	$row_color = '';
	
	// 用于风格 
	$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	
	// 举报
	if ( $userdata['session_logged_in'] && $userdata['user_id'] != $postrow[$i]['user_id'] )
	{
		$report = '<a href="' . append_sid($phpbb_root_path . 'viewtopic.'.$phpEx.'?report=true&amp;' . POST_POST_URL . '=' . $postrow[$i]['post_id']) . '">' . $lang['Report_post'] . '</a>|';
	}
	else
	{
		$report = '';
	}

	// 投诉
	if ( $is_auth_mod )
	{
		$sql_report = "SELECT COUNT(*) AS total
			FROM " . POST_REPORTS_TABLE . "
			WHERE post_id = " . $postrow[$i]['post_id'] . "
				AND report_status = 1";
		if ( !($result_report = $db->sql_query($sql_report)) )
		{
			message_die(GENERAL_ERROR, 'Could not query reports', '', __LINE__, __FILE__, $sql_report);
		}
		$row_report = $db->sql_fetchrow($result_report);
		$total_report = $row_report['total'];

		if ( $total_report > 0 )
		{
			$report_message = ( $total_report > 1 ) ? '<b><span style="color: red;">这篇文章被 ' . $total_report . ' 投诉！</span></b><br/>' : '<b><span style="color: red;">这篇文章被用户投诉！</span></b><br/>';
		}
		else
		{
			$report_message = '';
		}
	}
	else
	{
		$report_message = '';
	}

	// 签名
	$signture = ( empty($postrow[$i]['user_sig']) ) ? '这家伙很懒，什么也没留下！' : $postrow[$i]['user_sig'];
	switch ($nomer_posta)
	{
		case 1:
			$nomer_posta = '楼主';
			$avatar_img = $avatar_img;
			$poster_posts = $poster_posts;
			$special_select = ( $is_auth_mod ) ? special_select($forum_id, $topic_id, $default_special) : '';
			$special = $special;
			$br = '<br />';
		break;
		case 2:
			$nomer_posta = '沙发';
			$br = '<br />';
			$avatar_img = $poster_posts = $special_select = $special = '';
		break;
		case 3:
			$nomer_posta = '椅子';
			$br = '<br />';
			$avatar_img = $poster_posts = $special_select = $special = '';
		break;
		case 4:
			$nomer_posta = '板凳';
			$br = '<br />';
			$avatar_img =  $poster_posts = $special_select = $special = '';
		break;
		case 5:
			$nomer_posta = '地板';
			$br = '<br />';
			$avatar_img = $poster_posts = $special_select = $special = '';
		break;
		default:
			$nomer_posta = $nomer_posta . '楼';
			$br = '<br />';
			$avatar_img = $poster_posts = $special_select = $special = '';
	}

	$template->assign_block_vars('postrow', array(
		'POSTER_ID'		=> $poster_id,
		'AVATAR_IMG' 	=> $avatar_img,
		'RANK_IMAGE'	=> $rank_image,
		'ROW_COLOR' 	=> '#' . $row_color,
		'ROW_CLASS' 	=> $row_class,
		'POSTER_NAME' 	=> $poster,
		'POSTER_POSTS' 	=> $poster_posts,
		
		'POSTER_ONLINE_STATUS' 	=> $online_status,
		
		'POST_DATE' 		=> $post_date,
		'POST_SUBJECT' 		=> $post_subject,
		'MESSAGE' 			=> $message,
		'EDITED_MESSAGE' 	=> $l_edited_by,
		'POSTER_REPUTATION' => $user_reputation,
		'ICON' 				=> $icon,
		'NOMER_POSTA' 		=> $nomer_posta,
		'POSTER_RANK' 		=> $poster_rank,
		'USER_LEVEL'		=> $user_level,
		'SIGNATURE'			=> $signture,
		'POSTER_GENDER'		=> $poster_gender,
		'REPORT' 			=> $report,
		'REPORT_MESSAGE' 	=> $report_message,
		'RED' 				=> $ban,
		'YELLOW' 			=> $warn,
		'REVIEWS' 			=> $reviews,

		'SPECIAL_SELECT' 	=> $special_select,
		'SPECIAL'			=> $special,
		'BR'				=> $br,
		
		'EDIT' 		=> $edit,
		'QUOTE' 	=> $quote,
		'QUOTE2' 	=> $quote2,
		'IP' 		=> $ip,
		'DELETE' 	=> $delpost,

		'L_MINI_POST_ALT' 	=> $mini_post_alt,

		'U_MINI_POST' 		=> $mini_post_url,
		'U_POST_ID' 		=> $postrow[$i]['post_id'])
	);
	display_post_attachments($postrow[$i]['post_id'], $postrow[$i]['post_attachment']);

}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
