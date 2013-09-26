<?php
/*****************************************
 *		mods/deliver/issue.php
 *		-------------------
 *		简体中文：爱疯的云
 *		MODS说明：浏览活动信息
 ***************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_DELIVER);
init_userprefs($userdata);

$page_title = '活动';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$id = ( isset($HTTP_GET_VARS['id']) ) ? intval($HTTP_GET_VARS['id']) : '';
if ( !$id )
{
	message_die(GENERAL_ERROR, "请指定的ID！");
}

$sql = "SELECT * 
	FROM phpbb_deliver 
	WHERE deliver_id = $id";
	
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败！', '', __LINE__, __FILE__, $sql);
}

while ( $row = $db->sql_fetchrow($result) )
{
		
	$deliver_poster_id = $row['deliver_poster'];
	
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
	
	$template->assign_vars(array(
		'DELIVER_TITLE'	=> $row['deliver_title'],
		'DELIVER_REASON' => $row['deliver_reason'],
		'DELIVER_USERNAME' => $deliver_user['username'],
		'U_DELIVER_USERNAME' => append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&u=$deliver_poster_id"),
		'U_PARTAKE' => append_sid("partake.$phpEx?id=$id"),
		'POINT' => $row['deliver_point'],
		'CUT_POINT' => $row['deliver_cut_point'])
	);
}
	

$template->set_filenames(array(
  "body" => "mods/deliver_view.tpl")
);

$template->assign_vars(array(
	'U_DELIVER'	=> append_sid("index.$phpEx"))
);
$template->pparse('body');    
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>