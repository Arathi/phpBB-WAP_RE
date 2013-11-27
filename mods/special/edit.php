<?php
/**********************************
 *		delete.php
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
	redirect(append_sid("login.$phpEx?redirect=mods/special/index.$phpEx", true));
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

if ( isset($HTTP_GET_VARS['id']) )
{
	$special_id = intval($HTTP_GET_VARS['id']);
}
else
{
	$special_id = '';
}

if ( !$special_id || !$forum_id )
{
	message_die(GENERAL_MESSAGE, '请指定论坛和专题！');
}

$is_mod = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_level'] == MODCP ) ? true : false;

if( $is_mod )
{	
	$confirm = ( $HTTP_POST_VARS['confirm'] ) ? true : false;
	if ( isset($HTTP_POST_VARS['name']) )
	{
		$new_name = htmlspecialchars(trim($HTTP_POST_VARS['name']));
	}
	else
	{
		$new_name = '';
	}
	
	if( $confirm && !empty($new_name))
	{	
		$sql = "UPDATE " . SPECIAL_TABLE . " 
			SET special_name = '$new_name'
			WHERE special_id = $special_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, '专题名称更新失败！', '', __LINE__, __FILE__, $sql);
		}
		$message = '原专题已成功修改为 “' . $new_name . '” ！<br /><br />点击 <a href="' . append_sid("edit.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=$special_id") . '">这里</a> 上一页面！<br /><br />' . sprintf('点击 %s这里%s 返回首页！', '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
		message_die(GENERAL_MESSAGE, $message);
	}
	else
	{
		$page_title = '编辑专题';
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		
		
		$sql = "SELECT special_name
			FROM " . SPECIAL_TABLE . " 
			WHERE special_id = $special_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
		}
		if ( !($row = $db->sql_fetchrow($result)) )
		{
			message_die(GENERAL_ERROR, '数据读取失败！', '', __LINE__, __FILE__, $sql);
		}

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
			'body' => 'mods/special_edit.tpl')
		);
		$template->assign_vars(array(
			'FORUM_NAME' 		=> $forum_data['forum_name'],
			'SPECIAL_NAME'		=> $row['special_name'],
			'U_BACK_SPECIAL'	=> append_sid("{$phpbb_root_path}mods/special/index.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
			'U_BACK_FORUM' 		=> append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
			'S_HIDDEN'			=> '<input type="hidden" name="confirm" value="yes" />',
			'S_POST_ACTION'		=> append_sid("{$phpbb_root_path}mods/special/edit.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=$special_id"))
		);
		$template->pparse('body');
		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		exit;
	}
}
else
{
	message_die(GENERAL_MESSAGE, '您没有管理权限！');
}

?>