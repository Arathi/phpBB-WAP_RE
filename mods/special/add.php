<?php
/**********************************
 *		add.php
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

if ( isset($HTTP_GET_VARS[POST_FORUM_URL]) )
{
	$forum_id = intval($HTTP_GET_VARS[POST_FORUM_URL]);
}
else
{
	$forum_id = '';
}

if ( !$forum_id )
{
	message_die(GENERAL_MESSAGE, '请指定论坛的id！');
}

$is_mod = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_level'] == MODCP ) ? true : false;

if( $is_mod )
{
	if ( isset($HTTP_GET_VARS['mode']) )
	{
		$mode = $HTTP_GET_VARS['mode'];
	}
	else
	{
		$mode = '';
	}
	
	if ( isset($HTTP_POST_VARS['name']) )
	{
		$add_name = htmlspecialchars(trim($HTTP_POST_VARS['name']));
	}
	else
	{
		$add_name = '';
	}

	if ( !empty($add_name) && $mode == 'add' )
	{
		$sql = "INSERT INTO " . SPECIAL_TABLE . "(special_name, special_forum)
			VALUES ('$add_name', $forum_id)";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, '数据插入失败！', '', __LINE__, __FILE__, $sql);
		}
		$message = '专题 “' . $add_name . '” 添加成功！<br /><br />点击 <a href="' . append_sid("add.$phpEx?" . POST_FORUM_URL . "=$forum_id") . '">这里</a> 上一页面！<br /><br />' . sprintf('点击 %s这里%s 返回首页！', '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
		message_die(GENERAL_MESSAGE, $message);
	}
	else
	{
		$page_title = '添加专题';
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		
		$sql = "SELECT forum_id, forum_name 
			FROM " . FORUMS_TABLE . " 
			WHERE forum_id = " . $forum_id;

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, '无法查询论坛信息', '', __LINE__, __FILE__, $sql);
		}

		if ( !($forum_data = $db->sql_fetchrow($result)) )
		{
			message_die(GENERAL_MESSAGE, '论坛数据加载出错。。。');
		}
		
		$template->set_filenames(array(
			'body' => 'mods/special_add.tpl')
		);
		$template->assign_vars(array(
			'U_BACK_SPECIAL'	=> append_sid("{$phpbb_root_path}mods/special/index.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
			'U_BACK_FORUM' 		=> append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
			'S_POST_ACTION'		=> append_sid("{$phpbb_root_path}mods/special/add.$phpEx?mode=add&amp;" . POST_FORUM_URL . "=" . $forum_id),
			'FORUM_NAME' 		=> $forum_data['forum_name'])
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
}
else
{
	message_die(GENERAL_MESSAGE, '您没有权限操作！');
}

?>