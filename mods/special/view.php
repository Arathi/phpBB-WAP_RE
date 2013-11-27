<?php
/**********************************
 *		view.php
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

// 分页使用
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['posts_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

// 取得专题id用
if ( isset($HTTP_GET_VARS['id']) )
{
	// 把ID的值转换为整数
	$special_id = intval($HTTP_GET_VARS['id']);
}
else
{
	$special_id = '';
}

// 取得论坛id用
if ( isset($HTTP_GET_VARS[POST_FORUM_URL]) )
{
	// 把ID的值转换为整数
	$forum_id = intval($HTTP_GET_VARS[POST_FORUM_URL]);
}
else
{
	$forum_id = '';
}

if ( !$special_id || !$forum_id )
{
	message_die(GENERAL_MESSAGE, '请指定专题和论坛的id！');
}

// 页面标题
$page_title = '专题帖子列表';
// 头部
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

// 得到该论坛的专题总数
$sql = "SELECT topic_id, forum_id, topic_title, topic_special 
	FROM " . TOPICS_TABLE . " 
	WHERE topic_special = $special_id 
		AND forum_id = $forum_id";

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
}
$t_specials_topics = $db->sql_numrows($result);

// 查询xx论坛的专题
$sql = "SELECT topic_id, forum_id, topic_title, topic_special 
	FROM " . TOPICS_TABLE . " 
	WHERE topic_special =  $special_id 
		AND forum_id = $forum_id 
	LIMIT $start, " . $board_config['posts_per_page'];
	;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
}

// 得出该论坛的专题数量
$num_specials_t = $db->sql_numrows($result);
// 设置为数组
$t_specials_row = array();

// 循环出结果
while ( $row = $db->sql_fetchrow($result) )
{
	$t_specials_row[] = $row;
}

if( $t_specials_row )
{
	for ($i = 0; $i < $num_specials_t; $i++)
	{
		$t_special_num = $start + $i + 1;
		$template->assign_block_vars('t_specials_row', array(
			'L_NUMBER' 					=> $t_special_num,//专题号数
			'TOPIC_TITLE' 				=> $t_specials_row[$i]['topic_title'],
			'U_TOPIC' 					=> append_sid("{$phpbb_root_path}viewtopic.$phpEx?" . POST_TOPIC_URL . "=" . $t_specials_row[$i]['topic_id']),
			)
		);
	}
	$template->assign_vars(array(
		'PAGINATION' => generate_pagination("{$phpbb_root_path}mods/special/view.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=$special_id", $t_specials_topics, $board_config['posts_per_page'], $start))
	);
}
else
{
	$template->assign_vars(array(
		'L_NO_TOPICS' => '该专题没有帖子')
	);

	$template->assign_block_vars('switch_no_topics', array() );
}
// 用来取得返回论坛的名称
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

// 用来取得返回专题的名称
$sql = "SELECT * 
	FROM " . $table_prefix . "specials 
	WHERE special_forum = " . $forum_id;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '无法查询论坛信息', '', __LINE__, __FILE__, $sql);
}

if ( !($special_data = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_MESSAGE, '论坛数据加载出错。。。');
}

$template->set_filenames(array(
	'body' => 'mods/special_view.tpl')
);

$template->assign_vars(array(
	'U_BACK_SPECIAL'	=> append_sid("{$phpbb_root_path}mods/special/index.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
	'U_BACK_FORUM' 		=> append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
	'FORUM_NAME' 		=> $forum_data['forum_name'],
	'SPECIAL_NAME'		=> $special_data['special_name'])
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>