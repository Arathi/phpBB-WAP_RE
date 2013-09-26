<?php
/**************************************************
 *		admin_democracy.php
 *		-------------------
 *		Разработка: Carbofos.
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：评价系统的后台管理
 **************************************************/

define('IN_PHPBB', true);

if (!empty($setmodules))
{
	$filename = basename(__FILE__);
	$module['General'][$lang['reputation_democracy']] = $filename;
	return;
}

$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'includes/functions_reputation.' . $phpEx);

define('REPUTATION_MIN', -100000);
define('WARNINGS_MAX', 100000);

$mode = isset($HTTP_GET_VARS['mode']) ? $HTTP_GET_VARS['mode'] : '';
$mode = htmlspecialchars($mode);

if (isset($HTTP_POST_VARS['resync']))
{
	db_query('UPDATE {USERS_TABLE} SET user_reputation = 0, user_reputation_plus = 0, user_warnings = 0');

	$result = db_query('SELECT user_id, SUM(modification = {REPUTATION_INC}) AS reputation_plus, SUM(modification = {REPUTATION_DEC}) AS reputation_minus, SUM(modification = {REPUTATION_WARNING} OR modification = {REPUTATION_BAN}) AS warnings
		FROM {REPUTATION_TABLE}
		WHERE modification IN({REPUTATION_INC}, {REPUTATION_DEC}, {REPUTATION_WARNING}, {REPUTATION_BAN})
		GROUP BY user_id');

	while ($row = $db->sql_fetchrow($result))
	{
		$reputation = $row['reputation_plus'] - $row['reputation_minus'];
		db_query('UPDATE {USERS_TABLE} SET user_reputation = %d, user_reputation_plus = %d, user_warnings = %d
			WHERE user_id = %d',
			$reputation, $row['reputation_plus'], $row['warnings'], $row['user_id']);
	}

	db_query('UPDATE {POSTS_TABLE} SET post_reviews = 0');

	$result = db_query('SELECT post_id, COUNT(*) AS reviews
		FROM {REPUTATION_TABLE}
		WHERE modification IN({REPUTATION_INC}, {REPUTATION_DEC})
		GROUP BY post_id');

	while ($row = $db->sql_fetchrow($result))
	{
		db_query('UPDATE {POSTS_TABLE} SET post_reviews = %d WHERE post_id = %d', $row['reviews'], $row['post_id']);
	}

	$result = db_query('SELECT r.id, r.forum_id AS review_forum_id, p.post_id, p.forum_id AS post_forum_id
		FROM {REPUTATION_TABLE} r LEFT JOIN {POSTS_TABLE} p ON r.post_id = p.post_id
		WHERE p.forum_id <> r.forum_id OR (r.post_id <> {NO_ID} AND p.post_id IS NULL)');

	$no_post = ''; $wrong_forum = array();

	while ($row = $db->sql_fetchrow($result))
	{
		if (!$row['post_id'])
		{
			$no_post .= ($no_post ? ',' : '') . $row['id'];
		}
		elseif ($row['review_forum_id'] != $row['post_forum_id'])
		{
			if (isset($wrong_forum[$row['post_forum_id']]))
			{
				$wrong_forum[$row['post_forum_id']] .= ',' . $row['id'];
			}
			else
			{
				$wrong_forum[$row['post_forum_id']] = $row['id'];
			}
		}
	}

	if ($no_post)
	{
		db_query('UPDATE {REPUTATION_TABLE} SET post_id = {NO_ID} WHERE id IN(%s)', $no_post);
	}
	foreach ($wrong_forum as $forum_id => $review_ids)
	{
		db_query('UPDATE {REPUTATION_TABLE} SET forum_id = %d WHERE id IN(%s)', $forum_id, $review_ids);
	}

	$links = sprintf($lang['Click_return_reputation_index'], '<a href="' . append_sid("admin_democracy.$phpEx") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');
	$message = '<br />' . $lang['reputation_success'] . '<br /><br />' . $links . '<br /><br />';
 	message_die(GENERAL_MESSAGE, $message);
}

if (!isset($HTTP_POST_VARS['confirm']))
{
	$template->set_filenames(array(
		"body" => 'admin/reputation_body.tpl')
	);

	$auth_idx = array(AUTH_ALL => 3, AUTH_REG => 0, AUTH_MOD => 1, AUTH_ADMIN => 2);

	$reputation_auth = array();
	foreach ($reputation_auth_keys as $i => $key)
	{
		$reputation_auth[$key] = $auth_idx[$board_config['reputation_perms'][$i]];
	}

	$prepare_keys = array('reputation_days_req', 'reputation_posts_req', 'reputation_warnings_req', 'reputation_points_req', 'reputation_time_limit', 'reputation_rotation_limit', 'reputation_most_respected', 'reputation_least_respected', 'reputation_fixed', 'reputation_modifiable', 'reputation_delete_days', 'reputation_ban_warnings', 'reputation_check_rate');
	foreach ($prepare_keys as $key)
	{
		foreach (explode('%s', $lang[$key]) as $i => $part)
		{
			$lang[$key . '_' . $i] = $part;
		}
	}

	function decode_expiration($expiration)
	{
		$mode = count($expiration);
		if ($mode == 2)
		{
			$min = empty($expiration[0]) ? '' : $expiration[0];
			$max = empty($expiration[1]) ? '' : $expiration[1];
		}
		else
		{
			$min = empty($expiration[0]) ? 3 : $expiration[0];
			$max = $min + 7;
		}
		return array($mode, $min, $max);
	}

	list($warn_expire_mode, $warn_expire_min, $warn_expire_max) = decode_expiration($board_config['reputation_warning_expire']);
	list($ban_expire_mode, $ban_expire_min, $ban_expire_max) = decode_expiration($board_config['reputation_ban_expire']);
	$delete_expired = $board_config['reputation_delete_expired'] != -1;

	$template->assign_vars(array(
		'L_DEMOCRACY' => $lang['reputation_democracy'],
		'L_DEMOCRACY_EXP' => $lang['reputation_democracy_exp'],
		'L_CHECK_CONFIRM' => $lang['reputation_check_confirm'],
		'L_SUBMIT' => $lang['Submit'],
		'L_RESET' => $lang['Reset'],
		'L_CONFIRM' => $lang['Confirm'],
		'L_USER' => $lang['Forum_REG'],
		'L_MODERATOR' => $lang['Forum_MOD'],
		'L_ADMIN' => $lang['Forum_ADMIN'],

		'L_ENABLE_REPUTATION' => $lang['reputation_enable'],
		'L_ENABLE_WARNINGS' => $lang['reputation_enable_warnings'],
		'L_ENABLE_REPORTS' => $lang['reports_enabled'],
		'L_REPUTATION_OPTIONS' => $lang['reputation_reputation_options'],
		'L_WARNINGS_OPTIONS' => $lang['reputation_warnings_options'],
		'L_REPORTS_OPTIONS' => $lang['reputation_reports_options'],
		'L_POSITIVE_ONLY' => $lang['reputation_positive_only'],
		'L_POSITIVE_ONLY_EXP' => $lang['reputation_positive_only_exp'],
		'L_EMPTY_REVIEWS' => $lang['reputation_empty_reviews'],
		'L_ACCESS_RIGHTS' => $lang['reputation_access_rights'],
		'L_ADD_REP' => $lang['reputation_add_rep'],
		'L_ADD_REP_NONPOST' => $lang['reputation_add_rep_nonpost'],
		'L_EDIT_REP' => $lang['reputation_edit_rep'],
		'L_DELETE_REP' => $lang['reputation_delete_rep'],
		'L_NO_LIMITS' => $lang['reputation_no_limits'],
		'L_WARN' => $lang['reputation_warn'],
		'L_WARN_NONPOST' => $lang['reputation_warn_nonpost'],
		'L_BAN' => $lang['reputation_ban'],
		'L_BAN_NONPOST' => $lang['reputation_ban_nonpost'],
		'L_EDIT_WARNS' => $lang['reputation_edit_warns'],
		'L_DELETE_WARNS' => $lang['reputation_delete_warns'],
		'L_NOT_APPLICABLE' => $lang['reputation_not_applicable'],
		'L_ANONYMOUS_VIEW_REP' => $lang['reputation_anonymous_view_rep'],
		'L_ANONYMOUS_VIEW_WARNS' => $lang['reputation_anonymous_view_warns'],
		'L_REPUTATION_PERMS_NOTE' => $lang['reputation_perms_notes'],
		'L_WARNINGS_PERMS_NOTE' => $lang['reputation_warn_perms_notes'],

		'L_DAYS_REQ_0' => $lang['reputation_days_req_0'],
		'L_DAYS_REQ_1' => $lang['reputation_days_req_1'],
		'L_POSTS_REQ_0' => $lang['reputation_posts_req_0'],
		'L_POSTS_REQ_1' => $lang['reputation_posts_req_1'],
		'L_WARNINGS_REQ_0' => $lang['reputation_warnings_req_0'],
		'L_WARNINGS_REQ_1' => $lang['reputation_warnings_req_1'],
		'L_REPUTATION_REQ_0' => $lang['reputation_points_req_0'],
		'L_REPUTATION_REQ_1' => $lang['reputation_points_req_1'],
		'L_TIME_LIMIT_0' => $lang['reputation_time_limit_0'],
		'L_TIME_LIMIT_1' => $lang['reputation_time_limit_1'],
		'L_ROTATION_LIMIT_0' => $lang['reputation_rotation_limit_0'],
		'L_ROTATION_LIMIT_1' => $lang['reputation_rotation_limit_1'],
		'L_ROTATION_LIMIT_EXP' => $lang['reputation_rotation_limit_exp'],
		'L_MOST_RESPECTED_0' => $lang['reputation_most_respected_0'],
		'L_MOST_RESPECTED_1' => $lang['reputation_most_respected_1'],
		'L_LEAST_RESPECTED_0' => $lang['reputation_least_respected_0'],
		'L_LEAST_RESPECTED_1' => $lang['reputation_least_respected_1'],
		'L_REPUTATION_DISPLAY' => $lang['reputation_display'],
		'L_DISPLAY_SUM' => $lang['reputation_display_sum'],
		'L_DISPLAY_PLUSMINUS' => $lang['reputation_display_plusminus'],

		'L_INFINITE' => $lang['reputation_infinite'],
		'L_INFINITE_EXP' => $lang['reputation_infinite_exp'],
		'L_INFINITE_BAN_EXP' => $lang['reputation_infinite_ban_exp'],
		'L_FIXED_0' => $lang['reputation_fixed_0'],
		'L_FIXED_1' => $lang['reputation_fixed_1'],
		'L_MODIFIABLE_0' => $lang['reputation_modifiable_0'],
		'L_MODIFIABLE_1' => $lang['reputation_modifiable_1'],
		'L_MODIFIABLE_2' => $lang['reputation_modifiable_2'],
		'L_MODIFIABLE_EXP' => $lang['reputation_modifiable_exp'],
		'L_STORE' => $lang['reputation_store'],
		'L_DELETE_DAYS_0' => $lang['reputation_delete_days_0'],
		'L_DELETE_DAYS_1' => $lang['reputation_delete_days_1'],
		'L_BAN_WARNINGS_0' => $lang['reputation_ban_warnings_0'],
		'L_BAN_WARNINGS_1' => $lang['reputation_ban_warnings_1'],
		'L_BAN_WARNINGS_EXP' => $lang['reputation_ban_warnings_exp'],
		'L_CHECK_RATE_0' => $lang['reputation_check_rate_0'],
		'L_CHECK_RATE_1' => $lang['reputation_check_rate_1'],
		'L_CHECK_RATE_EXP' => $lang['reputation_check_rate_exp'],
		'L_REPORTS_COLOR' => $lang['reputation_reports_color'],
		'L_REPORTS_COLOR_EXP' => $lang['reputation_reports_color_exp'],

		'L_EXPIRED_WARNINGS' => $lang['reputation_expired_warnings'],
		'L_WARNING_EXPIRY' => $lang['reputation_warning_expiry'],
		'L_BAN_EXPIRY' => $lang['reputation_ban_expiry'],
		'L_INDEX_PAGE' => $lang['reputation_index_page'],
		'L_PREREQUIREMENTS' => $lang['reputation_prerequirements'],
		'L_LIMITS' => $lang['reputation_limits'],

		'L_MAINTENANCE' =>  $lang['reputation_maintenance'],
		'L_RESYNC' =>  $lang['reputation_resync'],
		'L_RESYNC_EXP' =>  $lang['reputation_resync_exp'],

		'S_REPUTATION_CHECKED' => $board_config['reputation_enabled'] ? 'checked="checked" ' : '',
		'S_POSITIVE_ONLY' => $board_config['reputation_positive_only'] ? 'checked="checked" ' : '',
		'S_EMPTY_REVIEWS' => $board_config['reputation_empty_reviews'] ? 'checked="checked" ' : '',
		'S_WARNINGS_CHECKED' => $board_config['warnings_enabled'] ? 'checked="checked" ' : '',
		'S_REPORTS_CHECKED' => $board_config['reports_enabled'] ? 'checked="checked" ' : '',
		'S_DAYS_REQ' => $board_config['reputation_days_req'],
		'S_POSTS_REQ' => $board_config['reputation_posts_req'],
		'S_WARNINGS_REQ' => ($board_config['reputation_warnings_req'] != WARNINGS_MAX) ? $board_config['reputation_warnings_req'] : '0',
		'S_REPUTATION_REQ' => ($board_config['reputation_points_req'] != REPUTATION_MIN) ? $board_config['reputation_points_req'] : '7',
		'S_TIME_LIMIT' => $board_config['reputation_time_limit'],
		'S_ROTATION_LIMIT' => $board_config['reputation_rotation_limit'],
		'S_ADD_REP_CHECKED_' . $reputation_auth['auth_add_rep'] => 'checked="checked" ',
		'S_ADD_REP_NONPOST_CHECKED_' . $reputation_auth['auth_add_rep_nonpost'] => 'checked="checked" ',
		'S_EDIT_REP_CHECKED_' . $reputation_auth['auth_edit_rep'] => 'checked="checked" ',
		'S_DELETE_REP_CHECKED_' . $reputation_auth['auth_delete_rep'] => 'checked="checked" ',
		'S_NO_LIMITS_CHECKED_' . $reputation_auth['auth_no_limits'] => 'checked="checked" ',
		'S_WARN_CHECKED_' . $reputation_auth['auth_warn'] => 'checked="checked" ',
		'S_WARN_NONPOST_CHECKED_' . $reputation_auth['auth_warn_nonpost'] => 'checked="checked" ',
		'S_BAN_CHECKED_' . $reputation_auth['auth_ban'] => 'checked="checked" ',
		'S_BAN_NONPOST_CHECKED_' . $reputation_auth['auth_ban_nonpost'] => 'checked="checked" ',
		'S_EDIT_WARN_CHECKED_' . $reputation_auth['auth_edit_warn'] => 'checked="checked" ',
		'S_DELETE_WARN_CHECKED_' . $reputation_auth['auth_delete_warn'] => 'checked="checked" ',
		'S_REPUTATION_ACTION' => append_sid("admin_democracy.$phpEx"),
		'S_DELETE_EXPIRED_CHECKED_' . ($delete_expired ? '1' : '0') => 'checked="checked" ',
		'S_DELETE_EXPIRED_DAYS' => ($delete_expired ? $board_config['reputation_delete_expired'] : '0'),
		'S_EXPIRE_MODE_' . $warn_expire_mode => 'checked="checked" ',
		'S_EXPIRE_FIXED' => $warn_expire_min,
		'S_EXPIRE_MIN' => $warn_expire_min,
		'S_EXPIRE_MAX' => $warn_expire_max,
		'S_BAN_EXPIRE_MODE_' . $ban_expire_mode => 'checked="checked" ',
		'S_BAN_EXPIRE_FIXED' => $ban_expire_min,
		'S_BAN_EXPIRE_MIN' => $ban_expire_min,
		'S_BAN_EXPIRE_MAX' => $ban_expire_max,
		'S_BAN_WARNINGS' => $board_config['reputation_ban_warnings'],
		'S_BAN_WARNINGS_CHECKED' => $board_config['reputation_ban_warnings'] ? 'checked="checked" ' : '',
		'S_MOST_RESPECTED' => $board_config['reputation_most_respected'],
		'S_LEAST_RESPECTED' => $board_config['reputation_least_respected'],
		'S_MOD_NOREP_CHECKED' => $board_config['reputation_mod_norep'] ? 'checked="checked" ' : '',
		'S_ADMIN_NOREP_CHECKED' => $board_config['reputation_admin_norep'] ? 'checked="checked" ' : '',
		'S_ANONYMOUS_VIEW_REP_CHECKED' => $reputation_auth['auth_view_rep'] ? 'checked="checked" ' : '',
		'S_ANONYMOUS_VIEW_WARNS_CHECKED' => $reputation_auth['auth_view_warns'] ? 'checked="checked" ' : '',
		'S_ENABLE_DAYS_REQ' => $board_config['reputation_days_req'] ? 'checked="checked" ' : '',
		'S_ENABLE_POSTS_REQ' => $board_config['reputation_posts_req'] ? 'checked="checked" ' : '',
		'S_ENABLE_WARNINGS_REQ' => ($board_config['reputation_warnings_req'] != WARNINGS_MAX) ? 'checked="checked" ' : '',
		'S_ENABLE_REPUTATION_REQ' => ($board_config['reputation_points_req'] != REPUTATION_MIN) ? 'checked="checked" ' : '',
		'S_ENABLE_TIME_LIMIT' => $board_config['reputation_time_limit'] ? 'checked="checked" ' : '',
		'S_ENABLE_ROTATION_LIMIT' => $board_config['reputation_rotation_limit'] ? 'checked="checked" ' : '',
		'S_ENABLE_MOST_RESPECTED' => $board_config['reputation_most_respected'] ? 'checked="checked" ' : '',
		'S_ENABLE_LEAST_RESPECTED' => $board_config['reputation_least_respected'] ? 'checked="checked" ' : '',
		'S_CHECK_RATE' => $board_config['reputation_check_rate'],
		'S_REPORTS_COLOR' => $board_config['reputation_reports_color'],
		'S_REPUTATION_DISPLAY_' . $board_config['reputation_display'] => 'checked="checked" ',

		'AUTH_REG' => AUTH_REG,
		'AUTH_MOD' => AUTH_MOD,
		'AUTH_ADMIN' => AUTH_ADMIN,

		'REPUTATION_SUM' => REPUTATION_SUM,
		'REPUTATION_PLUSMINUS' => REPUTATION_PLUSMINUS,
	));

	$template->pparse("body");

	include('./page_footer_admin.'.$phpEx);
}
else
{
	function positive($val)
	{
		$val = intval($val);
		return $val > 0 ? $val : 0;
	}
	function expiration($prefix)
	{
		global $HTTP_POST_VARS;

		switch ($HTTP_POST_VARS[$prefix . '_mode'])
		{
			case '1':
				return positive($HTTP_POST_VARS[$prefix . '_fixed']);
			case '2':
				return positive($HTTP_POST_VARS[$prefix . '_min']) . ',' . positive($HTTP_POST_VARS[$prefix . '_max']);
			default: // case '0':
				return '';
		}
	}

	$new_config = array(
		'reputation_enabled' => intval(isset($HTTP_POST_VARS['reputation'])),
		'warnings_enabled' => intval(isset($HTTP_POST_VARS['warnings'])),
		'reports_enabled' => intval(isset($HTTP_POST_VARS['reports'])),
		'reputation_positive_only' => intval(isset($HTTP_POST_VARS['positive_only'])),
		'reputation_empty_reviews' => intval(isset($HTTP_POST_VARS['empty_reviews'])),
		'reputation_admin_norep' => intval(isset($HTTP_POST_VARS['admin_norep'])),
		'reputation_mod_norep' => intval(isset($HTTP_POST_VARS['mod_norep'])),

		'reputation_days_req' => isset($HTTP_POST_VARS['enable_days_req']) ? positive($HTTP_POST_VARS['days_req']) : '0',
		'reputation_posts_req' => isset($HTTP_POST_VARS['enable_posts_req']) ? positive($HTTP_POST_VARS['posts_req']) : '0',
		'reputation_warnings_req' => isset($HTTP_POST_VARS['enable_warnings_req']) ? positive($HTTP_POST_VARS['warnings_req']) : WARNINGS_MAX,
		'reputation_points_req' => isset($HTTP_POST_VARS['enable_reputation_req']) ? intval($HTTP_POST_VARS['reputation_req']) : REPUTATION_MIN,
		'reputation_time_limit' => isset($HTTP_POST_VARS['enable_time_limit']) ? positive($HTTP_POST_VARS['time_limit']) : '0',
		'reputation_rotation_limit' => isset($HTTP_POST_VARS['enable_rotation_limit']) ? positive($HTTP_POST_VARS['rotation_limit']) : '0',
		'reputation_most_respected' => isset($HTTP_POST_VARS['enable_most_respected']) ? positive($HTTP_POST_VARS['most_respected']) : '0',
		'reputation_least_respected' => isset($HTTP_POST_VARS['enable_least_respected']) ? positive($HTTP_POST_VARS['least_respected']) : '0',
		'reputation_ban_warnings' => isset($HTTP_POST_VARS['enable_ban_warnings']) ? positive($HTTP_POST_VARS['ban_warnings']) : '0',

		'reputation_display' => intval($HTTP_POST_VARS['reputation_display']),

		'reputation_delete_expired' => !empty($HTTP_POST_VARS['enable_delete_expired']) ? positive($HTTP_POST_VARS['delete_expired']) : '-1',

		'reputation_check_rate' => positive($HTTP_POST_VARS['check_rate']),
		'reputation_reports_color' => trim($HTTP_POST_VARS['reports_color']),

		'reputation_warning_expire' => expiration('expire'),
		'reputation_ban_expire' => expiration('ban_expire'),
	);

	$reputation_auth = array(
		isset($HTTP_POST_VARS['anonymous_view_rep']) ? AUTH_ALL : AUTH_REG,
		isset($HTTP_POST_VARS['anonymous_view_warns']) ? AUTH_ALL : AUTH_REG,
	);
	for ($i = 2; $i < count($reputation_auth_keys); ++$i)
	{
		$key = $reputation_auth_keys[$i];
		$reputation_auth[] = isset($HTTP_POST_VARS[$key]) ? intval($HTTP_POST_VARS[$key]) : AUTH_ADMIN;
	}
	$new_config['reputation_perms'] = implode(',', $reputation_auth);

	foreach ($new_config as $name => $value)
	{
		db_query("UPDATE {CONFIG_TABLE} SET config_value = '%s' WHERE config_name = '%s'", $value, $name);
	}

	cache_set(RESPECTED_CACHE); 

	$links = sprintf($lang['Click_return_reputation_index'], '<a href="' . append_sid("admin_democracy.$phpEx") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');
	$message = '<br />' . $lang['Config_updated'] . '<br /><br />' . $links . '<br /><br />';
 	message_die(GENERAL_MESSAGE, $message);
}

?>