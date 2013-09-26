<?php
/***************************************************************************
 *                         functions_bookmark.php
 *                              -------------------
 *  Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 ***************************************************************************/

function is_bookmark_set($topic_id)
{
	global $db, $userdata;

	$user_id = $userdata['user_id'];
	$sql = "SELECT topic_id, user_id
		FROM " . BOOKMARK_TABLE . " 
		WHERE topic_id = $topic_id AND user_id = $user_id";
	if ( $result = $db->sql_query($sql) )
	{
		$is_bookmark_set = ($db->sql_fetchrow($result)) ? (TRUE) : (FALSE);
	}
	else
	{
		message_die(GENERAL_ERROR, 'Could not obtain bookmark information', '', __LINE__, __FILE__, $sql);
		$is_bookmark_set = FALSE;
	}
	$db->sql_freeresult($result);
	
	return $is_bookmark_set;
}

function set_bookmark($topic_id, $start)
{
	global $db, $userdata;

	$user_id = $userdata['user_id'];
	if ( !is_bookmark_set($topic_id) )
	{
		$sql = "INSERT INTO " . BOOKMARK_TABLE . " (topic_id, user_id, start)
			VALUES ($topic_id, $user_id, $start)";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not insert bookmark information', '', __LINE__, __FILE__, $sql);
		}
	}
	return;
}

function remove_bookmark($topic_id)
{
	global $db, $userdata;

	$user_id = $userdata['user_id'];
	$sql = "DELETE FROM " . BOOKMARK_TABLE . "
		WHERE topic_id IN ($topic_id) AND user_id = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not remove bookmark information', '', __LINE__, __FILE__, $sql);
	}
	return;
}

?>