<?php
/***************************************************
 *		links/submit.php
 *		---------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：提交友链信息
 ***************************************************/
 
define('IN_PHPBB', true);
$phpbb_root_path = './../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_link_exchange.' . $phpEx);

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['posts_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);

$page_title = $lang['Link_exchange'];

if ( isset($HTTP_POST_VARS['submit']) )
{
	$name = ( isset($HTTP_POST_VARS['name']) ) ? trim($HTTP_POST_VARS['name']) : '';
	$email_from = ( isset($HTTP_POST_VARS['email']) ) ? trim($HTTP_POST_VARS['email']) : '';	
	$website = ( isset($HTTP_POST_VARS['website']) ) ? trim($HTTP_POST_VARS['website']) : '';
	$banner = ( isset($HTTP_POST_VARS['banner']) ) ? trim($HTTP_POST_VARS['banner']) : '';		
	$site_desc = ( isset($HTTP_POST_VARS['site_desc']) ) ? trim($HTTP_POST_VARS['site_desc']) : '';
	$links_url = ( isset($HTTP_POST_VARS['links_url']) ) ? trim($HTTP_POST_VARS['links_url']) : '';
	$comments = ( isset($HTTP_POST_VARS['comments']) ) ? trim($HTTP_POST_VARS['comments']) : '';

	if ( empty($name) || mb_strlen($name,"UTF-8")>6 )
	{
		message_die(GENERAL_ERROR, $lang['Empty_name_submission']);
	}
	if ( empty($website) || mb_strlen($website,"UTF-8")>25 )
	{
		message(GENERAL_ERROR, $lang['Empty_website_submission']);
	}
	if ( !filter_var($email_from, FILTER_VALIDATE_EMAIL) )
	{
		message_die(GENERAL_ERROR, $lang['Empty_email_submission']);
	}
	if ( empty($site_desc) || mb_strlen($site_desc,"UTF-8")>255 )
	{
		message_die(GENERAL_ERROR, $lang['Empty_site_desc_submission']);
	}
	if ( !filter_var($links_url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) )
	{
		message_die(GENERAL_ERROR, $lang['Empty_links_url_submission']);
	}
	if ( !filter_var($banner, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) )
	{
		message_die(GENERAL_ERROR, 'LOGO不正确');
	}
	
	$sql = "SELECT MAX(link_id)+1 as link_id FROM " . LINK_EXCHANGE_TABLE; 
	if( !$result = $db->sql_query($sql) ) 
	{ 
		message_die(GENERAL_ERROR, "无法查询友情链接信息！", "", __LINE__, __FILE__, $sql); 
	} 
	$links_data = $db->sql_fetchrow($result); 
	
	$sql = "INSERT INTO " . LINK_EXCHANGE_TABLE . " (link_id, link_name, link_email, link_website, link_img, link_desc, link_url, link_active, link_in)
		VALUES (".$links_data['link_id'].", '" . str_replace("\'", "''", $name) . "', '" . str_replace("\'", "''", $email_from) . "', '" . str_replace("\'", "''", $website) . "', '" . str_replace("\'", "''", $banner) . "', '" . str_replace("\'", "''", $site_desc) . "', '" . str_replace("\'", "''", $links_url) . "', 0, 0)";
	
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "无法添加新网站！", "", __LINE__, __FILE__, $sql);
	}

	$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
	$server_name = trim($board_config['server_name']);
	$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
	$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
	$server_url = $server_protocol . $server_name . $server_port . $script_name;
	$link_in_url = $server_url . "links/index.$phpEx?id={$links_data['link_id']}";
	
	$message = '您的回链地址是：<br />' . $link_in_url . '<br /><input type="text" value="' . $link_in_url . '" style="width:96%;" />';
	$message .= $lang['Submission_sent'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
	message_die(GENERAL_MESSAGE, $message);

}
	
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'links/link_submit.tpl')
);

$template->assign_vars(array(
	'S_POST_ACTION' => append_sid('submit.'.$phpEx), 
	'U_LINKS' => append_sid("{$phpbb_root_path}links/index.$phpEx"))
);

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);		
?>