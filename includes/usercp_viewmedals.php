<?php
/**********************************
 *		usercp_viewmedals.php
 *		-------------------
 *      Разработка: Гутник Игорь
 *			2010 год
 *		简体中文：爱疯的云
 **********************************/
 
if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
$profiledata = get_userdata($HTTP_GET_VARS[POST_USERS_URL]);
if (!$profiledata)
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

$page_title = $lang['Medals'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'profile_view_medals.tpl')
);

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
}

for ($i = 0; $i < count($category_rows); $i++)
{
	$cat_id = $category_rows[$i]['cat_id'];

	$number = $i +1;
	$row_class = ( !($i % 2) ) ? 'row_hard' : 'row_easy';
	
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
		$medal_time = '<b>' . $lang['Medal_time'] . '</b>:';
		$medal_reason = '<b>' . $lang['Medal_reason'] . '</b>:';
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
		$idrow = 0;

		while (list($medal_name, $data) = @each($rowset))
		{
			if ( $cat_id == $data['cat_id'] ) { $display_medal = 1; }

			if ( !empty($display_medal) )
			{

				$template->assign_block_vars('details', array(
					'NUMBER'				=> $number,
					'ROW_CLASS' 			=> $row_class,
					'MEDAL_CAT' 			=> $data['cat_title'],
					'MEDAL_NAME' 			=> $medal_name,
					'MEDAL_DESCRIPTION' 	=> $data['medal_description'],
					'MEDAL_IMAGE' 			=> '<img src="'. $phpbb_root_path . $data['medal_image'] . '" alt="" />',
					'MEDAL_ISSUE' 			=> $data['medal_issue'],
					'MEDAL_COUNT' 			=> $data['medal_count'],
					'L_MEDAL_DESCRIPTION' 	=> $lang['Medal_description'])
				);
				$display_medal = 0;
			}
		}
	}
}

$template->assign_vars(array(
	'U_BACK_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $profiledata['user_id']),
	'USERNAME' 			=> $profiledata['username'])
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>