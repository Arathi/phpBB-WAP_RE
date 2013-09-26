<?php
/*****************************************
 *		mods/deliver/issue.php
 *		-------------------
 *		简体中文：爱疯的云
 *		MODS说明：处理创建活动的数据 
 ***************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_DELIVER);
init_userprefs($userdata);

if ( isset($HTTP_POST_VARS['submit']) )
{
	$title = ( isset($HTTP_POST_VARS['title']) ) ? trim($HTTP_POST_VARS['title']) : '';
	$reason = ( isset($HTTP_POST_VARS['reason']) ) ? trim($HTTP_POST_VARS['reason']) : '';
	$point = ( isset($HTTP_POST_VARS['point']) ) ? intval($HTTP_POST_VARS['point']) : '';
	$cut_point = ( isset($HTTP_POST_VARS['cut_point']) ) ? intval($HTTP_POST_VARS['cut_point']) : '';
	$poster = $userdata['user_id'];
	$user_points = $userdata['user_points'];
	
	if ( !$title || mb_strlen($title,"UTF-8")>64 )
	{
		message_die(GENERAL_MESSAGE, '标题填写不正确！');
	}
	if ( !$reason )
	{
		message_die(GENERAL_MESSAGE, '贺词填写不正确！');
	}
	if ( !$point || $point > $user_points )
	{
		message_die(GENERAL_MESSAGE, '拿出的积分不能大于自己的积分或者为空！');
	}
	if ( !$cut_point || $cut_point > $point )
	{
		message_die(GENERAL_MESSAGE, '恭喜者获得的积分不能大于拿出的积分或者为空！');
	}
	
	$sql = "INSERT INTO phpbb_deliver (deliver_poster, deliver_point, deliver_cut_point, deliver_title, deliver_reason)
		VALUES ('$poster',  '$point',  '$cut_point', '" . str_replace("\'", "''", $title) . "',  '" . str_replace("\'", "''", $reason) . "')";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "无法插入新数据！", "", __LINE__, __FILE__, $sql);
	}
	
	$new_user_points = $user_points - $point;
	$sql = "UPDATE " . USERS_TABLE . " SET user_points = '$new_user_points' WHERE user_id = '$poster'";
	if( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "无法更新数据！", "", __LINE__, __FILE__, $sql);
	}
	
	$message = '活动发布成功！<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 返回红包页面！<br /><br />点击 <a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">这里</a> 返回首页！';
	message_die(GENERAL_MESSAGE, $message);
}
?>