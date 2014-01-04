<?php

if ( !defined('IN_PHPBB') )
{
	die("ERROR!!! THIS FILE PROTECTED. IF YOU SAW THIS REPORT, MEANS HACKERS HERE IS NOTHING TO DO ");
}

if (!function_exists('html_entity_decode'))
{
	function html_entity_decode($given_html, $quote_style = ENT_QUOTES)
	{
		$trans_table = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style));
		$trans_table['&#39;'] = "'";
		return (strtr($given_html, $trans_table));
	}
}

function base64_pack($number) 
{ 
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	if ($number > 4096)
	{
		return;
	}
	else if ($number < $base)
	{
		return $chars[$number];
	}
	
	$hexval = '';
	
	while ($number > 0) 
	{ 
		$remainder = $number%$base;
	
		if ($remainder < $base)
		{
			$hexval = $chars[$remainder] . $hexval;
		}

		$number = floor($number/$base); 
	} 

	return $hexval; 
}

function base64_unpack($string)
{
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	$length = strlen($string); 
	$number = 0; 

	for($i = 1; $i <= $length; $i++)
	{ 
		$pos = $length - $i; 
		$operand = strpos($chars, substr($string,$pos,1));
		$exponent = pow($base, $i-1); 
		$decValue = $operand * $exponent; 
		$number += $decValue; 
	} 

	return $number; 
}

function auth_pack($auth_array)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';
	$one_char = $two_char = false;
	$auth_cache = '';
	
	for ($i = 0; $i < sizeof($auth_array); $i++)
	{
		$val = base64_pack(intval($auth_array[$i]));
		if (strlen($val) == 1 && !$one_char)
		{
			$auth_cache .= $one_char_encoding;
			$one_char = true;
		}
		else if (strlen($val) == 2 && !$two_char)
		{		
			$auth_cache .= $two_char_encoding;
			$two_char = true;
		}
		
		$auth_cache .= $val;
	}

	return $auth_cache;
}

function auth_unpack($auth_cache)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	$auth = array();
	$auth_len = 1;
	
	for ($pos = 0; $pos < strlen($auth_cache); $pos += $auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}
		
		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = base64_unpack($forum_auth);
		$auth[] = intval($forum_id);
	}
	return $auth;
}

function is_forum_authed($auth_cache, $check_forum_id)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	if (trim($auth_cache) == '')
	{
		return true;
	}

	$auth = array();
	$auth_len = 1;
	
	for ($pos = 0; $pos < strlen($auth_cache); $pos+=$auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}
		
		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = (int) base64_unpack($forum_auth);
		if ($forum_id == $check_forum_id)
		{
			return true;
		}
	}
	return false;
}

function attach_init_ftp($mode = false)
{
	global $lang, $attach_config;

	$server = (trim($attach_config['ftp_server']) == '') ? 'localhost' : trim($attach_config['ftp_server']);
	
	$ftp_path = ($mode == MODE_THUMBNAIL) ? trim($attach_config['ftp_path']) . '/' . THUMB_DIR : trim($attach_config['ftp_path']);

	$conn_id = @ftp_connect($server);

	if (!$conn_id)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_connect'], $server));
	}

	$login_result = @ftp_login($conn_id, $attach_config['ftp_user'], $attach_config['ftp_pass']);

	if (!$login_result)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_login'], $attach_config['ftp_user']));
	}
		
	if (!@ftp_pasv($conn_id, intval($attach_config['ftp_pasv_mode'])))
	{
		message_die(GENERAL_ERROR, $lang['Ftp_error_pasv_mode']);
	}
	
	$result = @ftp_chdir($conn_id, $ftp_path);

	if (!$result)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_path'], $ftp_path));
	}

	return $conn_id;
}

function unlink_attach($filename, $mode = false)
{
	global $upload_dir, $attach_config, $lang;

	$filename = basename($filename);
	
	if (!intval($attach_config['allow_ftp_upload']))
	{
		if ($mode == MODE_THUMBNAIL)
		{
			$filename = $upload_dir . '/' . THUMB_DIR . '/t_' . $filename;
		}
		else
		{
			$filename = $upload_dir . '/' . $filename;
		}

		$deleted = @unlink($filename);
	}
	else
	{
		$conn_id = attach_init_ftp($mode);

		if ($mode == MODE_THUMBNAIL)
		{
			$filename = 't_' . $filename;
		}
		
		$res = @ftp_delete($conn_id, $filename);
		if (!$res)
		{
			if (ATTACH_DEBUG)
			{
				$add = ($mode == MODE_THUMBNAIL) ? '/' . THUMB_DIR : ''; 
				message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_delete'], $attach_config['ftp_path'] . $add));
			}

			return $deleted;
		}

		@ftp_quit($conn_id);

		$deleted = true;
	}

	return $deleted;
}

function ftp_file($source_file, $dest_file, $mimetype, $disable_error_mode = false)
{
	global $attach_config, $lang, $error, $error_msg;

	$conn_id = attach_init_ftp();

	$mode = FTP_BINARY;
	if (preg_match("/text/i", $mimetype) || preg_match("/html/i", $mimetype))
	{
		$mode = FTP_ASCII;
	}

	$res = @ftp_put($conn_id, $dest_file, $source_file, $mode);

	if (!$res && !$disable_error_mode)
	{
		$error = true;
		if (!empty($error_msg))
		{
			$error_msg .= '<br />';
		}
		$error_msg = sprintf($lang['Ftp_error_upload'], $attach_config['ftp_path']) . '<br />';
		@ftp_quit($conn_id);
		return false;
	}

	if (!$res)
	{
		return false;
	}

	@ftp_site($conn_id, 'CHMOD 0644 ' . $dest_file);
	@ftp_quit($conn_id);
	return true;
}

function attachment_exists($filename)
{
	global $upload_dir, $attach_config;

	$filename = basename($filename);

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if (!@file_exists(@amod_realpath($upload_dir . '/' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp();

		$file_listing = array();

		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd') 
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2]; 
				$dirinfo[2] = $regs[3]; 
				$dirinfo[3] = $regs[4];
				$dirinfo[4] = $regs[5]; 
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);	
		
		return $found;
	}
}

function thumbnail_exists($filename)
{
	global $upload_dir, $attach_config;

	$filename = basename($filename);

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if (!@file_exists(@amod_realpath($upload_dir . '/' . THUMB_DIR . '/t_' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp(MODE_THUMBNAIL);

		$file_listing = array();

		$filename = 't_' . $filename;
		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd')
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2]; 
				$dirinfo[2] = $regs[3]; 
				$dirinfo[3] = $regs[4]; 
				$dirinfo[4] = $regs[5]; 
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);	
		
		return $found;
	}
}

function physical_filename_already_stored($filename)
{
	global $db;

	if ($filename == '')
	{
		return false;
	}

	$filename = basename($filename);

	$sql = 'SELECT attach_id 
		FROM ' . ATTACHMENTS_DESC_TABLE . "
		WHERE physical_filename = '" . attach_mod_sql_escape($filename) . "' 
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not get attachment information for filename: ' . htmlspecialchars($filename), '', __LINE__, __FILE__, $sql);
	}
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	return ($num_rows == 0) ? false : true;
}

function attachment_exists_db($post_id, $page = 0)
{
	global $db;

	$post_id = (int) $post_id;

	if ($page == PAGE_PRIVMSGS)
	{
		$sql_id = 'privmsgs_id';
	}
	else
	{
		$sql_id = 'post_id';
	}

	$sql = 'SELECT attach_id
		FROM ' . ATTACHMENTS_TABLE . "
		WHERE $sql_id = $post_id 
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not get attachment informations for specific posts', '', __LINE__, __FILE__, $sql);
	}
	
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_attachments_from_post($post_id_array)
{
	global $db, $attach_config;

	$attachments = array();

	if (!is_array($post_id_array))
	{
		if (empty($post_id_array))
		{
			return $attachments;
		}

		$post_id = intval($post_id_array);

		$post_id_array = array();
		$post_id_array[] = $post_id;
	}

	$post_id_array = implode(', ', array_map('intval', $post_id_array));

	if ($post_id_array == '')
	{
		return $attachments;
	}

	$display_order = (intval($attach_config['display_order']) == 0) ? 'DESC' : 'ASC';
	
	$sql = 'SELECT a.post_id, d.*
		FROM ' . ATTACHMENTS_TABLE . ' a, ' . ATTACHMENTS_DESC_TABLE . " d
		WHERE a.post_id IN ($post_id_array)
			AND a.attach_id = d.attach_id
		ORDER BY d.filetime $display_order";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get Attachment Informations for post number ' . $post_id_array, '', __LINE__, __FILE__, $sql);
	}
	
	$num_rows = $db->sql_numrows($result);
	$attachments = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return array();
	}
		
	return $attachments;
}

function get_attachments_from_pm($privmsgs_id_array)
{
	global $db, $attach_config;

	$attachments = array();

	if (!is_array($privmsgs_id_array))
	{
		if (empty($privmsgs_id_array))
		{
			return $attachments;
		}

		$privmsgs_id = intval($privmsgs_id_array);

		$privmsgs_id_array = array();
		$privmsgs_id_array[] = $privmsgs_id;
	}

	$privmsgs_id_array = implode(', ', array_map('intval', $privmsgs_id_array));

	if ($privmsgs_id_array == '')
	{
		return $attachments;
	}

	$display_order = (intval($attach_config['display_order']) == 0) ? 'DESC' : 'ASC';
	
	$sql = 'SELECT a.privmsgs_id, d.*
		FROM ' . ATTACHMENTS_TABLE . ' a, ' . ATTACHMENTS_DESC_TABLE . " d
		WHERE a.privmsgs_id IN ($privmsgs_id_array) 
			AND a.attach_id = d.attach_id
		ORDER BY d.filetime $display_order";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get Attachment Informations for private message number ' . $privmsgs_id_array, '', __LINE__, __FILE__, $sql);
	}
	
	$num_rows = $db->sql_numrows($result);
	$attachments = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0 )
	{
		return array();
	}

	return $attachments;
}

function get_total_attach_filesize($attach_ids)
{
	global $db;

	if (!is_array($attach_ids) || !sizeof($attach_ids))
	{
		return 0;
	}

	$attach_ids = implode(', ', array_map('intval', $attach_ids));

	if (!$attach_ids)
	{
		return 0;
	}

	$sql = 'SELECT filesize
		FROM ' . ATTACHMENTS_DESC_TABLE . "
		WHERE attach_id IN ($attach_ids)";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query Total Filesize', '', __LINE__, __FILE__, $sql);
	}

	$total_filesize = 0;

	while ($row = $db->sql_fetchrow($result))
	{
		$total_filesize += (int) $row['filesize'];
	}
	$db->sql_freeresult($result);

	return $total_filesize;
}

function get_total_attach_pm_filesize($direction, $user_id)
{
	global $db;

	if ($direction != 'from_user' && $direction != 'to_user')
	{
		return 0;
	}
	else
	{
		$user_sql = ($direction == 'from_user') ? '(a.user_id_1 = ' . intval($user_id) . ')' : '(a.user_id_2 = ' . intval($user_id) . ')';
	}

	$sql = 'SELECT a.attach_id
		FROM ' . ATTACHMENTS_TABLE . ' a, ' . PRIVMSGS_TABLE . " p
		WHERE $user_sql 
			AND a.privmsgs_id <> 0 AND a.privmsgs_id = p.privmsgs_id
			AND p.privmsgs_type <> " . PRIVMSGS_SENT_MAIL;

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query Attachment Informations', '', __LINE__, __FILE__, $sql);
	}
				
	$pm_filesize_total = 0;
	$attach_id = array();
	$num_rows = $db->sql_numrows($result);

	if ($num_rows == 0)
	{
		$db->sql_freeresult($result);
		return $pm_filesize_total;
	}
	
	while ($row = $db->sql_fetchrow($result))
	{
		$attach_id[] = $row['attach_id'];
	}
	$db->sql_freeresult($result);

	$pm_filesize_total = get_total_attach_filesize($attach_id);
	return $pm_filesize_total;
}

function get_extension_informations()
{
	global $db;

	$extensions = array();

	$sql = 'SELECT e.extension, g.cat_id, g.download_mode, g.upload_icon
		FROM ' . EXTENSIONS_TABLE . ' e, ' . EXTENSION_GROUPS_TABLE . ' g
		WHERE e.group_id = g.group_id
			AND g.allow_group = 1';
	
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not query Allowed Extensions.', '', __LINE__, __FILE__, $sql);
	}

	$extensions = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);
	return $extensions;
}

function attachment_sync_topic($topic_id)
{
	global $db;

	if (!$topic_id)
	{
		return;
	}

	$topic_id = (int) $topic_id;

	$sql = 'SELECT post_id 
		FROM ' . POSTS_TABLE . " 
		WHERE topic_id = $topic_id
		GROUP BY post_id";
		
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Couldn\'t select Post ID\'s', '', __LINE__, __FILE__, $sql);
	}

	$post_list = $db->sql_fetchrowset($result);
	$num_posts = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_posts == 0)
	{
		return;
	}
	
	$post_ids = array();

	for ($i = 0; $i < $num_posts; $i++)
	{
		$post_ids[] = intval($post_list[$i]['post_id']);
	}

	$post_id_sql = implode(', ', $post_ids);
	
	if ($post_id_sql == '')
	{
		return;
	}
	
	$sql = 'SELECT attach_id 
		FROM ' . ATTACHMENTS_TABLE . " 
		WHERE post_id IN ($post_id_sql) 
		LIMIT 1";
		
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Couldn\'t select Attachment ID\'s', '', __LINE__, __FILE__, $sql);
	}

	$set_id = ($db->sql_numrows($result) == 0) ? 0 : 1;

	$sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_attachment = $set_id WHERE topic_id = $topic_id";

	if ( !($db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Couldn\'t update Topics Table', '', __LINE__, __FILE__, $sql);
	}
		
	for ($i = 0; $i < sizeof($post_ids); $i++)
	{
		$sql = 'SELECT attach_id 
			FROM ' . ATTACHMENTS_TABLE . ' 
			WHERE post_id = ' . $post_ids[$i] . '
			LIMIT 1';

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Couldn\'t select Attachment ID\'s', '', __LINE__, __FILE__, $sql);
		}

		$set_id = ( $db->sql_numrows($result) == 0) ? 0 : 1;
		
		$sql = 'UPDATE ' . POSTS_TABLE . " SET post_attachment = $set_id WHERE post_id = {$post_ids[$i]}";

		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Couldn\'t update Posts Table', '', __LINE__, __FILE__, $sql);
		}
	}
}

function get_extension($filename)
{
	if (!stristr($filename, '.'))
	{
		return '';
	}

	$extension = strrchr(strtolower($filename), '.');
	$extension[0] = ' ';
	$extension = strtolower(trim($extension));
	
	if (is_array($extension))
	{
		return '';
	}
	else
	{
		return $extension;
	}
}

function delete_extension($filename)
{
	return substr($filename, 0, strrpos(strtolower(trim($filename)), '.'));
}

function user_in_group($user_id, $group_id)
{
	global $db;

	$user_id = (int) $user_id;
	$group_id = (int) $group_id;

	if (!$user_id || !$group_id)
	{
		return false;
	}
	
	$sql = 'SELECT u.group_id 
		FROM ' . USER_GROUP_TABLE . ' u, ' . GROUPS_TABLE . " g 
		WHERE g.group_single_user = 0
			AND u.user_pending = 0
			AND u.group_id = g.group_id
			AND u.user_id = $user_id 
			AND g.group_id = $group_id
		LIMIT 1";
			
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not get User Group', '', __LINE__, __FILE__, $sql);
	}

	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return false;
	}
	
	return true;
}

function amod_realpath($path)
{
	return (function_exists('realpath')) ? realpath($path) : $path;
}

function _set_var(&$result, $var, $type, $multibyte = false)
{
	settype($var, $type);
	$result = $var;

	if ($type == 'string')
	{
		$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r", '\xFF'), array("\n", "\n", ' '), $result)));
		$result = stripslashes($result);
		if ($multibyte)
		{
			$result = preg_replace('#&amp;(\#[0-9]+;)#', '&\1', $result);
		}
	}
}

function get_var($var_name, $default, $multibyte = false)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;

	$request_var = (isset($HTTP_POST_VARS[$var_name])) ? $HTTP_POST_VARS : $HTTP_GET_VARS;

	if (!isset($request_var[$var_name]) || (is_array($request_var[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($request_var[$var_name])))
	{
		return (is_array($default)) ? array() : $default;
	}

	$var = $request_var[$var_name];

	if (!is_array($default))
	{
		$type = gettype($default);
	}
	else
	{
		list($key_type, $type) = each($default);
		$type = gettype($type);
		$key_type = gettype($key_type);
	}

	if (is_array($var))
	{
		$_var = $var;
		$var = array();

		foreach ($_var as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $_k => $_v)
				{
					_set_var($k, $k, $key_type);
					_set_var($_k, $_k, $key_type);
					_set_var($var[$k][$_k], $_v, $type, $multibyte);
				}
			}
			else
			{
				_set_var($k, $k, $key_type);
				_set_var($var[$k], $v, $type, $multibyte);
			}
		}
	}
	else
	{
		_set_var($var, $var, $type, $multibyte);
	}
		
	return $var;
}

function attach_mod_sql_escape($text)
{
	switch (SQL_LAYER)
	{
		case 'postgresql':
			return pg_escape_string($text);
		break;

		case 'mysql':
		case 'mysql4':
			if (function_exists('mysql_escape_string'))
			{
				return mysql_escape_string($text);
			}
			else
			{
				return str_replace("'", "''", str_replace('\\', '\\\\', $text));
			}
		break;

		default:
			return str_replace("'", "''", str_replace('\\', '\\\\', $text));
		break;
	}
}

function attach_mod_sql_build_array($query, $assoc_ary = false)
{
	if (!is_array($assoc_ary))
	{
		return false;
	}

	$fields = array();
	$values = array();
	if ($query == 'INSERT' || $query == 'INSERT_SELECT')
	{
		foreach ($assoc_ary as $key => $var)
		{
			$fields[] = $key;

			if (is_null($var))
			{
				$values[] = 'NULL';
			}
			else if (is_string($var))
			{
				$values[] = "'" . attach_mod_sql_escape($var) . "'";
			}
			else if (is_array($var) && is_string($var[0]))
			{
				$values[] = $var[0];
			}
			else
			{
				$values[] = (is_bool($var)) ? intval($var) : $var;
			}
		}

		$query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
	}
	else if ($query == 'MULTI_INSERT')
	{
		$ary = array();
		foreach ($assoc_ary as $id => $sql_ary)
		{
			$values = array();
			foreach ($sql_ary as $key => $var)
			{
				if (is_null($var))
				{
					$values[] = 'NULL';
				}
				elseif (is_string($var))
				{
					$values[] = "'" . attach_mod_sql_escape($var) . "'";
				}
				else
				{
					$values[] = (is_bool($var)) ? intval($var) : $var;
				}
			}
			$ary[] = '(' . implode(', ', $values) . ')';
		}

		$query = ' (' . implode(', ', array_keys($assoc_ary[0])) . ') VALUES ' . implode(', ', $ary);
	}
	else if ($query == 'UPDATE' || $query == 'SELECT')
	{
		$values = array();
		foreach ($assoc_ary as $key => $var)
		{
			if (is_null($var))
			{
				$values[] = "$key = NULL";
			}
			elseif (is_string($var))
			{
				$values[] = "$key = '" . attach_mod_sql_escape($var) . "'";
			}
			else
			{
				$values[] = (is_bool($var)) ? "$key = " . intval($var) : "$key = $var";
			}
		}
		$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
	}

	return $query;
}

?>