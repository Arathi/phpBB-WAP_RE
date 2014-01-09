<?php
/****************************************
 * 文件名称：admin_uid_changer.php      *
 * 版权所有：Arathi Software Foundation *
 * 文件说明：拍卖行首页                 *
 ****************************************/

define('IN_PHPBB', 1);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['系统插件']['用户ID修改器'] = "$file";
	return;
}

$phpbb_root_path = './../../';
require($phpbb_root_path . 'extension.inc');
require('pagestart.' . $phpEx);
//include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/mods/lang_bank.' . $phpEx);

define('UIDCHANGER_TABLE', $table_prefix . "uid_changer");

if( !$userdata['session_logged_in'] )
{
	header('Location: ' . append_sid("login.$phpEx?redirect=admin/admin_bank.$phpEx", true));
}

if( $userdata['user_level'] != ADMIN )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

if ( isset($HTTP_GET_VARS['olduid']) || isset($HTTP_POST_VARS['olduid']) ) {
    $olduid = ( isset($HTTP_POST_VARS['olduid']) ) ? $HTTP_POST_VARS['olduid'] : $HTTP_GET_VARS['olduid'];
}
else {
    $olduid = ''; 
}
if ( isset($HTTP_GET_VARS['newuid']) || isset($HTTP_POST_VARS['newuid']) ) {
    $newuid = ( isset($HTTP_POST_VARS['newuid']) ) ? $HTTP_POST_VARS['newuid'] : $HTTP_GET_VARS['newuid'];
}
else {
    $newuid = ''; 
}

if ( is_numeric($olduid) && $olduid>0 && is_numeric($newuid) && $newuid>0  )
{
    //执行修改ID的逻辑
    //先检查原ID是否存在
    $sql_check = "SELECT user_id FROM " . USERS_TABLE . " WHERE user_id = " . $olduid;
    if ( !($result = $db->sql_query($sql_check)) )
    {
        message_die(GENERAL_ERROR, '查询出错！', '', __LINE__, __FILE__, $sql_check);
    }
    if ( !($row = $db->sql_fetchrow($result)) )
    { 
        message_die(GENERAL_ERROR, '需要修改的原id[' . $olduid .']不存在！'); 
    }
    //再检查新ID是否存在
    $sql_check = "SELECT user_id FROM " . USERS_TABLE . " WHERE user_id = " . $newuid;
    if ( !($result = $db->sql_query($sql_check)) )
    {
        message_die(GENERAL_ERROR, '查询出错！', '', __LINE__, __FILE__, $sql_check);
    }
    if ( ($row = $db->sql_fetchrow($result)) )
    {
        message_die(GENERAL_ERROR, '需要修改的新id[' . $olduid .']已存在！'); 
    }
    //载入UID字段列表
    $sql_load_fields = "SELECT * FROM " . UIDCHANGER_TABLE;
    if ( !($result = $db->sql_query($sql_load_fields)) )
    {
        message_die(GENERAL_ERROR, '查询出错！', '', __LINE__, __FILE__, $sql_load_fields);
    }
    $fields_list = array();
    while ( !($row = $db->sql_fetchrow($result)) )
    {
        $fields_list[] = $row;
    }
    $error_message = "";
    //执行修改
    foreach ($fields_list as $field_info)
    {
        $table_name = $table_prefix . $field_info['table_name'];
        $field_name = $field_info['field_name'];
        $sql = "UPDATE $table_name SET $field_name=$newuid WHERE $field_name=$olduid";
        if ( !($result = $db->sql_query($sql)) )
        {
            //message_die(GENERAL_ERROR, '更新出错！', '', __LINE__, __FILE__, $sql_load_fields);
            $error_message .= $table_name . "." . $field_name . "更新失败";
        }
    }
    
    $template->assign_block_vars( 'message', array(
            'MESSAGE' => $error_message
        )
    );
}
else
{
    //显示页面
    $template->assign_block_vars( 'change_form', array() );
}

$template->pparse('body');
include('page_footer_mods.' . $phpEx);
?>
