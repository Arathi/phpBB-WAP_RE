<?php
/*******************************************************
 *		functions_reputation.php
 *		Разработка: Carbofos.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *******************************************************/

if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

if (!empty($images))
{
	$warned_img = $lang['Warning'];
	$banned_img = $lang['reputation_ban'];
	$thumb_up_img = $lang['reputation_approve'];
	$thumb_dn_img = $lang['reputation_disapprove'];
}
$current_time = time();

$reputation_auth_keys = array('auth_view_rep', 'auth_view_warns', 'auth_add_rep', 'auth_add_rep_nonpost', 'auth_edit_rep', 'auth_delete_rep', 'auth_warn', 'auth_warn_nonpost', 'auth_ban', 'auth_ban_nonpost', 'auth_edit_warn', 'auth_delete_warn', 'auth_no_limits');

function input_var($name, $default, $required_msg = null)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;

	if (isset($HTTP_POST_VARS[$name]))
	{
		$var = $HTTP_POST_VARS[$name];
	}
	elseif (isset($HTTP_GET_VARS[$name]))
	{
		$var = $HTTP_GET_VARS[$name];
	}
	elseif ($required_msg)
	{
		message_die(GENERAL_ERROR, $required_msg);
	}
	else
	{
		return addslashes(is_array($default) ? $default[0] : $default);
	}

	if (is_array($default))
	{
		settype($var, gettype($default[0]));

		if (in_array($var, $default))
		{
			return $var;
		}
		return $default[0];
	}
	return settype($var, gettype($default)) ? $var : $default;
}

function censor($message, $html = false)
{
	static $orig_word = null, $replacement_word = null;

	if (is_null($orig_word))
	{
		if (isset($GLOBALS['orig_word']) && isset($GLOBALS['replacement_word']))
		{
			$orig_word = $GLOBALS['orig_word'];
			$replacement_word = $GLOBALS['replacement_word'];
		}
		else
		{
			obtain_word_list($orig_word, $replacement_word);
		}
	}
	if ($orig_word)
	{
		return str_replace($orig_word, $replacement_word, $message);
	}
	return $message;
}

function prepare_display($message, $bbcode_uid = '', $enable_html = false, $enable_smilies = true, $enable_links = true)
{
	global $board_config;

	if (!$board_config['allow_html'] && $enable_html)
	{
		$message = preg_replace('#(<)([\/]?.*?)(>)#is', "&lt;\\2&gt;", $message);
	}

	if ($bbcode_uid)
	{
		if ($board_config['allow_bbcode'])
		{
			$message = bbencode_second_pass($message, $bbcode_uid);
		}
		else
		{
			$message = preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);
		}
	}

	if ($enable_links)
	{
		$message = make_clickable($message);
	}

	if ($board_config['allow_smilies'] && $enable_smilies)
	{
		$message = smilies_pass($message);
	}

	$message = censor($message, true);

	return nl2br($message);
}

function html_select($name, $values, $titles, $default = null)
{
	global $lang;

	$select = '<select name="' . $name . '" onchange="this.form.submit();">';

	foreach ($values as $i => $value)
	{
		$checked = ($value == $default) ? ' selected="selected"' : '';
		$select .= "<option value=\"$value\"$checked>" . htmlspecialchars($lang[$titles[$i]]) . '</option>';
	}

	$select .= '</select>';

	return $select;
}

function reputation_display($userdata, $is_auth, $for_post)
{
	global $lang, $thumb_up_img, $thumb_dn_img, $board_config, $phpEx;

	if ($is_auth['no_rep'])
	{
		return '';
	}
	if ($userdata['user_reputation'] || $userdata['user_reputation_plus'])
	{
		if ($for_post)
		{
			if ($is_auth['auth_view_rep'])
			{
				$user_reputation = '<a href="' . append_sid("profile.$phpEx?mode=reputation&amp;" . POST_USERS_URL . '=' . $userdata['user_id']) . '" title="' . $lang['reputation_search_reputation'] . '">' . $lang['Reputation'] . '</a>: ';
			}
			else
			{
				$user_reputation = $lang['Reputation'] . ': ';
			}
		}
		else
		{
			$user_reputation = '';
		}

		switch ($board_config['reputation_display'])
		{
			case REPUTATION_SUM:
				$user_reputation .= $userdata['user_reputation'];
				break;

			case REPUTATION_PLUSMINUS:
				$user_reputation .= ($userdata['user_reputation_plus'] - $userdata['user_reputation']) + $userdata['user_reputation_plus'] . ' ';
				if ($userdata['user_reputation_plus'])
				{
					if ($userdata['user_reputation_plus'] - $userdata['user_reputation'] == 0)
					{
						$user_reputation .= '(+' . $userdata['user_reputation_plus'] . '/-0)';
					} else {
						$user_reputation .= '(+' . $userdata['user_reputation_plus'];
					}
				}
				if ($reputation_minus = $userdata['user_reputation_plus'] - $userdata['user_reputation'])
				{
					$user_reputation .= ($userdata['user_reputation_plus'] ? '/' : '(+0/') . '-' . $reputation_minus . ')';
				}
				break;

			default:
				message_die(GENERAL_ERROR, 'Reputation config is damaged');
		}
	}
	else
	{
		$user_reputation = $for_post ? ($lang['Reputation'] . ': 0') : '0';
	}

	if ($is_auth['auth_add_rep'])
	{
		$url_param = $for_post ? (POST_POST_URL . '=' . $userdata['post_id']) : (POST_USERS_URL . '=' . $userdata['user_id']);

		$user_reputation .= '<br/>' . $lang['reputation_polezn'] . '<br/><a href="' . append_sid("reputation.$phpEx?mode=inc&amp;$url_param") . '">' . $lang['reputation_za'] . '</a>';

		if (!$board_config['reputation_positive_only'])
		{
			$user_reputation .= '  <a href="' . append_sid("reputation.$phpEx?mode=dec&amp;$url_param") . '">' . $lang['reputation_protiv'] . '</a>';
		}
	}
	return $user_reputation;
}

function cache_set($id, $data = null)
{
	global $phpbb_root_path;

	$cache_file = $phpbb_root_path . 'cache/' . $id;

	if ($data)
	{
		if ($f = fopen($cache_file, 'wb'))
		{
			fwrite($f, serialize($data));
			fclose($f);
			return true;
		}
		return false;
	}
	elseif (file_exists($cache_file))
	{
		@unlink($cache_file);
	}
}

function cache_get($id)
{
	global $phpbb_root_path;

	$cache_file = $phpbb_root_path . 'cache/' . $id;

	if (is_readable($cache_file) && ($f = fopen($cache_file, 'rb')))
	{
		if ($data = fread($f, filesize($cache_file)))
		{
			$data = unserialize($data);

		}
		fclose($f);
		return $data;
	}

	return false;
}

function reputation_auth($auth_or_forum_id, $userdata, $subject = null, $quick = false)
{
	global $board_config, $reputation_auth_keys;

	if (is_array($auth_or_forum_id))
	{
		$auth = $auth_or_forum_id;
	}
	elseif ($auth_or_forum_id == NO_ID)
	{
		$auth = array('auth_read' => true, 'auth_view' => true, 'auth_mod' => ($userdata['user_level'] == MOD || $userdata['user_level'] == ADMIN));
	}
	else
	{
		$auth = auth(AUTH_ALL, $auth_or_forum_id, $userdata);
	}

	if ($auth_or_forum_id == AUTH_LIST_ALL)
	{
		$auth[NO_ID] = NO_ID;

		foreach ($auth as $forum_id => $forum_auth)
		{
			$auth[$forum_id] = reputation_auth($forum_auth, $userdata, $subject, $quick);
		}

		return $auth;
	}

	if (!isset($auth['auth_add_rep']))
	{
		$admin = ($userdata['user_id'] != ANONYMOUS) && ($userdata['user_level'] == ADMIN);
		$moder = ($userdata['user_id'] != ANONYMOUS) && ($userdata['user_level'] == MOD);
		$guest = $auth['auth_read'] && $auth['auth_view'];
		$reg = ($userdata['user_id'] != ANONYMOUS) && $guest;

		foreach ($reputation_auth_keys as $i => $key)
		{
			switch ($board_config['reputation_perms'][$i])
			{
				case AUTH_ADMIN:
					$auth[$key] = ($userdata['user_level'] == ADMIN);
					break;
				case AUTH_MOD:
					$auth[$key] = ($userdata['user_level'] == MOD || $userdata['user_level'] == ADMIN);
					break;
				case AUTH_REG:
					$auth[$key] = $reg;
					break;
				case AUTH_ALL:
					$auth[$key] = $guest;
					break;
				default:
					message_die(GENERAL_ERROR, 'Reputation config is damaged');
			}
		}
		if ($auth_or_forum_id == NO_ID)
		{
			$auth['auth_add_rep'] = $auth['auth_add_rep_nonpost'];
			$auth['auth_warn'] = $auth['auth_warn_nonpost'];
			$auth['auth_ban'] = $auth['auth_ban_nonpost'];
		}
	}

	if (!empty($subject))
	{
		reputation_auth_personal($auth, $userdata, $subject, $quick);
	}

	return $auth;
}

function reputation_check_limits($actor, $subject, $quick = false)
{
	global $db, $board_config, $lang, $current_time;

	if ($actor['user_posts'] < $board_config['reputation_posts_req'])
	{
		return $lang['reputation_limits_apply'];
	}
	if (($current_time - $actor['user_regdate']) < $board_config['reputation_days_req'] * 86400)
	{
		return $lang['reputation_limits_apply'];
	}
	if ($actor['user_warnings'] > $board_config['reputation_warnings_req'])
	{
		return $lang['reputation_limits_apply'];
	}
	if ($actor['user_reputation'] < $board_config['reputation_points_req'])
	{
		return $lang['reputation_limits_apply'];
	}

	if (!$quick && ($board_config['reputation_time_limit'] || $board_config['reputation_rotation_limit']))
	{
		$result = db_query('SELECT id, date FROM {REPUTATION_TABLE}
			WHERE voter_id = %d AND user_id = %d
				AND (modification = {REPUTATION_INC} OR modification = {REPUTATION_DEC})
			ORDER BY id DESC
			LIMIT 1', $actor['user_id'], empty($subject['voter_id']) ? $subject['user_id'] : $subject['voter_id']);
		if ($row = $db->sql_fetchrow($result))
		{
			if ($board_config['reputation_time_limit'] && ($current_time < $row['date'] + $board_config['reputation_time_limit'] * 60))
			{
				return sprintf($lang['reputation_time_limit'], $board_config['reputation_time_limit']);
			}
			if ($board_config['reputation_rotation_limit'])
			{
				$result = db_query('SELECT COUNT(*) AS cnt FROM {REPUTATION_TABLE}
					WHERE voter_id = %d AND id > %d
						AND (modification = {REPUTATION_INC} OR modification = {REPUTATION_DEC})
					GROUP BY voter_id',
					$actor['user_id'], $row['id']);

				if (!($row = $db->sql_fetchrow($result)) || $row['cnt'] < $board_config['reputation_rotation_limit'])
				{
					return sprintf($lang['reputation_rotation_limit'], $board_config['reputation_rotation_limit']);
				}
			}
		}
	}

	return false;
}

function reputation_auth_personal(&$auth, $actor, $subject, $quick)
{
	global $db, $lang, $board_config;

	static $cache = array();
	$subject_id = empty($subject['voter_id']) ? $subject['user_id'] : $subject['voter_id'];
	$pair_id = $actor['user_id'] . ',' . $subject_id;

	if (isset($cache[$pair_id]))
	{
		$fix = $cache[$pair_id];
	}
	else
	{
		$subject_is_mod = ($subject['user_level'] == ADMIN || $subject['user_level'] == MOD);

		$fix = array(
			'no_rep' => ($subject['user_id'] == ANONYMOUS || $subject['user_level'] == MOD && $board_config['reputation_mod_norep'] || $subject['user_level'] == ADMIN && $board_config['reputation_admin_norep']),
			'no_warn' => ($subject_id == ANONYMOUS || $subject_is_mod),
		);

		if ($subject_id == ANONYMOUS)
		{
			$fix['auth_view_rep'] = $fix['auth_add_rep'] = $fix['auth_warn'] = $lang['reputation_anonymous_no_reputation'];
		}
		elseif ($actor['user_id'] == $subject_id)
		{
			$fix['auth_add_rep'] = $fix['auth_warn'] = $fix['auth_ban'] = $lang['reputation_self_no_modify'];
		}
		else
		{
			if ($fix['no_rep'])
			{
				$fix['auth_view_rep'] = $fix['auth_add_rep'] = $lang['reputation_not_applicable'];
			}
			elseif (!$auth['auth_no_limits'])
			{
				if ($message = reputation_check_limits($actor, $subject, $quick))
				{
					$fix['auth_add_rep'] = $message;
				}
			}

			if ($subject_is_mod)
			{
				$fix['auth_view_warns'] = $fix['auth_warn'] = $fix['auth_ban'] = $lang['reputation_cant_warn_mods'];
			}

			if ($actor['user_level'] == MOD)
			{
				/*if ($subject_is_mod)
				{
					$fix['auth_edit_rep'] = $fix['auth_delete_rep'] = $lang['reputation_other_mods_no_edit'];
				}*/
			}
			elseif ($actor['user_level'] != ADMIN)
			{
				$fix['auth_edit_rep'] = $fix['auth_delete_rep'] = $lang['reputation_others_no_edit'];
			}
		}

		/*if ($subject_is_mod)
		{
			if ($actor['user_level'] == MOD)
			{
				$fix['auth_edit_warn'] = $fix['auth_delete_warn'] = false;
			}
		}*/

		$cache[$pair_id] = $fix;
	}

	foreach ($fix as $key => $value)
	{
		if (!empty($auth[$key]))
		{
			$auth[$key] = false;
			if ($value)
			{
				$auth[$key . '_msg'] = $value;
			}
		}
	}
	$auth['no_rep'] = $fix['no_rep']; $auth['no_warn'] = $fix['no_warn'];
}

function server_url()
{
	global $board_config, $phpEx;

	if ($script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path'])))
	{
		$script_name .= '/';
	}

	$server_port = ($board_config['server_port'] != 80) ? (':' . trim($board_config['server_port']) . '/') : '/';

	return ($board_config['cookie_secure'] ? 'https://' : 'http://') . trim($board_config['server_name']) . $server_port . $script_name;
}

$board_config['reputation_perms'] = explode(',', $board_config['reputation_perms']);
$board_config['reputation_warning_expire'] = $board_config['reputation_warning_expire'] ? explode(',', $board_config['reputation_warning_expire']) : array();
$board_config['reputation_ban_expire'] = $board_config['reputation_ban_expire'] ? explode(',', $board_config['reputation_ban_expire']) : array();

?>