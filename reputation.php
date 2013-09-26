<?php
/*************************************************************
 *                            reputation.php
 *                             -------------------
 *      Разработка: Carbofos
 *      Оптимизация под WAP: Гутник Игорь ( чел )
 *          2012 год
 *		简体中文：爱疯的云
 *		描述：评价系统
 ************************************************************/

define('IN_PHPBB', true);

$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_post.' . $phpEx);

$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);

include($phpbb_root_path . 'includes/functions_reputation.' . $phpEx); 

$page_title = $lang['Reputation'];

$mode = input_var('mode', '', $lang['Not_Authorised']);
$mode = htmlspecialchars($mode);
$ret = input_var('ret', 'reputation');
$self_params = "mode=$mode&ret=$ret";

cache_set(RESPECTED_CACHE);

function forum_post_tpl($user_post_data)
{
	global $board_config;

	$poster = $user_post_data['username'];
	$post_date = create_date($board_config['default_dateformat'], $user_post_data['post_time'], $board_config['board_timezone']);
	$post_subject = censor($user_post_data['post_subject']);
	$message = prepare_display($user_post_data['post_text'], $user_post_data['bbcode_uid'], $user_post_data['enable_html'], $user_post_data['enable_smilies']);

	return array(
		'USER_NAME' 		=> $poster,
		'POST_DATE' 		=> $post_date,
		'POST_SUBJECT' 		=> $post_subject,
		'POSTER_MESSAGE' 	=> $message,
	);
}

if ($mode == 'inc' || $mode == 'dec' || $mode == 'warning' || $mode == 'ban')
{
	if ($mode == 'inc' || $mode == 'dec')
	{
		if (!$board_config['reputation_enabled'] || ($mode == 'dec' && $board_config['reputation_positive_only']))
		{
			message_die(GENERAL_MESSAGE, $lang['No_post_mode']);
		}
	}
	else
	{
		if (!$board_config['warnings_enabled'])
		{
			message_die(GENERAL_MESSAGE, $lang['No_post_mode']);
		}
	}

	$post_id = input_var(POST_POST_URL, NO_ID);
	if ($post_id != NO_ID)
	{
		$result = db_query('SELECT u.username, u.user_id, u.user_email, u.user_allowsmile, u.user_warnings, u.user_level, u.user_lang, u.user_reputation, p.*, pt.post_text, pt.post_subject, pt.bbcode_uid
			FROM {POSTS_TABLE} p, {USERS_TABLE} u, {POSTS_TEXT_TABLE} pt
			WHERE p.post_id = %d
				AND p.poster_id = u.user_id
				AND p.post_id = pt.post_id',
			$post_id);

		if (!($user_post_data = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_MESSAGE, $lang['No_posts_topic']);
		}

		$self_params .= '&' . POST_POST_URL . '=' . $post_id;

		$user_id = intval($user_post_data['user_id']);
		$forum_id = intval($user_post_data['forum_id']);

		$back_url = sprintf($lang['reputation_msg_back_to_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id") . "#$post_id\">", '</a>');
	}
	else
	{
		$user_id = input_var(POST_USERS_URL, 0, $lang['reputation_no_user_spec']); 
		$forum_id = NO_ID;

		$result = db_query('SELECT u.username, u.user_id, u.user_level, u.user_warnings, u.user_lang, u.user_email
			FROM {USERS_TABLE} u
			WHERE u.user_id = %d',
			$user_id);
		if (!($user_post_data = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
		}

		$self_params .= '&' . POST_USERS_URL . '=' . $user_id;

		$back_url = sprintf($lang['reputation_msg_view_profile'], '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id") . '">', '</a>');
	}
	$user_post_text = ( !$user_post_data['post_text'] ) ? '' : $user_post_data['post_text'];
	$user_mess_bbcode_uid = ( !$user_post_data['bbcode_uid'] ) ? '' : $user_post_data['bbcode_uid'];
	$is_auth = reputation_auth($forum_id, $userdata, $user_post_data, false);

	$mode_auth_keys = array('inc' => 'auth_add_rep', 'dec' => 'auth_add_rep', 'warning' => 'auth_warn', 'ban' => 'auth_ban');
	$auth_key = $mode_auth_keys[$mode];

	if (!$is_auth[$auth_key])
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=reputation.$phpEx?$self_params", true));
		}

		message_die(GENERAL_MESSAGE, isset($is_auth[$auth_key . '_msg']) ? $is_auth[$auth_key . '_msg'] : $lang['Not_Authorised']);
	}
}

if ($mode == 'inc' || $mode == 'dec')
{
	include($phpbb_root_path . 'includes/page_header.' . $phpEx);

	if ($post_id != NO_ID)
	{
		$result = db_query('SELECT id FROM {REPUTATION_TABLE}
			WHERE post_id = %d
			AND (modification = {REPUTATION_INC} OR modification = {REPUTATION_DEC})
			AND voter_id = %d',
			$post_id, $userdata['user_id']);

		if ($reputation_data = $db->sql_fetchrow($result))
		{
			$review_id = $reputation_data['id'];
			message_die(GENERAL_MESSAGE, $lang['reputation_already_voted'] . '<br /><br />' . sprintf($lang['reputation_msg_view_your_review'], '<a href="' . append_sid("profile.$phpEx?mode=reputation&" . POST_REVIEWS_URL . "=$review_id") . "#$review_id\">", '</a>') . $back_url);
		}
	}

	if (isset($HTTP_POST_VARS['submit']))
	{
		if ( $board_config['reputation_empty_reviews'] )
		{
			$user_comments = $HTTP_POST_VARS['message'];
		} else {
			if (!empty($HTTP_POST_VARS['message']))
			{
				$user_comments = $HTTP_POST_VARS['message'];
			} else {
				message_die(GENERAL_ERROR, $lang['reputation_no_comments_entered']);
			}
		}

		$bbcode_uid = $board_config['allow_bbcode'] ? make_bbcode_uid() : '';
		$user_comments = stripslashes(prepare_message($user_comments, $board_config['allow_html'], $board_config['allow_bbcode'], $board_config['allow_smilies'], $bbcode_uid));

		if ($mode == 'inc')
		{
			$modification = REPUTATION_INC;
			$reputation = 'user_reputation = user_reputation + 1, user_reputation_plus = user_reputation_plus + 1';
		}
		else
		{
			$modification = REPUTATION_DEC;
			$reputation = 'user_reputation = user_reputation - 1';
		}

		db_transaction(BEGIN_TRANSACTION);
		db_query('INSERT INTO {REPUTATION_TABLE} (modification, user_id, voter_id, post_id, forum_id, poster_ip, date)
			VALUES (%d, %d, %d, %d, %d, \'%s\', %d)',
			$modification, $user_post_data['user_id'], $userdata['user_id'], $post_id, $forum_id, $user_ip, time());

		$review_id = $db->sql_nextid();

		db_query("INSERT INTO {REPUTATION_TEXT_TABLE} (id, text, bbcode_uid) VALUES (%d, '%s', '%s')",
			$review_id, $user_comments, $bbcode_uid);

		db_query("UPDATE {USERS_TABLE} SET $reputation WHERE user_id = %d", $user_id);
		db_query('UPDATE {POSTS_TABLE} SET post_reviews = post_reviews + 1 WHERE post_id = %d', $post_id);

		db_transaction(END_TRANSACTION);

		message_die(GENERAL_MESSAGE, $lang['reputation_update_successfull'] . '<br /><br />' . sprintf($lang['reputation_msg_view_your_review'], '<a href="' . append_sid("profile.$phpEx?mode=reputation&amp;" . POST_REVIEWS_URL . "=$review_id") . "#$review_id\">", '</a>') . $back_url);
	}
	else
	{
		$template->set_filenames(array('body' => 'profile_modify_reputation.tpl'));

		if ($post_id != NO_ID)
		{
			$template->assign_block_vars('postrow', forum_post_tpl($user_post_data));
		}

		$html_status = ($userdata['user_allowhtml'] && $board_config['allow_html']) ? $lang['HTML_is_ON'] : $lang['HTML_is_OFF'];
		$bbcode_status = ($userdata['user_allowbbcode'] && $board_config['allow_bbcode']) ? $lang['BBCode_is_ON'] : $lang['BBCode_is_OFF'];
		$smilies_status = ($userdata['user_allowsmile'] && $board_config['allow_smilies']) ? $lang['Smilies_are_ON'] : $lang['Smilies_are_OFF'];

		$template->assign_vars(array(
			'L_MODIFY_REPUTATION' 		=> $lang['reputation_modify'],
			'L_WROTE' 					=> $lang['wrote'],
			'L_REPUTATION' 				=> $lang['Reputation'],
			'L_POSTED' 					=> $lang['Posted'],
			'L_POST_SUBJECT' 			=> $lang['Post_subject'],
			'L_DESCRIPTON' 				=> $lang['Review'],
			'L_NOTE' 					=> $is_auth['auth_edit_rep'] ? $lang['reputation_note_can_edit'] : $lang['reputation_note_cant_edit'],
			'L_SUBMIT' 					=> $lang['Submit'],
			'L_OPTIONS' 				=> $lang['Options'],
			'REVIEW_IMG' 				=> ($mode == 'inc') ? '好评' : '差评',

			'HTML_STATUS' 				=> $html_status,
			'BBCODE_STATUS' 			=> sprintf($bbcode_status, '<a href="' . append_sid("faq.$phpEx?mode=bbcode") . '" target="_phpbbcode">', '</a>'),
			'SMILIES_STATUS' 			=> $smilies_status,
			
			'U_REPUTATION_BACK_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $HTTP_GET_VARS['u']),
			
			'S_HIDDEN_FORM_FIELDS' 		=> preg_replace('#([^=]+)=([^&]+)&#si', '<input type="hidden" name="\1" value="\2" />', $self_params . '&'),
			'S_PROFILE_ACTION' 			=> append_sid("reputation.$phpEx"))
		);

		$template->pparse('body');
	}
}
elseif ($mode == 'warning' || $mode == 'ban')
{

	include($phpbb_root_path . 'includes/page_header.' . $phpEx);

	if ($post_id != NO_ID)
	{
		$result = db_query('SELECT * FROM {REPUTATION_TABLE}
			WHERE post_id = %d
				AND (modification = {REPUTATION_BAN} OR modification = {REPUTATION_WARNING})', $post_id);
		if ($row = $db->sql_fetchrow($result))
		{
			$review_id = $reputation_data['id'];
			message_die(GENERAL_MESSAGE, $lang['reputation_already_warned'] . '<br /><br />' . sprintf($lang['reputation_msg_view_warning'], '<a href="' . append_sid("profile.$phpEx?mode=reputation&" . POST_REVIEWS_URL . "=$review_id") . "#$review_id\">", '</a>') . $back_url);
		}

		$topic_id = intval($user_post_data['topic_id']);
		$back_url .= "<br /><br /><a href=\"posting.$phpEx?mode=delete&amp;sid=" . $userdata['session_id'] . '&amp;' . POST_POST_URL . "=$post_id\">取消该用户的警告</a><br /><a href=\"modcp.$phpEx?mode=lock&amp;sid=" . $userdata['session_id'] . '&amp;' . POST_TOPIC_URL . "=$topic_id\">" . $lang['Lock_topic'] . '</a>';
	}
	$poster = $user_post_data['username'];

	if ($board_config['reputation_ban_warnings'])
	{
		if ($mode == 'warning' && ($user_post_data['user_warnings'] + 1 >= $board_config['reputation_ban_warnings']))
		{
			$mode = 'ban';
			$last_warning_hint = ' (' . $lang['reputation_last_warning_issued'] . ')';
		}
	}

	$result = db_query('SELECT * FROM {BANLIST_TABLE} WHERE ban_userid = %d', $user_id);
	if ($db->sql_fetchrow($result))
	{
		message_die(GENERAL_MESSAGE, $lang['reputation_already_banned']);
	}

	if (isset($HTTP_POST_VARS['submit']))
	{
		$expiration = $board_config["reputation_{$mode}_expire"];

		switch (count($expiration))
		{
			case 0:
				$expire = null;
				break;

			case 1:
				$expire = $expiration[0];
				break;

			case 2:
				if (empty($HTTP_POST_VARS['expire_never']))
				{
					$expire = input_var('expire_days', 0, $lang['reputation_no_expire_entered']);

					$min = empty($expiration[0]) ? 1 : $expiration[0];
					$max = empty($expiration[1]) ? $expire : $expiration[1];

					if ($expire > $max || $expire < $min)
					{
						message_die(GENERAL_MESSAGE, $lang['reputation_no_expire_entered']);
					}
				}
				elseif (empty($expiration[1]))
				{
					$expire = null;
				}
				else
				{
					message_die(GENERAL_MESSAGE, $lang['reputation_no_expire_entered']);
				}
				break;

			default:
				message_die(GENERAL_ERROR, 'Reputation config is damaged');
		}
		$ban_unit = intval($HTTP_POST_VARS['ban_unit']);
		$expire = is_null($expire) ? 'NULL' : (($expire * $ban_unit) + $current_time);

		$user_comments = $text_comments = input_var('message', '', $lang['reputation_no_comments_entered']);

		$bbcode_uid = $board_config['allow_bbcode'] ? make_bbcode_uid() : '';
		$user_comments = stripslashes(prepare_message($user_comments, $board_config['allow_html'], $board_config['allow_bbcode'], $board_config['allow_smilies'], $bbcode_uid));

		$lock_topic = !empty($HTTP_POST_VARS['lock_topic']); 
		$delete_post = !empty($HTTP_POST_VARS['delete_post']);

		db_transaction(BEGIN_TRANSACTION);

		$sql_post_id = $delete_post ? NO_ID : $post_id;
		$modification = ($mode == 'warning') ? REPUTATION_WARNING : REPUTATION_BAN;

		if ( $HTTP_POST_VARS['rule_match'] == '1' )
		{
			if ( !empty($HTTP_POST_VARS['r_id']) )
			{
				$rule_id = abs(intval($HTTP_POST_VARS['r_id']));
				$sql = "SELECT * FROM " . RULES_TABLE . " WHERE rule_id = $rule_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
				}
				$row = $db->sql_fetchrow($result);
				if ( $row )
				{
					$user_comments .= "\n". '按照第 [url=' . server_url() . 'rules.' . $phpEx . '?crid=' . $row['rule_cat_id'] . '&amp;hl=' . $row['rule_id'] . ']' . $row['rule_name'] . '[/url]';
				}
			}
			else
			{
				message_die(GENERAL_MESSAGE, '请选择违反了哪条规则！');
			}
		}

		db_query('INSERT INTO {REPUTATION_TABLE} (modification, user_id, voter_id, post_id, forum_id, poster_ip, date, expire)
			VALUES (%d, %d, %d , %d, %d, \'%s\', %d, %s)',
			$modification, $user_id, $userdata['user_id'], $sql_post_id, $forum_id, $user_ip, $current_time, $expire);
		$review_id = $db->sql_nextid();

		db_query("INSERT INTO {REPUTATION_TEXT_TABLE} (id, text, bbcode_uid, text_message, mess_bbcode_uid) VALUES (%d, '%s', '%s', '%s', '%s')",
			$review_id, $user_comments, $bbcode_uid, $user_post_text, $user_mess_bbcode_uid);

		$result = db_query('SELECT COUNT(*) AS cnt FROM {REPUTATION_TABLE}
			WHERE (modification = {REPUTATION_WARNING} OR modification = {REPUTATION_BAN})
				AND user_id = %d', $user_id);
		$warnings = abs(intval($db->sql_fetchfield('cnt', -1, $result)));

		db_query('UPDATE {USERS_TABLE} SET user_warnings = %d WHERE user_id = %d', $warnings, $user_id);

		if ($mode == 'ban')
		{
			if (!empty($HTTP_POST_VARS['allow_pm']))
			{
				db_query('UPDATE {USERS_TABLE} SET user_allow_pm = 0 WHERE user_id = %d', $user_id);
			}
			db_query('INSERT INTO {BANLIST_TABLE} (ban_userid) VALUES (%d)', $user_id);
			db_query('DELETE FROM {SESSIONS_TABLE} WHERE session_user_id = %d', $user_id);
			db_query('DELETE FROM {SESSIONS_KEYS_TABLE} WHERE user_id = %d', $user_id);

			$e_template = 'reputation_ban';
			$e_subject = $lang['reputation_mail_ban'];
			$e_link = '';
		}
		else
		{
			$e_template = 'reputation_warning';
			$e_subject = $lang['reputation_mail_warning'];
			$e_link = server_url() . "profile.$phpEx?mode=warnings&" . POST_REVIEWS_URL . '=' . $review_id;
		}
		db_transaction(END_TRANSACTION);

		include($phpbb_root_path . 'includes/emailer.' . $phpEx);
		$emailer = new emailer($board_config['smtp_delivery']);

		$emailer->from($board_config['board_email']);
		$emailer->replyto($board_config['board_email']);

		$emailer->use_template($e_template, stripslashes($user_post_data['user_lang']));
		$emailer->email_address($user_post_data['user_email']);
		$emailer->set_subject($e_subject);

		$unhtml_specialchars_match = array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
		$unhtml_specialchars_replace = array('>', '<', '"', '&');

		$text_comments = preg_replace(
			array('#\[quote(|="\w+")\]#si', '#\[/quote\]#si', '#\[code\]#si', '#\[/code\]#si', '#\[/?\w+(|=\w+)\]#si'),
			array("\n{$lang['Quote']} >>\n", "\n<< {$lang['Quote']}\n", "\n{$lang['Code']} >>\n", "\n<< {$lang['Code']}\n"),
			stripslashes($text_comments));
		$text_comments = preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, $text_comments);

		$emailer->assign_vars(array(
			'SITENAME' 			=> $board_config['sitename'],
			'USERNAME' 			=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $poster), 0, 25)),
			'MODERATOR' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $userdata['username']), 0, 25)),
			'TIME' 				=> $expire > 0 ? sprintf($lang['reputation_for_days'], round(($expire - $current_time) / 86400)) : '',
			'REASON' 			=> $text_comments,
			'EMAIL_SIG' 		=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',

			'U_LINK' 			=> $e_link)
		);
		$emailer->send();
		$emailer->reset();

		message_die(GENERAL_MESSAGE,
			$lang['reputation_warning_successfull'] . '<br /><br />' .
			sprintf($lang['reputation_msg_view_your_review'], '<a href="' . append_sid("profile.$phpEx?mode=warnings&amp;" . POST_REVIEWS_URL . "=$review_id") . "#$review_id\">", '</a>') .
			$back_url);
	} 
	else
	{
		if ($post_id != NO_ID)
		{
			$template->assign_block_vars('postrow', forum_post_tpl($user_post_data));
		}

		$template->set_filenames(array('body' => 'profile_warning.tpl'));

		if ($mode == 'warning')
		{
			$m_title = $lang['reputation_warn_user'];
			$img = $warned_img;
			$expire = $board_config['reputation_warning_expire'];
		}
		else
		{
			$m_title = $lang['reputation_ban_user'];
			$img = $banned_img;
			$expire = $board_config['reputation_ban_expire'];
		}

		switch (count($expire))
		{
			case 0:
				$block = 'switch_expire_fixed';
				$message = $lang['reputation_expire_never'];
				break;
			case 1:
				$block = 'switch_expire_fixed';
				$message = sprintf($lang['reputation_expire_fixed'], $expire[0]);
				break;
			case 2:
				if (empty($expire[1]))
				{
					$block = 'switch_expire_limited_bottom';
					$message = sprintf($lang['reputation_expire_limited_bottom'], empty($expire[0]) ? 1 : $expire[0]);
				}
				else
				{
					$block = 'switch_expire_limited';
					$message = sprintf($lang['reputation_expire_limited'], $expire[0], $expire[1]);
				}
				break;
			default:
				message_die(GENERAL_ERROR, 'Reputation config is damaged');
		}

		$l_expire_days = explode('%s', $lang['reputation_expire_fixed']);

		$html_status = ($userdata['user_allowhtml'] && $board_config['allow_html']) ? $lang['HTML_is_ON'] : $lang['HTML_is_OFF'];
		$bbcode_status = ($userdata['user_allowbbcode'] && $board_config['allow_bbcode']) ? $lang['BBCode_is_ON'] : $lang['BBCode_is_OFF'];
		$smilies_status = ($userdata['user_allowsmile'] && $board_config['allow_smilies']) ? $lang['Smilies_are_ON'] : $lang['Smilies_are_OFF'];

		$msql = "SELECT c.cat_r_id, c.cat_r_name, r.rule_id, r.rule_name
			FROM " . RULES_CAT_TABLE . " c, " . RULES_TABLE . " r
			WHERE r.rule_moder = '1' AND r.rule_cat_id = c.cat_r_id
			ORDER BY cat_r_id, rule_id";
		if ( !($mresult = $db->sql_query($msql)) )
		{
			message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
		}
		if ( $mrow = $db->sql_fetchrow($mresult) )
		{
			do
			{
				$rcatsrow[] = $mrow;
			}
			while ($mrow = $db->sql_fetchrow($mresult));
			$db->sql_freeresult($mresult);
		}

		$rule_list = '';
		if ( $rcatsrow )
		{
			$rule_list .= '<select name="r_id">';
			$rule_list .= '<option value=""></option>';
			$rule_list .= '<optgroup label="' . $rcatsrow[0]['cat_r_name'] . ':">';
			$rule_list .= '<option value="' . $rcatsrow[0]['rule_id'] . '">' . $rcatsrow[0]['rule_name'] . '</option>';
			for ( $i=1; $i < count($rcatsrow); $i++ )
			{
				if ( $rcatsrow[$i]['cat_r_id'] != $rcatsrow[$i-1]['cat_r_id'] )
				{
					$rule_list .= '</optgroup><optgroup label="' . $rcatsrow[$i]['cat_r_name'] . ':">';
				}
				$rule_list .= '<option value="' . $rcatsrow[$i]['rule_id'] . '">' . $rcatsrow[$i]['rule_name'] . '</option>';
			}
			$rule_list .= '</optgroup>';
			$rule_list .= '</select>';
			$rule_list .= '<input type="hidden" name="rule_match" value="1" />';
		}

		$template->assign_block_vars($block, array());

		$template->assign_vars(array(
			'L_MODIFY_REPUTATION' 	=> $m_title,
			'L_WROTE' 				=> $lang['wrote'],
			'L_POSTED' 				=> $lang['Posted'],
			'L_POST_SUBJECT' 		=> $lang['Post_subject'],
			'L_DESCRIPTON' 			=> $lang['Review'],
			'L_SUBMIT' 				=> $lang['Submit'],
			'L_EXPIRE' 				=> $lang['reputation_expire'],
			'L_EXPIRE_NEVER' 		=> $lang['reputation_expire_never'],
			'L_EXPIRE_DAYS_0' 		=> $l_expire_days[0],
			'L_EXPIRE_DAYS_1' 		=> $l_expire_days[1],
			'L_EXPIRE_MESSAGE' 		=> $message,
			'L_LAST_WARNING_HINT' 	=> empty($last_warning_hint) ? '' : $last_warning_hint,

			'REVIEW_IMG' 			=> $img,
			'RULES_LIST' 			=> $rule_list,
			'HTML_STATUS' 			=> $html_status,
			'SMILIES_STATUS' 		=> $smilies_status,

			'U_WARNING_BACK_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $HTTP_GET_VARS['u']),
			
			'S_HIDDEN_FORM_FIELDS' 	=> preg_replace('#([^=]+)=([^&]+)&#si', '<input type="hidden" name="\1" value="\2" />', $self_params . '&'),
			'S_EXPIRE_DAYS' 		=> (count($expire) == 2) ? $expire[0] : '',
			'S_PROFILE_ACTION' 		=> append_sid("reputation.$phpEx"))
		);

		if ($mode == 'ban')
		{
			$template->assign_block_vars('allow_pm', array());
		}

		$template->pparse('body');
	}
}
elseif ($mode == 'edit')
{

	$review_id = input_var(POST_REVIEWS_URL, 0, $lang['reputation_no_review_spec']);
	$self_params .= '&' . POST_REVIEWS_URL . '=' . $review_id;

	$result = db_query('SELECT u.username, u.user_allowsmile, u.user_level, r.modification, r.post_id, r.voter_id, r.user_id, r.forum_id, r.edit_time, r.edit_count, rt.text, rt.bbcode_uid AS rt_uid
		FROM {REPUTATION_TABLE} r, {USERS_TABLE} u, {REPUTATION_TEXT_TABLE} rt
		WHERE r.id = %d
			AND r.voter_id = u.user_id
			AND r.id = rt.id', $review_id);

	if ($review_data = $db->sql_fetchrow($result))
	{
		if ($review_data['post_id'] == NO_ID && $review_data['forum_id'] != NO_ID)
		{
			message_die(GENERAL_MESSAGE, $lang['reputation_deleted_no_edit']);
		}
	}
	else
	{
		message_die(GENERAL_MESSAGE, $lang['Topic_post_not_exist']); 
	}

	$forum_id = intval($review_data['forum_id']);
	$user_id = intval($review_data['user_id']);
	$post_id = intval($review_data['post_id']);

	$is_auth = reputation_auth($forum_id, $userdata, $review_data, true);
	$auth_key = ($review_data['modification'] == REPUTATION_INC || $review_data['modification'] == REPUTATION_DEC) ? 'auth_edit_rep' : 'auth_edit_warn';

	if (!$is_auth[$auth_key])
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=reputation.$phpEx?$self_params", true));
		}

		message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
	}

	include($phpbb_root_path . 'includes/page_header.' . $phpEx);

	if (isset($HTTP_POST_VARS['submit']))
	{
		if ($post_id != NO_ID)
		{
			$back_url = '<br /><br />' . sprintf($lang['reputation_msg_back_to_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . "=$post_id") . "#$post_id\">", '</a>');
		}
		else
		{
			$back_url = '<br /><br />' . sprintf($lang['reputation_msg_view_profile'], '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id") . '">', '</a>');
		}

		$user_comments = input_var('message', '', $lang['reputation_no_comments_entered']);

		$bbcode_uid = $board_config['allow_bbcode'] ? make_bbcode_uid() : '';
		$user_comments = stripslashes(prepare_message($user_comments, $board_config['allow_html'], $board_config['allow_bbcode'], $board_config['allow_smilies'], $bbcode_uid));

		if (!$is_auth['auth_mod'])
		{
			db_query('UPDATE {REPUTATION_TABLE}
				SET edit_count = edit_count + 1, edit_time = %d
				WHERE id = %d', time(), $review_id);
		}
		db_query('UPDATE {REPUTATION_TEXT_TABLE}
			SET text = \'%s\', bbcode_uid = \'%s\'
			WHERE id = %d', $user_comments, $bbcode_uid, $review_id);

		message_die(GENERAL_MESSAGE, $lang['reputation_update_successfull'] . '<br/>' . sprintf($lang['reputation_msg_view_your_review'], ' ') . $back_url);
	} 
	else
	{
		if ($post_id != NO_ID)
		{
			$result = db_query('SELECT u.username, p.*, pt.post_text, pt.post_subject, pt.bbcode_uid
				FROM {POSTS_TABLE} p, {USERS_TABLE} u, {POSTS_TEXT_TABLE} pt
				WHERE p.post_id = %d
					AND p.post_id = pt.post_id
					AND p.poster_id = u.user_id', $post_id);
			if (!($post_data = $db->sql_fetchrow($result)))
			{
				message_die(GENERAL_MESSAGE, $lang['reputation_deleted_no_edit']);
			}

			$template->assign_block_vars('postrow', forum_post_tpl($post_data));
		}

		$review = $review_data['text'];
		if ($review_data['rt_uid'])
		{
			$review = preg_replace('/\:(([a-z0-9]:)?)' . $review_data['rt_uid'] . '/s', '', $review);
		}

		$template->set_filenames(array('body' => 'profile_modify_reputation.tpl'));

		$review_imgs = array(
			REPUTATION_INC 		=> '好评',
			REPUTATION_DEC 		=> '差评',
			REPUTATION_WARNING 	=> $warned_img,
			REPUTATION_BAN 		=> $banned_img
		);

		$template->assign_vars(array(
			'L_MODIFY_REPUTATION' 		=> $lang['reputation_edit_review'],
			'L_REPUTATION' 				=> $lang['Reputation'],
			'L_WROTE' 					=> $lang['wrote'],
			'L_POSTED' 					=> $lang['Posted'],
			'L_POST_SUBJECT' 			=> $lang['Post_subject'],
			'L_DESCRIPTON' 				=> $lang['Review'],
			'L_SUBMIT' 					=> $lang['Submit'],
			'L_OPTIONS' 				=> $lang['Options'],

			'REVIEW' 					=> $review,
			'REVIEW_IMG' 				=> $review_imgs[$review_data['modification']],
			
			'U_WARNING_BACK_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $HTTP_GET_VARS['u']),
			
			'S_HIDDEN_FORM_FIELDS' 		=> preg_replace('#([^=]+)=([^&]+)&#si', '<input type="hidden" name="\1" value="\2" />', $self_params . '&'),
			'S_PROFILE_ACTION' 			=> append_sid("reputation.$phpEx")
		));

		$template->pparse('body');
	}
}
elseif ($mode == 'delete')
{

	$review_id = input_var(POST_REVIEWS_URL, 0, $lang['reputation_no_review_spec']);
	$self_params .= '&' . POST_REVIEWS_URL . '=' . $review_id;

	if (isset($HTTP_POST_VARS['cancel']))
	{
		redirect(append_sid("profile.$phpEx?mode=$ret&" . POST_REVIEWS_URL . "=$review_id", true) . "#$review_id");
	}

	$result = db_query('SELECT p.forum_id, r.post_id, r.voter_id, r.user_id, u.user_level, r.modification FROM {POSTS_TABLE} p, {REPUTATION_TABLE} r, {USERS_TABLE} u
		WHERE r.post_id = p.post_id
			AND r.voter_id = u.user_id
			AND r.id = %d', $review_id);
	if (!($forum_topic_data = $db->sql_fetchrow($result)))
	{
		$result = db_query('SELECT r.post_id, r.voter_id, r.user_id, u.user_level, r.modification, r.forum_id FROM {REPUTATION_TABLE} r, {USERS_TABLE} u
			WHERE r.voter_id = u.user_id
				AND r.id = %d', $review_id);
		if (!($forum_topic_data = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_MESSAGE, $lang['Topic_post_not_exist']);
		}
	}

	$forum_id = intval($forum_topic_data['forum_id']);
	$user_id = intval($forum_topic_data['user_id']);
	$modification = intval($forum_topic_data['modification']);
	$post_id = intval($forum_topic_data['post_id']);

	$is_auth = reputation_auth($forum_id, $userdata, $forum_topic_data, true);
	$auth_key = ($modification == REPUTATION_INC || $modification == REPUTATION_DEC) ? 'auth_delete_rep' : 'auth_delete_warn';

	if (!$is_auth[$auth_key])
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx?$self_params", true));
		}

		message_die(GENERAL_MESSAGE, isset($is_auth[$auth_key . '_msg']) ? $is_auth[$auth_key . '_msg'] : $lang['Not_Authorised']);
	}

	include($phpbb_root_path . 'includes/page_header.' . $phpEx);

	if (!isset($HTTP_POST_VARS['confirm']))
	{
		$template->set_filenames(array('confirm_body' => 'confirm_body.tpl'));

		$template->assign_vars(array(
			'MESSAGE_TITLE' 		=> $lang['Information'],
			'MESSAGE_TEXT' 			=> $lang['reputation_confirm_delete'],

			'L_YES' 				=> $lang['Yes'],
			'L_NO' 					=> $lang['No'],

			'S_HIDDEN_FIELDS' 		=> preg_replace('#([^=]+)=([^&]+)&#si', '<input type="hidden" name="\1" value="\2" />', $self_params . '&'),
			'S_CONFIRM_ACTION' 		=> append_sid("reputation.$phpEx")
		));

		$template->pparse('confirm_body');

		include($phpbb_root_path . 'includes/page_tail.' . $phpEx);
	}
	else
	{
		if ($post_id != NO_ID)
		{
			$back_url = '<br /><br />' . sprintf($lang['reputation_msg_back_to_topic'], '<a href="' . append_sid("viewtopic.$phpEx?" . POST_POST_URL . '=' . $post_id) . '#' . $post_id . '">', '</a>');
		}
		else
		{
			$back_url = '<br /><br />' . sprintf($lang['reputation_msg_view_profile'], '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id") . '">', '</a>');
		}

		db_transaction(BEGIN_TRANSACTION);
		db_query('DELETE FROM {REPUTATION_TABLE} WHERE id = %d', $review_id);
		db_query('DELETE FROM {REPUTATION_TEXT_TABLE} WHERE id = %d', $review_id);

		if($modification == REPUTATION_WARNING OR $modification == REPUTATION_BAN) 
		{ 
			$u_data = get_userdata($user_id); 
		}

		switch ($modification)
		{
			case REPUTATION_INC:
				$set = 'user_reputation = user_reputation - 1, user_reputation_plus = user_reputation_plus - 1';
				break;
			case REPUTATION_DEC:
				$set = 'user_reputation = user_reputation + 1';
				break;
			case REPUTATION_WARNING:
				$set = (($u_data['user_warnings'] > 0) ? 'user_warnings = user_warnings - 1' : 'user_warnings = 0');
				break;
			case REPUTATION_BAN:
				$set = (($u_data['user_warnings'] > 0) ? 'user_warnings = user_warnings - 1' : 'user_warnings = 0');
				db_query('DELETE FROM {BANLIST_TABLE} WHERE ban_userid = %d', $user_id);
				db_query('UPDATE {USERS_TABLE} SET user_allow_pm = 1 WHERE user_id = %d', $user_id);
				break;
			case REPUTATION_WARNING_EXPIRED:
			case REPUTATION_BAN_EXPIRED:
				$set = '';
				break;
			default:
				message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
				break;
		}
		if ($set)
		{
			db_query('UPDATE {USERS_TABLE} SET ' . $set . ' WHERE user_id = %d', $user_id);
		}
		if ($modification == REPUTATION_INC || $modification == REPUTATION_DEC)
		{
			db_query('UPDATE {POSTS_TABLE} SET post_reviews = post_reviews - 1 WHERE post_id = %d', $post_id);
		}
		db_query('UPDATE {CONFIG_TABLE} SET config_value = \'\' WHERE config_name = \'reputation_respected\'');
		db_transaction(END_TRANSACTION);

		message_die(GENERAL_MESSAGE, $lang['reputation_delete_success'] . $back_url);
	}
}

include($phpbb_root_path . 'includes/page_tail.' . $phpEx);

?>