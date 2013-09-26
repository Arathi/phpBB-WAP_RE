<?php
/*************************************************
 *		memberlist.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 ************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_VIEWMEMBERS);
init_userprefs($userdata);

$start1 = intval($HTTP_POST_VARS['start1']);
if ( !empty($start1) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['topics_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

// 黑名单
if (isset($HTTP_GET_VARS['ban']))
{
	$page_title = '黑名单';
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
	$template->set_filenames(array(
		'body' => 'banlist_body.tpl')
	);

	$sql = "SELECT u.username, u.user_id, u.user_posts, r.user_id, r.expire, r.modification 
		FROM " . REPUTATION_TABLE . " r, " . USERS_TABLE . " u 
		WHERE u.user_id <> " . ANONYMOUS . " AND u.user_id = r.user_id AND r.modification = 4 AND r.expire > " . time() . " 
		ORDER BY r.expire ASC LIMIT $start, " . $board_config['topics_per_page'];
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$user_id = $row['user_id'];
			$username = $row['username'];
			$posts = $row['user_posts'];
			$expire = create_date('d M, G:i', $row['expire'], $board_config['board_timezone']);

			$template->assign_block_vars('banrow', array(
				'USERNAME' => $username,
				'POSTS' => $posts,
				'EXPIRE' => $expire,
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	}
	else
	{
		message_die(GENERAL_MESSAGE, $lang['No_banlist']);
	}

	$sql = "SELECT count(*) AS total 
		FROM " . REPUTATION_TABLE . " r, " . USERS_TABLE . " u 
		WHERE u.user_id <> " . ANONYMOUS . " AND u.user_id = r.user_id AND r.modification = 4 AND r.expire > " . time();
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
	}

	if ( $total = $db->sql_fetchrow($result) )
	{
		$total_members = $total['total'];
		$pagination = generate_pagination("memberlist.$phpEx?ban", $total_members, $board_config['topics_per_page'], $start). '';
	}
	$db->sql_freeresult($result);

	if ( $total_members > $board_config['topics_per_page'] )
	{
		$template->assign_vars(array(
			'PAGINATION' => $pagination)
		);
	}
	$template->pparse('body');
	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

}
// 超级管理员和版主列表
elseif (isset($HTTP_GET_VARS['admin']))
{
	$page_title = '管理员';
	
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
	$exclude_users = '';
	$template->assign_block_vars('switch_list_staff', array());
	
	$template->set_filenames(array(
		'body' => 'staff_body.tpl')
	);

	$is_auth_ary = array();
	$is_auth_ary = auth(AUTH_VIEW, AUTH_LIST_ALL, $userdata, $forums);

	$sql = "SELECT count(*) AS total
		FROM " . FORUMS_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting total forums', '', __LINE__, __FILE__, $sql);
	}
	$total = $db->sql_fetchrow($result);
	$total_forums = $total['total'];

	$sql_forums = "SELECT ug.user_id, f.forum_id, f.forum_name
				FROM ". AUTH_ACCESS_TABLE ." aa, ". USER_GROUP_TABLE ." ug, ". FORUMS_TABLE ." f
				WHERE aa.auth_mod = ". TRUE ." AND ug.group_id = aa.group_id AND f.forum_id = aa.forum_id
		        ORDER BY f.forum_order";
				
	if( !$result_forums = $db->sql_query($sql_forums) )
	{
		message_die(GENERAL_ERROR, 'could not query forums.', '', __LINE__, __FILE__, $sql_forums);
	}
	while( $row = $db->sql_fetchrow($result_forums) )
	{
		$display_forums = ( $is_auth_ary[$row['forum_id']]['auth_view'] ) ? true : false;
		if( $display_forums )
		{
			$forum_id = $row['forum_id'];
			$staff2[$row['user_id']][$row['forum_id']] = '<a href="'. append_sid("viewforum.$phpEx?f=$forum_id") .'">'. $row['forum_name'] .'</a>';
		}
	}
	$db->sql_freeresult($result_forums);

	$level_cat = $lang['Staff_level'];
	for( $i = 0; $i < count($level_cat); $i++ )
	{
		$user_level = $level_cat[$i];

		$template->assign_block_vars('switch_list_staff.user_level', array(
			'USER_LEVEL' => $user_level)
		);

		if( $level_cat['0'] )
		{
			$where = 'user_level = '. ADMIN;
		}
		else if( $level_cat['1'] )
		{
			$where = 'user_level = '. MODCP;
		}
		else if( $level_cat['2'] )
		{
			$where = 'user_level = '. MOD;
		}

		$level_cat[$i] = '';

		$sql_exclude_users = ( !empty($exclude_users) ) ? ' AND user_id NOT IN ('. $exclude_users .')' : '';
		
		$sql_user = "SELECT * FROM ". USERS_TABLE ." 
				WHERE $where $sql_exclude_users 
				ORDER BY user_regdate";
				
		if( !($result_user = $db->sql_query($sql_user)) )
		{
			message_die(GENERAL_ERROR, 'could not obtain user information.', '', __LINE__, __FILE__, $sql_user);
		}
		if( $staff = $db->sql_fetchrow($result_user) )
		{
			$k = 0;
			do
			{
				$user_id = $staff['user_id'];
				$user_status = ( $staff['user_session_time'] >= (time() - 60) ) ? (( $row['user_allow_viewonline'] ) ? $lang['Staff_online'] : (( $userdata['user_level'] == ADMIN || $userdata['user_id'] == $user_id ) ? $lang['Staff_online'] : '')) : '';

				$forums = '';
				if( !empty($staff2[$staff['user_id']]) )
				{
					asort($staff2[$staff['user_id']]);
					$forums = implode(', ',$staff2[$staff['user_id']]);
				}
				if ( $total_forums == count($staff2[$staff['user_id']]) )
				{
					$forums = '<a href="'. append_sid("index.$phpEx") .'">全部论坛</a>';
				}
				if ( $staff['user_level'] == 1 )
				{
					$forums = '';
				}

				$template->assign_block_vars('switch_list_staff.user_level.staff', array(
					'USERNAME' => $staff['username'],
					'POSTS' => $staff['user_posts'],
					'USER_STATUS' => $user_status,
					'U_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL ."=$user_id"),
					'FORUMS' => $forums,
				));
				$k++;
			}
			while( $staff = $db->sql_fetchrow($result_user) );
			$db->sql_freeresult($result_user);
		}
		else
		{
			$template->assign_block_vars('switch_list_staff.user_level.no_staff', array());
		}
	}

	$template->pparse('body');
	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

}
else
{
	if ( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
	{
		$mode = ( isset($HTTP_POST_VARS['mode']) ) ? htmlspecialchars($HTTP_POST_VARS['mode']) : htmlspecialchars($HTTP_GET_VARS['mode']);
	}
	else
	{
		$mode = 'posts';
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
		$sort_order = 'DESC';
	}

	$mode_types_text = array($lang['Sort_Joined'], $lang['Sort_Username'], $lang['Sort_Posts'], '金币数量', $lang['Sort_Top_Ten']);
	$mode_types = array('joined', 'username', 'posts', 'money', 'topten');

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
	
	$page_title = '会员';
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
	
	$template->set_filenames(array(
		'body' => 'memberlist_body.tpl')
	);

	$template->assign_vars(array(
		'L_SELECT_SORT_METHOD' 	=> $lang['Select_sort_method'],
		'L_ORDER' 				=> $lang['Order'],
		'L_SORT' 				=> $lang['Sort'],
		'L_SUBMIT' 				=> $lang['Sort'],
		'L_SEARCH_USER' 		=> $lang['Search_user'],
		
		'U_SEARCH_USER' 		=> append_sid("search.$phpEx?mode=searchuser"),

		'S_MODE_SELECT' 		=> $select_sort_mode,
		'S_ORDER_SELECT' 		=> $select_sort_order,
		'S_MODE_ACTION' 		=> append_sid("memberlist.$phpEx"))
	);

	switch( $mode )
	{
		case 'joined':
			$order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'username':
			$order_by = "username $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'posts':
			$order_by = "user_posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'money':
			$order_by = "user_points $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'topten':
			$order_by = "user_posts $sort_order LIMIT 10";
			break;
		default:
			$order_by = "user_posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
	}

	$sql = "SELECT username, user_id, user_level, user_posts, user_points, user_nic_color 
		FROM " . USERS_TABLE . "
		WHERE user_id <> " . ANONYMOUS . " 
		ORDER BY $order_by";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$number = $i + $start + 1;
			$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
			$username = $row['username'];
			$user_color = ( !empty($row['user_nic_color']) ) ? ' style="color: ' . $row['user_nic_color'] . '"' : '';
			$user_id = $row['user_id'];
			$posts = ( $mode == 'money' ) ? $row['user_points'] : $row['user_posts'];

			$template->assign_block_vars('memberrow', array(
				'USERNAME' => $username,
				'NUMBER' => $number,
				'ROW_CLASS' => $row_class,
				'POSTS' => $posts,
				'COLOR' => $user_color,
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	}

	if ( $mode != 'topten' || $board_config['topics_per_page'] < 10 )
	{
		$sql = "SELECT count(*) AS total
			FROM " . USERS_TABLE . "
			WHERE user_id <> " . ANONYMOUS;

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
		}

		if ( $total = $db->sql_fetchrow($result) )
		{
			$total_members = $total['total'];
			$pagination = generate_pagination("memberlist.$phpEx?mode=$mode&amp;order=$sort_order", $total_members, $board_config['topics_per_page'], $start). '';
		}
		$db->sql_freeresult($result);
	}
	else
	{
		$pagination = '';
		$total_members = 10;
	}

	$template->assign_vars(array(
		'PAGINATION' => $pagination)
	);

	$template->pparse('body');
	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}

?>