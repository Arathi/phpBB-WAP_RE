<?php
/*****************************************
 *		usercp_selectstyle.php
 *		---------------------
 *		作者：爱疯的云
 ******************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

define('DEFAULT_STYLE', 1);

$confirm = ( isset($HTTP_POST_VARS['confirm']) ) ? TRUE : FALSE;

$default_style = ( empty($userdata['user_style']) ) ? DEFAULT_STYLE : intval($userdata['user_style']);
$sql = "SELECT style_id 
	FROM " . STYLES_TABLE . " 
	WHERE style_id = $default_style";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "无法查询 style 表！", '', __LINE__, __FILE__, $sql);
}
if ( !$row = $db->sql_fetchrow($result) )
{
	$default_style = DEFAULT_STYLE;
}
$style_select = style_select($default_style, 'style');

if ( $confirm )
{
	if ( isset($HTTP_POST_VARS['style']) )
	{
		$style = intval($HTTP_POST_VARS['style']);
	}
	else
	{
		$style = DEFAULT_STYLE;
	}
	if ( !$style )
	{
		$style = DEFAULT_STYLE;
	}
	
	$sql = "UPDATE " . USERS_TABLE . " 
		SET user_style = $style 
		WHERE user_id = " . $userdata['user_id'];
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "无法更新风格信息！", "", __LINE__, __FILE__, $sql);
	}
	$message = '风格设置成功！<br /><br />点击 <a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=selectstyle") . '">这里</a> 返回上一页面<br /><br />点击 <a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">这里</a>返回首页';
	message_die(GENERAL_MESSAGE, $message);

}
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	"body" => "profile_style_select.tpl")
);
$template->assign_vars(array(
	'STYLE_SELECT' => $style_select,
	'L_SUBMIT' => $lang['Submit'],
	'S_POST_ACTION' => append_sid("{$phpbb_root_path}profile.$phpEx?mode=selectstyle"))
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>