<?php
/*************************************************************
 *		admin_shop_daoju.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2013 год
 *		作者:dilu
 *************************************************************/
define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Shop']['Shop_daoju'] = $file;
	return;
}

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);
function check_into($num){
	if(!preg_match("/^[0-9]*$/",$num)){
		message_die(GENERAL_ERROR, '输入不合法，请输入数字');
	}
}
	$template->set_filenames(array(
		'body' => 'admin/admin_shop_daoju.tpl')
	);
function update_config($config_name,$config_value){
	global $db;
	$sql = "UPDATE " . CONFIG_TABLE . " SET config_value = '" . $config_value . "' WHERE config_name = '" .$config_name . "'";
			if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, '道具设置更新出错', '', __LINE__, __FILE__, $sql);
		}

}
	
//获取表单传来的值
if ( isset($HTTP_POST_VARS['submit']) ){
$daoju_swtich = ( isset($HTTP_POST_VARS['daoju_swtich']) ) ? $HTTP_POST_VARS['daoju_swtich'] : '0';
$stick_price = ( isset($HTTP_POST_VARS['stick_price']) ) ? $HTTP_POST_VARS['stick_price'] : '0';
$stick_time_1 = ( isset($HTTP_POST_VARS['stick_time']) ) ? $HTTP_POST_VARS['stick_time'] : '0';
$highlight_price = ( isset($HTTP_POST_VARS['highlight_price']) ) ? $HTTP_POST_VARS['highlight_price'] : '0';
$highlight_time_1 = ( isset($HTTP_POST_VARS['highlight_time']) ) ? $HTTP_POST_VARS['highlight_time'] : '0';
//检查取值是否正确

check_into($daoju_swtich);
check_into($stick_price);
check_into($stick_time_1);
check_into($highlight_price);
check_into($highlight_time_1);

//更新道具功能设置
update_config("daoju_swtich",$daoju_swtich);
update_config("hightlight_price",$highlight_price);
update_config("stick_price",$stick_price);
$stick_time_1 = $stick_time_1 * 3600;
$highlight_time_1 = $highlight_time_1 * 3600;
update_config("highlight_time",$highlight_time_1);
update_config("stick_time",$stick_time_1);

message_die(GENERAL_MESSAGE, '更新完毕！<br /><br />点击 <a href="' . append_sid("admin_shop_daoju.$phpEx") . '">这里</a> 返回上一页面');
}

//把默认时间化为小时
$highlight_time = $board_config['highlight_time']/3600;
$stick_time = $board_config['stick_time']/3600;
	
	$template->assign_vars(array(
		'S_ACTION'             => append_sid("admin_shop_daoju.$phpEx"),
		'POINT_NAME'           => $attach_config['point_name'],
		'DAOJU_SWTICH'         => $board_config['daoju_swtich'],
		'HIGHLIGHT_PRICE'      => $board_config['highlight_price'],
		'STICK_PRICE'          => $board_config['stick_price'],
		'HIGHLIGHT_TIME'       => $highlight_time,
		'STICK_TIME'           => $stick_time)
	);

	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);



?>