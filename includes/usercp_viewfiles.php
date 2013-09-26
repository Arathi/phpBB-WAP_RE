<?php
/***************************************************************
 *		usercp_viewfiles.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：查看用户附件
 **************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}

if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
$user_id = intval($HTTP_GET_VARS[POST_USERS_URL]);

$attachment_mod_installed = ( defined('ATTACH_CONFIG_TABLE') ) ? TRUE : FALSE;

if (!$attachment_mod_installed)
{
	message_die(GENERAL_MESSAGE, "论坛的附件功能未启用！");
}

$sql = 'SELECT config_value
	FROM ' . ATTACH_CONFIG_TABLE . "
	WHERE config_name = 'attach_version'";

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, "Unable to query config table.", '', __LINE__, __FILE__, $sql);
}

if (!($row = $db->sql_fetchrow($result)))
{
	message_die(GENERAL_MESSAGE, '附加版本错误！');
}

$attachment_version = $row['config_value'];

$real_filename = 'real_filename';
$attach_table = ATTACHMENTS_TABLE;
$attach_desc_table = ATTACHMENTS_DESC_TABLE;

$sql = 'SELECT config_value
	FROM ' . CONFIG_TABLE . "
	WHERE config_name = 'default_lang'";

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, "Unable to query config table.", '', __LINE__, __FILE__, $sql);
}

$default_lang = $db->sql_fetchrow($result);
$default_lang = $default_lang['config_value'];

$language = $board_config['default_lang'];

if( !file_exists($phpbb_root_path . 'language/lang_' . $language . '/lang_admin_attach.'.$phpEx) )
{
	$language = $default_lang;
}
include($phpbb_root_path . 'language/lang_' . $language . '/lang_admin_attach.' . $phpEx);

$default_sort_method = 'downloads';
$default_sort_order = 'DESC';

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start = (($start1 - 1) * $board_config['topics_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

if(isset($HTTP_POST_VARS['order']))
{
	$sort_order = ($HTTP_POST_VARS['order'] == 'ASC') ? 'ASC' : 'DESC';
}
else if(isset($HTTP_GET_VARS['order']))
{
	$sort_order = ($HTTP_GET_VARS['order'] == 'ASC') ? 'ASC' : 'DESC';
}
else
{
	$sort_order = $default_sort_order;
}

if(isset($HTTP_GET_VARS['mode_sort']) || isset($HTTP_POST_VARS['mode_sort']))
{
	$mode = (isset($HTTP_POST_VARS['mode_sort'])) ? $HTTP_POST_VARS['mode_sort'] : $HTTP_GET_VARS['mode_sort'];
}
else
{
	$mode = $default_sort_method;
}

$mode_types_text = array($lang['Sort_Filename'], $lang['Sort_Size'], $lang['Sort_Downloads'], $lang['Sort_Posttime']);
$mode_types = array('filename', 'filesize', 'downloads', 'post_time');

$select_sort_mode = '<select name="mode">';
for($i = 0; $i < count($mode_types_text); $i++)
{
	$selected = ( $mode == $mode_types[$i] ) ? ' selected="selected"' : '';
	$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
}
$select_sort_mode .= '</select>';

$select_sort_order = '<select name="order">';
if($sort_order == 'ASC')
{
	$select_sort_order .= '<option value="ASC" selected="selected">' . $lang['Sort_Ascending'] . '</option><option value="DESC">' . $lang['Sort_Descending'] . '</option>';
}
else
{
	$select_sort_order .= '<option value="ASC">' . $lang['Sort_Ascending'] . '</option><option value="DESC" selected="selected">' . $lang['Sort_Descending'] . '</option>';
}
$select_sort_order .= '</select>';

switch ($mode)
{
	case 'filename':
		$order_by = '' . $real_filename . ' ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'filesize':
		$order_by = 'filesize ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'downloads':
		$order_by = 'download_count ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'post_time':
		$order_by = 'filetime ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	default:
		message_die(GENERAL_MESSAGE, "Please have a look at the attachments.php file and define valid sort order default values.");
		break;
}

$sql = "SELECT c.cat_title, c.cat_id, f.forum_name, f.forum_id  
	FROM " . CATEGORIES_TABLE . " c, " . FORUMS_TABLE . " f
	WHERE f.cat_id = c.cat_id 
	ORDER BY c.cat_id, f.forum_order";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain forum_name/forum_id', '', __LINE__, __FILE__, $sql);
}

$is_auth_ary = auth(AUTH_READ, AUTH_LIST_ALL, $userdata);
$is_download_auth_ary = auth(AUTH_DOWNLOAD, AUTH_LIST_ALL, $userdata);

$forum_ids = array();
$select_forums = '';
while( $row = $db->sql_fetchrow($result) )
{
	if ( ( $is_auth_ary[$row['forum_id']]['auth_read'] ) && ( $is_download_auth_ary[$row['forum_id']]['auth_download'] ) )
	{
		$select_forums = true;
		$forum_ids[] = $row['forum_id'];
	}
}

if ( $select_forums == '' )
{
	message_die(GENERAL_MESSAGE, "您不能查看这附件！");
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx); 

$template->set_filenames(array(
	'body' => 'profile_view_attachments.tpl')
);

$template->assign_vars(array(
	'L_SUBMIT' 			=> $lang['Submit'],

	'S_MODE_SELECT' 	=> $select_sort_mode,
	'S_ORDER_SELECT' 	=> $select_sort_order,
	'S_MODE_ACTION' 	=> append_sid("profile.$phpEx?mode=viewfiles&amp;" . POST_USERS_URL ."=$user_id"))
);

$sql = "SELECT a.post_id, t.topic_title, d.*
	FROM " . $attach_table . " a, " . $attach_desc_table . " d, "  . POSTS_TABLE . " p, " . TOPICS_TABLE . " t
	WHERE (a.post_id = p.post_id) AND a.user_id_1 = $user_id AND (p.forum_id IN (" . implode(', ', $forum_ids) . ")) AND (p.topic_id = t.topic_id) AND (a.attach_id = d.attach_id)
	ORDER BY $order_by";
if (!($result = $db->sql_query($sql)))
{ 
	message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql); 
}
	
if ( !($attachments = $db->sql_fetchrowset($result)) )
{
	message_die(GENERAL_MESSAGE, "Нет вложений");
}
$num_attachments = $db->sql_numrows($result);

for ($i = 0; $i < $num_attachments; $i++) 
{ 
	$class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	$post_title = $attachments[$i]['topic_title'];

	$view_topic = append_sid('viewtopic.' . $phpEx . '?' . POST_POST_URL . '=' . $attachments[$i]['post_id'] . '#' . $attachments[$i]['post_id']);
	$post_title = '<a href="' . $view_topic . '">' . $post_title . '</a>';
	$filename = $attachments[$i][$real_filename];

	$view_attachment = append_sid($phpbb_root_path . 'download.' . $phpEx . '?id=' . intval($attachments[$i]['attach_id']));
	$filename_link = '<a href="' . $view_attachment . '">' . $filename . '</a>';

	$filesize = $attachments[$i]['filesize'];
	$size_lang = ($filesize >= 1048576) ? $lang['MB'] : ( ($filesize >= 1024) ? $lang['KB'] : $lang['Bytes'] );

	if ($filesize >= 1048576)
	{
		$filesize = (round((round($filesize / 1048576 * 100) / 100), 2));
	}
	else if ($filesize >= 1024)
	{
		$filesize = (round((round($filesize / 1024 * 100) / 100), 2));
	}

	$template->assign_block_vars('attachrow', array(
		'ROW_NUMBER' 		=> $i + ( $HTTP_GET_VARS['start'] + 1 ),
		'ROW_CLASS' 		=> $class,

		'FILENAME' 			=> $filename,
		'SIZE' 				=> $filesize,
		'SIZE_LANG' 		=> $size_lang,
		'DOWNLOAD_COUNT' 	=> $attachments[$i]['download_count'],
		'POST_TIME' 		=> create_date($board_config['default_dateformat'], $attachments[$i]['filetime'], $board_config['board_timezone']),
		'POST_TITLE' 		=> $post_title,

		'VIEW_ATTACHMENT' 	=> $filename_link)
	);
}

$sql = "SELECT count(*) AS total
	FROM " . $attach_table . " a, " . POSTS_TABLE . " p
	WHERE a.user_id_1 = $user_id 
		AND (a.post_id = p.post_id) 
		AND (p.forum_id IN (" . implode(', ', $forum_ids) . "))";
if (!($result = $db->sql_query($sql))) 
{
	message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
}

if ( $total = $db->sql_fetchrow($result) )
{
	$total = $total['total'];

	$pagination = generate_pagination("profile.$phpEx?mode=viewfiles&amp;" . POST_USERS_URL ."=$user_id&amp;mode_sort=$mode&amp;order=$sort_order", $total, $board_config['topics_per_page'], $start);
}

$template->assign_vars(array(
	'PAGINATION' => $pagination)
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx); 

?>
