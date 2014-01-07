<?php
/******************************
 * 文件名称：addgoods.php
 * 版权所有：Arathi Software Foundation
 * 文件说明：拍卖物信息查看
 ******************************/
define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
$userdata = session_pagestart($user_ip, PAGE_SALESROOM);
init_userprefs($userdata);
if ( !$userdata['session_logged_in'] )
{
    redirect(append_sid("login.$phpEx?redirect=mods/salesroom/index.$phpEx", true));
    exit;
}



$page_title = '拍卖行';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
$template->set_filenames(array(
    'body' => 'mods/salesroom_body.tpl'
));
$template->assign_vars( array() );
$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>