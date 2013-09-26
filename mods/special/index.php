<?php
/*******************************************************
 *		index.php
 *		---------	
 *		版权所有：爱疯的云
 *		网站：http://phpbb-wap.com
 *		说明：专题功能
 *			用户可以根据 General Public License 自由使用 
 *******************************************************/
 
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
if ( !$forum_id )
{
	message_die(GENERAL_MESSAGE, '请指定论坛的id！');
}

$is_mod = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_level'] == MODCP ) ? true : false;

// 页面标题
$page_title = '专题列表';
// 头部
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

// 得到该论坛的专题总数
$sql = "SELECT special_id 
	FROM phpbb_specials 
	WHERE special_forum = " . $forum_id;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
}
$total_specials_id = $db->sql_numrows($result);

// 查询xx论坛的专题
$sql = "SELECT * 
	FROM phpbb_specials 
	WHERE special_forum = " . $forum_id . "
	LIMIT $start, " . $board_config['posts_per_page'];
	;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
}

// 得出该论坛的专题数量
$number_specials = $db->sql_numrows($result);
// 设置为数组

$specials_row = array();

// 循环出结果
while ( $row = $db->sql_fetchrow($result) )
{
	$specials_row[] = $row;
}

if( $number_specials )
{
	for ($i = 0; $i < $number_specials; $i++)
	{
		$special_number = $start + $i + 1;
		$template->assign_block_vars('specials_row', array(
			'L_NUMBER' 				=> $special_number,//专题号数
			'SPECIAL_TITLE' 		=> $specials_row[$i]['special_name'],
			'L_CUT_1'				=> ( $is_mod ) ? '（' : '',
			'L_CUT_2'				=> ( $is_mod ) ? '）' : '',
			'EDIT'					=> ( $is_mod ) ? '<a href="' . append_sid("{$phpbb_root_path}mods/special/edit.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=" . $specials_row[$i]['special_id']) . '">' . $lang['Edit'] . '</a>' : '',
			'DELETE'				=> ( $is_mod ) ? '<a href="' . append_sid("{$phpbb_root_path}mods/special/delete.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=" . $specials_row[$i]['special_id']) . '">/' . $lang['Delete'] . '</a>' : '',
			'U_SPECIAL' 			=> append_sid("{$phpbb_root_path}mods/special/view.$phpEx?" . POST_FORUM_URL . "=$forum_id&amp;id=" . $specials_row[$i]['special_id']),
			)
		);
	}
	
	$template->assign_vars(array(
		'PAGINATION' => generate_pagination("{$phpbb_root_path}mods/special/index.$phpEx?" . POST_FORUM_URL . "=$forum_id", $total_specials_id, $board_config['posts_per_page'], $start))
	);
}
else
{
	$template->assign_vars(array(
		'L_NO_SPECIAL' => '该论坛没有专题')
	);

	$template->assign_block_vars('switch_no_special', array() );
}

// 用来取得返回论坛的名称
$sql = "SELECT forum_id, forum_name 
	FROM phpbb_forums 
	WHERE forum_id = " . $forum_id;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, '无法查询论坛信息', '', __LINE__, __FILE__, $sql);
}

if ( !($forum_data = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_MESSAGE, '论坛数据加载出错。。。');
}

if( $is_mod )
{	
	$template->assign_vars(array(
		'ADD_SPECIAL' => append_sid("{$phpbb_root_path}mods/special/add.$phpEx?" . POST_FORUM_URL . "=$forum_id"))
	);
	$template->assign_block_vars('switch_mod_link', array() );
}

$template->set_filenames(array(
	'body' => 'mods/special_body.tpl')
);

$template->assign_vars(array(
	'U_BACK_FORUM' 	=> append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=" . $forum_id),
	'FORUM_NAME' 	=> $forum_data['forum_name'])
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>