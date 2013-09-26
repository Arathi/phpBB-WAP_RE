<?php
/**************************************************
 *		functions_admin.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************/

function make_forum_select($box_name, $ignore_forum = false, $select_forum = '')
{
	global $db, $userdata, $lang;

	$is_auth_ary = auth(AUTH_READ, AUTH_LIST_ALL, $userdata);

	$sql = 'SELECT f.forum_id, f.forum_name
		FROM ' . CATEGORIES_TABLE . ' c, ' . FORUMS_TABLE . ' f
		WHERE f.cat_id = c.cat_id 
		ORDER BY c.cat_order, f.forum_order';
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Couldn not obtain forums information', '', __LINE__, __FILE__, $sql);
	}

	$forum_list = '';
	while( $row = $db->sql_fetchrow($result) )
	{
		if ( $is_auth_ary[$row['forum_id']]['auth_read'] && $ignore_forum != $row['forum_id'] )
		{
			$selected = ( $select_forum == $row['forum_id'] ) ? ' selected="selected"' : '';
			$forum_list .= '<option value="' . $row['forum_id'] . '"' . $selected .'>' . $row['forum_name'] . '</option>';
		}
	}

	$forum_list = ( $forum_list == '' ) ? $lang['No_forums'] : '<select name="' . $box_name . '">' . $forum_list . '</select>';

	return $forum_list;
}

function sync($type, $id = false)
{
	global $db;

	switch($type)
	{
		case 'all forums':
			$sql = "SELECT forum_id
				FROM " . FORUMS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get forum IDs', '', __LINE__, __FILE__, $sql);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				sync('forum', $row['forum_id']);
			}
		   	break;

		case 'all topics':
			$sql = "SELECT topic_id
				FROM " . TOPICS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get topic ID', '', __LINE__, __FILE__, $sql);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				sync('topic', $row['topic_id']);
			}
			break;

	  	case 'forum':
			$sql = "SELECT MAX(post_id) AS last_post, COUNT(post_id) AS total 
				FROM " . POSTS_TABLE . "  
				WHERE forum_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get post ID', '', __LINE__, __FILE__, $sql);
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				$last_post = ( $row['last_post'] ) ? $row['last_post'] : 0;
				$total_posts = ($row['total']) ? $row['total'] : 0;
			}
			else
			{
				$last_post = 0;
				$total_posts = 0;
			}

			$sql = "SELECT COUNT(topic_id) AS total
				FROM " . TOPICS_TABLE . "
				WHERE forum_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get topic count', '', __LINE__, __FILE__, $sql);
			}

			$total_topics = ( $row = $db->sql_fetchrow($result) ) ? ( ( $row['total'] ) ? $row['total'] : 0 ) : 0;

			$sql = "UPDATE " . FORUMS_TABLE . "
				SET forum_last_post_id = $last_post, forum_posts = $total_posts, forum_topics = $total_topics
				WHERE forum_id = $id";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update forum', '', __LINE__, __FILE__, $sql);
			}
			global $board_config;

			if ($board_config['reputation_enabled'] || $board_config['warnings_enabled'])
			{
				$result = db_query('SELECT r.id, r.forum_id AS review_forum_id, p.post_id, p.forum_id AS post_forum_id
					FROM {REPUTATION_TABLE} r LEFT JOIN {POSTS_TABLE} p ON r.post_id = p.post_id
					WHERE r.forum_id = %d
						AND (p.forum_id <> r.forum_id OR (r.post_id <> {NO_ID} AND p.post_id IS NULL))', $id);

				$no_post = ''; $wrong_forum = array();

				while ($row = $db->sql_fetchrow($result))
				{
					if (!$row['post_id'])
					{
						$no_post .= ($no_post ? ',' : '') . $row['id'];
					}
					elseif ($row['review_forum_id'] != $row['post_forum_id'])
					{
						if (isset($wrong_forum[$row['post_forum_id']]))
						{
							$wrong_forum[$row['post_forum_id']] .= ',' . $row['id'];
						}
						else
						{
							$wrong_forum[$row['post_forum_id']] = $row['id'];
						}
					}
				}

				if ($no_post)
				{
					db_query('UPDATE {REPUTATION_TABLE} SET post_id = {NO_ID} WHERE id IN(%s)', $no_post);
				}
				foreach ($wrong_forum as $forum_id => $review_ids)
				{
					db_query('UPDATE {REPUTATION_TABLE} SET forum_id = %d WHERE id IN(%s)', $forum_id, $review_ids);
				}
			}

			break;

		case 'topic':
			$sql = "SELECT MAX(post_id) AS last_post, MIN(post_id) AS first_post, COUNT(post_id) AS total_posts
				FROM " . POSTS_TABLE . "
				WHERE topic_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not get post ID', '', __LINE__, __FILE__, $sql);
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				if ($row['total_posts'])
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . ' 
						SET topic_replies = ' . ($row['total_posts'] - 1) . ', topic_first_post_id = ' . $row['first_post'] . ', topic_last_post_id = ' . $row['last_post'] . "
						WHERE topic_id = $id";

					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not update topic', '', __LINE__, __FILE__, $sql);
					}
				}
				else
				{
					$sql = 'SELECT topic_moved_id 
						FROM ' . TOPICS_TABLE . " 
						WHERE topic_id = $id";

					if (!($result = $db->sql_query($sql)))
					{
						message_die(GENERAL_ERROR, 'Could not get topic ID', '', __LINE__, __FILE__, $sql);
					}

					if ($row = $db->sql_fetchrow($result))
					{
						if (!$row['topic_moved_id'])
						{
							$sql = 'DELETE FROM ' . TOPICS_TABLE . " WHERE topic_id = $id";
			
							if (!$db->sql_query($sql))
							{
								message_die(GENERAL_ERROR, 'Could not remove topic', '', __LINE__, __FILE__, $sql);
							}
						}
					}

					$db->sql_freeresult($result);
				}
				attachment_sync_topic($id);
			}
			break;
	}
	
	return true;
}

?>