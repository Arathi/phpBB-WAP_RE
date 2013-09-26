<?php
/***************************************************
 *		usercp_viewprofile.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}
if ( isset($HTTP_GET_VARS[POST_USERNAME_URL]))
{
if ( empty($HTTP_GET_VARS[POST_USERNAME_URL]))
{
$HTTP_GET_VARS[POST_USERNAME_URL] = NULL;
}
}else
{
$HTTP_GET_VARS[POST_USERNAME_URL] = NULL;
}
if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
$profiledata = get_userdata($HTTP_GET_VARS[POST_USERS_URL],urldecode($postuser=$HTTP_GET_VARS[POST_USERNAME_URL]));

if (!$profiledata)
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

$sql = "SELECT cat_id, cat_title
	FROM " . MEDAL_CAT_TABLE . "
	ORDER BY cat_order";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query medal categories list', '', __LINE__, __FILE__, $sql);
}

$category_rows = array();
while ( $row = $db->sql_fetchrow($result) )
{
	$category_rows[] = $row;
}
$db->sql_freeresult($result);

$sql = "SELECT m.medal_id, mu.user_id
	FROM " . MEDAL_TABLE . " m, " . MEDAL_USER_TABLE . " mu
	WHERE mu.user_id = '" . $profiledata['user_id'] . "'
	AND m.medal_id = mu.medal_id
	ORDER BY m.medal_name";
	
if($result = $db->sql_query($sql))
{
	$medal_list = $db->sql_fetchrowset($result);
	$medal_count = count($medal_list);

	if ( $medal_count )
	{
		$template->assign_block_vars('switch_display_medal', array());
		$template->assign_block_vars('switch_display_medal.medal', array(
			'MEDAL_BUTTON' => append_sid("profile.$phpEx?mode=medals&amp;" . POST_USERS_URL . "=" . $profiledata['user_id']))
		);
	}
}

for ($i = 0; $i < count($category_rows); $i++)
{
	$cat_id = $category_rows[$i]['cat_id'];

	$sql = "SELECT m.medal_id, m.medal_name,m.medal_description, m.medal_image, m.cat_id, mu.issue_reason, mu.issue_time, c.cat_id, c.cat_title
		FROM " . MEDAL_TABLE . " m, " . MEDAL_USER_TABLE . " mu, " . MEDAL_CAT_TABLE . " c
		WHERE mu.user_id = '" . $profiledata['user_id'] . "'
		AND m.cat_id = c.cat_id
		AND m.medal_id = mu.medal_id
		ORDER BY c.cat_order, m.medal_name, mu.issue_time";

	if ($result = $db->sql_query($sql))
	{
		$row = array();
		$rowset = array();
		$medal_time = $lang['Medal_time'] . ': ';
		$medal_reason = $lang['Medal_reason'] . ': ';
		while ($row = $db->sql_fetchrow($result))
		{
			if (empty($rowset[$row['medal_name']]))
			{
				$rowset[$row['medal_name']]['cat_id'] = $row['cat_id'];
				$rowset[$row['medal_name']]['cat_title'] = $row['cat_title'];
				$rowset[$row['medal_name']]['medal_description'] .= $row['medal_description'];
				$rowset[$row['medal_name']]['medal_image'] = $row['medal_image'];
				$row['issue_reason'] = ( $row['issue_reason'] ) ? $row['issue_reason'] : $lang['Medal_no_reason'];
				$rowset[$row['medal_name']]['medal_issue'] = $medal_time . create_date($board_config['default_dateformat'], $row['issue_time'], $board_config['board_timezone']) . '<br/>' . $medal_reason . $row['issue_reason'] . '<br/>';
				$rowset[$row['medal_name']]['medal_count'] = '1';
			}
			else
			{
				$row['issue_reason'] = ( $row['issue_reason'] ) ? $row['issue_reason'] : $lang['Medal_no_reason'];
				$rowset[$row['medal_name']]['medal_issue'] .= $medal_time . create_date($board_config['default_dateformat'], $row['issue_time'], $board_config['board_timezone']) . '<br/>' . $medal_reason . $row['issue_reason'] . '<br/>';
				$rowset[$row['medal_name']]['medal_count'] += '1';
			}
		}

		$medal_name = array();
		$data = array();

		$display_medal = 0;

		while (list($medal_name, $data) = @each($rowset))
		{
			if ( $cat_id == $data['cat_id'] ) { $display_medal = 1; }

			if ( !empty($display_medal) )
			{
				$template->assign_block_vars('switch_display_medal.details', array(
					'MEDAL_CAT' 			=> $data['cat_title'],
					'MEDAL_NAME' 			=> $medal_name,
					'MEDAL_DESCRIPTION' 	=> $data['medal_description'],
					'MEDAL_IMAGE' 			=> '<img src="'. $phpbb_root_path . $data['medal_image'] . '" alt="" />',
					
					'MEDAL_ISSUE' 		=> $data['medal_issue'],
					'MEDAL_COUNT' 		=> $data['medal_count'],
					
					'L_MEDAL_DESCRIPTION' 	=> $lang['Medal_description'])
				);
				$display_medal = 0;
			}
		}
	}
}

$sql = 'SELECT count(*) AS total
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE user_id_1 = '.$profiledata['user_id'].' AND user_id_2 = 0';
$result = $db->sql_query($sql);
if (!$result)
{
	message_die(GENERAL_ERROR, 'Unable to get attachment information.', '', __LINE__, __FILE__, $sql);
}
$rowatt = $db->sql_fetchrow($result);
if ( $rowatt['total'] > 0 )
{
	$totalfiles = '<a href="' . append_sid("profile.$phpEx?mode=viewfiles&amp;" . POST_USERS_URL .'=' . $profiledata['user_id']) . '">' . $rowatt['total'] . '</a>';
} else {
	$totalfiles = 0;
}

$sql = "SELECT *
	FROM " . RANKS_TABLE . " 
	ORDER BY rank_special, rank_min";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain ranks information', '', __LINE__, __FILE__, $sql);
}

$ranksrow = array();
while ( $row = $db->sql_fetchrow($result) )
{
	$ranksrow[] = $row;
}
$db->sql_freeresult($result);

$regdate = $profiledata['user_regdate'];
$memberdays = max(1, round( ( time() - $regdate ) / 86400 ));
$posts_per_day = $profiledata['user_posts'] / $memberdays;

if ( $profiledata['user_posts'] != 0  )
{
	$total_posts = get_db_stat('postcount');
	$percentage = ( $total_posts ) ? min(100, ($profiledata['user_posts'] / $total_posts) * 100) : 0;
}
else
{
	$percentage = 0;
}

// 个性头像
$avatar_img = '';
if ( $profiledata['user_avatar_type'] && $profiledata['user_allowavatar'] )
{
	switch( $profiledata['user_avatar_type'] )
	{
		case USER_AVATAR_UPLOAD:
			$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
		case USER_AVATAR_REMOTE:
			$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
		case USER_AVATAR_GALLERY:
			$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
	}
}

$poster_rank = '';
$rank_image = '';
if ( !empty($profiledata['user_zvanie']) )
{
	$poster_rank = $profiledata['user_zvanie'];
} else {
	if ( $profiledata['user_rank'] )
	{
		for($i = 0; $i < count($ranksrow); $i++)
		{
			if ( $profiledata['user_rank'] == $ranksrow[$i]['rank_id'] && $ranksrow[$i]['rank_special'] )
			{
				$poster_rank = $ranksrow[$i]['rank_title'];
				$rank_image = ( $ranksrow[$i]['rank_image'] ) ? '<img src="' . $ranksrow[$i]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />' : '';
			}
		}
	}
	else
	{
		for($i = 0; $i < count($ranksrow); $i++)
		{
			if ( $profiledata['user_posts'] >= $ranksrow[$i]['rank_min'] && !$ranksrow[$i]['rank_special'] )
			{
				$poster_rank = $ranksrow[$i]['rank_title'];
				$rank_image = ( $ranksrow[$i]['rank_image'] ) ? '<img src="' . $ranksrow[$i]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />' : '';
			}
		}
	}
}

$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=" . $profiledata['user_id']);
$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

if ( ($userdata['session_logged_in'] && !empty($profiledata['user_viewemail'])) || $userdata['user_level'] == ADMIN )
{
	$template->assign_block_vars('email', array());
	$email_uri = ( $board_config['board_email_form'] ) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL .'=' . $profiledata['user_id']) : 'mailto:' . $profiledata['user_email'];
	$email = '<a href="' . $email_uri . '">' . $profiledata['user_email'] . '</a>';
}

if ( !empty($profiledata['user_website']) ) 
{ 
	$template->assign_block_vars('www', array());
	$www = '<a href="' . $profiledata['user_website'] . '">' . $profiledata['user_website'] . '</a>';
}
if ( !empty($profiledata['user_sig']) ) 
{ 
	$template->assign_block_vars('signature', array());
	$signature = $profiledata['user_sig'];
}
if ( !empty($profiledata['user_icq']) ) 
{ 
	$template->assign_block_vars('icq', array());
	$icq_status_img = '<a href="http://wwp.icq.com/' . $profiledata['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $profiledata['user_icq'] . '&img=5" width="18" height="18" border="0" /></a>';
	if ( $board_config['send_user_icq'] && $profiledata['user_icq_send'] )
	{
		$icq = '<a href="' . append_sid("profile.$phpEx?mode=sendicq&amp;" . POST_USERS_URL .'=' . $profiledata['user_id']) . '">' . $profiledata['user_icq'] . '</a>';
	} else {
		$icq = $profiledata['user_icq'];
	}
}

if ( !empty($profiledata['user_number']) ) 
{ 
	$template->assign_block_vars('number', array());
	$number = $profiledata['user_number'];
}

if ( !empty($profiledata['user_aim']) ) 
{ 
	$template->assign_block_vars('aim', array());
	$aim = $profiledata['user_aim'];
}

if ( !empty($profiledata['user_msnm']) ) 
{ 
	$template->assign_block_vars('msn', array());
	$msn = $profiledata['user_msnm'];
}

if ( !empty($profiledata['user_yim']) ) 
{ 
	$template->assign_block_vars('yim', array());
	$yim = $profiledata['user_yim'];
}

if ( !empty($profiledata['user_from']) ) 
{ 
	$template->assign_block_vars('from', array());
	$from = $profiledata['user_from'];
}

if ( !empty($profiledata['user_occ']) ) 
{ 
	$template->assign_block_vars('occ', array());
	$occ = $profiledata['user_occ'];
}

if ( !empty($profiledata['user_interests']) ) 
{ 
	$template->assign_block_vars('interests', array());
	$interests = $profiledata['user_interests'];
}

$temp_url = append_sid("search.$phpEx?search_author=" . urlencode($profiledata['username']) . "&amp;showresults=posts");
$search = '<a href="' . $temp_url . '">' . sprintf($lang['Search_user_posts'], $profiledata['username']) . '</a>';

if ($userdata['user_level'] == ADMIN && $profiledata['user_level'] == USER)
{
	if ($profiledata['user_active'] == 1)
	{
		$link_lock = '<br/>- <a href="' . append_sid("profile.$phpEx?mode=lock&amp;u=" . $profiledata['user_id']) . '">停用</a>';
	} elseif ($profiledata['user_active'] == 0) {
		$link_lock = '<br/>- <a href="' . append_sid("profile.$phpEx?mode=lock&amp;u=" . $profiledata['user_id']) . '">激活</a>';
	}
} else {
	$link_lock = '';
}

$this_year = create_date('Y', time(), $board_config['board_timezone']);
$this_date = create_date('md', time(), $board_config['board_timezone']);

if ( $profiledata['user_birthday'] != 999999 )
{ 
	$user_birthdate = realdate('md', $profiledata['user_birthday']);
	$i=0;
	while ($i<26)
	{
		if ($user_birthdate>=$zodiacdates[$n] && $user_birthdate<=$zodiacdates[$i+1])
		{
			$zodiac = $lang[$zodiacs[($i/2)]];
			$u_zodiac = $images[$zodiacs[($i/2)]];
			$i=26;
		} else
		{
			$i=$i+2;
		}
	}
	$template->assign_block_vars('birthday', array());
	$user_birthday = realdate($lang['DATE_FORMAT'], $profiledata['user_birthday']);
	$userbirthdate = '';
	// 显示用户年龄
	$userbdate = realdate('md', $profiledata['user_birthday']);
	$userbirthdate = $this_year - realdate ('Y',$profiledata['user_birthday']);
	if ( $this_date < $userbdate )
	{
		$userbirthdate--;
	}
	$userbirthdate = '&nbsp;（' . $userbirthdate . '岁）';
} 

if ( !empty($profiledata['user_gender'])) 
{ 
	$template->assign_block_vars('gender', array());
	switch ($profiledata['user_gender']) 
	{
		case 1: $gender=$lang['Male'];break; 
		case 2: $gender=$lang['Female'];break; 
		default:$gender=$lang['No_gender_specify']; 
	}
}
$page_title = $lang['Viewing_profile'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
display_upload_attach_box_limits($profiledata['user_id']);

$template->set_filenames(array(
	'body' => 'profile_view_body.tpl')
);

if (function_exists('get_html_translation_table'))
{
	$u_search_author = urlencode(strtr($profiledata['username'], array_flip(get_html_translation_table(HTML_ENTITIES))));
}
else
{
	$u_search_author = urlencode(str_replace(array('&amp;', '&#039;', '&quot;', '&lt;', '&gt;'), array('&', "'", '"', '<', '>'), $profiledata['username']));
}
if ($board_config['warnings_enabled'] || $board_config['reputation_enabled'])
{
	include($phpbb_root_path . 'includes/functions_reputation.' . $phpEx);

	$is_auth = reputation_auth(NO_ID, $userdata, $profiledata);

	if ($board_config['warnings_enabled'] && !$is_auth['no_warn'])
	{
		$warn_img = $warn = $ban_img = $ban = '';

		if ($is_auth['auth_warn'])
		{
			$temp_url = "reputation.$phpEx?mode=warning&amp;" . POST_USERS_URL . "=" . $profiledata['user_id'] . "&amp;sid=" . $userdata['session_id'];
			$warn_img = '<a href="' . $temp_url . '">' . $lang['reputation_warn_user'] . '</a><br/>';
			$warn = '<a href="' . $temp_url . '">' . $lang['reputation_warn'] . '</a>';
		}
		if ($is_auth['auth_ban'])
		{
			$temp_url = "reputation.$phpEx?mode=ban&amp;" . POST_USERS_URL . "=" . $profiledata['user_id'] . "&amp;sid=" . $userdata['session_id'];
			$ban_img = '<a href="' . $temp_url . '">' . $lang['reputation_ban_user'] . '</a><br/>';
			$ban = '<a href="' . $temp_url . '">' . $lang['reputation_ban'] . '</a>';
		}

		$template->assign_block_vars('warnings', array(
			'L_WARNINGS' 	=> $lang['Warnings'],
			'WARNINGS' 		=> $profiledata['user_warnings'],

			'RED' 			=> $ban,
			'RED_IMG' 		=> $ban_img,
			'YELLOW' 		=> $warn,
			'YELLOW_IMG' 	=> $warn_img,
		));

		if ($is_auth['auth_view_warns'] && $profiledata['user_warnings'])
		{
			$template->assign_block_vars('warnings.details', array(
				'U_SEARCH' => append_sid("profile.$phpEx?mode=warnings&amp;" . POST_USERS_URL . '=' . $profiledata['user_id']),
				'L_SEARCH' => sprintf($lang['reputation_search_warnings'], $profiledata['username']),
			));
		}
		if ($is_auth['auth_view_warns'])
		{
			$template->assign_block_vars('warnings.details_all', array(
				'U_SEARCH_ALL' => append_sid("profile.$phpEx?mode=expired&amp;" . POST_USERS_URL . '=' . $profiledata['user_id']),
			));
		}
	}
	if ($board_config['reputation_enabled'] && !$is_auth['no_rep'])
	{
		if ( ($board_config['reputation_display'] == REPUTATION_PLUSMINUS) && ((($profiledata['user_reputation_plus'] - $profiledata['user_reputation']) + $profiledata['user_reputation_plus']) > 0) )
		{
			$poloska = @round(($profiledata['user_reputation_plus'])/(($profiledata['user_reputation_plus'] - $profiledata['user_reputation']) + $profiledata['user_reputation_plus'])*100,0);
			$poloska = '<img src="images/rate.php?i=' . $poloska . '" alt=""><br/>';
		} else {
			$poloska = '';
		}
		$template->assign_block_vars('reputation', array(
			'L_REPUTATION' 	=> $lang['Reputation'],
			'POLOSKA' 		=> $poloska,
			
			'U_VIEW_REPUTATION' 	=> append_sid("profile.$phpEx?mode=reputation&amp;" . POST_USERS_URL . '=' . $profiledata['user_id']),
			'REPUTATION' 			=> reputation_display($profiledata, $is_auth, false),
		));

		if ($is_auth['auth_view_rep'] && ($profiledata['user_reputation'] || $profiledata['user_reputation_plus']))
		{
			$template->assign_block_vars('reputation.details', array(
				'U_SEARCH' 	=> append_sid("profile.$phpEx?mode=reputation&amp;" . POST_USERS_URL . '=' . $profiledata['user_id']),
				'L_SEARCH' 	=> sprintf($lang['reputation_search_reputation'], $profiledata['username']),
			));
		}
	}
}

$sql = "SELECT COUNT(topic_id) AS total 
	FROM " . TOPICS_TABLE . " 
	WHERE topic_poster = " . $profiledata['user_id'];
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain matched topics list', '', __LINE__, __FILE__, $sql);
}
$row = $db->sql_fetchrow($result);
$total_topics = $row['total'];

$sql = "SELECT u.user_id, u.username, u.user_level, g.group_id, g.group_name, g.group_single_user, ug.user_pending
	FROM " . USERS_TABLE . " u, " . GROUPS_TABLE . " g, " . USER_GROUP_TABLE . " ug 
	WHERE u.user_id = " . $profiledata['user_id'] . " AND ug.user_id = u.user_id AND g.group_id = ug.group_id AND ug.user_pending = 0";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "Couldn't obtain user/group information", "", __LINE__, __FILE__, $sql);
}
$ug_info = array();
while( $row = $db->sql_fetchrow($result) )
{
	$ug_info[] = $row;
}
$db->sql_freeresult($result);

$name = array();
$id = array();
for($i = 0; $i < count($ug_info); $i++)
{
	if( !$ug_info[$i]['group_single_user'] )
	{
		$name[] = $ug_info[$i]['group_name'];
		$id[] = intval($ug_info[$i]['group_id']);
	}
}

$t_usergroup_list = '';
if( count($name) )
{
	for($i = 0; $i < (count($ug_info) - 1); $i++)
	{
		if (!$ug_info[$i]['user_pending'])
		{
			$t_usergroup_list .= '- <a href="' . append_sid("groupcp.$phpEx?g=" . $id[$i]) . '">' . $name[$i] . '</a><br/>';
		}
	}
}

$user_delete = ( ($userdata['user_level'] == ADMIN) && ($profiledata['user_level'] == USER) ) ? '<br />- <a href="' . append_sid("profile.$phpEx?mode=delete&amp;u=" . $profiledata['user_id']) . '">删除该用户</a>' : '- 删除该用户';
$user_clone = ( $userdata['user_level'] == ADMIN ) ? '<br />- <a href="' . append_sid("profile.$phpEx?mode=clone&amp;u=" . $profiledata['user_id']) . '">查看类似的用户</a>' : '<br />- 查看类似的用户';
$user_edit = ( $userdata['user_level'] == ADMIN ) ? '<br />- <a href="' . append_sid("admin/admin_users.$phpEx?mode=edit&amp;". POST_USERS_URL. '='. $profiledata['user_id']. '&amp;sid='. $userdata['session_id']) . '">编辑Ta的资料</a>' : '<br />- 编辑Ta的资料';

// 银行
if ( empty($holding[$poster_id]) )
{
	$sql = "SELECT holding
		FROM " . BANK_TABLE . "
		WHERE user_id = '{$profiledata['user_id']}'";
	$result = $db->sql_query($sql);
	$bank_row = $db->sql_fetchrow($result);

	$bank_row['holding'] = ( empty($bank_row['holding']) ) ? '0' : $bank_row['holding'];
}

$template->assign_vars(array(
	'USERNAME' 			=> $profiledata['username'],
	'USER_ID'			=> $profiledata['user_id'],
	'USERGROUP' 		=> $t_usergroup_list,
	
	'JOINED' 			=> create_date($lang['DATE_FORMAT'], $profiledata['user_regdate'], $board_config['board_timezone']),
	'LASTVISIT' 		=> ( $profiledata['user_lastvisit'] != 0 ) ? create_date($lang['DATE_FORMAT'], $profiledata['user_lastvisit'], $board_config['board_timezone']) : '从来没有',
	
	'POSTER_RANK' 			=> $poster_rank,
	'RANK_IMAGE' 			=> $rank_image,
	'L_USER_MEDAL' 			=>$lang['Medals'],
	'USER_MEDAL_COUNT' 		=> $medal_count,
	'L_MEDAL_INFORMATION' 	=> $lang['Medal_Information'],
	'L_MEDAL_NAME' 			=> $lang['Medal_name'],
	'L_MEDAL_DETAIL' 		=> $lang['Medal_details'],
	'POSTS_PER_DAY' 		=> $posts_per_day,
	
	'POSTS' 		=> $profiledata['user_posts'],
	'TOPICS' 		=> $total_topics,
	'MONEY' 		=> $profiledata['user_points'],
	'MONEY_PAYMENT'	=> $profiledata['user_money_payment'],
	'MONEY_EARNED' 	=> $profiledata['user_money_earned'],
	'ATTACH' 		=> $totalfiles,
	'USER_DELETE' 	=> $user_delete,
	'USER_CLONE' 	=> $user_clone,
	'USER_LOCK' 	=> $link_lock,
	
	'U_ADMIN_PROFILE' 	=> $user_edit,
	'PERCENTAGE' 		=> $percentage . '%', 
	
	'POST_DAY_STATS' 		=> sprintf($lang['User_post_day_stats'], $posts_per_day), 
	'POST_PERCENT_STATS' 	=> sprintf($lang['User_post_pct_stats'], $percentage), 

	'SEARCH_IMG' 	=> $search_img,
	'SEARCH' 		=> $search,
	'PM_IMG' 		=> $pm_img,
	'PM' 			=> $pm,
	'EMAIL_IMG' 	=> $email_img,
	'EMAIL' 		=> $email,
	'WWW_IMG' 		=> $www_img,
	'WWW' 			=> $www,
	'SIGNATURE'		=> $signature,
	
	'ICQ_STATUS_IMG' 	=> $icq_status_img,
	'ICQ_IMG' 			=> $icq_img, 
	'ICQ' 				=> $icq, 
	'NUMBER' 			=> $number, 
	'AIM_IMG' 			=> $aim_img,
	'AIM' 				=> $aim,
	'MSN_IMG' 			=> $msn_img,
	'MSN' 				=> $msn,
	'YIM_IMG' 			=> $yim_img,
	'YIM' 				=> $yim,
	'BANK_GOLD' 		=> $bank_row['holding'],
	'L_POINTS_NAME' 	=> $board_config['points_name'],
	
	'LOCATION' 		=> $from,
	'OCCUPATION' 	=> $occ,
	'INTERESTS' 	=> $interests,
    'GENDER' 		=> $gender, 
	'BIRTHDAY' 		=> $user_birthday,
	'ZODIAC' 		=> $zodiac,
	'USER_AGE' 		=> $user_birthday,
	'U_ZODIAC' 		=> $u_zodiac,
	'AVATAR_IMG' 	=> $avatar_img,

	'L_USERNAME'			=> $lang['Username'],
	'L_VIEWING_PROFILE' 	=> sprintf($lang['Viewing_user_profile'], $profiledata['username']), 
	'L_ABOUT_USER' 			=> sprintf($lang['About_user'], $profiledata['username']), 
	'L_AVATAR' 				=> $lang['Avatar'], 
	'L_POSTER_RANK' 		=> $lang['Poster_rank'], 
	'L_JOINED' 				=> $lang['Joined'], 
	'L_TOTAL_POSTS' 		=> $lang['Total_posts'], 
	'L_SEARCH_USER_POSTS' 	=> sprintf($lang['Search_user_posts'], $profiledata['username']), 
	'L_CONTACT' 			=> $lang['Contact'],
	'L_EMAIL_ADDRESS' 		=> $lang['Email_address'],
	'L_EMAIL' 				=> $lang['Email'],
	'L_PM' 					=> $lang['Private_Message'],
	'L_ICQ_NUMBER' 			=> $lang['ICQ'],
	'L_NUMBER' 				=> $lang['Number'],
	'L_YAHOO' 				=> $lang['YIM'],
	'L_AIM' 				=> $lang['AIM'],
	'L_MESSENGER' 			=> $lang['MSNM'],
	'L_WEBSITE' 			=> $lang['Website'],
	'L_LOCATION' 			=> $lang['Location'],
	'L_OCCUPATION' 			=> $lang['Occupation'],
	'L_INTERESTS' 			=> $lang['Interests'],
    'L_GENDER' 				=> $lang['Gender'], 
	'L_BIRTHDAY' 			=> $lang['Birthday'],
	'L_BANK' 				=> $lang['bank'],
	'L_ZODIAC' 				=> $lang['Zodiac'],
	
	'U_EDITPROFILE' 		=> '<a href="'.append_sid('profile.'.$phpEx.'?mode=editprofile').'">'.$lang['Edit_Prorile_Reg'].'</a>',
	'U_SELECTSTYLE' 		=> '<a href="'.append_sid('profile.'.$phpEx.'?mode=selectstyle').'">'.$lang['Edit_Prorile_Style'].'</a>',
	'U_EDITCONFIG' 			=> '<a href="'.append_sid('profile.'.$phpEx.'?mode=editconfig').'">'.$lang['Edit_Prorile_Config'].'</a>',
	'U_EDITPROFILEINFO' 	=> '<a href="'.append_sid('profile.'.$phpEx.'?mode=editprofileinfo').'">'.$lang['Edit_Prorile_Info'].'</a>',

	'U_GUESTBOOK'			=> append_sid("profile.$phpEx?mode=guestbook&amp;u=" . $profiledata['user_id']),
	'U_SEARCH_USER' 		=> append_sid("search.$phpEx?search_author=" . $u_search_author),
	'U_SEARCH_USER_TOPICS' 	=> append_sid("search.$phpEx?search_author=" . $u_search_author . "&amp;mode=all_topics"),
	'U_MONEY_SEND' 			=> append_sid("profile.$phpEx?mode=money&amp;u=" . $profiledata['user_id']),
	'U_PERSONAL_GALLERY' 	=> append_sid("album.$phpEx?action=personal&amp;user_id=" . $profiledata['user_id']),
	'L_PERSONAL_GALLERY' 	=> sprintf($lang['Personal_Gallery_Of_User'], $profiledata['username']),

	'S_PROFILE_ACTION' 		=> append_sid("profile.$phpEx"))
);

if ( $board_config['pay_money'] )
{
	$template->assign_block_vars('pay_money', array() );
}
if ( $userdata['user_id'] == $profiledata['user_id'] )
{
	$template->assign_block_vars('editprofile', array() );
}
if ( $userdata['user_points'] > 0 && $userdata['user_id'] != $profiledata['user_id'] )
{
	$template->assign_block_vars('money', array() );
}
if ( $t_usergroup_list != '' )
{
	$template->assign_block_vars('usergroup', array() );
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>