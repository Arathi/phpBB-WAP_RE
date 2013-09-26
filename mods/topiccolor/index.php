<?php
/*****************************************
 *		mods/topiccolor/index.php
 *		-------------------
 *		简体中文：爱疯的云
 *		MODS说明：修改帖子标题显示的颜色 
 ***************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_TOPICS);
init_userprefs($userdata);

// 只有管理员和版主才有权限修改
if ( $userdata['user_level'] == MOD || $userdata['user_level']== ADMIN || $userdata['user_level']== MODCP )
{
	$page_title = '更改帖子标题颜色';
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	if ( isset($HTTP_GET_VARS['id']) && isset($HTTP_GET_VARS[POST_FORUM_URL]))
	{
		$id = intval($HTTP_GET_VARS['id']);
		$forum_id = intval($HTTP_GET_VARS[POST_FORUM_URL]);
	}
	else
	{
		message_die(GENERAL_ERROR, '必须指定主题的ID和论坛ID！');
	}
	
	if ( isset($HTTP_GET_VARS['submit']) && intval($HTTP_GET_VARS['submit']) == 'Submit' )
	{
		
			$update_topic_color = $HTTP_GET_VARS['color'];
			$sql = "UPDATE " . TOPICS_TABLE . " SET topic_color ='" . $update_topic_color . "' WHERE topic_id =" . $id;
		
		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could not update topic color", '', __LINE__, __FILE__, $sql);
		}
		$redirect = append_sid($phpbb_root_path . "viewforum.$phpEx?" . POST_FORUM_URL . '=' . $forum_id);
		message_die(GENERAL_MESSAGE, '已把颜色更改为 ' . $update_topic_color . '<br />点击 <a href ="' . $redirect . '">这里</a> 返回上一页！');
	}
	else
	{
		$sql = "SELECT topic_color FROM " . TOPICS_TABLE . " WHERE topic_id = $id";
		if ( !$result = $db->sql_query($sql) )
		{
		  message_die(GENERAL_ERROR, "Couldn't get users", "", __LINE__, __FILE__, $sql);
		}

		$topic_color = $db->sql_fetchrow($result);
		$topic_color = $topic_color['topic_color'];
		$s_hidden_fields = '<input type="hidden" name="id" value="' . $id . '">';
		$s_hidden_fields .= '<input type="hidden" name="f" value="' . $forum_id . '">';
	 
		$template->set_filenames(array(
		  "body" => "mods/topic_color.tpl")
		);

		$template->assign_vars(array(
			'L_COLOR_TITLE' 		=> '修改帖子标题颜色',
			'L_COLOR_EXPLAIN'		=> '输入十六进制格式的颜色，例如：FFFFFF (白色)，不需要 #',
			'TOPIC_COLOR' 			=> $topic_color,
			'L_COLOR' 				=> '修改帖子标题颜色',
			'L_SUBMIT' 				=> $lang['Submit'],
			'HIDDEN_VAR' 			=> $s_hidden_fields,
			'S_POST_ACTION' 		=> append_sid("index.$phpEx?mode=submit&id=$id", true))
		);
	}
}
else
{ 
	message_die(GENERAL_ERROR, '您不是管理员或者版主！'); 
}

$template->pparse('body');    
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>

