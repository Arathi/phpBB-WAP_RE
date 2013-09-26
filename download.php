<?php
/***************************************************
 *		download.php
 *		---------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：下载
 ***************************************************/

if (defined('IN_PHPBB'))
{
	die('Hacking attempt');
	exit;
}

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.' . $phpEx);


$download_id = get_var('id', 0);
$thumbnail = get_var('thumb', 0);

$userdata = session_pagestart($user_ip, PAGE_DOWNLOAD);
init_userprefs($userdata);

if (!$download_id)
{
	message_die(GENERAL_ERROR, $lang['No_attachment_selected']);
}

if ($attach_config['disable_mod'] && $userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_MESSAGE, $lang['Attachment_feature_disabled']);
}
	
$sql = 'SELECT *
	FROM ' . ATTACHMENTS_DESC_TABLE . '
	WHERE attach_id = ' . (int) $download_id;

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query attachment informations', '', __LINE__, __FILE__, $sql);
}

if (!($attachment = $db->sql_fetchrow($result)))
{
	message_die(GENERAL_MESSAGE, $lang['Error_no_attachment']);
}

$attachment['physical_filename'] = basename($attachment['physical_filename']);

$db->sql_freeresult($result);

$authorised = false;

$sql = 'SELECT *
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE attach_id = ' . (int) $attachment['attach_id'];

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query attachment informations', '', __LINE__, __FILE__, $sql);
}

$auth_pages = $db->sql_fetchrowset($result);
$num_auth_pages = $db->sql_numrows($result);

for ($i = 0; $i < $num_auth_pages && $authorised == false; $i++)
{
	$auth_pages[$i]['post_id'] = intval($auth_pages[$i]['post_id']);

	if ($auth_pages[$i]['post_id'] != 0)
	{
		$sql = 'SELECT forum_id
			FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . (int) $auth_pages[$i]['post_id'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query post information', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);

		$forum_id = $row['forum_id'];

		$is_auth = array();
		$is_auth = auth(AUTH_ALL, $forum_id, $userdata); 

		if ($is_auth['auth_download'])
		{
			$authorised = TRUE;
		}
	}
	else
	{
		if ( (intval($attach_config['allow_pm_attach'])) && ( ($userdata['user_id'] == $auth_pages[$i]['user_id_2']) || ($userdata['user_id'] == $auth_pages[$i]['user_id_1']) ) || ($userdata['user_level'] == ADMIN) )
		{
			$authorised = TRUE;
		}
	}
}


if (!$authorised)
{
	message_die(GENERAL_MESSAGE, $lang['Sorry_auth_view_attach']);
}

$sql = "SELECT e.extension, g.download_mode
	FROM " . EXTENSION_GROUPS_TABLE . " g, " . EXTENSIONS_TABLE . " e
	WHERE (g.allow_group = 1) AND (g.group_id = e.group_id)";

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not query Allowed Extensions.', '', __LINE__, __FILE__, $sql);
}

$rows = $db->sql_fetchrowset($result);
$num_rows = $db->sql_numrows($result);

for ($i = 0; $i < $num_rows; $i++)
{
	$extension = strtolower(trim($rows[$i]['extension']));
	$allowed_extensions[] = $extension;
	$download_mode[$extension] = $rows[$i]['download_mode'];
}

if (!in_array($attachment['extension'], $allowed_extensions) && $userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_MESSAGE, sprintf($lang['Extension_disabled_after_posting'], $attachment['extension']));
} 

$download_mode = intval($download_mode[$attachment['extension']]);
	
if ($thumbnail)
{
	$attachment['physical_filename'] = THUMB_DIR . '/t_' . $attachment['physical_filename'];
}

if ( !$thumbnail )
{
	$sql = "SELECT *
		FROM " . ATTACHMENTS_TABLE . "
		WHERE attach_id = " . (int) $download_id;
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, '无法查询 attachments 表', '', __LINE__, __FILE__, $sql);
	}

	if (!($attachments = $db->sql_fetchrow($result)))
	{
		message_die(GENERAL_MESSAGE, '结果不存在！');
	}
	
	$is_auth_download = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_level'] == MODCP || $attachments['user_id_1'] == $userdata['user_id'] ) ? true : false;
	
	if ( $userdata['session_logged_in'] )
	{
		// 管理员、版主、自己下载附件无需金币
		if ( $is_auth_download )
		{
			$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
				SET download_count = download_count + 1 
				WHERE attach_id = ' . (int) $attachment['attach_id'];
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, '无法更新 attachments_desc 表', '', __LINE__, __FILE__, $sql);
			}
		}
		// 用户积分必须大于10
		else if ( $userdata['user_level'] == USER && $userdata['user_points'] >= $attach_config['download_cut_points'] )
		{
			// 扣取下载者的积分和添加下载次数
			$sql = "UPDATE " . ATTACHMENTS_DESC_TABLE . " adt, " . USERS_TABLE . " u
				SET adt.download_count = adt.download_count + 1, 
					u.user_points = u.user_points - " . $attach_config['download_cut_points'] . "
				WHERE adt.attach_id = " . (int) $attachment['attach_id'] . "
					AND u.user_id = " . $userdata['user_id'];
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, '无法更新 users 表', '', __LINE__, __FILE__, $sql);
			}
			
			// 增加文件上传者的积分
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points + " . $attach_config['download_add_points'] . " 
				WHERE user_id = " . $attachments['user_id_1'];
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, '无法更新 users 表', '', __LINE__, __FILE__, $sql);
			}
			
		}
		else
		{
			message_die(GENERAL_MESSAGE, '您可能没有足够的金币下载此附件！');
		}

	}
	else
	{
		message_die(GENERAL_MESSAGE, '请 <a href="' . append_sid("login.$phpEx?redirect=download.$phpEx?id=$download_id") . '">登录</a> 后再下载、查看附件！');
	}
}

$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://';
$server_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['server_name']));
$server_port = ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
$script_name = preg_replace('/^\/?(.*?)\/?$/', '/\1', trim($board_config['script_path']));

if ($script_name[strlen($script_name)] != '/')
{
	$script_name .= '/';
}
$url = $upload_dir . '/' . $attachment['physical_filename'];
//$url = preg_replace('/^\/?(.*?\/)?$/', '\1', trim($url));
$redirect_path = $server_protocol . $server_name . $server_port . $script_name . $url;

if (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')))
{
	header('Refresh: 0; URL=' . $redirect_path);
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
		 '<html>';
		 '<head>';
		 '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		 '<meta http-equiv="refresh" content="0; url=' . $redirect_path . '">';
		 '<title>跳转</title>';
		 '</head>';
		 '<body>';
		 '<div align="center">如果浏览器无法跳转请点击 <a href="' . $redirect_path . '">这里</a> 下载！</div>';
		 '</body>';
		 '</html>';
	exit;
}
$mimetype = $attachment['mimetype'];
$real_filename = htmlspecialchars($attachment['real_filename']);
$filesize = $attachment['filesize'];

header('Content-Encoding: none');
header('Content-Type: ' . $mimetype . ';name="' . $real_filename . '"');
header('Content-Disposition: attachment;filename='.$real_filename);
Header("Accept-Ranges: bytes");
Header('Accept-Length: ' . $filesize);
header('Pragma: no-cache');
header('Expires: 0');

header('Location: ' . $redirect_path);

exit;

?>