<?php
/***************************************************************************
 *			pagestart.php
 *			----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		中文网站：http://phpbb-wap.com
 *		本程序的汉化修改作者：张云
 *		ＱＱ联系：53109774 ( 爱疯的云 ).
 *		电子邮件：zhangyunwap@qq.com
 *		最后修改时间：2011-10-01
 *		对该文件的说明：开始( modcp )
 ***************************************************************************/
/***************************************************************************
 *	本程序为自由软件
 *	您可依据自由软件基金会所发表的GNU通用公共授权条款规定，就本程序再为散布与／或修改
 *	无论您依据的是本授权的第二版或（您自行选择的）任一日后发行的版本
 *	本程序系基于使用目的而加以散布，然而不负任何担保责任
 *	亦无对适售性或特定目的适用性所为的默示性担保
 *	详情请参照GNU通用公共授权
 ***************************************************************************/

if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

define('IN_ADMIN', true);
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);

if (!$userdata['session_logged_in'])
{
	redirect(append_sid('login.' . $phpEx . '?redirect=modcp/index.' . $phpEx, true));
}
else if ($userdata['user_level'] == USER)
{
	message_die(GENERAL_MESSAGE, '您没有全局版主权限！');
}
else if ($userdata['user_level'] == MOD)
{
	message_die(GENERAL_MESSAGE, '您没有全局版主权限！');
}

if ($HTTP_GET_VARS['sid'] != $userdata['session_id'])
{
	$url = str_replace(preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['server_name'])), '', $HTTP_SERVER_VARS['REQUEST_URI']);
	$url = str_replace(preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['script_path'])), '', $url);
	$url = str_replace('//', '/', $url);
	$url = preg_replace('/sid=([^&]*)(&?)/i', '', $url);
	$url = preg_replace('/\?$/', '', $url);
	$url .= ((strpos($url, '?')) ? '&' : '?') . 'sid=' . $userdata['session_id'];

	redirect("index.$phpEx?sid=" . $userdata['session_id']);
}

if (!$userdata['session_admin'])
{
	redirect(append_sid('login.' . $phpEx . '?redirect=modcp/index.' . $phpEx . '&admin=1', true));
}

if (empty($no_page_header))
{
	include('./page_header_modcp.' . $phpEx);
}

?>