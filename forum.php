<?php
/***************************************************************************
 *		forum.php (phpBB-WAP v4.0)
 *		--------------------------
 *		Разработка: phpBB Group.
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		简体中文：http://phpBB-WAP.com （爱疯的云）
 ***************************************************************************/

define('IN_PHPBB', true);// 定义常量
$phpbb_root_path = './';// 表示当前目录，用来表示phpbb 的安装目录
include($phpbb_root_path . 'extension.inc');// include() 函数用于包含 extension.inc 这个文件
include($phpbb_root_path . 'common.'.$phpEx);// $phpEx 为 .php 后缀的变量
// basename() 函数返回路径中的文件名部分
$this_file = basename(__FILE__);// __FILE__ 的作用是返回文件的完整路径和文件名。如果用在被包含文件中，则返回被包含的文件名

// empty() 函数检查一个变量是否为空
// 如果公告存在，引用 bbcode.php 文件
if (!empty($board_config['index_announcement']))
{
	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
}

// 开启 session 管理
$userdata = session_pagestart($user_ip, PAGE_FORUM);// session_pagestart() 为内建函数，PAGE_INDEX 这些为常量
init_userprefs($userdata);// 内建函数

//论坛分类
$viewcat = ( !empty($HTTP_GET_VARS[POST_CAT_URL]) ) ? $HTTP_GET_VARS[POST_CAT_URL] : -1;
// 像 $board_config['index_spisok'] 这些为数组
if( (!$board_config['index_spisok'] && !$userdata['session_logged_in']) || ($userdata['session_logged_in'] && !$userdata['user_index_spisok']) )
{
	if ( $viewcat < 0 )
	$viewcat = -2 ;
}

// 论坛
$sql = "SELECT * FROM ". FORUMS_TABLE . " ORDER BY forum_id";
if (!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}
$forum_data = array();
while( $row = $db->sql_fetchrow($result) )
{
	$forum_data[] = $row;
}

$is_auth_ary = array();
$is_auth_ary = auth(AUTH_ALL, AUTH_LIST_ALL, $userdata, $forum_data);

//
// 帖子动态
//
$set_show_topics = '5';//显示最新帖子的数量
$set_exception = '';//不显示最新帖子的论坛，用英文逗号,分隔
$set_hot_day = 1;//显示热门帖子的天数

$hot_day = ( 3600 * 24 * $set_hot_day ) + time();
$except_forum_id = ( $set_exception == '' ) ? '\'start\'' : $set_exception;

for ($i = 0; $i < count($forum_data); $i++)
{
	if ( (!$is_auth_ary[$forum_data[$i]['forum_id']]['auth_read']) or (!$is_auth_ary[$forum_data[$i]['forum_id']]['auth_view']) )
	{
		if ($except_forum_id == '\'start\'')
		{
			$except_forum_id = $forum_data[$i]['forum_id'];
		}
		else
		{
			$except_forum_id .= ',' . $forum_data[$i]['forum_id'];
		}
	}
}

$mode = ( isset($HTTP_GET_VARS['mode']) ) ? $HTTP_GET_VARS['mode'] : 'new';
$mode = ( !empty($HTTP_GET_VARS['mode']) ) ? $HTTP_GET_VARS['mode'] : 'new';

switch ( $mode )
{
	case 'new':
		$sql = "SELECT t.topic_title, p.post_id
			FROM " . TOPICS_TABLE . " AS t, " . POSTS_TABLE . " AS p
			WHERE t.forum_id NOT IN (" . $except_forum_id . ")
				AND t.topic_status <> 2
				AND p.post_id = t.topic_last_post_id
			ORDER BY t.topic_id DESC
			LIMIT 0, $set_show_topics";
		$empty_show = '还没有最新帖子';
		break;
	
	case 'verb':
		$sql = "SELECT t.topic_title, p.post_id
			FROM " . TOPICS_TABLE . " AS t, " . POSTS_TABLE . " AS p
			WHERE t.forum_id NOT IN (" . $except_forum_id . ")
				AND t.topic_status <> 2
				AND p.post_id = t.topic_last_post_id
			ORDER BY p.post_id DESC
			LIMIT 0, $set_show_topics";
		$empty_show = '还没有动态帖子';
		break;
		
	case 'hot':
		$sql = "SELECT t.topic_title, p.post_id
			FROM " . TOPICS_TABLE . " AS t, " . POSTS_TABLE . " AS p
			WHERE t.forum_id NOT IN (" . $except_forum_id . ")
				AND t.topic_status <> 2
				AND p.post_id = t.topic_last_post_id
				AND p.post_time >= $hot_day
			ORDER BY t.topic_replies DESC
			LIMIT 0, $set_show_topics";
		$empty_show = '还没有热门帖子';
		break;
		
	default:
		redirect(append_sid("index.$phpEx?mode=new"), true);
		exit;
}

if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$number_recent_topics = $db->sql_numrows($result);
$recent_topic_row = array();
while ($row = $db->sql_fetchrow($result))
{
	$recent_topic_row[] = $row;
}
if ( empty($number_recent_topics) )
{
	$template->assign_block_vars('empty_recent_topic_row', array());
}
else
{
	for ($i = 0; $i < $number_recent_topics; $i++)
	{
		$number = $i+1;
		$template->assign_block_vars('recent_topic_row', array(
			'L_THEME_NUMBER' 	=> $number,
			'U_TITLE' 			=> append_sid("viewtopic.$phpEx?" . POST_POST_URL . '=' . $recent_topic_row[$i]['post_id']) . '#' .$recent_topic_row[$i]['post_id'],
			'L_TITLE' 			=> $recent_topic_row[$i]['topic_title'],
			'S_POSTTIME' 		=> create_date($board_config['default_dateformat'], $recent_topic_row[$i]['post_time'], $board_config['board_timezone'])
			)
		);
	}
}

/**
* 跟踪主题论坛，isset() 函数检测变量是否设置
* 下面的语句同于 if 语句 $xxx ? $xx : $zz 等于 
* if ( $xxx )
* {
*	echo $xxx;
* } else {
*	echo $zzz;
* ｝
**/
$tracking_topics = ( isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . "_t"]) : array();
$tracking_forums = ( isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . "_f"]) : array();

// 论坛分类
$sql = "SELECT c.cat_id, c.cat_title, c.cat_order, c.cat_icon
	FROM " . CATEGORIES_TABLE . " c
	ORDER BY c.cat_order";
	
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
}

$category_rows = array();
while ($row = $db->sql_fetchrow($result))
{
	$category_rows[] = $row;
}
$db->sql_freeresult($result);
$total_categories = count($category_rows);

$sql = "SELECT f.*, p.post_time, p.post_username, u.username, u.user_id
	FROM (( " . FORUMS_TABLE . " f
	LEFT JOIN " . POSTS_TABLE . " p ON p.post_id = f.forum_last_post_id )
	LEFT JOIN " . USERS_TABLE . " u ON u.user_id = p.poster_id )
	ORDER BY f.cat_id, f.forum_order";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query forums information', '', __LINE__, __FILE__, $sql);
}

$forum_data = array();
while( $row = $db->sql_fetchrow($result) )
{
	$forum_data[] = $row;
}
$db->sql_freeresult($result);

$total_forums = count($forum_data);// count() 函数计算数组中的单元数目或对象中的属性个数

if ($userdata['session_logged_in'])
{
	if ($userdata['user_lastvisit'] < (time() - 5184000))
	{
		$userdata['user_lastvisit'] = time() - 5184000;
	}

	$sql = "SELECT t.forum_id, t.topic_id, p.post_time 
		FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p 
		WHERE p.post_id = t.topic_last_post_id 
			AND p.post_time > " . $userdata['user_lastvisit'] . " 
			AND t.topic_moved_id = 0"; 
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query new topic information', '', __LINE__, __FILE__, $sql);
	}

	$new_topic_data = array();
	while( $topic_data = $db->sql_fetchrow($result) )
	{
		$new_topic_data[$topic_data['forum_id']][$topic_data['topic_id']] = $topic_data['post_time'];
	}
	$db->sql_freeresult($result);
}

//计算帖子、用户等，用于论坛底部统计，下面几个函数为内建函数
$totla_topics = get_db_stat('topiccount');
$total_posts = get_db_stat('postcount');
$total_users = get_db_stat('usercount');
$total_attach = get_db_stat('attachcount');
$newest_userdata = get_db_stat('newestuser');
$newest_user = $newest_userdata['username'];
$newest_uid = $newest_userdata['user_id'];

$page_title = $lang['Forum_Index'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

// 调用tpl模版文件
$template->set_filenames(array(
	'body' => 'forum_body.tpl')
);

$template->assign_vars(array(
	'EMPTY_SHOW' 	=> $empty_show,
	
	'U_NEW_TOPIC'	=> append_sid("forum.$phpEx?mode=new"),
	'U_VERB_TOPIC'	=> append_sid("forum.$phpEx?mode=verb"),
	'U_HOT_TOPIC'	=> append_sid("forum.$phpEx?mode=hot"),
	
	'TOTLA_TOPICS'	=> sprintf($lang['Topiced_total'], $totla_topics),
	'TOTAL_POSTS' 	=> sprintf($lang['Posted_articles_total'], $total_posts),
	'TOTAL_ATTACH'	=> sprintf($lang['Attach_total'], $total_attach),
	'TOTAL_USERS'	=> sprintf($lang['Registered_users_total'], $total_users),
	'NEWEST_USER'	=> sprintf($lang['Newest_user'], '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$newest_uid") . '">', $newest_user, '</a>'))
);

$display_categories = array();

for ($i = 0; $i < $total_forums; $i++ )
{
	if ($is_auth_ary[$forum_data[$i]['forum_id']]['auth_view'])
	{
		$display_categories[$forum_data[$i]['cat_id']] = true;
	}
}

for($i = 0; $i < $total_categories; $i++)
{
	$cat_id = $category_rows[$i]['cat_id'];

	if (isset($display_categories[$cat_id]) && $display_categories[$cat_id])
	{
		if ($category_rows[$i]['cat_icon'] != '')
		{
			$c_icon = '&nbsp;<img src="' . $category_rows[$i]['cat_icon'] . '"/>&nbsp;';
		}
        else 
		{
			$c_icon = '';
		}
		$template->assign_block_vars('catrow', array(
			'CAT_DESC' 		=> $category_rows[$i]['cat_title'],
			'CAT_ICON' 		=> $c_icon,
			'U_VIEWCAT' 	=> append_sid("forum.$phpEx?" . POST_CAT_URL . "=$cat_id"))
		);

		if ( $viewcat == $cat_id || $viewcat == -1 )
		{
			for($j = 0; $j < $total_forums; $j++)
			{
				if ( $forum_data[$j]['cat_id'] == $cat_id )
				{
					$forum_id = $forum_data[$j]['forum_id'];

					if ( $is_auth_ary[$forum_id]['auth_view'] )
					{
						$posts = $forum_data[$j]['forum_posts'];
						
						if ($forum_data[$j]['forum_icon'] != '')
						{
							$f_icon = '&nbsp;<img src="'.$forum_data[$j]['forum_icon'].'">&nbsp;';
						}
						else 
						{
							$f_icon = '';	
						}
						$template->assign_block_vars('catrow.forumrow',	array(
							'FORUM_ICON' 	=> $f_icon,
							'FORUM_NAME' 	=> $forum_data[$j]['forum_name'],
							'POSTS' 		=> $forum_data[$j]['forum_posts'],
							'TOPICS' 		=> $forum_data[$j]['forum_topics'],
							'U_VIEWFORUM' 	=> append_sid("viewforum.$phpEx?" . POST_FORUM_URL . "=$forum_id"))// 内建函数，创建 sid 用
						);
					}
				}
			}
		}
	}
}

// 生成页面
$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>