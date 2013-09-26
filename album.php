<?php
/***************************************
 *		album.php
 *		-------------------
 *   Разработка: (C) 2003 Smartor
 *   Модификация: Гутник Игорь ( чел )
 *          2012 год
 *	 简体中文：爱疯的云
 ***************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
$album_root_path = $phpbb_root_path . 'album/';
$opera_mini = "./opera_mini";
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_ALBUM);
init_userprefs($userdata);

include($album_root_path . 'album_common.'.$phpEx);

if ( isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']) )
{
	$action = ( isset($HTTP_POST_VARS['action']) ) ? htmlspecialchars($HTTP_POST_VARS['action']) : htmlspecialchars($HTTP_GET_VARS['action']);
}
else
{
	$action = '';
}

if ( $action == 'cat' )
{
	if( isset($HTTP_POST_VARS['cat_id']) )
	{
		$cat_id = intval($HTTP_POST_VARS['cat_id']);
	}
	else if( isset($HTTP_GET_VARS['cat_id']) )
	{
		$cat_id = intval($HTTP_GET_VARS['cat_id']);
	}
	else
	{
		message_die(GENERAL_ERROR, 'No categories specified');
	}

	if ($cat_id == PERSONAL_GALLERY)
	{
		redirect(append_sid("album.$phpEx?action=personal"));
	}

	$sql = "SELECT c.*, COUNT(p.pic_id) AS count
		FROM ". ALBUM_CAT_TABLE ." AS c LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
		WHERE c.cat_id <> 0
		GROUP BY c.cat_id
		ORDER BY cat_order";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = array();
	$catrows = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$album_user_access = album_user_access($row['cat_id'], $row, 1, 0, 0, 0, 0, 0); // VIEW
		if ($album_user_access['view'] == 1)
		{
			$catrows[] = $row;

			if( $row['cat_id'] == $cat_id )
			{
				$thiscat = $row;
				$auth_data = album_user_access($cat_id, $row, 1, 1, 1, 1, 1, 1); // ALL
				$total_pics = $thiscat['count'];
			}
		}
	}

	if (empty($thiscat))
	{
		message_die(GENERAL_MESSAGE, $lang['Category_not_exist']);
	}

	if( !$auth_data['view'] )
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=cat&cat_id=$cat_id"));		
		}
		else
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}

	$auth_key = array_keys($auth_data);

	$auth_list = '';
	for ($i = 0; $i < (count($auth_data) - 1); $i++)
	{
		if( ( ($album_config['rate'] == 0) and ($auth_key[$i] == 'rate') ) or ( ($album_config['comment'] == 0) and ($auth_key[$i] == 'comment') ) )
		{
			continue;
		}

		$auth_list .= ($auth_data[$auth_key[$i]] == 1) ? $lang['Album_'. $auth_key[$i] .'_can'] : $lang['Album_'. $auth_key[$i] .'_cannot'];
		$auth_list .= '<br />';
	}

	if( ($userdata['user_level'] == ADMIN) or ($auth_data['moderator'] == 1) )
	{
		$auth_list .= sprintf($lang['Album_moderate_can'], '<a href="'. append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") .'">', '</a>');
		$moderka = '<a href="'. append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") .'">'.$lang['Album_moderka'].'</a>';
	}

	$grouprows = array();
	$moderators_list = '';

	if ($thiscat['cat_moderator_groups'] != '')
	{
		$sql = "SELECT group_id, group_name, group_type, group_single_user
			FROM " . GROUPS_TABLE . "
			WHERE group_single_user <> 1
				AND group_type <> ". GROUP_HIDDEN ."
				AND group_id IN (". $thiscat['cat_moderator_groups'] .")
			ORDER BY group_name ASC";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not get group list', '', __LINE__, __FILE__, $sql);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$grouprows[] = $row;
		}

		if( count($grouprows) > 0 )
		{
			for ($j = 0; $j < count($grouprows); $j++)
			{
				$group_link = '<a href="'. append_sid("groupcp.$phpEx?". POST_GROUPS_URL .'='. $grouprows[$j]['group_id']) .'">'. $grouprows[$j]['group_name'] .'</a>';
				$moderators_list .= ($moderators_list == '') ? $group_link : ', ' . $group_link;
			}
		}
	}

	if( empty($moderators_list) )
	{
		$moderators_list = $lang['None'];
	}
	$pics_per_page = $album_config['rows_per_page'] * $album_config['cols_per_page'];
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $pics_per_page);
}
else
{
	if( isset($HTTP_GET_VARS['start']) )
	{
		$start = intval($HTTP_GET_VARS['start']);
	}
	else if( isset($HTTP_POST_VARS['start']) )
	{
		$start = intval($HTTP_POST_VARS['start']);
	}
	else
	{
		$start = 0;
	}
	$start = ($start < 0) ? 0 : $start;
}

	if( isset($HTTP_GET_VARS['sort_method']) )
	{
		switch ($HTTP_GET_VARS['sort_method'])
		{
			case 'pic_time':
				$sort_method = 'p.pic_time';
				break;
			case 'pic_title':
				$sort_method = 'p.pic_title';
				break;
			case 'username':
				$sort_method = 'u.username';
				break;
			case 'pic_view_count':
				$sort_method = 'p.pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else if( isset($HTTP_POST_VARS['sort_method']) )
	{
		switch ($HTTP_POST_VARS['sort_method'])
		{
			case 'pic_time':
				$sort_method = 'p.pic_time';
				break;
			case 'pic_title':
				$sort_method = 'p.pic_title';
				break;
			case 'username':
				$sort_method = 'u.username';
				break;
			case 'pic_view_count':
				$sort_method = 'p.pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else
	{
		$sort_method = $album_config['sort_method'];
	}

	if( isset($HTTP_GET_VARS['sort_order']) )
	{
		switch ($HTTP_GET_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else if( isset($HTTP_POST_VARS['sort_order']) )
	{
		switch ($HTTP_POST_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else
	{
		$sort_order = $album_config['sort_order'];
	}

	if ($total_pics > 0)
	{
		$limit_sql = ($start == 0) ? $pics_per_page : $start .','. $pics_per_page;

		$pic_approval_sql = 'AND p.pic_approval = 1';
		if ($thiscat['cat_approval'] != ALBUM_USER)
		{
			if( ($userdata['user_level'] == ADMIN) or (($auth_data['moderator'] == 1) and ($thiscat['cat_approval'] == ALBUM_MOD)) )
			{
				$pic_approval_sql = '';
			}
		}

		$sql = "SELECT p.pic_id, p.pic_title, p.pic_desc, p.pic_user_id, p.pic_user_ip, p.pic_username, p.pic_time, p.pic_cat_id, p.pic_view_count, p.pic_lock, p.pic_approval, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments, MAX(c.comment_id) as new_comment
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE p.pic_cat_id = '$cat_id' $pic_approval_sql
			GROUP BY p.pic_id
			ORDER BY $sort_method $sort_order
			LIMIT $limit_sql";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query pics information', '', __LINE__, __FILE__, $sql);
		}

		$picrow = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$picrow[] = $row;
		}


		for ($i = 0; $i < count($picrow); $i += $album_config['cols_per_page'])
		{
			for ($j = $i; $j < ($i + $album_config['cols_per_page']); $j++)
			{
				if( $j >= count($picrow) )
				{
					break;
				}

				if(!$picrow[$j]['rating'])
				{
					$picrow[$j]['rating'] = $lang['Not_rated'];
				}
				else
				{
					$picrow[$j]['rating'] = round($picrow[$j]['rating'], 2);
				}

				if ($thiscat['cat_approval'] != ALBUM_USER)
				{
					if( ($userdata['user_level'] == ADMIN) or (($auth_data['moderator'] == 1) and ($thiscat['cat_approval'] == ALBUM_MOD)) )
					{
						$approval_mode = ($picrow[$j]['pic_approval'] == 0) ? 'approval' : 'unapproval';
						$approval_link = '<a href="'. append_sid("album.$phpEx?action=modcp&amp;mode=$approval_mode&amp;pic_id=". $picrow[$j]['pic_id']) .'">';
						$approval_link .= ($picrow[$j]['pic_approval'] == 0) ? '<b>'. $lang['Approve'] .'</b>' : $lang['Unapprove'];
						$approval_link .= '</a><br/>';
					}
				}

				if( ($picrow[$j]['user_id'] == ALBUM_GUEST) or ($picrow[$j]['username'] == '') )
				{
					$pic_poster = ($picrow[$j]['pic_username'] == '') ? $lang['Guest'] : $picrow[$j]['pic_username'];
				}
				else
				{
					$pic_poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $picrow[$j]['user_id']) .'">'. $picrow[$j]['username'] .'</a>';
				}

				$row_class = ( !($j % 2) ) ? 'row_easy' : 'row_hard';

				$template->assign_block_vars('picrow', array(
					'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.$phpEx?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
					'TITLE' => $picrow[$j]['pic_title'],
					'ROW_CLASS' => $row_class,
					'POSTER' => $pic_poster,
					'TIME' => create_date($board_config['default_dateformat'], $picrow[$j]['pic_time'], $board_config['board_timezone']),
					'VIEW' => $picrow[$j]['pic_view_count'],
					'RATING' => ($album_config['rate'] == 1) ? ( '<a href="'. append_sid("album.$phpEx?action=rate&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Rating'] . '</a>: ' . $picrow[$j]['rating'] . '<br />') : '',
					'COMMENTS' => ($album_config['comment'] == 1) ? ( '<a href="'. append_sid("album.$phpEx?action=comment&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Comments'] . '</a>: ' . $picrow[$j]['comments'] . '<br />') : '',
					'EDIT' => ( ( $auth_data['edit'] and ($picrow[$j]['pic_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_edit_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.$phpEx?action=edit&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Edit_pic'] . '</a>|' : '',
					'DELETE' => ( ( $auth_data['delete'] and ($picrow[$j]['pic_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_delete_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.$phpEx?action=delete&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Delete_pic'] . '</a>|' : '',
					'MOVE' => ($auth_data['moderator']) ? '<a href="'. append_sid("album.$phpEx?action=modcp&amp;mode=move&amp;pic_id=". $picrow[$j]['pic_id']) .'">'. $lang['Move'] .'</a>' : '',
					'LOCK' => ($auth_data['moderator']) ? '<a href="'. append_sid("album.$phpEx?action=modcp&amp;mode=". (($picrow[$j]['pic_lock'] == 0) ? 'lock' : 'unlock') ."&amp;pic_id=". $picrow[$j]['pic_id']) .'">'. (($picrow[$j]['pic_lock'] == 0) ? $lang['Lock'] : $lang['Unlock']) .'</a>|' : '',
					'IP' => ($userdata['user_level'] == ADMIN) ? $lang['IP_Address'] . ': ' . decode_ip($picrow[$j]['pic_user_ip']) .'<br />' : ''
					)
				);

				$template->assign_block_vars('picrow.piccol', array(
					'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.$phpEx?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
					'THUMBNAIL' => append_sid("album.$phpEx?action=thumbnail&amp;pic_id=". $picrow[$j]['pic_id']),
					'DESC' => $picrow[$j]['pic_desc'],
					'APPROVAL' => $approval_link,
					)
				);
			}
		}

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination(append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start))
		);
	}
	else
	{
		$template->assign_block_vars('no_pics', array());
	}

	$album_jumpbox = '<form name="jumpbox" action="'. append_sid("album.$phpEx?action=cat") .'" method="post">';
	$album_jumpbox .= $lang['Jump_to'] . ':&nbsp;<select name="cat_id">';
	for ($i = 0; $i < count($catrows); $i++)
	{
		$album_jumpbox .= '<option value="'. $catrows[$i]['cat_id'] .'"';
		$album_jumpbox .= ($catrows[$i]['cat_id'] == $cat_id) ? 'selected="selected"' : '';
		$album_jumpbox .= '>' . $catrows[$i]['cat_title'] .'</option>';
	}
	$album_jumpbox .= '</select>';
	$album_jumpbox .= '&nbsp;<input type="submit" value="'. $lang['Go'] .'" />';
	$album_jumpbox .= '<input type="hidden" name="sid" value="'. $userdata['session_id'] .'" />';
	$album_jumpbox .= '</form>';

	$sort_rating_option = '';
	$sort_comments_option = '';
	if( $album_config['rate'] == 1 )
	{
		$sort_rating_option = '<option value="rating" ';
		$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
		$sort_rating_option .= '>' . $lang['Rating'] .'</option>';
	}
	if( $album_config['comment'] == 1 )
	{
		$sort_comments_option = '<option value="comments" ';
		$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
		$sort_comments_option .= '>' . $lang['Comments'] .'</option>';
		$sort_new_comment_option = '<option value="new_comment" ';
		$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
		$sort_new_comment_option .= '>' . $lang['New_Comment'] .'</option>';
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_cat_body.tpl')
	);

	$template->assign_vars(array(
		'U_VIEW_CAT' => append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id"),
		'CAT_TITLE' => $thiscat['cat_title'],
		'L_MODERATORS' => $lang['Moderators'],
		'MODERATORS' => $moderators_list,
		'U_UPLOAD_PIC' => append_sid("album.$phpEx?action=upload&amp;cat_id=$cat_id"),
		'UPLOAD_PIC_IMG' => $images['upload_pic'],
		'L_UPLOAD_PIC' => $lang['Upload_Pic'],
		'L_CATEGORY' => $lang['Category'],
		'L_NO_PICS' => $lang['No_Pics'],
		'S_COLS' => $album_config['cols_per_page'],
		'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
		'L_VIEW' => $lang['View'],
		'L_POSTER' => $lang['Poster'],
		'L_POSTED' => $lang['Posted'],
		'ALBUM_JUMPBOX' => $album_jumpbox,
		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id"),
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
		'L_SELECT_SORT_METHOD' => $lang['Select_sort_method'],
		'L_ORDER' => $lang['Order'],
		'L_SORT' => $lang['Sort'],
		'L_TIME' => $lang['Time'],
		'L_PIC_TITLE' => $lang['Pic_Title'],
		'L_USERNAME' => $lang['Sort_Username'],
		'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
		'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
		'SORT_USERNAME' => ($sort_method == 'pic_user_id') ? 'selected="selected"' : '',
		'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
		'SORT_RATING_OPTION' => $sort_rating_option,
		'SORT_COMMENTS_OPTION' => $sort_comments_option,
		'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
		'L_ASC' => $lang['Sort_Ascending'],
		'L_DESC' => $lang['Sort_Descending'],
		'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
		'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '',
		'U_MODERKA' => $moderka,
		'S_AUTH_LIST' => $auth_list)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} elseif ( $action == 'comment' ) {

	if( $album_config['comment'] == 0 )
	{
		message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
	}

	if( isset($HTTP_GET_VARS['pic_id']) )
	{
		$pic_id = intval($HTTP_GET_VARS['pic_id']);
	}
	else if( isset($HTTP_POST_VARS['pic_id']) )
	{
		$pic_id = intval($HTTP_POST_VARS['pic_id']);
	}
	else
	{
		if( isset($HTTP_GET_VARS['comment_id']) )
		{
			$comment_id = intval($HTTP_GET_VARS['comment_id']);
		}
		else if( isset($HTTP_POST_VARS['comment_id']) )
		{
			$comment_id = intval($HTTP_POST_VARS['comment_id']);
		}
		else
		{
			message_die(GENERAL_ERROR, 'Bad request');
		}
	}

if( isset($comment_id) )
{
	$sql = "SELECT comment_id, comment_pic_id
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query comment and pic information', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);

	if( empty($row) )
	{
		message_die(GENERAL_ERROR, 'This comment does not exist');
	}

	$pic_id = $row['comment_pic_id'];
}

$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id
		LIMIT 1";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$total_comments = $thispic['comments_count'];
$comments_per_page = $board_config['posts_per_page'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist'] . ' -> ' . $pic_id);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$auth_data = album_user_access($cat_id, $thiscat, 1, 0, 0, 1, 1, 1);

if ($auth_data['view'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=comment&pic_id=$pic_id"));
		exit;
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if( !isset($HTTP_POST_VARS['comment']) )
{
	if( !isset($comment_id) )
	{
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['topics_per_page']);
}
else
{
		if( isset($HTTP_GET_VARS['start']) )
		{
			$start = intval($HTTP_GET_VARS['start']);
		}
		else if( isset($HTTP_POST_VARS['start']) )
		{
			$start = intval($HTTP_POST_VARS['start']);
		}
		else
		{
			$start = 0;
		}
		$start = ($start < 0) ? 0 : $start;
}
	}
	else
	{
		$sql = "SELECT COUNT(comment_id) AS count
				FROM ". ALBUM_COMMENT_TABLE ."
				WHERE comment_pic_id = $pic_id
					AND comment_id < $comment_id";

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain comments information from the database', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);

		if( !empty($row) )
		{
			$start = floor( $row['count'] / $comments_per_page ) * $comments_per_page;
		}
		else
		{
			$start = 0;
		}
	}

	if( isset($HTTP_GET_VARS['sort_order']) )
	{
		switch ($HTTP_GET_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			default:
				$sort_order = 'DESC';
		}
	}
	else if( isset($HTTP_POST_VARS['sort_order']) )
	{
		switch ($HTTP_POST_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			default:
				$sort_order = 'DESC';
		}
	}
	else
	{
		$sort_order = 'ASC';
	}

	if ($total_comments > 0)
	{
		$limit_sql = ($start == 0) ? $comments_per_page : $start .','. $comments_per_page;

		$sql = "SELECT c.*, u.user_id, u.username
				FROM ". ALBUM_COMMENT_TABLE ." AS c
					LEFT JOIN ". USERS_TABLE ." AS u ON c.comment_user_id = u.user_id
				WHERE c.comment_pic_id = '$pic_id'
				ORDER BY c.comment_id $sort_order
				LIMIT $limit_sql";

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain comments information from the database', '', __LINE__, __FILE__, $sql);
		}

		$commentrow = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$commentrow[] = $row;
		}

		for ($i = 0; $i < count($commentrow); $i++)
		{
			if( ($commentrow[$i]['user_id'] == ALBUM_GUEST) or ($commentrow[$i]['username'] == '') )
			{
				$poster = ($commentrow[$i]['comment_username'] == '') ? $lang['Guest'] : $commentrow[$i]['comment_username'];
			}
			else
			{
				$poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $commentrow[$i]['user_id']) .'">'. $commentrow[$i]['username'] .'</a>';
			}

			if ($commentrow[$i]['comment_edit_count'] > 0)
			{
				$sql = "SELECT c.comment_id, c.comment_edit_user_id, u.user_id, u.username
						FROM ". ALBUM_COMMENT_TABLE ." AS c
							LEFT JOIN ". USERS_TABLE ." AS u ON c.comment_edit_user_id = u.user_id
						WHERE c.comment_id = '".$commentrow[$i]['comment_id']."'
						LIMIT 1";

				if( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not obtain last edit information from the database', '', __LINE__, __FILE__, $sql);
				}

				$lastedit_row = $db->sql_fetchrow($result);

				$edit_info = ($commentrow[$i]['comment_edit_count'] == 1) ? $lang['Edited_time_total'] : $lang['Edited_times_total'];

				$edit_info = '<br /><br />&raquo;&nbsp;'. sprintf($edit_info, $lastedit_row['username'], create_date($board_config['default_dateformat'], $commentrow[$i]['comment_edit_time'], $board_config['board_timezone']), $commentrow[$i]['comment_edit_count']) .'<br />';
			}
			else
			{
				$edit_info = '';
			}
			$commentrow[$i]['comment_text'] = smilies_pass($commentrow[$i]['comment_text']);
			$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';

			$template->assign_block_vars('commentrow', array(
				'ID' => $commentrow[$i]['comment_id'],
				'ROW_CLASS' => $row_class,
				'POSTER' => $poster,
				'TIME' => create_date($board_config['default_dateformat'], $commentrow[$i]['comment_time'], $board_config['board_timezone']),
				'IP' => ($userdata['user_level'] == ADMIN) ? '<br/>' . $lang['IP_Address'] . ': ' . decode_ip($commentrow[$i]['comment_user_ip']) : '',
				'TEXT' => nl2br($commentrow[$i]['comment_text']),
				'EDIT_INFO' => $edit_info,
				'EDIT' => ( ( $auth_data['edit'] and ($commentrow[$i]['comment_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_edit_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.$phpEx?action=comment_edit&amp;comment_id=". $commentrow[$i]['comment_id']) .'">'. $lang['Edit_pic'] .'</a>|' : '',
				'DELETE' => ( ( $auth_data['delete'] and ($commentrow[$i]['comment_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_delete_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.$phpEx?action=comment_delete&amp;comment_id=". $commentrow[$i]['comment_id']) .'">'. $lang['Delete_pic'] .'</a>' : ''
				)
			);
		}

		$template->assign_block_vars('switch_comment', array());

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination(append_sid("album.$phpEx?action=comment&amp;pic_id=$pic_id&amp;sort_order=$sort_order"), $total_comments, $comments_per_page, $start),
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $comments_per_page ) + 1 ), ceil( $total_comments / $comments_per_page ))
			)
		);
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_comment_body.tpl')
	);

	if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
	{
		$poster = ($thispic['pic_username'] == '') ? $lang['Guest'] : $thispic['pic_username'];
	}
	else
	{
		$poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
	}

	if ($auth_data['comment'] == 1)
	{
		$template->assign_block_vars('switch_comment_post', array());

		if( !$userdata['session_logged_in'] )
		{
			$template->assign_block_vars('switch_comment_post.logout', array());
		}
	}

	$template->assign_vars(array(
		'CAT_TITLE' => $thiscat['cat_title'],
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),
		'U_THUMBNAIL' => append_sid("album.$phpEx?action=thumbnail&amp;pic_id=$pic_id"),
		'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=$pic_id") : append_sid("album.$phpEx?action=page&amp;pic_id=$pic_id"),
		'PIC_TITLE' => $thispic['pic_title'],
		'PIC_DESC' => nl2br($thispic['pic_desc']),
		'POSTER' => $poster,
		'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
		'PIC_VIEW' => $thispic['pic_view_count'],
		'PIC_COMMENTS' => $total_comments,
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
		'L_PIC_TITLE' => $lang['Pic_Title'],
		'L_PIC_DESC' => $lang['Pic_Desc'],
		'L_POSTER' => $lang['Poster'],
		'L_POSTED' => $lang['Posted'],
		'L_VIEW' => $lang['View'],
		'L_COMMENTS' => $lang['Comments'],
		'L_POST_YOUR_COMMENT' => $lang['Post_your_comment'],
		'L_MESSAGE' => $lang['Message'],
		'L_USERNAME' => $lang['Username'],
		'L_COMMENT_NO_TEXT' => $lang['Comment_no_text'],
		'L_COMMENT_TOO_LONG' => $lang['Comment_too_long'],
		'L_MAX_LENGTH' => $lang['Max_length'],
		'S_MAX_LENGTH' => $album_config['desc_length'],
		'L_ORDER' => $lang['Order'],
		'L_SORT' => $lang['Sort'],
		'L_ASC' => $lang['Sort_Ascending'],
		'L_DESC' => $lang['Sort_Descending'],
		'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
		'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '',
		'L_SUBMIT' => $lang['Submit'],
		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=comment&amp;pic_id=$pic_id")
		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	if ($auth_data['comment'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=comment&pic_id=$pic_id"));
		}
		else
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}

	$comment_text = str_replace("\'", "''", htmlspecialchars(substr(trim($HTTP_POST_VARS['comment']), 0, $album_config['desc_length'])));
	$comment_username = (!$userdata['session_logged_in']) ? str_replace("\'", "''", substr(htmlspecialchars(trim($HTTP_POST_VARS['comment_username'])), 0, 32)) : str_replace("'", "''", htmlspecialchars(trim($userdata['username'])));

	if( empty($comment_text) )
	{
		message_die(GENERAL_ERROR, $lang['Comment_no_text']);
	}

	if( ($thispic['pic_lock'] == 1) and (!$auth_data['moderator']) )
	{
		message_die(GENERAL_ERROR, $lang['Pic_Locked']);
	}

	if (!$userdata['session_logged_in'])
	{
		if ($comment_username != '')
		{
			$result = validate_username($comment_username);
			if ( $result['error'] )
			{
				message_die(GENERAL_MESSAGE, $result['error_msg']);
			}
		}
	}

	$comment_time = time();
	$comment_user_id = $userdata['user_id'];
	$comment_user_ip = $userdata['session_ip'];

	$sql = "SELECT MAX(comment_id) AS max
			FROM ". ALBUM_COMMENT_TABLE;

	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not found comment_id', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);

	$comment_id = $row['max'] + 1;

	$sql = "INSERT INTO ". ALBUM_COMMENT_TABLE ." (comment_id, comment_pic_id, comment_user_id, comment_username, comment_user_ip, comment_time, comment_text)
			VALUES ('$comment_id', '$pic_id', '$comment_user_id', '$comment_username', '$comment_user_ip', '$comment_time', '$comment_text')";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not insert new entry', '', __LINE__, __FILE__, $sql);
	}

	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=comment&amp;comment_id=$comment_id") . '#'.$comment_id.'">')
	);

	$message = $lang['Stored'] . "<br /><br />" . sprintf($lang['Click_view_message'], "<a href=\"" . append_sid("album.$phpEx?action=comment&amp;comment_id=$comment_id") . "#$comment_id\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);
}

} elseif ( $action == 'comment_delete' ) {

if( $album_config['comment'] == 0 )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

if( isset($HTTP_GET_VARS['comment_id']) )
{
	$comment_id = intval($HTTP_GET_VARS['comment_id']);
}
else if( isset($HTTP_POST_VARS['comment_id']) )
{
	$comment_id = intval($HTTP_POST_VARS['comment_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No comment_id specified');
}

$sql = "SELECT *
		FROM ". ALBUM_COMMENT_TABLE ."
		WHERE comment_id = '$comment_id'";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query this comment information', '', __LINE__, __FILE__, $sql);
}

$thiscomment = $db->sql_fetchrow($result);

if( empty($thiscomment) )
{
	message_die(GENERAL_ERROR, 'This comment does not exist');
}

$sql = "SELECT comment_id, comment_pic_id
		FROM ". ALBUM_COMMENT_TABLE ."
		WHERE comment_id = '$comment_id'";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query comment and pic information', '', __LINE__, __FILE__, $sql);
}

$row = $db->sql_fetchrow($result);

if( empty($row) )
{
	message_die(GENERAL_ERROR, 'This comment does not exist');
}

$pic_id = $row['comment_pic_id'];

$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id
		LIMIT 1";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$total_comments = $thispic['comments_count'];
$comments_per_page = $board_config['posts_per_page'];

$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($thispic['pic_cat_id'], $thiscat, 0, 0, 0, 1, 0, 1);

if( ($album_user_access['comment'] == 0) or ($album_user_access['delete'] == 0) )
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=comment_delete&comment_id=$comment_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}
else
{	
	if( (!$album_user_access['moderator']) or ($userdata['user_level'] != ADMIN) )
	{
		if ($thiscomment['comment_user_id'] != $userdata['user_id'])
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}
}

if( !isset($HTTP_POST_VARS['confirm']) )
{
	if( isset($HTTP_POST_VARS['cancel']) )
	{
		redirect(append_sid("album.$phpEx?action=comment&comment_id=$comment_id"));
		exit;
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' => $lang['Confirm'],

		'MESSAGE_TEXT' => $lang['Comment_delete_confirm'],

		'L_NO' => $lang['No'],
		'L_YES' => $lang['Yes'],

		'S_CONFIRM_ACTION' => append_sid("album.$phpEx?action=comment_delete&amp;comment_id=$comment_id"),
		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$sql = "DELETE
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete this comment', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Deleted'];

	if ($cat_id != PERSONAL_GALLERY)
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>");
	}
	else
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=personal&amp;user_id=$user_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal&amp;user_id=$user_id") . "\">", "</a>");
	}

	$message .= "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);
}

} elseif ( $action == 'comment_edit' ) {

if( $album_config['comment'] == 0 )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

if( isset($HTTP_GET_VARS['comment_id']) )
{
	$comment_id = intval($HTTP_GET_VARS['comment_id']);
}
else if( isset($HTTP_POST_VARS['comment_id']) )
{
	$comment_id = intval($HTTP_POST_VARS['comment_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No comment_id specified');
}

$sql = "SELECT *
		FROM ". ALBUM_COMMENT_TABLE ."
		WHERE comment_id = '$comment_id'";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query this comment information', '', __LINE__, __FILE__, $sql);
}

$thiscomment = $db->sql_fetchrow($result);

if( empty($thiscomment) )
{
	message_die(GENERAL_ERROR, 'This comment does not exist');
}

$sql = "SELECT comment_id, comment_pic_id
		FROM ". ALBUM_COMMENT_TABLE ."
		WHERE comment_id = '$comment_id'";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query comment and pic information', '', __LINE__, __FILE__, $sql);
}

$row = $db->sql_fetchrow($result);

$pic_id = $row['comment_pic_id'];

$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id
		LIMIT 1";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$total_comments = $thispic['comments_count'];
$comments_per_page = $board_config['posts_per_page'];

$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($thispic['pic_cat_id'], $thiscat, 0, 0, 0, 1, 1, 0);

if( ($album_user_access['comment'] == 0) or ($album_user_access['edit'] == 0) )
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=comment_edit&comment_id=$comment_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}
else
{	
	if( (!$album_user_access['moderator']) or ($userdata['user_level'] != ADMIN) )
	{
		if ($thiscomment['comment_user_id'] != $userdata['user_id'])
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}
}

if( !isset($HTTP_POST_VARS['comment']) )
{
	if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
	{
		$poster = ($thispic['pic_username'] == '') ? $lang['Guest'] : $thispic['pic_username'];
	}
	else
	{
		$poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_comment_body.tpl')
	);

	$template->assign_block_vars('switch_comment_post', array());

	$template->assign_vars(array(
		'CAT_TITLE' => $thiscat['cat_title'],
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),

		'U_THUMBNAIL' => append_sid("album.$phpEx?action=thumbnail&amp;pic_id=$pic_id"),
		'U_PIC' => append_sid("album.$phpEx?action=pic&amp;pic_id=$pic_id"),

		'PIC_TITLE' => $thispic['pic_title'],
		'PIC_DESC' => nl2br($thispic['pic_desc']),
		'POSTER' => $poster,
		'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
		'PIC_VIEW' => $thispic['pic_view_count'],
		'PIC_COMMENTS' => $total_comments,
		'S_MESSAGE' => $thiscomment['comment_text'],

		'L_PIC_TITLE' => $lang['Pic_Title'],
		'L_PIC_DESC' => $lang['Pic_Desc'],
		'L_POSTER' => $lang['Poster'],
		'L_POSTED' => $lang['Posted'],
		'L_VIEW' => $lang['View'],
		'L_COMMENTS' => $lang['Comments'],

		'L_POST_YOUR_COMMENT' => $lang['Post_your_comment'],
		'L_MESSAGE' => $lang['Message'],
		'L_USERNAME' => $lang['Username'],
		'L_COMMENT_NO_TEXT' => $lang['Comment_no_text'],
		'L_COMMENT_TOO_LONG' => $lang['Comment_too_long'],
		'L_MAX_LENGTH' => $lang['Max_length'],
		'S_MAX_LENGTH' => $album_config['desc_length'],

		'L_SUBMIT' => $lang['Submit'],

		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=comment_edit&amp;comment_id=$comment_id")
		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$comment_text = str_replace("\'", "''", htmlspecialchars(substr(trim($HTTP_POST_VARS['comment']), 0, $album_config['desc_length'])));

	if( empty($comment_text) )
	{
		message_die(GENERAL_ERROR, $lang['Comment_no_text']);
	}

	$comment_edit_time = time();
	$comment_edit_user_id = $userdata['user_id'];

	$sql = "UPDATE ". ALBUM_COMMENT_TABLE ."
			SET comment_text = '$comment_text', comment_edit_time = '$comment_edit_time', comment_edit_count = comment_edit_count + 1, comment_edit_user_id = '$comment_edit_user_id'
			WHERE comment_id = '$comment_id'";

	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update comment data', '', __LINE__, __FILE__, $sql);
	}

	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=comment&amp;comment_id=$comment_id") . '#'.$comment_id.'">')
	);

	$message = $lang['Stored'] . "<br /><br />" . sprintf($lang['Click_view_message'], "<a href=\"" . append_sid("album.$phpEx?action=comment&amp;comment_id=$comment_id") . "#$comment_id\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);
}

} elseif ( $action == 'delete' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No pics specified');
}

$sql = "SELECT *
		FROM ". ALBUM_TABLE ."
		WHERE pic_id = '$pic_id'";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 0, 1);

if ($album_user_access['delete'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=delete&pic_id=$pic_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}
else
{
	if( (!$album_user_access['moderator']) and ($userdata['user_level'] != ADMIN) )
	{
		if ($thispic['pic_user_id'] != $userdata['user_id'])
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}
}

if( !isset($HTTP_POST_VARS['confirm']) )
{
	if( isset($HTTP_POST_VARS['cancel']) )
	{
		redirect(append_sid("album.$phpEx?action=cat&cat_id=$cat_id"));
		exit;
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' => $lang['Confirm'],

		'MESSAGE_TEXT' => $lang['Album_delete_confirm'],

		'L_NO' => $lang['No'],
		'L_YES' => $lang['Yes'],

		'S_CONFIRM_ACTION' => append_sid("album.$phpEx?action=delete&amp;pic_id=$pic_id"),
		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$sql = "DELETE FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_pic_id = '$pic_id'";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete related comments', '', __LINE__, __FILE__, $sql);
	}

	$sql = "DELETE FROM ". ALBUM_RATE_TABLE ."
			WHERE rate_pic_id = '$pic_id'";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete related ratings', '', __LINE__, __FILE__, $sql);
	}

	if(($thispic['pic_thumbnail'] != '') and @file_exists(ALBUM_CACHE_PATH . $thispic['pic_thumbnail']))
	{
		@unlink(ALBUM_CACHE_PATH . $thispic['pic_thumbnail']);
	}

	@unlink(ALBUM_UPLOAD_PATH . $thispic['pic_filename']);

	$sql = "DELETE FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not delete DB entry', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Pics_deleted_successfully'];

	if ($cat_id != PERSONAL_GALLERY)
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>");
	}
	else
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=personal") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal") . "\">", "</a>");
	}

	$message .= "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);

}

} elseif ( $action == 'edit' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No pics specified');
}

$sql = "SELECT *
		FROM ". ALBUM_TABLE ."
		WHERE pic_id = '$pic_id'";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 1, 0);

if ($album_user_access['edit'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=edit&pic_id=$pic_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}
else
{	
	if( (!$album_user_access['moderator']) and ($userdata['user_level'] != ADMIN) )
	{
		if ($thispic['pic_user_id'] != $userdata['user_id'])
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}
}

if( !isset($HTTP_POST_VARS['pic_title']) )
{
	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_edit_body.tpl')
	);

	$template->assign_vars(array(
		'L_EDIT_PIC_INFO' => $lang['Edit_Pic_Info'],

		'CAT_TITLE' => $thiscat['cat_title'],
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),

		'L_PIC_TITLE' => $lang['Pic_Title'],
		'PIC_TITLE' => $thispic['pic_title'],
		'PIC_DESC' => $thispic['pic_desc'],

		'L_PIC_DESC' => $lang['Pic_Desc'],
		'L_PLAIN_TEXT_ONLY' => $lang['Plain_text_only'],
		'L_MAX_LENGTH' => $lang['Max_length'],

		'L_UPLOAD_NO_TITLE' => $lang['Upload_no_title'],
		'L_DESC_TOO_LONG' => $lang['Desc_too_long'],
		'S_PIC_DESC_MAX_LENGTH' => $album_config['desc_length'],

		'L_RESET' => $lang['Reset'],
		'L_SUBMIT' => $lang['Submit'],

		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=edit&amp;pic_id=$pic_id"),
		)
	);
	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$pic_title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['pic_title'])));
	$pic_desc = str_replace("\'", "''", htmlspecialchars(substr(trim($HTTP_POST_VARS['pic_desc']), 0, $album_config['desc_length'])));

	if( empty($pic_title) )
	{
		message_die(GENERAL_ERROR, $lang['Missed_pic_title']);
	}

	$sql = "UPDATE ". ALBUM_TABLE ."
			SET pic_title = '$pic_title', pic_desc= '$pic_desc'
			WHERE pic_id = '$pic_id'";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update pic information', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Pics_updated_successfully'];

	if ($cat_id != PERSONAL_GALLERY)
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>");
	}
	else
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=personal") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal") . "\">", "</a>");
	}

	$message .= "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);

}

} elseif ( $action == 'modcp' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else
{
	$pic_id = FALSE;
}

if( $pic_id != FALSE )
{
	$sql = "SELECT *
			FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
	}
	$thispic = $db->sql_fetchrow($result);
	if( empty($thispic) )
	{
		message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
	}
	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];
}
else
{
	if( isset($HTTP_POST_VARS['cat_id']) )
	{
		$cat_id = intval($HTTP_POST_VARS['cat_id']);
	}
	else if( isset($HTTP_GET_VARS['cat_id']) )
	{
		$cat_id = intval($HTTP_GET_VARS['cat_id']);
	}
	else
	{
		message_die(GENERAL_ERROR, 'No categories specified');
	}
}

if( ($cat_id == PERSONAL_GALLERY) and (($HTTP_GET_VARS['mode'] == 'lock') or ($HTTP_GET_VARS['mode'] == 'unlock')) )
{
	$thiscat = init_personal_gallery_cat($user_id);
}
else
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$auth_data = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 0, 0);

if( isset($HTTP_POST_VARS['mode']) )
{
	if( isset($HTTP_POST_VARS['move']) )
	{
		$mode = 'move';
	}
	else if( isset($HTTP_POST_VARS['lock']) )
	{
		$mode = 'lock';
	}
	else if( isset($HTTP_POST_VARS['unlock']) )
	{
		$mode = 'unlock';
	}
	else if( isset($HTTP_POST_VARS['delete']) )
	{
		$mode = 'delete';
	}
	else if( isset($HTTP_POST_VARS['approval']) )
	{
		$mode = 'approval';
	}
	else if( isset($HTTP_POST_VARS['unapproval']) )
	{
		$mode = 'unapproval';
	}
	else
	{
		$mode = '';
	}
}
else if( isset($HTTP_GET_VARS['mode']) )
{
	$mode = trim(htmlspecialchars($HTTP_GET_VARS['mode']));
}
else
{
	$mode = '';
}

if ($auth_data['moderator'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=modcp&cat_id=$cat_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if ($mode == '')
{
	$pics_per_page = $board_config['topics_per_page'];
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $pics_per_page);
}
else
{
	if( isset($HTTP_GET_VARS['start']) )
	{
		$start = intval($HTTP_GET_VARS['start']);
	}
	else if( isset($HTTP_POST_VARS['start']) )
	{
		$start = intval($HTTP_POST_VARS['start']);
	}
	else
	{
		$start = 0;
	}
	$start = ($start < 0) ? 0 : $start;
}

	if( isset($HTTP_GET_VARS['sort_method']) )
	{
		switch ($HTTP_GET_VARS['sort_method'])
		{
			case 'pic_title':
				$sort_method = 'pic_title';
				break;
			case 'pic_user_id':
				$sort_method = 'pic_user_id';
				break;
			case 'pic_view_count':
				$sort_method = 'pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = 'pic_time';
		}
	}
	else if( isset($HTTP_POST_VARS['sort_method']) )
	{
		switch ($HTTP_POST_VARS['sort_method'])
		{
			case 'pic_title':
				$sort_method = 'pic_title';
				break;
			case 'pic_user_id':
				$sort_method = 'pic_user_id';
				break;
			case 'pic_view_count':
				$sort_method = 'pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = 'pic_time';
		}
	}
	else
	{
		$sort_method = 'pic_time';
	}

	if( isset($HTTP_GET_VARS['sort_order']) )
	{
		switch ($HTTP_GET_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			default:
				$sort_order = 'DESC';
		}
	}
	else if( isset($HTTP_POST_VARS['sort_order']) )
	{
		switch ($HTTP_POST_VARS['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			default:
				$sort_order = 'DESC';
		}
	}
	else
	{
		$sort_order = 'DESC';
	}

	$sql = "SELECT COUNT(pic_id) AS count
			FROM ". ALBUM_TABLE ."
			WHERE pic_cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not count pics in this category', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);

	$total_pics = $row['count'];

	if ($total_pics > 0)
	{
		$limit_sql = ($start == 0) ? $pics_per_page : $start .', '. $pics_per_page;

		$pic_approval_sql = '';
		if( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) )
		{
			$pic_approval_sql = 'AND p.pic_approval = 1';
		}

		$sql = "SELECT p.pic_id, p.pic_title, p.pic_user_id, p.pic_user_ip, p.pic_username, p.pic_time, p.pic_cat_id, p.pic_view_count, p.pic_lock, p.pic_approval, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(c.comment_id) AS comments, MAX(c.comment_id) AS new_comment
				FROM ". ALBUM_TABLE ." AS p
					LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
					LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
					LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
				WHERE p.pic_cat_id = '$cat_id' $pic_approval_sql
				GROUP BY p.pic_id
				ORDER BY $sort_method $sort_order
				LIMIT $limit_sql";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query pics information', '', __LINE__, __FILE__, $sql);
		}

		$picrow = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$picrow[] = $row;
		}

		for ($i = 0; $i <count($picrow); $i++)
		{
			if( ($picrow[$i]['user_id'] == ALBUM_GUEST) or ($picrow[$i]['username'] == '') )
			{
				$pic_poster = ($picrow[$i]['pic_username'] == '') ? $lang['Guest'] : $picrow[$i]['pic_username'];
			}
			else
			{
				$pic_poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $picrow[$i]['user_id']) .'">'. $picrow[$i]['username'] .'</a>';
			}
			$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';

			$template->assign_block_vars('picrow', array(
				'PIC_ID' => $picrow[$i]['pic_id'],
				'ROW_CLASS' => $row_class,
				'PIC_TITLE' => '<a href="'. append_sid("album.$phpEx?action=pic&amp;pic_id=". $picrow[$i]['pic_id']) .'" target="_blank">'. $picrow[$i]['pic_title'] .'</a>',
				'POSTER' => $pic_poster,
				'TIME' => create_date($board_config['default_dateformat'], $picrow[$i]['pic_time'], $board_config['board_timezone']),
				'RATING' => ($picrow[$i]['rating'] == 0) ? $lang['Not_rated'] : round($picrow[$i]['rating'], 2),
				'COMMENTS' => $picrow[$i]['comments'],
				'LOCK' => ($picrow[$i]['pic_lock'] == 0) ? '' : $lang['Locked'],
				'APPROVAL' => ($picrow[$i]['pic_approval'] == 0) ? $lang['Not_approved'] : $lang['Approved']
				)
			);
		}

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination(append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start),
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $pics_per_page ) + 1 ), ceil( $total_pics / $pics_per_page ))
			)
		);
	}
	else
	{
		$template->assign_block_vars('no_pics', array());
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_modcp_body.tpl')
	);

	$sort_rating_option = '';
	$sort_comments_option = '';
	if( $album_config['rate'] == 1 )
	{
		$sort_rating_option = '<option value="rating" ';
		$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
		$sort_rating_option .= '>' . $lang['Rating'] .'</option>';
	}
	if( $album_config['comment'] == 1 )
	{
		$sort_comments_option = '<option value="comments" ';
		$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
		$sort_comments_option .= '>' . $lang['Comments'] .'</option>';
		$sort_new_comment_option = '<option value="new_comment" ';
		$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
		$sort_new_comment_option .= '>' . $lang['New_Comment'] .'</option>';
	}

	$template->assign_vars(array(
		'U_VIEW_CAT' => append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id"),
		'CAT_TITLE' => $thiscat['cat_title'],
		'L_CATEGORY' => $lang['Category'],
		'L_MODCP' => $lang['Mod_CP'],
		'L_NO_PICS' => $lang['No_Pics'],
		'L_VIEW' => $lang['View'],
		'L_POSTER' => $lang['Poster'],
		'L_POSTED' => $lang['Posted'],
		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id"),
		'L_SELECT_SORT_METHOD' => $lang['Select_sort_method'],
		'L_ORDER' => $lang['Order'],
		'L_SORT' => $lang['Sort'],
		'L_TIME' => $lang['Time'],
		'L_PIC_TITLE' => $lang['Pic_Title'],
		'L_POSTER' => $lang['Poster'],
		'L_RATING' => $lang['Rating'],
		'L_COMMENTS' => $lang['Comments'],
		'L_STATUS' => $lang['Status'],
		'L_APPROVAL' => $lang['Approval'],
		'L_SELECT' => $lang['Select'],
		'L_DELETE' => $lang['Delete'],
		'L_MOVE' => $lang['Move'],
		'L_LOCK' => $lang['Lock'],
		'L_UNLOCK' => $lang['Unlock'],
		'DELETE_BUTTON' => ($auth_data['delete'] == 1) ? '<input type="submit" name="delete" value="'. $lang['Delete'] .'" />' : '',
		'APPROVAL_BUTTON' => ( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) ) ? '' : '<input type="submit" name="approval" value="'. $lang['Approve'] .'" />',
		'UNAPPROVAL_BUTTON' => ( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) ) ? '' : '<input type="submit" name="unapproval" value="'. $lang['Unapprove'] .'" />',
		'L_USERNAME' => $lang['Sort_Username'],
		'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
		'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
		'SORT_USERNAME' => ($sort_method == 'pic_user_id') ? 'selected="selected"' : '',
		'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
		'SORT_RATING_OPTION' => $sort_rating_option,
		'SORT_COMMENTS_OPTION' => $sort_comments_option,
		'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
		'L_ASC' => $lang['Sort_Ascending'],
		'L_DESC' => $lang['Sort_Descending'],
		'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
		'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : ''
		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	if ($mode == 'move')
	{
		if( !isset($HTTP_POST_VARS['target']) )
		{
			$pic_id_array = array();
			if ($pic_id != FALSE)
			{
				$pic_id_array[] = $pic_id;
			}
			else
			{
				if( isset($HTTP_POST_VARS['pic_id']) )
				{
					$pic_id_array = $HTTP_POST_VARS['pic_id'];
					if( !is_array($pic_id_array) )
					{
						message_die(GENERAL_ERROR, 'Invalid request');
					}
				}
				else
				{
					message_die(GENERAL_ERROR, 'No pics specified');
				}
			}
			for ($i = 0; $i < count($pic_id_array); $i++)
			{
				$template->assign_block_vars('pic_id_array', array(
					'VALUE' => $pic_id_array[$i])
				);
			}

			$sql = "SELECT *
					FROM ". ALBUM_CAT_TABLE ."
					WHERE cat_id <> '$cat_id'
					ORDER BY cat_order ASC";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
			}

			$catrows = array();

			while( $row = $db->sql_fetchrow($result) )
			{
				$album_user_access = album_user_access($row['cat_id'], $row, 0, 1, 0, 0, 0, 0);

				if ($album_user_access['upload'] == 1)
				{
					$catrows[] = $row;
				}
			}

			if( count($catrows) == 0 )
			{
				message_die(GENERAL_MESSAGE, 'There is no more categories which you have permisson to move pics to');
			}

			$category_select = '<select name="target">';

			for ($i = 0; $i < count($catrows); $i++)
			{
				$category_select .= '<option value="'. $catrows[$i]['cat_id'] .'">'. $catrows[$i]['cat_title'] .'</option>';
			}

			$category_select .= '</select>';

			$page_title = $lang['Album'];
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);

			$template->set_filenames(array(
				'body' => 'album_move_body.tpl')
			);

			$template->assign_vars(array(
				'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=modcp&amp;mode=move&amp;cat_id=$cat_id"),
				'L_MOVE' => $lang['Move'],
				'L_MOVE_TO_CATEGORY' => $lang['Move_to_Category'],
				'S_CATEGORY_SELECT' => $category_select)
			);

			$template->pparse('body');

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}

			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
			}

			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_cat_id = ". intval($HTTP_POST_VARS['target']) ."
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update album information', '', __LINE__, __FILE__, $sql);
			}

			$message = $lang['Pics_moved_successfully'] .'<br /><br />'. sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
	}
	else if ($mode == 'lock')
	{
		if ($pic_id != FALSE)
		{
			$pic_id_sql = $pic_id;
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}
		}

		$sql = "SELECT pic_id
				FROM ". ALBUM_TABLE ."
				WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
		}
		if( $db->sql_numrows($result) > 0 )
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}

		$sql = "UPDATE ". ALBUM_TABLE ."
				SET pic_lock = 1
				WHERE pic_id IN ($pic_id_sql)";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update album information', '', __LINE__, __FILE__, $sql);
		}

		$message = $lang['Pics_locked_successfully'] .'<br /><br />';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />";
		}
		else
		{
			$message .= sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal") . "\">", "</a>");
		}

		$message .= '<br /><br />' . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if ($mode == 'unlock')
	{
		if ($pic_id != FALSE)
		{
			$pic_id_sql = $pic_id;
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}
		}

		$sql = "SELECT pic_id
				FROM ". ALBUM_TABLE ."
				WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
		}
		if( $db->sql_numrows($result) > 0 )
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}

		$sql = "UPDATE ". ALBUM_TABLE ."
				SET pic_lock = 0
				WHERE pic_id IN ($pic_id_sql)";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update album information', '', __LINE__, __FILE__, $sql);
		}

		$message = $lang['Pics_unlocked_successfully'] .'<br /><br />';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />";
		}
		else
		{
			$message .= sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal") . "\">", "</a>");
		}

		$message .= '<br /><br />' . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if ($mode == 'approval')
	{
		if ($pic_id != FALSE)
		{
			$pic_id_sql = $pic_id;
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}
		}

		$sql = "SELECT pic_id
				FROM ". ALBUM_TABLE ."
				WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
		}
		if( $db->sql_numrows($result) > 0 )
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}

		$sql = "UPDATE ". ALBUM_TABLE ."
				SET pic_approval = 1
				WHERE pic_id IN ($pic_id_sql)";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update album information', '', __LINE__, __FILE__, $sql);
		}

		$message = $lang['Pics_approved_successfully'] .'<br /><br />'. sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if ($mode == 'unapproval')
	{
		if ($pic_id != FALSE)
		{
			$pic_id_sql = $pic_id;
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}
		}

		$sql = "SELECT pic_id
				FROM ". ALBUM_TABLE ."
				WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
		}
		if( $db->sql_numrows($result) > 0 )
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}

		$sql = "UPDATE ". ALBUM_TABLE ."
				SET pic_approval = 0
				WHERE pic_id IN ($pic_id_sql)";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update album information', '', __LINE__, __FILE__, $sql);
		}

		$message = $lang['Pics_unapproved_successfully'] .'<br /><br />'. sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if ($mode == 'delete')
	{
		if ($auth_data['delete'] == 0)
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}

		if( !isset($HTTP_POST_VARS['confirm']) )
		{
			$pic_id_array = array();
			if ($pic_id != FALSE)
			{
				$pic_id_array[] = $pic_id;
			}
			else
			{
				if( isset($HTTP_POST_VARS['pic_id']) )
				{
					$pic_id_array = $HTTP_POST_VARS['pic_id'];
					if( !is_array($pic_id_array) )
					{
						message_die(GENERAL_ERROR, 'Invalid request');
					}
				}
				else
				{
					message_die(GENERAL_ERROR, 'No pics specified');
				}
			}

			if ( isset($HTTP_POST_VARS['cancel']) )
			{
				$redirect = "album.$phpEx?action=modcp&cat_id=$cat_id";
				redirect(append_sid($redirect, true));
			}			

			$hidden_field = '';
			for ($i = 0; $i < count($pic_id_array); $i++)
			{
				$hidden_field .= '<input name="pic_id[]" type="hidden" value="'. $pic_id_array[$i] .'" />' . "\n";
			}

			$page_title = $lang['Album'];
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);

			$template->set_filenames(array(
				'body' => 'confirm_body.tpl')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $lang['Confirm'],
				'MESSAGE_TEXT' => $lang['Album_delete_confirm'],
				'S_HIDDEN_FIELDS' => $hidden_field,
				'L_NO' => $lang['No'],
				'L_YES' => $lang['Yes'],
				'S_CONFIRM_ACTION' => append_sid("album.$phpEx?action=modcp&amp;mode=delete&amp;cat_id=$cat_id"),
				)
			);

			$template->pparse('body');

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
		else
		{
			if( isset($HTTP_POST_VARS['pic_id']) )
			{
				$pic_id = $HTTP_POST_VARS['pic_id'];
				if( is_array($pic_id) )
				{
					$pic_id_sql = implode(',', $pic_id);
				}
				else
				{
					message_die(GENERAL_ERROR, 'Invalid request');
				}
			}
			else
			{
				message_die(GENERAL_ERROR, 'No pics specified');
			}
			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain album information', '', __LINE__, __FILE__, $sql);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				message_die(GENERAL_ERROR, $lang['Not_Authorised']);
			}

			$sql = "DELETE FROM ". ALBUM_COMMENT_TABLE ."
					WHERE comment_pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete related comments', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM ". ALBUM_RATE_TABLE ."
					WHERE rate_pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete related ratings', '', __LINE__, __FILE__, $sql);
			}

			$sql = "SELECT pic_filename, pic_thumbnail
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain filenames', '', __LINE__, __FILE__, $sql);
			}
			$filerow = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$filerow[] = $row;
			}
			for ($i = 0; $i < count($filerow); $i++)
			{
				if( ($filerow[$i]['pic_thumbnail'] != '') and (@file_exists(ALBUM_CACHE_PATH . $filerow[$i]['pic_thumbnail'])) )
				{
					@unlink(ALBUM_CACHE_PATH . $filerow[$i]['pic_thumbnail']);
				}
				@unlink(ALBUM_UPLOAD_PATH . $filerow[$i]['pic_filename']);
			}

			$sql = "DELETE FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete DB entry', '', __LINE__, __FILE__, $sql);
			}

			$message = $lang['Pics_deleted_successfully'] .'<br /><br />'. sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>") .'<br /><br />'. sprintf($lang['Click_return_modcp'], "<a href=\"" . append_sid("album.$phpEx?action=modcp&amp;cat_id=$cat_id") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
	}
	else
	{
		message_die(GENERAL_ERROR, 'Invalid_mode');
	}
}

} elseif ( $action == 'page' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No pic_id set');
}

if( isset($HTTP_GET_VARS['mode']) ) 
{ 
        if( ($HTTP_GET_VARS['mode'] == 'next') or ($HTTP_GET_VARS['mode'] == 'previous') ) 
        { 
                $sql = "SELECT pic_id, pic_cat_id, pic_user_id 
                                FROM ". ALBUM_TABLE ." 
                                WHERE pic_id = $pic_id"; 

                if( !($result = $db->sql_query($sql)) ) 
                { 
                        message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql); 
                } 
		  
		        $row = $db->sql_fetchrow($result); 
				$cur_pic_cat = $row['pic_cat_id'];

                if( empty($row) ) 
                { 
                        message_die(GENERAL_ERROR, 'Bad pic_id'); 
                } 

                $sql = "SELECT new.pic_id, new.pic_time 
                                FROM ". ALBUM_TABLE ." AS new, ". ALBUM_TABLE ." AS cur 
                                WHERE cur.pic_id = $pic_id 
                                        AND new.pic_id <> cur.pic_id 
                                        AND new.pic_cat_id = cur.pic_cat_id"; 
                $sql .= ($HTTP_GET_VARS['mode'] == 'next') ? " AND new.pic_time >= cur.pic_time" : " AND new.pic_time <= cur.pic_time"; 
                $sql .= ($row['pic_cat_id'] == PERSONAL_GALLERY) ? " AND new.pic_user_id = cur.pic_user_id" : ""; 
                $sql .= ($HTTP_GET_VARS['mode'] == 'next') ? " ORDER BY pic_time ASC LIMIT 1" : " ORDER BY pic_time DESC LIMIT 1"; 
                if( !($result = $db->sql_query($sql)) ) 
                { 
                        message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql); 
                } 

                $row = $db->sql_fetchrow($result); 

                $sql = "SELECT min(pic_id), max(pic_id)
                                FROM ". ALBUM_TABLE ."
			WHERE pic_cat_id = $cur_pic_cat"; 

                if( !($result = $db->sql_query($sql)) ) 
                { 
                        message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql); 
                } 

                $next = $db->sql_fetchrow($result);
				
				$first_pic = $next['min(pic_id)'];
				$last_pic = $next['max(pic_id)'];
				
	if( empty($row) AND ($HTTP_GET_VARS['mode'] == 'next')) 
            { 						  
		redirect(append_sid("album.$phpEx?action=page&pic_id=$first_pic"));
	} 
                if( empty($row) AND ($HTTP_GET_VARS['mode'] == 'previous')) 
                { 
                        redirect(append_sid("album.$phpEx?action=page&pic_id=$last_pic"));
                } 
						
                $pic_id = $row['pic_id'];
        } 
}

$sql = "SELECT p.*, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
			LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);

if ($album_user_access['view'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=page&pic_id=$pic_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if ($userdata['user_level'] != ADMIN)
{
	if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
	{
		if ($thispic['pic_approval'] != 1)
		{
			message_die(GENERAL_ERROR, $lang['Not_Authorised']);
		}
	}
}

$page_title = $lang['Album'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'album_page_body.tpl')
);

if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
{
	$poster = ($thispic['pic_username'] == '') ? $lang['Guest'] : $thispic['pic_username'];
}
else
{
	$poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
}


$template->assign_vars(array(
	'CAT_TITLE' => $thiscat['cat_title'],
	'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),
	'U_PIC' => append_sid("album.$phpEx?action=pic&amp;pic_id=$pic_id"),
	'PIC_TITLE' => $thispic['pic_title'],
	'PIC_DESC' => nl2br($thispic['pic_desc']),
	'POSTER' => $poster,
	'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
	'PIC_VIEW' => $thispic['pic_view_count'],
	'PIC_RATING' => ($thispic['rating'] != 0) ? round($thispic['rating'], 2) : $lang['Not_rated'],
	'PIC_COMMENTS' => $thispic['comments'],
	'U_RATE' => append_sid("album.$phpEx?action=rate&amp;pic_id=$pic_id"),
	'U_COMMENT' => append_sid("album.$phpEx?action=comment&amp;pic_id=$pic_id"),
	'U_NEXT' => append_sid("album.$phpEx?action=page&amp;pic_id=$pic_id&amp;mode=next"),
	'U_PREVIOUS' => append_sid("album.$phpEx?action=page&amp;pic_id=$pic_id&amp;mode=previous"),
	'L_NEXT' => $lang['Next'],
	'L_PREVIOUS' => $lang['Previous'],
	'L_RATING' => $lang['Rating'],
	'L_PIC_TITLE' => $lang['Pic_Title'],
	'L_PIC_DESC' => $lang['Pic_Desc'],
	'L_POSTER' => $lang['Poster'],
	'L_POSTED' => $lang['Posted'],
	'L_VIEW' => $lang['View'],
	'L_COMMENTS' => $lang['Comments'])
);

if ($album_config['rate'])
{
	$template->assign_block_vars('rate_switch', array());
}

if ($album_config['comment'])
{
	$template->assign_block_vars('comment_switch', array());
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} elseif ( $action == 'personal' ) {

if( isset($HTTP_POST_VARS['user_id']) )
{
	$user_id = intval($HTTP_POST_VARS['user_id']);
}
else if( isset($HTTP_GET_VARS['user_id']) )
{
	$user_id = intval($HTTP_GET_VARS['user_id']);
}
else
{
	$user_id = $userdata['user_id'];
}

if( ($user_id < 1) and (!$userdata['session_logged_in']) )
{
	redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=personal"));
}

$sql = "SELECT username
		FROM ". USERS_TABLE ."
		WHERE user_id = $user_id";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not get the username of this category owner', '', __LINE__, __FILE__, $sql);
}

$row = $db->sql_fetchrow($result);

$username = $row['username'];

if( empty($username) )
{
	message_die(GENERAL_ERROR, 'Sorry, this user does not exist');
}

$personal_gallery_access = personal_gallery_access(1,1);

if( $personal_gallery_access['view'] == 0 )
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=personal&user_id=$user_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if ($user_id == $userdata['user_id'])
{
	if( $personal_gallery_access['upload'] == 0 )
	{
		message_die(GENERAL_MESSAGE, $lang['Not_allowed_to_create_personal_gallery']);
	}
}
$pics_per_page = $album_config['rows_per_page'] * $album_config['cols_per_page'];
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $pics_per_page);
}
else
{
if( isset($HTTP_GET_VARS['start']) )
{
	$start = intval($HTTP_GET_VARS['start']);
}
else if( isset($HTTP_POST_VARS['start']) )
{
	$start = intval($HTTP_POST_VARS['start']);
}
else
{
	$start = 0;
}
$start = ($start < 0) ? 0 : $start;
}

if( isset($HTTP_GET_VARS['sort_method']) )
{
	switch ($HTTP_GET_VARS['sort_method'])
	{
		case 'pic_title':
			$sort_method = 'pic_title';
			break;
		case 'pic_view_count':
			$sort_method = 'pic_view_count';
			break;
		case 'rating':
			$sort_method = 'rating';
			break;
		case 'comments':
			$sort_method = 'comments';
			break;
		case 'new_comment':
			$sort_method = 'new_comment';
			break;
		default:
			$sort_method = $album_config['sort_method'];
	}
}
else if( isset($HTTP_POST_VARS['sort_method']) )
{
	switch ($HTTP_POST_VARS['sort_method'])
	{
		case 'pic_title':
			$sort_method = 'pic_title';
			break;
		case 'pic_view_count':
			$sort_method = 'pic_view_count';
			break;
		case 'rating':
			$sort_method = 'rating';
			break;
		case 'comments':
			$sort_method = 'comments';
			break;
		case 'new_comment':
			$sort_method = 'new_comment';
			break;
		default:
			$sort_method = $album_config['sort_method'];
	}
}
else
{
	$sort_method = $album_config['sort_method'];
}

if( isset($HTTP_GET_VARS['sort_order']) )
{
	switch ($HTTP_GET_VARS['sort_order'])
	{
		case 'ASC':
			$sort_order = 'ASC';
			break;
		case 'DESC':
			$sort_order = 'DESC';
			break;
		default:
			$sort_order = $album_config['sort_order'];
	}
}
else if( isset($HTTP_POST_VARS['sort_order']) )
{
	switch ($HTTP_POST_VARS['sort_order'])
	{
		case 'ASC':
			$sort_order = 'ASC';
			break;
		case 'DESC':
			$sort_order = 'DESC';
			break;
		default:
			$sort_order = $album_config['sort_order'];
	}
}
else
{
	$sort_order = $album_config['sort_order'];
}

$sql = "SELECT COUNT(pic_id) AS count
		FROM ". ALBUM_TABLE ."
		WHERE pic_cat_id = ". PERSONAL_GALLERY ."
			AND pic_user_id = $user_id";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not count pics', '', __LINE__, __FILE__, $sql);
}

$row = $db->sql_fetchrow($result);

$total_pics = $row['count'];

if ($total_pics > 0)
{
	$limit_sql = ($start == 0) ? $pics_per_page : $start .','. $pics_per_page;

	$sql = "SELECT p.pic_id, p.pic_title, p.pic_desc, p.pic_user_id, p.pic_user_ip, p.pic_time, p.pic_view_count, p.pic_lock, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments, MAX(c.comment_id) as new_comment
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE p.pic_cat_id = ". PERSONAL_GALLERY ."
				AND p.pic_user_id = $user_id
			GROUP BY p.pic_id
			ORDER BY $sort_method $sort_order
			LIMIT $limit_sql";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query pics information', '', __LINE__, __FILE__, $sql);
	}

	$picrow = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$picrow[] = $row;
	}

	for ($i = 0; $i < count($picrow); $i += $album_config['cols_per_page'])
	{

		for ($j = $i; $j < ($i + $album_config['cols_per_page']); $j++)
		{
			if( $j >= count($picrow) )
			{
				break;
			}

			if(!$picrow[$j]['rating'])
			{
				$picrow[$j]['rating'] = $lang['Not_rated'];
			}
			else
			{
				$picrow[$j]['rating'] = round($picrow[$j]['rating'], 2);
			}

			$row_class = ( !($j % 2) ) ? 'row_easy' : 'row_hard';

			$template->assign_block_vars('picrow', array(
				'TITLE' => $picrow[$j]['pic_title'],
				'ROW_CLASS' => $row_class,
				'TIME' => create_date($board_config['default_dateformat'], $picrow[$j]['pic_time'], $board_config['board_timezone']),
				'VIEW' => $picrow[$j]['pic_view_count'],
				'RATING' => ($album_config['rate'] == 1) ? ( '<a href="'. append_sid("album.$phpEx?action=rate&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Rating'] . '</a>: ' . $picrow[$j]['rating'] . '<br />') : '',
				'COMMENTS' => ($album_config['comment'] == 1) ? ( '<a href="'. append_sid("album.$phpEx?action=comment&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Comments'] . '</a>: ' . $picrow[$j]['comments'] . '<br />') : '',
				'EDIT' => ( ($userdata['user_level'] == ADMIN) or ($userdata['user_id'] == $picrow[$j]['pic_user_id']) ) ? '<a href="'. append_sid("album.$phpEx?action=edit&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Edit_pic'] . '</a>|' : '',
				'DELETE' => ( ($userdata['user_level'] == ADMIN) or ($userdata['user_id'] == $picrow[$j]['pic_user_id']) ) ? '<a href="'. append_sid("album.$phpEx?action=delete&amp;pic_id=". $picrow[$j]['pic_id']) . '">' . $lang['Delete_pic'] . '</a>|' : '',
				'LOCK' => ($userdata['user_level'] == ADMIN) ? '<a href="'. append_sid("album.$phpEx?action=modcp&amp;mode=". (($picrow[$j]['pic_lock'] == 0) ? 'lock' : 'unlock') ."&amp;pic_id=". $picrow[$j]['pic_id']) .'">'. (($picrow[$j]['pic_lock'] == 0) ? $lang['Lock'] : $lang['Unlock']) .'</a>' : '',
				'IP' => ($userdata['user_level'] == ADMIN) ? $lang['IP_Address'] . ': ' . decode_ip($picrow[$j]['pic_user_ip']) .'<br />' : ''
				)
			);

			$template->assign_block_vars('picrow.piccol', array(
				'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.$phpEx?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
				'THUMBNAIL' => append_sid("album.$phpEx?action=thumbnail&amp;pic_id=". $picrow[$j]['pic_id']),
				'DESC' => $picrow[$j]['pic_desc']
				)
			);
		}
	}

	$template->assign_vars(array(
		'PAGINATION' => generate_pagination(append_sid("album.$phpEx?action=personal&amp;user_id=$user_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start))
	);
}
else
{
	$template->assign_block_vars('no_pics', array());
}

$sort_rating_option = '';
$sort_comments_option = '';
if( $album_config['rate'] == 1 )
{
	$sort_rating_option = '<option value="rating" ';
	$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
	$sort_rating_option .= '>' . $lang['Rating'] .'</option>';
}
if( $album_config['comment'] == 1 )
{
	$sort_comments_option = '<option value="comments" ';
	$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
	$sort_comments_option .= '>' . $lang['Comments'] .'</option>';

	$sort_new_comment_option = '<option value="new_comment" ';
	$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
	$sort_new_comment_option .= '>' . $lang['New_Comment'] .'</option>';
}

$page_title = $lang['Album'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'album_personal_body.tpl')
);

if( $user_id == $userdata['user_id'] )
{
	$template->assign_block_vars('your_personal_gallery', array());
}

$template->assign_vars(array(
	'U_UPLOAD_PIC' => append_sid("album.$phpEx?action=upload&amp;cat_id=". PERSONAL_GALLERY),
	'UPLOAD_PIC_IMG' => $images['upload_pic'],
	'L_UPLOAD_PIC' => $lang['Upload_Pic'],
	'L_PERSONAL_GALLERY_NOT_CREATED' => sprintf($lang['Personal_gallery_not_created'], $username),
	'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
	'S_COLS' => $album_config['cols_per_page'],
	'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
	'L_VIEW' => $lang['View'],
	'L_POSTED' => $lang['Posted'],
	'U_PERSONAL_GALLERY' => append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),
	'L_YOUR_PERSONAL_GALLERY' => $lang['Your_Personal_Gallery'],
	'L_PERSONAL_GALLERY_EXPLAIN' => $lang['Personal_Gallery_Explain'],
	'L_PERSONAL_GALLERY_OF_USER' => sprintf($lang['Personal_Gallery_Of_User'], $username),
	'L_SELECT_SORT_METHOD' => $lang['Select_sort_method'],
	'L_ORDER' => $lang['Order'],
	'L_SORT' => $lang['Sort'],
	'L_NO_PICS' => $lang['No_Pics'],
	'L_TIME' => $lang['Time'],
	'L_PIC_TITLE' => $lang['Pic_Title'],
	'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
	'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
	'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
	'SORT_RATING_OPTION' => $sort_rating_option,
	'SORT_COMMENTS_OPTION' => $sort_comments_option,
	'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
	'L_ASC' => $lang['Sort_Ascending'],
	'L_DESC' => $lang['Sort_Descending'],
	'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
	'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '')
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} elseif ( $action == 'personal_index' ) {

if ( isset($HTTP_POST_VARS['start1']) )
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

if ( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = 'joined';
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
	$sort_order = 'ASC';
}

$mode_types_text = array($lang['Sort_Joined'], $lang['Sort_Username'], $lang['Pics'], $lang['Last_Pic']);
$mode_types = array('joindate', 'username', 'pics', 'last_pic');

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

$page_title = $lang['Album'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'album_personal_index_body.tpl')
);

$template->assign_vars(array(
	'L_SELECT_SORT_METHOD' => $lang['Select_sort_method'],
	'L_ORDER' => $lang['Order'],
	'L_SORT' => $lang['Sort'],
	'L_JOINED' => $lang['Joined'],
	'L_PICS' => $lang['Pics'],
	'L_USERS_PERSONAL_GALLERIES' => $lang['Users_Personal_Galleries'],
	'S_MODE_SELECT' => $select_sort_mode,
	'S_ORDER_SELECT' => $select_sort_order,
	'S_MODE_ACTION' => append_sid("album.$phpEx?action=personal_index")
	)
);


switch( $mode )
{
	case 'joined':
		$order_by = "user_regdate ASC LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'username':
		$order_by = "username $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'pics':
		$order_by = "pics $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'last_pic':
		$order_by = "last_pic $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	default:
		$order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
}

$sql = "SELECT u.username, u.user_id, u.user_regdate, COUNT(p.pic_id) AS pics, MAX(p.pic_id) AS last_pic
		FROM ". USERS_TABLE ." AS u, ". ALBUM_TABLE ." as p
		WHERE u.user_id <> ". ANONYMOUS ."
			AND u.user_id = p.pic_user_id
			AND p.pic_cat_id = ". PERSONAL_GALLERY ."
		GROUP BY user_id
		ORDER BY $order_by";

if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
}

$memberrow = array();

while( $row = $db->sql_fetchrow($result) )
{
	$memberrow[] = $row;
}

for ($i = 0; $i < count($memberrow); $i++)
{
	$template->assign_block_vars('memberrow', array(
		'ROW_CLASS' => ( !($i % 2) ) ? 'row_easy' : 'row_hard',
		'USERNAME' => $memberrow[$i]['username'],
		'U_VIEWGALLERY' => append_sid("album.$phpEx?action=personal&amp;user_id=". $memberrow[$i]['user_id']),
		'JOINED' => create_date($lang['DATE_FORMAT'], $memberrow[$i]['user_regdate'], $board_config['board_timezone']),
		'PICS' => $memberrow[$i]['pics'])
	);
}

$sql = "SELECT COUNT(DISTINCT u.user_id) AS total
		FROM ". USERS_TABLE ." AS u, ". ALBUM_TABLE ." AS p
		WHERE u.user_id <> ". ANONYMOUS ."
			AND u.user_id = p.pic_user_id
			AND p.pic_cat_id = ". PERSONAL_GALLERY;

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Error getting total galleries', '', __LINE__, __FILE__, $sql);
}

if ( $total = $db->sql_fetchrow($result) )
{
	$total_galleries = $total['total'];

	$pagination = ( $total_galleries > $board_config['topics_per_page'] ) ? generate_pagination("album.$phpEx?action=personal_index&amp;mode=$mode&amp;order=$sort_order", $total_galleries, $board_config['topics_per_page'], $start) : '';
}

$template->assign_vars(array(
	'PAGINATION' => $pagination,
	'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $board_config['topics_per_page'] ) + 1 ), ceil( $total_galleries / $board_config['topics_per_page'] ))
	)
);

if ( $total_galleries == 0 )
{
	$template->assign_block_vars('no_pics', array());
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

} elseif ( $action == 'pic' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	die('No pics specified');
}

$sql = "SELECT *
		FROM ". ALBUM_TABLE ."
		WHERE pic_id = '$pic_id'";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$pic_filetype = substr($thispic['pic_filename'], strlen($thispic['pic_filename']) - 4, 4);
$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
{
	die($lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	die($lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);
if ($album_user_access['view'] == 0)
{
	die($lang['Not_Authorised']);
}

if ($userdata['user_level'] != ADMIN)
{
	if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
	{
		if ($thispic['pic_approval'] != 1)
		{
			die($lang['Not_Authorised']);
		}
	}
}

if( ($album_config['hotlink_prevent'] == 1) and (isset($HTTP_SERVER_VARS['HTTP_REFERER'])) )
{
	$check_referer = explode('?', $HTTP_SERVER_VARS['HTTP_REFERER']);
	$check_referer = trim($check_referer[0]);

	$good_referers = array();

	if ($album_config['hotlink_allowed'] != '')
	{
		$good_referers = explode(',', $album_config['hotlink_allowed']);
	}

	$good_referers[] = $board_config['server_name'] . $board_config['script_path'];

	$errored = TRUE;

	for ($i = 0; $i < count($good_referers); $i++)
	{
		$good_referers[$i] = trim($good_referers[$i]);

		if( (strstr($check_referer, $good_referers[$i])) and ($good_referers[$i] != '') )
		{
			$errored = FALSE;
		}
	}

	if ($errored)
	{
		die($lang['Not_Authorised']);
	}
}

$sql = "UPDATE ". ALBUM_TABLE ."
		SET pic_view_count = pic_view_count + 1
		WHERE pic_id = '$pic_id'";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not update pic information', '', __LINE__, __FILE__, $sql);
}

switch ( $pic_filetype )
{
	case '.png':
		header('Content-type: image/png');
		break;
	case '.gif':
		header('Content-type: image/gif');
		break;
	case '.jpg':
		header('Content-type: image/jpeg');
		break;
	default:
		die('The filename data in the DB was corrupted');
}

readfile(ALBUM_UPLOAD_PATH  . $thispic['pic_filename']);

exit;

} elseif ( $action == 'rate' ) {

if( $album_config['rate'] == 0 )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No pics specified');
}

$sql = "SELECT p.*, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) )
{
	message_die(GENERAL_ERROR, $lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 1, 0, 0, 0);

if ($album_user_access['rate'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=rate&pic_id=$pic_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if( $userdata['session_logged_in'] )
{
	$sql = "SELECT *
			FROM ". ALBUM_RATE_TABLE ."
			WHERE rate_pic_id = '$pic_id'
				AND rate_user_id = '". $userdata['user_id'] ."'
			LIMIT 1";

	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not query rating information', '', __LINE__, __FILE__, $sql);
	}

	if ($db->sql_numrows($result) > 0)
	{
		$already_rated = TRUE;
	}
	else
	{
		$already_rated = FALSE;
	}
}

if( !isset($HTTP_POST_VARS['rate']) )
{
	if (!$already_rated)
	{
		for ($i = 0; $i < $album_config['rate_scale']; $i++)
		{
			$template->assign_block_vars('rate_row', array(
				'POINT' => ($i + 1)
				)
			);
		}
	}

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => 'album_rate_body.tpl')
	);

	if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
	{
		$poster = ($thispic['pic_username'] == '') ? $lang['Guest'] : $thispic['pic_username'];
	}
	else
	{
		$poster = '<a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
	}

	$template->assign_vars(array(
		'CAT_TITLE' => $thiscat['cat_title'],
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal&amp;user_id=$user_id"),
		'U_THUMBNAIL' => append_sid("album.$phpEx?action=thumbnail&amp;pic_id=$pic_id"),
		'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=$pic_id") : append_sid("album.$phpEx?action=page&amp;pic_id=$pic_id"),
		'PIC_TITLE' => $thispic['pic_title'],
		'PIC_DESC' => nl2br($thispic['pic_desc']),
		'POSTER' => $poster,
		'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
		'PIC_VIEW' => $thispic['pic_view_count'],
		'PIC_RATING' => ($thispic['rating'] != 0) ? round($thispic['rating'], 2) : $lang['Not_rated'],
		'S_RATE_MSG' => ($already_rated) ? $lang['Already_rated'] : $lang['Rating'],
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
		'L_RATING' => $lang['Rating'],
		'L_PIC_TITLE' => $lang['Pic_Title'],
		'L_PIC_DESC' => $lang['Pic_Desc'],
		'L_POSTER' => $lang['Poster'],
		'L_POSTED' => $lang['Posted'],
		'L_VIEW' => $lang['View'],
		'L_CURRENT_RATING' => $lang['Current_Rating'],
		'L_PLEASE_RATE_IT' => $lang['Please_Rate_It'],
		'L_SUBMIT' => $lang['Submit'],
		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=rate&amp;pic_id=$pic_id"),

		)
	);

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$rate_point = intval($HTTP_POST_VARS['rate']);

	if( ($rate_point <= 0) or ($rate_point > $album_config['rate_scale']) )
	{
		message_die(GENERAL_ERROR, 'Bad submited value');
	}

	$rate_user_id = $userdata['user_id'];
	$rate_user_ip = $userdata['session_ip'];

	if ($already_rated)
	{
		message_die(GENERAL_ERROR, $lang['Already_rated']);
	}

	$sql = "INSERT INTO ". ALBUM_RATE_TABLE ." (rate_pic_id, rate_user_id, rate_user_ip, rate_point)
			VALUES ('$pic_id', '$rate_user_id', '$rate_user_ip', '$rate_point')";

	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not insert new rating', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Album_rate_successfully'];

	if ($cat_id != PERSONAL_GALLERY)
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>");
	}
	else
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("album.$phpEx?action=personal&amp;user_id=$user_id") . '">')
		);

		$message .= "<br /><br />" . sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal&amp;user_id=$user_id") . "\">", "</a>");
	}

	$message .= "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);
}

} elseif ( $action == 'thumbnail' ) {

if( isset($HTTP_GET_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_GET_VARS['pic_id']);
}
else if( isset($HTTP_POST_VARS['pic_id']) )
{
	$pic_id = intval($HTTP_POST_VARS['pic_id']);
}
else
{
	die('No pics specified');
}

$sql = "SELECT *
		FROM ". ALBUM_TABLE ."
		WHERE pic_id = '$pic_id'";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query pic information', '', __LINE__, __FILE__, $sql);
}
$thispic = $db->sql_fetchrow($result);

$cat_id = $thispic['pic_cat_id'];
$user_id = $thispic['pic_user_id'];

$pic_filetype = substr($thispic['pic_filename'], strlen($thispic['pic_filename']) - 4, 4);
$pic_filename = $thispic['pic_filename'];
$pic_thumbnail = $thispic['pic_thumbnail'];

if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
{
	die($lang['Pic_not_exist']);
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT *
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_id);
}

if (empty($thiscat))
{
	die($lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);

if ($album_user_access['view'] == 0)
{
	die($lang['Not_Authorised']);
}

if ($userdata['user_level'] != ADMIN)
{
	if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
	{
		if ($thispic['pic_approval'] != 1)
		{
			die($lang['Not_Authorised']);
		}
	}
}

if( ($album_config['hotlink_prevent'] == 1) and (isset($HTTP_SERVER_VARS['HTTP_REFERER'])) )
{
	$check_referer = explode('?', $HTTP_SERVER_VARS['HTTP_REFERER']);
	$check_referer = trim($check_referer[0]);

	$good_referers = array();

	if ($album_config['hotlink_allowed'] != '')
	{
		$good_referers = explode(',', $album_config['hotlink_allowed']);
	}

	$good_referers[] = $board_config['server_name'] . $board_config['script_path'];

	$errored = TRUE;

	for ($i = 0; $i < count($good_referers); $i++)
	{
		$good_referers[$i] = trim($good_referers[$i]);

		if( (strstr($check_referer, $good_referers[$i])) and ($good_referers[$i] != '') )
		{
			$errored = FALSE;
		}
	}

	if ($errored)
	{
		die($lang['Not_Authorised']);
	}
}

if( ($pic_filetype != '.jpg') and ($pic_filetype != '.png') and ($pic_filetype != '.gif') )
{
	header('Content-type: image/jpeg');
	readfile($images['no_thumbnail']);
	exit;
}
else
{
	if( ($album_config['thumbnail_cache'] == 1) and ($pic_thumbnail != '') and file_exists(ALBUM_CACHE_PATH . $pic_thumbnail) )
	{
		switch ($pic_filetype)
		{
		  case '.gif':
			case '.jpg':
				header('Content-type: image/jpeg');
				break;
			case '.png':
				header('Content-type: image/png');
				break;
		}

		readfile(ALBUM_CACHE_PATH . $pic_thumbnail);
		exit;
	}

	$pic_size = @getimagesize(ALBUM_UPLOAD_PATH . $pic_filename);
	$pic_width = $pic_size[0];
	$pic_height = $pic_size[1];

	$gd_errored = FALSE;
	switch ($pic_filetype)
	{
	 case '.gif':
      $read_function = 'imagecreatefromgif';
      $pic_filetype = '.jpg';
   break;
		case '.jpg':
			$read_function = 'imagecreatefromjpeg';
			break;
		case '.png':
			$read_function = 'imagecreatefrompng';
			break;
	}

	$src = @$read_function(ALBUM_UPLOAD_PATH  . $pic_filename);

	if (!$src)
	{
		$gd_errored = TRUE;
		$pic_thumbnail = '';
	}
	else if( ($pic_width > $album_config['thumbnail_size']) or ($pic_height > $album_config['thumbnail_size']) )
	{
		if ($pic_width > $pic_height)
		{
			$thumbnail_width = $album_config['thumbnail_size'];
			$thumbnail_height = $album_config['thumbnail_size'] * ($pic_height/$pic_width);
		}
		else
		{
			$thumbnail_height = $album_config['thumbnail_size'];
			$thumbnail_width = $album_config['thumbnail_size'] * ($pic_width/$pic_height);
		}

		$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height);

		$resize_function = ($album_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

		@$resize_function($thumbnail, $src, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $pic_width, $pic_height);
	}
	else
	{
		$thumbnail = $src;
	}

	if (!$gd_errored)
	{
		if ($album_config['thumbnail_cache'] == 1)
		{
			$pic_thumbnail = $pic_filename;

			switch ($pic_filetype)
			{
				case '.jpg':
					@imagejpeg($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail, $album_config['thumbnail_quality']);
					break;
				case '.png':
					@imagepng($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail);
					break;
			}

			@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);
		}

		switch ($pic_filetype)
		{
			case '.jpg':
				@imagejpeg($thumbnail, '', $album_config['thumbnail_quality']);
				break;
			case '.png':
				@imagepng($thumbnail);
				break;
		}

		exit;
	}
	else
	{
		header('Content-type: image/jpeg');
		readfile('images/nothumbnail.jpg');
		exit;
	}
}

} elseif ( $action == 'upload' ) {

if( isset($HTTP_POST_VARS['cat_id']) )
{
	$cat_id = intval($HTTP_POST_VARS['cat_id']);
}
else if( isset($HTTP_GET_VARS['cat_id']) )
{
	$cat_id = intval($HTTP_GET_VARS['cat_id']);
}
else
{
	message_die(GENERAL_ERROR, 'No categories specified');
}

if ($cat_id != PERSONAL_GALLERY)
{
	$sql = "SELECT c.*, COUNT(p.pic_id) AS count
			FROM ". ALBUM_CAT_TABLE ." AS c
				LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
			WHERE c.cat_id = '$cat_id'
			GROUP BY c.cat_id
			LIMIT 1";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query category information', '', __LINE__, __FILE__, $sql);
	}

	$thiscat = $db->sql_fetchrow($result);
}
else
{
	$thiscat = init_personal_gallery_cat($user_data['user_id']);
}

$current_pics = $thiscat['count'];

if (empty($thiscat))
{
	message_die(GENERAL_ERROR, $lang['Category_not_exist']);
}

$album_user_access = album_user_access($cat_id, $thiscat, 0, 1, 0, 0, 0, 0);

if ($album_user_access['upload'] == 0)
{
	if (!$userdata['session_logged_in'])
	{
		redirect(append_sid("login.$phpEx?redirect=album.$phpEx&action=upload&cat_id=$cat_id"));
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorised']);
	}
}

if ($cat_id != PERSONAL_GALLERY)
{
	if ($album_config['max_pics'] >= 0)
	{
		if( $current_pics >= $album_config['max_pics'] )
		{
			message_die(GENERAL_MESSAGE, $lang['Album_reached_quota']);
		}
	}

	$check_user_limit = FALSE;

	if( ($userdata['user_level'] != ADMIN) and ($userdata['session_logged_in']) )
	{
		if ($album_user_access['moderator'])
		{
			if ($album_config['mod_pics_limit'] >= 0)
			{
				$check_user_limit = 'mod_pics_limit';
			}
		}
		else
		{
			if ($album_config['user_pics_limit'] >= 0)
			{
				$check_user_limit = 'user_pics_limit';
			}
		}
	}

	if ($check_user_limit != FALSE)
	{
		$sql = "SELECT COUNT(pic_id) AS count
				FROM ". ALBUM_TABLE ."
				WHERE pic_user_id = '". $userdata['user_id'] ."'
					AND pic_cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not count your pic', '', __LINE__, __FILE__, $sql);
		}
		$row = $db->sql_fetchrow($result);
		$own_pics = $row['count'];

		if( $own_pics >= $album_config[$check_user_limit] )
		{
			message_die(GENERAL_MESSAGE, $lang['User_reached_pics_quota']);
		}
	}
}
else
{
	if( ($current_pics >= $album_config['personal_gallery_limit']) and ($album_config['personal_gallery_limit'] >= 0) )
	{
		message_die(GENERAL_MESSAGE, $lang['Album_reached_quota']);
	}
}

if( !isset($HTTP_POST_VARS['pic_title']) )
{
	$sql = "SELECT *
			FROM " . ALBUM_CAT_TABLE ."
			ORDER BY cat_order ASC";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
	}

	$catrows = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$thiscat_access = album_user_access($row['cat_id'], $row, 0, 1, 0, 0, 0, 0);

		if ($thiscat_access['upload'] == 1)
		{
			$catrows[] = $row;
		}
	}

	$select_cat = '<select name="cat_id">';

	if ($cat_id == PERSONAL_GALLERY)
	{
		$select_cat .= '<option value="$cat_id" selected="selected">';
		$select_cat .= sprintf($lang['Personal_Gallery_Of_User'], $userdata['username']);
		$select_cat .= '</option>';
	}

	for ($i = 0; $i < count($catrows); $i++)
	{
		$select_cat .= '<option value="'. $catrows[$i]['cat_id'] .'" ';
		$select_cat .= ($cat_id == $catrows[$i]['cat_id']) ? 'selected="selected"' : '';
		$select_cat .= '>'. $catrows[$i]['cat_title'] .'</option>';
	}

	$select_cat .= '</select>';

	$page_title = $lang['Album'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
		'body' => ($result_ua) ? 'album_upload_body_om.tpl' : 'album_upload_body.tpl')
	);

	$template->assign_vars(array(
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") : append_sid("album.$phpEx?action=personal"),
		'CAT_TITLE' => $thiscat['cat_title'],

		'L_UPLOAD_PIC' => $lang['Upload_Pic'],

		'L_USERNAME' => $lang['Username'],
		'L_PIC_TITLE' => $lang['Pic_Title'],

		'L_PIC_DESC' => $lang['Pic_Desc'],
		'L_PLAIN_TEXT_ONLY' => $lang['Plain_text_only'],
		'L_MAX_LENGTH' => $lang['Max_length'],
		'S_PIC_DESC_MAX_LENGTH' => $album_config['desc_length'],

		'L_UPLOAD_PIC_FROM_MACHINE' => $lang['Upload_pic_from_machine'],
		'L_UPLOAD_PIC_FROM_MACHINE_OM' => $lang['Upload_pic_from_machine_om'],
		'L_UPLOAD_TO_CATEGORY' => $lang['Upload_to_Category'],

		'SELECT_CAT' => $select_cat,

		'L_MAX_FILESIZE' => $lang['Max_file_size'],
		'S_MAX_FILESIZE' => $album_config['max_file_size'],

		'L_MAX_WIDTH' => $lang['Max_width'],
		'L_MAX_HEIGHT' => $lang['Max_height'],

		'S_MAX_WIDTH' => $album_config['max_width'],
		'S_MAX_HEIGHT' => $album_config['max_height'],

		'L_ALLOWED_JPG' => $lang['JPG_allowed'],
		'L_ALLOWED_PNG' => $lang['PNG_allowed'],
		'L_ALLOWED_GIF' => $lang['GIF_allowed'],

		'S_JPG' => ($album_config['jpg_allowed'] == 1) ? $lang['Yes'] : $lang['No'],
		'S_PNG' => ($album_config['png_allowed'] == 1) ? $lang['Yes'] : $lang['No'],
		'S_GIF' => ($album_config['gif_allowed'] == 1) ? $lang['Yes'] : $lang['No'],

		'L_UPLOAD_NO_TITLE' => $lang['Upload_no_title'],
		'L_UPLOAD_NO_FILE' => $lang['Upload_no_file'],
		'L_DESC_TOO_LONG' => $lang['Desc_too_long'],

		'L_UPLOAD_THUMBNAIL' => $lang['Upload_thumbnail'],
		'L_UPLOAD_THUMBNAIL_EXPLAIN' => $lang['Upload_thumbnail_explain'],
		'L_THUMBNAIL_SIZE' => $lang['Thumbnail_size'],
		'S_THUMBNAIL_SIZE' => $album_config['thumbnail_size'],

		'L_RESET' => $lang['Reset'],
		'L_SUBMIT' => $lang['Submit'],

		'S_ALBUM_ACTION' => append_sid("album.$phpEx?action=upload&amp;cat_id=$cat_id"),
		)
	);

	if ($album_config['gd_version'] == 0)
	{
		$template->assign_block_vars('switch_manual_thumbnail', array());
	}

	$template->pparse('body');

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
else
{
	$pic_title = str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['pic_title'])));
	$pic_desc = str_replace("\'", "''", htmlspecialchars(substr(trim($HTTP_POST_VARS['pic_desc']), 0, $album_config['desc_length'])));
	$pic_username = (!$userdata['session_logged_in']) ? substr(str_replace("\'", "''", htmlspecialchars(trim($HTTP_POST_VARS['pic_username']))), 0, 32) : str_replace("'", "''", $userdata['username']);

	if( empty($pic_title) )
	{
		message_die(GENERAL_ERROR, $lang['Missed_pic_title']);
	}

	if ($result_ua)
	{
		if( !isset($HTTP_POST_VARS['picupload']) )
		{
			message_die(GENERAL_ERROR, 'Bad Upload');
		}
	} else {
		if( !isset($HTTP_POST_FILES['pic_file']) )
		{
			message_die(GENERAL_ERROR, 'Bad Upload');
		}
	}

	if (!$userdata['session_logged_in'])
	{
		if ($pic_username != '')
		{
			$result = validate_username($pic_username);
			if ( $result['error'] )
			{
				message_die(GENERAL_MESSAGE, $result['error_msg']);
			}
		}
	}	

	if ($result_ua)
	{
		$uploadedfile = $HTTP_POST_VARS['picupload'];

		if (strlen($uploadedfile)) 
		{ 
			$array = explode('file=', $uploadedfile);
			$tmp_name = $array[0];
			$filebase64 = $array[1]; 
		} 

		$tmp_name = basename($tmp_name);

		if (strlen($filebase64)) 
		{ 
			$filedata = base64_decode($filebase64);
		} 

		$fileom = @fopen($opera_mini . "/" . $tmp_name, "wb");

		if($fileom) 
		{
			if(flock($fileom, LOCK_EX)) 
			{ 
				fwrite($fileom, $filedata);
				flock($fileom, LOCK_UN); 
			} 
			fclose($fileom); 
		}

		$filetmp = $opera_mini . "/" . $tmp_name;
		$filesize = @filesize($filetmp);
		$tmp_name_type = strrchr($tmp_name, '.');
		$repl=array("."=>"");
		$type = strtr($tmp_name_type, $repl);
		$filetype = 'image/'.$type;

	} else {
		$filetype = $HTTP_POST_FILES['pic_file']['type'];
		$filesize = $HTTP_POST_FILES['pic_file']['size'];
		$filetmp = $HTTP_POST_FILES['pic_file']['tmp_name'];
	}

	if ($album_config['gd_version'] == 0)
	{
		$thumbtype = $HTTP_POST_FILES['pic_thumbnail']['type'];
		$thumbsize = $HTTP_POST_FILES['pic_thumbnail']['size'];
		$thumbtmp = $HTTP_POST_FILES['pic_thumbnail']['tmp_name'];
	}

	$pic_time = time();
	$pic_user_id = $userdata['user_id'];
	$pic_user_ip = $userdata['session_ip'];

	if( ($filesize == 0) or ($filesize > $album_config['max_file_size']) )
	{
		@unlink($filetmp);
		message_die(GENERAL_MESSAGE, $lang['Bad_upload_file_size']);
	}

	if ($album_config['gd_version'] == 0)
	{
		if( ($thumbsize == 0) or ($thumbsize > $album_config['max_file_size']) )
		{
			@unlink($filetmp);
			message_die(GENERAL_MESSAGE, $lang['Bad_upload_file_size']);
		}
	}

	switch ($filetype)
	{
		case 'image/jpeg':
		case 'image/jpg':
		case 'image/pjpeg':
			if ($album_config['jpg_allowed'] == 0)
			{
				@unlink($filetmp);
				message_die(GENERAL_ERROR, $lang['Not_allowed_file_type']);
			}
			$pic_filetype = '.jpg';
			break;

		case 'image/png':
		case 'image/x-png':
			if ($album_config['png_allowed'] == 0)
			{
				@unlink($filetmp);
				message_die(GENERAL_ERROR, $lang['Not_allowed_file_type']);
			}
			$pic_filetype = '.png';
			break;

		case 'image/gif':
			if ($album_config['gif_allowed'] == 0)
			{
				@unlink($filetmp);
				message_die(GENERAL_ERROR, $lang['Not_allowed_file_type']);
			}
			$pic_filetype = '.gif';
			break;
		default:
			@unlink($filetmp);
			message_die(GENERAL_ERROR, $lang['Not_allowed_file_type']);
	}

	if ($album_config['gd_version'] == 0)
	{
		if ($filetype != $thumbtype)
		{
			@unlink($filetmp);
			message_die(GENERAL_ERROR, $lang['Filetype_and_thumbtype_do_not_match']);
		}
	}

	srand((double)microtime()*1000000);

	do
	{
		$pic_filename = md5(uniqid(rand())) . $pic_filetype;
	}
	while( file_exists(ALBUM_UPLOAD_PATH . $pic_filename) );

	if ($album_config['gd_version'] == 0)
	{
		$pic_thumbnail = $pic_filename;
	}

	$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';

	if ( @$ini_val('open_basedir') != '' )
	{
		if ( @phpversion() < '4.0.3' )
		{
			@unlink($filetmp);
			message_die(GENERAL_ERROR, 'open_basedir is set and your PHP version does not allow move_uploaded_file<br /><br />Please contact your server admin', '', __LINE__, __FILE__);
		}

		$move_file = 'move_uploaded_file';
	}
	else
	{
		$move_file = 'copy';
	}

	if ($result_ua)
	{
		$move_file = 'copy';
	}

	$move_file($filetmp, ALBUM_UPLOAD_PATH . $pic_filename);

	@chmod(ALBUM_UPLOAD_PATH . $pic_filename, 0777);

	if ($album_config['gd_version'] == 0)
	{
		$move_file($thumbtmp, ALBUM_CACHE_PATH . $pic_thumbnail);

		@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);
	}

	$pic_size = getimagesize(ALBUM_UPLOAD_PATH . $pic_filename);

	$pic_width = $pic_size[0];
	$pic_height = $pic_size[1];

	if ( ($pic_width > $album_config['max_width']) or ($pic_height > $album_config['max_height']) )
	{
		@unlink(ALBUM_UPLOAD_PATH . $pic_filename);

		if ($album_config['gd_version'] == 0)
		{
			@unlink(ALBUM_CACHE_PATH . $pic_thumbnail);
		}
		@unlink($filetmp);
		message_die(GENERAL_ERROR, $lang['Upload_image_size_too_big']);
	}

	if ($album_config['gd_version'] == 0)
	{
		$thumb_size = getimagesize(ALBUM_CACHE_PATH . $pic_thumbnail);

		$thumb_width = $thumb_size[0];
		$thumb_height = $thumb_size[1];

		if ( ($thumb_width > $album_config['thumbnail_size']) or ($thumb_height > $album_config['thumbnail_size']) )
		{
			@unlink(ALBUM_UPLOAD_PATH . $pic_filename);

			@unlink(ALBUM_CACHE_PATH . $pic_thumbnail);

			@unlink($filetmp);
			message_die(GENERAL_ERROR, $lang['Upload_thumbnail_size_too_big']);
		}
	}

	if( ($album_config['thumbnail_cache'] == 1) and ($pic_filetype != '.gif') and ($album_config['gd_version'] > 0) )
	{
		$gd_errored = FALSE;

		switch ($pic_filetype)
		{
			case '.jpg':
				$read_function = 'imagecreatefromjpeg';
				break;
			case '.png':
				$read_function = 'imagecreatefrompng';
				break;
		}

		$src = @$read_function(ALBUM_UPLOAD_PATH  . $pic_filename);

		if (!$src)
		{
			$gd_errored = TRUE;
			$pic_thumbnail = '';
		}
		else if( ($pic_width > $album_config['thumbnail_size']) or ($pic_height > $album_config['thumbnail_size']) )
		{
			if ($pic_width > $pic_height)
			{
				$thumbnail_width = $album_config['thumbnail_size'];
				$thumbnail_height = $album_config['thumbnail_size'] * ($pic_height/$pic_width);
			}
			else
			{
				$thumbnail_height = $album_config['thumbnail_size'];
				$thumbnail_width = $album_config['thumbnail_size'] * ($pic_width/$pic_height);
			}

			$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height);

			$resize_function = ($album_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

			@$resize_function($thumbnail, $src, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $pic_width, $pic_height);
		}
		else
		{
			$thumbnail = $src;
		}

		if (!$gd_errored)
		{
			$pic_thumbnail = $pic_filename;

			switch ($pic_filetype)
			{
				case '.jpg':
					@imagejpeg($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail, $album_config['thumbnail_quality']);
					break;
				case '.png':
					@imagepng($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail);
					break;
			}

			@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);

		}

	}
	else if ($album_config['gd_version'] > 0)
	{
		$pic_thumbnail = '';
	}

	$pic_approval = ($thiscat['cat_approval'] == 0) ? 1 : 0;

	$sql = "INSERT INTO ". ALBUM_TABLE ." (pic_filename, pic_thumbnail, pic_title, pic_desc, pic_user_id, pic_user_ip, pic_username, pic_time, pic_cat_id, pic_approval)
			VALUES ('$pic_filename', '$pic_thumbnail', '$pic_title', '$pic_desc', '$pic_user_id', '$pic_user_ip', '$pic_username', '$pic_time', '$cat_id', '$pic_approval')";
	if( !$result = $db->sql_query($sql) )
	{
		@unlink($filetmp);
		message_die(GENERAL_ERROR, 'Could not insert new entry', '', __LINE__, __FILE__, $sql);
	}

	if ($thiscat['cat_approval'] == 0)
	{
		$message = $lang['Album_upload_successful'];
	}
	else
	{
		$message = $lang['Album_upload_need_approval'];
	}
	@unlink($filetmp);

	if ($cat_id != PERSONAL_GALLERY)
	{
		if ($thiscat['cat_approval'] == 0)
		{
			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="2;url=' . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . '">')
			);
		}

		$message .= "<br /><br />" . sprintf($lang['Click_return_category'], "<a href=\"" . append_sid("album.$phpEx?action=cat&amp;cat_id=$cat_id") . "\">", "</a>");
	}
	else
	{
		if ($thiscat['cat_approval'] == 0)
		{
			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="2;url=' . append_sid("album.$phpEx?action=personal") . '">')
			);
		}

		$message .= "<br /><br />" . sprintf($lang['Click_return_personal_gallery'], "<a href=\"" . append_sid("album.$phpEx?action=personal") . "\">", "</a>");
	}

	$message .= "<br /><br />" . sprintf($lang['Click_return_album_index'], "<a href=\"" . append_sid("album.$phpEx") . "\">", "</a>");

	message_die(GENERAL_MESSAGE, $message);
}

} else {

$sql = "SELECT c.*, COUNT(p.pic_id) AS count
		FROM ". ALBUM_CAT_TABLE ." AS c
			LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
		WHERE cat_id <> 0
		GROUP BY cat_id
		ORDER BY cat_order ASC";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
}

$catrows = array();

while( $row = $db->sql_fetchrow($result) )
{
	$album_user_access = album_user_access($row['cat_id'], $row, 1, 0, 0, 0, 0, 0);
	if ($album_user_access['view'] == 1)
	{
		$catrows[] = $row;
	}
}

$allowed_cat = '';

for ($i = 0; $i < count($catrows); $i++)
{
	$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	$allowed_cat .= ($allowed_cat == '') ? $catrows[$i]['cat_id'] : ',' . $catrows[$i]['cat_id'];
	$l_moderators = '';
	$moderators_list = '';

	$grouprows= array();

	if( $catrows[$i]['cat_moderator_groups'] != '')
	{
		$sql = "SELECT group_id, group_name
				FROM " . GROUPS_TABLE . "
				WHERE group_single_user <> 1
					AND group_type <> ". GROUP_HIDDEN ."
					AND group_id IN (". $catrows[$i]['cat_moderator_groups'] .")
				ORDER BY group_name ASC";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain usergroups data', '', __LINE__, __FILE__, $sql);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$grouprows[] = $row;
		}
	}

	if( count($grouprows) > 0 )
	{
		$l_moderators = $lang['Moderators'];

		for ($j = 0; $j < count($grouprows); $j++)
		{
			$group_link = '<a href="'. append_sid("groupcp.$phpEx?". POST_GROUPS_URL .'='. $grouprows[$j]['group_id']) .'">'. $grouprows[$j]['group_name'] .'</a>';

			$moderators_list .= ($moderators_list == '') ? $group_link : ', ' . $group_link;
		}
	}

	if ($catrows[$i]['count'] == 0)
	{
		$last_pic_info = $lang['No_Pics'];
		$u_last_pic = '';
		$last_pic_title = '';
	}
	else
	{
		if(($catrows[$i]['cat_approval'] == ALBUM_ADMIN) or ($catrows[$i]['cat_approval'] == ALBUM_MOD))
		{
			$pic_approval_sql = 'AND p.pic_approval = 1';
		}
		else
		{
			$pic_approval_sql = '';
		}

		$sql = "SELECT p.pic_id, p.pic_title, p.pic_user_id, p.pic_username, p.pic_time, p.pic_cat_id, u.user_id, u.username
				FROM ". ALBUM_TABLE ." AS p	LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				WHERE p.pic_cat_id = '". $catrows[$i]['cat_id'] ."' $pic_approval_sql
				ORDER BY p.pic_time DESC
				LIMIT 1";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not get last pic information', '', __LINE__, __FILE__, $sql);
		}
		$lastrow = $db->sql_fetchrow($result);

		$last_pic_info = create_date($board_config['default_dateformat'], $lastrow['pic_time'], $board_config['board_timezone']);

		$last_pic_info .= '<br />';

		if( ($lastrow['user_id'] == ALBUM_GUEST) or ($lastrow['username'] == '') )
		{
			$last_pic_info .= ($lastrow['pic_username'] == '') ? $lang['Guest'] : $lastrow['pic_username'];
		}
		else
		{
			$last_pic_info .= $lang['Poster'] .': <a href="'. append_sid("profile.$phpEx?mode=viewprofile&amp;". POST_USERS_URL .'='. $lastrow['user_id']) .'">'. $lastrow['username'] .'</a>';
		}

		if( !isset($album_config['last_pic_title_length']) )
		{
			$album_config['last_pic_title_length'] = 25;
		}

		$lastrow['pic_title'] = $lastrow['pic_title'];

		if (strlen($lastrow['pic_title']) > $album_config['last_pic_title_length'])
		{
			$lastrow['pic_title'] = substr($lastrow['pic_title'], 0, $album_config['last_pic_title_length']) . '...';
		}

		$last_pic_info .= '<br />'. $lang['Pic_Title'] .': <a href="';

		$last_pic_info .= ($album_config['fullpic_popup']) ? append_sid("album.$phpEx?action=pic&amp;pic_id=". $lastrow['pic_id']) .'" target="_blank">' : append_sid("album.$phpEx?action=page&amp;pic_id=". $lastrow['pic_id']) .'">' ;

		$last_pic_info .= $lastrow['pic_title'] .'</a>';
	}

	$template->assign_block_vars('catrow', array(
		'ROW_CLASS'		=> $row_class,
		'U_VIEW_CAT' => append_sid("album.$phpEx?action=cat&amp;cat_id=". $catrows[$i]['cat_id']),
		'CAT_TITLE' => $catrows[$i]['cat_title'],
		'CAT_DESC' => $catrows[$i]['cat_desc'],
		'L_MODERATORS' => $l_moderators,
		'MODERATORS' => $moderators_list,
		'PICS' => $catrows[$i]['count'],
		'LAST_PIC_INFO' => $last_pic_info)
	);
}

if ($allowed_cat == '')
{
	$template->assign_block_vars('no_cats', array());
}

$page_title = $lang['Album'];
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'album_index_body.tpl')
);

$template->assign_vars(array(
	'L_CATEGORY' => $lang['Category'],
	'L_PICS' => $lang['Pics'],
	'L_LAST_PIC' => $lang['Last_Pic'],

	'U_YOUR_PERSONAL_GALLERY' => append_sid("album.$phpEx?action=personal&amp;user_id=". $userdata['user_id']),
	'L_YOUR_PERSONAL_GALLERY' => $lang['Your_Personal_Gallery'],

	'U_USERS_PERSONAL_GALLERIES' => append_sid("album.$phpEx?action=personal_index"),
	'L_USERS_PERSONAL_GALLERIES' => $lang['Users_Personal_Galleries'],

	'S_COLS' => $album_config['cols_per_page'],
	'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
	'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
	'L_RECENT_PUBLIC_PICS' => $lang['Recent_Public_Pics'],
	'L_NO_PICS' => $lang['No_Pics'],
	'L_PIC_TITLE' => $lang['Pic_Title'],
	'L_VIEW' => $lang['View'],
	'L_POSTER' => $lang['Poster'],
	'L_POSTED' => $lang['Posted'],
	'L_PUBLIC_CATS' => $lang['Public_Categories'])
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

}

?>
