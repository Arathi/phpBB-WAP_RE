<?php
/*****************************************
 *		mods/deliver/index.php
 *		-------------------
 *		简体中文：爱疯的云
 *		MODS说明：修改帖子标题显示的颜色 
 ***************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_DELIVER);
init_userprefs($userdata);

$page_title = '红包';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

//开始自动删除一个月的数据

// 占位中。。。

//结束自动删除一个月的数据

$deliver_per = 5;//此处是设置每页显示多少条活动
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $deliver_per);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}
$sql = "SELECT * 
	FROM phpbb_deliver";
	
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$total_all_deliver = $db->sql_numrows($result);

$pagination = ( $total_all_deliver <= $deliver_per ) ? '' : generate_pagination("index.$phpEx?per", $total_all_deliver, $deliver_per, $start);

$sql = "SELECT * 
	FROM phpbb_deliver 
	ORDER BY deliver_id DESC
	LIMIT $start , $deliver_per";
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '活动数据查询失败！', '', __LINE__, __FILE__, $sql);
}
$total_deliver = $db->sql_numrows($result);
$deliver_row = array();

while ($row = $db->sql_fetchrow($result))
{
	$deliver_row[] = $row;
}
for($i = 0; $i < $total_deliver; $i++)
{
	$number = $i + 1;
	$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
		
	$deliver_poster_id = $deliver_row[$i]['deliver_poster'];
	
	$sql = "SELECT user_id, username 
		FROM " . USERS_TABLE . " 
		WHERE user_id = $deliver_poster_id";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, '用户数据查询失败！', '', __LINE__, __FILE__, $sql);
	}
	if ( !$deliver_user = $db->sql_fetchrow($result) )
	{
		message_die(GENERAL_ERROR, '用户数据查询失败！', '', __LINE__, __FILE__, $sql);
	}
	
	$template->assign_block_vars('deliver', array(
		'NUMBER' => $number,
		'ROW_CLASS'	=> $row_class,
		
		'DELIVER_TITLE'	=> $deliver_row[$i]['deliver_title'],
		'DELIVER_USERNAME' => $deliver_user['username'],
		'POINT' => $deliver_row[$i]['deliver_point'],
		'CUT_POINT' => $deliver_row[$i]['deliver_cut_point'],
		
		'U_VIEW_DELIVER' => append_sid("view.$phpEx?id={$deliver_row[$i]['deliver_id']}"),
		'U_DELIVER_USERNAME' => append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&u=$deliver_poster_id"))
	);
}
$template->set_filenames(array(
  "body" => "mods/deliver_body.tpl")
);

$template->assign_vars(array(
	'S_DELIVER_ACTION' => append_sid("submit.$phpEx"),
	'PAGINATION' => $pagination)
);

$template->pparse('body');    
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>