<?php
/**********************************
 *		select.php
 *		---------	
 *		简体中文：爱疯的云
 *		说明：专题功能
 **********************************/
 
define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_SPECIAL);
init_userprefs($userdata);

// 验证用户是否登录
if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.$phpEx?redirect=mods/special/add.$phpEx", true));
	exit;
}

if ( isset($HTTP_GET_VARS[POST_TOPIC_URL]) )
{
	$topic_id = intval($HTTP_GET_VARS[POST_TOPIC_URL]);
}
else
{
	$topic_id = '';
}

if ( isset($HTTP_POST_VARS['id']) )
{
	$special_id = intval($HTTP_POST_VARS['id']);
}
else
{
	$special_id = '';
}

if ( !$topic_id && empty($special_id) )
{
	message_die(GENERAL_MESSAGE, '请指定帖子和专题！');
}
$is_mod = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_level'] == MODCP ) ? true : false;

if( $is_mod )
{
	$sql = "UPDATE " . TOPICS_TABLE . " 
		SET topic_special = $special_id
		WHERE topic_id = $topic_id";
		
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, '设为专题失败！', '', __LINE__, __FILE__, $sql);
	}
	
	$message = '设置帖子专题成功！<br /><br />点击 <a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx?" . POST_TOPIC_URL . "=$topic_id") . '">这里</a> 帖子页面！<br /><br />' . sprintf('点击 %s这里%s 返回首页！', '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
	message_die(GENERAL_MESSAGE, $message);
}
else
{
	message_die(GENERAL_MESSAGE, '您没有权限操作！');
}

?>