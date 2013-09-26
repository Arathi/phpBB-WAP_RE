<?php
/*****************************************
 *		mods/deliver/partake.php
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

if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.$phpEx?redirect=mods/deliver/index.$phpEx", true));
	exit;
}
// 没时间写了，简单一点吧。。。

$deliver_id = ( isset($HTTP_GET_VARS['id']) ) ? $HTTP_GET_VARS['id'] : '';

if ( !$deliver_id )
{
	message_die(GENERAL_ERROR, "请指定id");
}

$user_id = $userdata['user_id'];
$sql = "SELECT partake_user_id 
	FROM phpbb_partake_deliver
	WHERE partake_user_id = $user_id 
		AND deliver_id = $deliver_id";
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败！', '', __LINE__, __FILE__, $sql);
}
$total_deliver = $db->sql_numrows($result);

if ( $total_deliver )
{
	message_die(GENERAL_MESSAGE, "您已经拿过红包了！");
}
else
{
	$sql = "INSERT INTO phpbb_partake_deliver (partake_user_id, deliver_id)
		VALUES ('$user_id', '$deliver_id')";	
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, '数据插入失败！', '', __LINE__, __FILE__, $sql);
	}
	$message = '哈哈，红包拿到了！<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 返回红包页面！<br /><br />点击 <a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">这里</a> 返回首页！';
	message_die(GENERAL_MESSAGE, $message);
}
?>