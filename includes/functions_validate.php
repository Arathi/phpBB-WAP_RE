<?php
/***************************************************************************
 *                          functions_validate.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************************************/

/**
* 验证用户名
**/
function validate_username($username)
{
	global $db, $lang, $userdata;

	
	$username = preg_replace('#\s+#', ' ', trim($username)); 
	$username = phpbb_clean_username($username);

	$sql = "SELECT username 
		FROM " . USERS_TABLE . "
		WHERE LOWER(username) = '" . strtolower($username) . "'";
	if ($result = $db->sql_query($sql))
	{
		while ($row = $db->sql_fetchrow($result))
		{
			if (($userdata['session_logged_in'] && $row['username'] != $userdata['username']) || !$userdata['session_logged_in'])
			{
				$db->sql_freeresult($result);
				return array('error' => true, 'error_msg' => $lang['Username_taken']);
			}
		}
	}
	$db->sql_freeresult($result);

	$sql = "SELECT group_name
		FROM " . GROUPS_TABLE . " 
		WHERE LOWER(group_name) = '" . strtolower($username) . "'";
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return array('error' => true, 'error_msg' => $lang['Username_taken']);
		}
	}
	$db->sql_freeresult($result);

	$sql = "SELECT disallow_username
		FROM " . DISALLOW_TABLE;
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if (preg_match("#\b(" . str_replace("\*", ".*?", preg_quote($row['disallow_username'], '#')) . ")\b#i", $username))
				{
					$db->sql_freeresult($result);
					return array('error' => true, 'error_msg' => $lang['Username_disallowed']);
				}
			}
			while($row = $db->sql_fetchrow($result));
		}
	}
	$db->sql_freeresult($result);

	$sql = "SELECT word 
		FROM  " . WORDS_TABLE;
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if (preg_match("#\b(" . str_replace("\*", ".*?", preg_quote($row['word'], '#')) . ")\b#i", $username))
				{
					$db->sql_freeresult($result);
					return array('error' => true, 'error_msg' => $lang['Username_disallowed']);
				}
			}
			while ($row = $db->sql_fetchrow($result));
		}
	}
	$db->sql_freeresult($result);

	
	if (strstr($username, '"') || strstr($username, '&quot;') || strstr($username, ';'))
	{
		return array('error' => true, 'error_msg' => $lang['Username_invalid']);
	}

	return array('error' => false, 'error_msg' => '');
}

/**
* 验证电子邮件地址
**/
function validate_email($email)
{
	global $db, $lang;

	if ($email != '')
	{
		if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email))
		{
			$sql = "SELECT ban_email
				FROM " . BANLIST_TABLE;
			if ($result = $db->sql_query($sql))
			{
				if ($row = $db->sql_fetchrow($result))
				{
					do
					{
						$match_email = str_replace('*', '.*?', $row['ban_email']);
						if (preg_match('/^' . $match_email . '$/is', $email))
						{
							$db->sql_freeresult($result);
							return array('error' => true, 'error_msg' => $lang['Email_banned']);
						}
					}
					while($row = $db->sql_fetchrow($result));
				}
			}
			$db->sql_freeresult($result);

			$sql = "SELECT user_email
				FROM " . USERS_TABLE . "
				WHERE user_email = '" . str_replace("\'", "''", $email) . "'";
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, "Couldn't obtain user email information.", "", __LINE__, __FILE__, $sql);
			}
		
			if ($row = $db->sql_fetchrow($result))
			{
				return array('error' => true, 'error_msg' => $lang['Email_taken']);
			}
			$db->sql_freeresult($result);

			return array('error' => false, 'error_msg' => '');
		}
	}

	return array('error' => true, 'error_msg' => $lang['Email_invalid']);
}

/**
* 验证其他选项，例如 ICQ 等
**/
function validate_optional_fields(&$icq, &$aim, &$msnm, &$yim, &$website, &$location, &$occupation, &$interests, &$signature)
{
	$check_var_length = array('aim', 'msnm', 'yim', 'location', 'occupation', 'interests', 'signature');

	for($i = 0; $i < count($check_var_length); $i++)
	{
		if (strlen($$check_var_length[$i]) < 2)
		{
			$$check_var_length[$i] = '';
		}
	}

	if (!preg_match('/^[0-9]+$/', $icq))
	{
		$icq = '';
	}

	if ($website != "")
	{
		if (!preg_match('#^http[s]?:\/\/#i', $website))
		{
			$website = 'http://' . $website;
		}

		if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $website))
		{
			$website = '';
		}
	}

	return;
}

?>