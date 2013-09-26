<?php
/**************************************************
 *		admin_users_list.php
 *		-------------------
 *      Разработка: Smartor.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Users']['Admin_users_list'] = $filename;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

$users_per_page = 25;

if ( isset($HTTP_POST_VARS['start1']) )
{
$start1 = abs(intval($HTTP_POST_VARS['start1']));
$start = (($start1 - 1) * $board_config['topics_per_page']);
} else {
$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
$start = ($start < 0) ? 0 : $start;
}

if( isset($HTTP_POST_VARS['sort']) )
{
	$sort_method = $HTTP_POST_VARS['sort'];
}
else if( isset($HTTP_GET_VARS['sort']) )
{
	$sort_method = $HTTP_GET_VARS['sort'];
}
else
{
	$sort_method = 'user_id';
}

if( isset($HTTP_POST_VARS['order']) )
{
	$sort_order = $HTTP_POST_VARS['order'];
}
else if( isset($HTTP_GET_VARS['order']) )
{
	$sort_order = $HTTP_GET_VARS['order'];
}
else
{
	$sort_order = '';
}


$template->set_filenames(array(
	'body' => 'admin/admin_users_list_body.tpl')
);

$sql = "SELECT count(user_id) as total FROM ".USERS_TABLE." WHERE user_id > 0";
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not count users", "", __LINE__, __FILE__, $sql);
}
$row = $db->sql_fetchrow($result);
$total_users = $row['total'];

$template->assign_vars(array(
	'L_SELECT_SORT_METHOD' 		=> $lang['Select_sort_method'],
	'U_LIST_ACTION' 			=> append_sid("admin_users_list.$phpEx"),
	'L_SORT' 					=> $lang['Sort'],
	'L_ORDER' 					=> $lang['Order'],
	'L_SORT_DESCENDING' 		=> $lang['Sort_Descending'],
	'L_SORT_ASCENDING' 			=> $lang['Sort_Ascending'],
	'ID_SELECTED' 				=> ($sort_method == 'user_id') ? 'selected="selected"' : '',
	'USERNAME_SELECTED' 		=> ($sort_method == 'username') ? 'selected="selected"' : '',
	'POSTS_SELECTED' 			=> ($sort_method == 'user_posts') ? 'selected="selected"' : '',
	'LASTVISIT_SELECTED' 		=> ($sort_method == 'user_lastvisit') ? 'selected="selected"' : '',
	'ASC_SELECTED' 				=> ($sort_order != 'DESC') ? 'selected="selected"' : '',
	'DESC_SELECTED' 			=> ($sort_order == 'DESC') ? 'selected="selected"' : '',
	'TOTAL_USERS' 				=> $total_users)
);

$sql = "SELECT user_id, username, user_email, user_regdate, user_lastvisit, user_posts, user_active
		FROM ".USERS_TABLE."
		WHERE user_id > 0
		ORDER BY " . $sort_method . " " . $sort_order . "
		LIMIT ".$start.",".$users_per_page;
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not query Users information", "", __LINE__, __FILE__, $sql);
}

while( $row = $db->sql_fetchrow($result) )
{
	$userrow[] = $row;
}

for ($i = 0; $i < $users_per_page; $i++)
{
	if (empty($userrow[$i]))
	{
		break;
	}
	$number = $i + 1;
	$row_color = (($i % 2) == 0) ? "row_easy" : "row_hard";
	
	$template->assign_block_vars('userrow', array(
		'L_NUMBER'				=> $number,
		'COLOR' 				=> $row_color,
		'NUMBER' 				=> $userrow[$i]['user_id'],
		'USERNAME' 				=> $userrow[$i]['username'],
		'U_ADMIN_USER' 			=> append_sid("admin_users.$phpEx?mode=edit&amp;" . POST_USERS_URL . "=" . $userrow[$i]['user_id']),
		'U_ADMIN_USER_AUTH' 	=> append_sid("admin_ug_auth.$phpEx?mode=user&amp;" . POST_USERS_URL . "=" . $userrow[$i]['user_id']),
		'EMAIL' 				=> $userrow[$i]['user_email'],
		'JOINED'	 			=> create_date($lang['DATE_FORMAT'], $userrow[$i]['user_regdate'], $board_config['board_timezone']),
		'LAST_VISIT' 			=> (!$userrow[$i]['user_lastvisit']) ? '' : create_date($lang['DATE_FORMAT'], $userrow[$i]['user_lastvisit'], $board_config['board_timezone']),
		'POSTS' 				=> $userrow[$i]['user_posts'],
		'ACTIVE' 				=> ( $userrow[$i]['user_active'] ) ? $lang['Yes'] : $lang['No']
		) 
	);
} 

$template->assign_vars(array(
	'PAGINATION' 	=> generate_pagination(append_sid("admin_users_list.$phpEx?sort=$sort_method&amp;order=$sort_order"), $total_users, $users_per_page, $start),
	'PAGE_NUMBER' 	=> sprintf($lang['Page_of'], ( floor( $start / $users_per_page ) + 1 ), ceil( $total_users / $users_per_page ))
	) 
);

$template->pparse('body');

include('./page_footer_admin.'.$phpEx);

?>