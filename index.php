<?php
/**********************************
 *		index.php
 *		--------
 *		简体中文：爱疯的云
 *		描述：首页（phpBB-WAP v4.0）
 **********************************/

define('IN_PHPBB', true);// 常量
$phpbb_root_path = './';// 路径
include($phpbb_root_path . 'extension.inc');// 文件的头部
include($phpbb_root_path . 'common.'.$phpEx);// 包含常用文件等
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);// 包含BBcode函数
include($phpbb_root_path . 'includes/functions_bbcode.'.$phpEx);

$this_file = basename(__FILE__);

// 开启 session 管理
$userdata = session_pagestart($user_ip, PAGE_INDEX);// session_pagestart() 为内建函数，PAGE_INDEX 这些为常量
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

// 论坛顶部和底部的广告
$verh = '';
$niz = '';
$sql = "SELECT * 
	FROM ".$table_prefix."shop_sites
	ORDER BY id ASC";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
}
if ( $row = $db->sql_fetchrow($result) )
{
	$i = 0;
	do
	{
		if ( $row['site_order'] )
		{
			if ( $row['site_time'] < time() )
			{
				$sqldel = "DELETE FROM " . $table_prefix . "shop_sites WHERE id = " . $row['id'];
				$resultdel = $db->sql_query($sqldel);
			}
			else
			{
				$niz .= '<a href="http://' . $row['site_url'] . '">' . $row['site_desc'] . '</a><br />';
			}
		}
		else
		{
			if ( $row['site_time'] < time() )
			{
				$sqldel = "DELETE FROM ".$table_prefix."shop_sites WHERE id = " . $row['id'];
				$resultdel = $db->sql_query($sqldel);
			}
			else
			{
				$verh .= '<a href="http://' . $row['site_url'] . '">' . $row['site_desc'] . '</a><br />';
			}
		}

		$i++;
	}
	while ( $row = $db->sql_fetchrow($result) );
	$db->sql_freeresult($result);
}

//黑名单，该函数为内建函数
$ban_information = session_userban($user_ip, $userdata['user_id']);
//用户被加黑时提示
if ($ban_information)
{
	$ban = '<div style="border: 1px solid #d4d6d4;background:#ffffa0;color:#494949;margin-bottom:4px;border-radius:6px;padding:5px"><span style="color: red; font-weight: bold;">注意！</span><br/>您的'.$ban_information.'</div>';
}
else
{
	$ban = '';
}

// 生日
$sql = ($board_config['birthday_check_day']) ? "SELECT user_id, username, user_birthday,user_level FROM " . USERS_TABLE. " WHERE user_birthday!=999999 ORDER BY username" :"";
if($result = $db->sql_query($sql)) 
{ 
	if (!empty($result)) 
	{ 
		$time_now = time();
		// create_date() 为内建函数
		$this_year = create_date('Y', $time_now, $board_config['board_timezone']);
		$date_today = create_date('Ymd', $time_now, $board_config['board_timezone']);
		$date_forward = create_date('Ymd', $time_now+($board_config['birthday_check_day']*86400), $board_config['board_timezone']);
		while ($birthdayrow = $db->sql_fetchrow($result))
		{ 
			$user_birthday2 = $this_year.($user_birthday = realdate("md",$birthdayrow['user_birthday'] )); 
			if ( $user_birthday2 < $date_today ) $user_birthday2 += 10000;
			if ( $user_birthday2 > $date_today  && $user_birthday2 <= $date_forward ) 
			{ 
				$user_age = ( $this_year.$user_birthday < $date_today ) ? $this_year - realdate ('Y',$birthdayrow['user_birthday'])+1 : $this_year- realdate ('Y',$birthdayrow['user_birthday']); 
				// switch 语句
				switch ($birthdayrow['user_level'])
				{
					case ADMIN :
		      			$birthdayrow['username'] = $birthdayrow['username']; 
      					$style_color = 'style="color:#ffcc00"';
						break;
					case MOD :
		      			$birthdayrow['username'] = $birthdayrow['username']; 
      					$style_color = 'style="color:#943043"';
						break;
					default: $style_color = '';
				}
				$birthday_week_list .= ' <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $birthdayrow['user_id']) . '"' . $style_color .'>' . $birthdayrow['username'] . ' ('.$user_age.')</a>,'; 

			} else if ( $user_birthday2 == $date_today ) { 

				$user_age = $this_year - realdate ( 'Y',$birthdayrow['user_birthday'] ); 
				switch ($birthdayrow['user_level'])
				{
					case ADMIN :
		      			$birthdayrow['username'] = $birthdayrow['username']; 
      					$style_color = 'style="color:#ffcc00"';
						break;
					case MOD :
			      		$birthdayrow['username'] = $birthdayrow['username']; 
      					$style_color = 'style="color:#943043"';
						break;
					default: $style_color = '';
				}

				$birthday_today_list .= ' <a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $birthdayrow['user_id']) . '"' . $style_color .'>' . $birthdayrow['username'] . ' ('.$user_age.')</a>,'; 
			}
			 
		}
		if ($birthday_today_list) $birthday_today_list[ strlen( $birthday_today_list)-1] = ' ';
		if ($birthday_week_list) $birthday_week_list[ strlen( $birthday_week_list)-1] = ' ';
	} 
	$db->sql_freeresult($result);
}

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

//
// 聊天室动态
//
$num_shout = 1;//显示聊天内容条数
$sql = "SELECT s.*, u.* FROM " . SHOUTBOX_TABLE . " s, " . USERS_TABLE . " u
	WHERE s.shout_user_id = u.user_id 
	ORDER BY s.shout_session_time DESC 
	LIMIT 0, " . $num_shout;
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not get shoutbox information', '', __LINE__, __FILE__, $sql);
}
while ($shout_row = $db->sql_fetchrow($result))
{
	$user_id = $shout_row['shout_user_id'];
	$shout_username = ( $user_id == ANONYMOUS ) ? (( $shout_row['shout_username'] == '' ) ? $lang['Guest'] : $shout_row['shout_username'] ) : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&u=$user_id ") . '">' . $shout_row['username'] . '</a>';
	$shout = (! $shout_row['shout_active']) ? $shout_row['shout_text'] : $lang['Shout_censor'].(($is_auth['auth_mod']) ? '<br/><hr/><br/>'.$shout_row['shout_text'] : '');
	$shout = smilies_pass($shout);
	$shout = bbencode_second_pass($shout,$shout_row['shout_bbcode_uid']);
	$shout = str_replace("\n", "\n<br />\n", $shout);

	if( $userdata['user_on_off'] == 1)
	{
		if ( $shout_row['user_session_time'] >= (time()-$board_config['online_time']) )
		{
			if ($shout_row['user_allow_viewonline'])
			{
				$online_status = '<span' . $online_color . '>' . $lang['Online'] . '</span>';
			}
			else if ( $is_auth['auth_mod'] || $userdata['user_id'] == $poster_id )
			{
				$online_status = '<span' . $hidden_color . '>' . $lang['Hidden'] . '</span>';
			}
			else
			{
				$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
			}
		}
		else
		{
			$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
		}
	} 
	else 
	{
		$online_status = '';
	}

	$template->assign_block_vars('shoutrow', array(
		'SHOUT' 				=> $shout,
		'TIME' 					=> create_date('G:i', $shout_row['shout_session_time'], $board_config['board_timezone']),
		'SHOUT_USERNAME' 		=> $shout_username,
		'POSTER_ONLINE_STATUS' 	=> $online_status)
	);
}

// 统计
$totla_topics 		= get_db_stat('topiccount');
$total_posts 		= get_db_stat('postcount');
$total_users 		= get_db_stat('usercount');
$total_attach 		= get_db_stat('attachcount');
$newest_userdata 	= get_db_stat('newestuser');
$newest_user 		= $newest_userdata['username'];
$newest_uid 		= $newest_userdata['user_id'];

// 显示首页公告
if (!empty($board_config['index_announcement']))
{
	$announcement = smilies_pass($board_config['index_announcement']);// 内建函数，用于解析表情代码
	$announcement = str_replace("\n", "\n<br/>\n", $announcement);

	$template->assign_block_vars('announcement', array(
		'ANNOUNCEMENT' => $announcement)
	);
}

// 首页内容
// 查询内容的SQL语句
$sql = "SELECT index_content FROM " . INDEX_PAGE_TABLE;

// 执行查询
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain index page data for pages', '', __LINE__, __FILE__, $sql);
}

// 循环出结果
if ( !($indexdate = $db->sql_fetchrow($result)) )
{
	message_die(GENERAL_MESSAGE, '无法得到网页的内容！');
}

$index_body = $indexdate['index_content'];// 得出数据库里面的内容

// 先把实体标签转换为普通标签
$index_body = htmlspecialchars_decode($index_body);


//首页特殊BBcode
$index_body = str_replace('[totla_topics]', $totla_topics, $index_body);
$index_body = str_replace('[total_posts]', $total_posts, $index_body);
$index_body = str_replace('[total_users]', $total_users, $index_body);
$index_body = str_replace('[total_attach]', $total_attach, $index_body);
$index_body = str_replace('[new_userlink]', $newest_userdata, $index_body);
$index_body = str_replace('[new_user]', $newest_user, $index_body);
$index_body = str_replace('[new_userid]', $newest_uid, $index_body);

// 解析BBcode
$index_body = bbencode_second_pass($index_body, $bbcode_uid);
$index_body = make_clickable($index_body);
$index_body = page_bbcode($index_body);

// 解析表情
$index_body = smilies_pass($index_body);

// 后台地址
if( ( $userdata['user_level'] == ADMIN ) )
{
	$admin_link = '<a href="admin/index.' . $phpEx . '?sid=' . $userdata['session_id'] . '">' . $lang['Admin_panel'] . '</a>';
	$edit_index = '<a href="admin/edit_page.' . $phpEx . '?mode=index&amp;sid=' . $userdata['session_id'] . '">页面编辑模式</a>';
	
	$template->assign_vars(array(
		'EDIT_INDEX' => $edit_index,
		'ADMIN_LINK' => $admin_link)
	);
	
	$template->assign_block_vars('switch_admin_link', array() );
}
else if ( $userdata['user_level'] == MODCP )
{
	$modcp_link = '<a href="modcp/index.' . $phpEx . '?sid=' . $userdata['session_id'] . '">' . $lang['Mod_CP'] . '</a>';
		$template->assign_vars(array(
		'MODCP_LINK' => $modcp_link)
	);
	
	$template->assign_block_vars('switch_modcp_link', array() );
}
else
{
	$template->assign_vars(array(
		'ADMIN_LINK' => '')
	);
}
$page_title = $board_config['sitename'];// 定义首页标题
include($phpbb_root_path . 'includes/page_header.'.$phpEx);// 头部文件

// 使用模版文件
$template->set_filenames(array(
	'body' => 'index_body.tpl')
);

// 定义模版语言
$template->assign_vars(array(
	'EMPTY_SHOW' 			=> $empty_show,
	
	'U_NEW_TOPIC'			=> append_sid("index.$phpEx?mode=new"),
	'U_VERB_TOPIC'			=> append_sid("index.$phpEx?mode=verb"),
	'U_HOT_TOPIC'			=> append_sid("index.$phpEx?mode=hot"),
	
	'USERNAME'				=> $userdata['username'],
	
	'INDEX_PAGE_BODY' 		=> $index_body,
	'VERH' 					=> $verh,
	'NIZ' 					=> $niz,
	'BAN_INFO' 				=> $ban)
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
			'U_VIEWCAT' 	=> append_sid("index.$phpEx?" . POST_CAT_URL . "=$cat_id"))
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

// 定义网页内容
$template->pparse('body');

// 底部文件
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>
