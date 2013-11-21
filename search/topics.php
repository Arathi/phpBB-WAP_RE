<?php
/***************************************************
 *		viewtopic.php
 *		-------------------
 *      创始者: phpBB Group.
 *		简体中文：爱疯的云
 ***************************************************/
define('IN_PHPBB', true);
$phpbb_root_path = './../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

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

if ( isset($HTTP_GET_VARS[POST_FORUM_URL]) )
{
	$forum_id = intval($HTTP_GET_VARS[POST_FORUM_URL]);
}
if ( !$forum_id )
{
	message_die(GENERAL_MESSAGE, '请指定论坛id！');
}

if ( isset($HTTP_POST_VARS['search_keywords']) || isset($HTTP_GET_VARS['search_keywords']) )
{
	$search_keywords = ( isset($HTTP_POST_VARS['search_keywords']) ) ? $HTTP_POST_VARS['search_keywords'] : $HTTP_GET_VARS['search_keywords'];
}
else
{
	$search_keywords = '';
}

if ( !$search_keywords )
{
	message_die(GENERAL_MESSAGE, '请指定搜索关键词！');
}

$userdata = session_pagestart($user_ip, PAGE_SEARCH);
init_userprefs($userdata);

if ( !$userdata['session_logged_in'] )
{
	$redirect = "index.$phpEx";
	redirect(append_sid("login.$phpEx?redirect=$redirect", true));
}
	
$page_title = $lang['Search'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$sql = "SELECT topic_title 
	FROM  phpbb_topics 
	WHERE forum_id = $forum_id 
		AND topic_title 
	LIKE '%$search_keywords%'";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "无法统计搜索数据！", "", __LINE__, __FILE__, $sql);
}
$total_search_topic = $db->sql_numrows($result);

$sql = "SELECT topic_id, topic_title  
	FROM  phpbb_topics 
	WHERE forum_id = $forum_id
		AND topic_title LIKE '%$search_keywords%' 
	LIMIT $start, " . $board_config['topics_per_page'];

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "搜索失败，请稍候重试！", "", __LINE__, __FILE__, $sql);
}
$total_export_topic = $db->sql_numrows($result);
$topic_row = array();
while ($row = $db->sql_fetchrow($result))
{
	$topic_row[] = $row;
}

$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';

for ($i = 0; $i < $total_export_topic; $i++)
{
	$number = $i+1;
	$template->assign_block_vars('search_topic_row', array(
		'ROW_CLASS' => $row_class,
		'L_NUMBER' => $number,
		'TOPIC_TITLE' => $topic_row[$i]['topic_title'],
		'U_VIEW_TOPIC' => append_sid("{$phpbb_root_path}viewtopic.php?t=" . $topic_row[$i]['topic_id']))
	);
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
	'body' => 'search/search_topics.tpl')
);

$base_url = "topics.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;search_keywords=$search_keywords";
$pagination = generate_pagination($base_url, $total_search_topic, $board_config['topics_per_page'], $start);

$template->assign_vars(array(
	'PAGINATION' => $pagination,
	'FORUM_NAME' => $forum_data['forum_name'],
	'U_BACK_FORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
	'TOTAL_NUMBER' => $total_search_topic)
);
$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>