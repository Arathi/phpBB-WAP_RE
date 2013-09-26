<?php
/**********************************
 *		page.php (phpBB-WAP v4.0)
 *		-------------------------
 *		简体中文：爱疯的云
 **********************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
include($phpbb_root_path . 'includes/functions_bbcode.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_PAGE);
init_userprefs($userdata);

// 如果网页 ID 参数存在
if ( isset($HTTP_GET_VARS[PAGE_URL]) )
{
	// 把ID的值转换为整数，防止 SQL 查询出错
	$page_id = intval($HTTP_GET_VARS[PAGE_URL]);
}
else
{
	$page_id = '';
}

if ( !$page_id )
{
	message_die(GENERAL_MESSAGE, '网页不存在！');
}

$sql = "SELECT * 
	FROM " . PAGES_TABLE . " 
	WHERE page_id = " . $page_id;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain pages data for pages', '', __LINE__, __FILE__, $sql);
}

if ( !($pagedata = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_MESSAGE, '您选择的网页不存在或已经被删除！');
}

if( ( $userdata['user_level'] == ADMIN ) )
{
	$edit_page = '<a href="admin/edit_page.' . $phpEx . '?mode=pages&amp;id=' . $page_id . '&amp;sid=' . $userdata['session_id'] . '">页面编辑模式</a>';
	
	$template->assign_vars(array('EDIT_PAGE' => $edit_page)
	);
	$template->assign_block_vars('switch_admin_link', array() );
}
else
{
	$template->assign_vars(array(
		'ADMIN_LINK' => '')
	);
}

$page_title = $pagedata['page_title'];// 网页的标题
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$page_body = $pagedata['page_contents'];

// 先把实体标签转换为普通标签
$page_body = htmlspecialchars_decode($page_body);

$page_body = preg_replace('/&#40;/', '(', $page_body);
$page_body = preg_replace('/&#41;/', ')', $page_body);
$page_body = preg_replace('/&#58;/', ':', $page_body);
$page_body = preg_replace('/&#91;/', '[', $page_body);
$page_body = preg_replace('/&#93;/', ']', $page_body);
$page_body = preg_replace('/&#123;/', '{', $page_body);
$page_body = preg_replace('/&#125;/', '}', $page_body);

// BBcode
$page_body = bbencode_second_pass($page_body, $bbcode_uid);
$page_body = make_clickable($page_body);
$page_body = page_bbcode($page_body);

// 解析表情
$page_body = smilies_pass($page_body);

$template->set_filenames(array(
	'body' => 'page_body.tpl')
);

$template->assign_vars(array(
	'PAGE_BODY'		=> $page_body)
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>