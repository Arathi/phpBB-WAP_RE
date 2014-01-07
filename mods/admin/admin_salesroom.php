<?php

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['活动插件']['拍卖系统'] = "$file";
	return;
}
define('IN_PHPBB', 1);

$phpbb_root_path = './../../';
require($phpbb_root_path . 'extension.inc');
require('pagestart.' . $phpEx);

if( !$userdata['session_logged_in'] )
{
	header('Location: ' . append_sid("login.$phpEx?redirect=admin/admin_salesroom.$phpEx", true));
}

if( $userdata['user_level'] != ADMIN )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}



//$template->pparse('body');

include('page_footer_mods.' . $phpEx);
?>
