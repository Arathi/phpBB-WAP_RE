<?php
/***************************************************************
 *		admin_styles.php
 *		--------------------
 *      Разработка и оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['General']['管理风格'] = $filename;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

$confirm = ( isset($HTTP_POST_VARS['confirm']) ) ? TRUE : FALSE;

if( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
{
	$mode = ( isset($HTTP_GET_VARS['mode']) ) ? $HTTP_GET_VARS['mode'] : $HTTP_POST_VARS['mode'];
	$mode = htmlspecialchars($mode);
}
else 
{
	$mode = "";
}

switch( $mode )
{

case "delete":
	$style_id = ( isset($HTTP_GET_VARS['style_id']) ) ? intval($HTTP_GET_VARS['style_id']) : intval($HTTP_POST_VARS['style_id']);

	if( !$style_id )
	{
		message_die(GENERAL_MESSAGE, "没有选中任何风格！");
	}
	if ( isset($HTTP_POST_VARS['cancel']) )
	{
		redirect(append_sid("admin/admin_styles.$phpEx", true));
	}
	if( !$confirm )
	{
		if ($style_id == 1)
		{
			message_die(GENERAL_MESSAGE, '默认风格是不能卸载的！<br ><br />点击 <a href="' . append_sid("admin_styles.$phpEx") . '">这里</a> 返回风格管理页面！<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>'));
		}
		if($style_id == $board_config['default_style'])
		{
			message_die(GENERAL_MESSAGE, $lang['Cannot_remove_style']);
		}
		
		$hidden_fields = '<input type="hidden" name="mode" value="'.$mode.'" /><input type="hidden" name="style_id" value="'.$style_id.'" />';

		$template->set_filenames(array(
			"confirm" => "admin/confirm_body.tpl")
		);

		$template->assign_vars(array(
			"MESSAGE_TITLE" 	=> $lang['Confirm'],
			"MESSAGE_TEXT" 		=> $lang['Confirm_delete_style'],

			"L_YES" 			=> $lang['Yes'],
			"L_NO" 				=> $lang['No'],

			"S_CONFIRM_ACTION" 	=> append_sid("admin_styles.$phpEx"),
			"S_HIDDEN_FIELDS" 	=> $hidden_fields)
		);

		$template->pparse("confirm");

	}
	else
	{
		
		$sql = "DELETE FROM " . STYLES_TABLE . " 
			WHERE style_id = $style_id";
		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could not remove style data!", "", __LINE__, __FILE__, $sql);
		}
		
		$sql = "UPDATE " . USERS_TABLE . " 
			SET user_style = 1
			WHERE user_style = $style_id";
		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could not remove style data!", "", __LINE__, __FILE__, $sql);
		}
		
		$message = "风格已成功删除 <br /><br />点击 <a href=\"" . append_sid("admin_styles.$phpEx") . "\">这里</a> 返回风格管理页面！<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	break;
	
case "addnew":
	$install_to = ( isset($HTTP_GET_VARS['install_to']) ) ? urldecode($HTTP_GET_VARS['install_to']) : $HTTP_POST_VARS['install_to'];
	$style_name = ( isset($HTTP_GET_VARS['style']) ) ? urldecode($HTTP_GET_VARS['style']) : $HTTP_POST_VARS['style'];

	if( isset($install_to) && isset($style_name) )
	{
		if ( empty($install_to) || empty($style_name) )
		{
			$message = '风格名称或路径为空！';
			message_die(GENERAL_MESSAGE, $message);
		}
		
		$sql = "SELECT style_id FROM " . STYLES_TABLE; 
		
		if( !$total_result = $db->sql_numrows($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "无法统计风格id", "", __LINE__, __FILE__, $sql);
		}
		$new_style_id = $total_result + 1;
		
		$sql = "INSERT INTO " . STYLES_TABLE . " (style_id, style_name, style_path) VALUES ($new_style_id, '$style_name', '$install_to')";
		
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could not insert theme data!", "", __LINE__, __FILE__, $sql);
		}
		
		$message = '风格安装成功！<br /><br />点击 <a href="' . append_sid("admin_styles.$phpEx") . '">这里</a> 返回风格列表！<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');
		message_die(GENERAL_MESSAGE, $message);
	}
	else
	{
		$message = '风格安装失败，请稍候重试！<br /><br />点击 <a href="' . append_sid("admin_styles.$phpEx") . '">这里</a> 返回风格列表！<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');
		message_die(GENERAL_MESSAGE, $message);
	}
	break;
	
default:
	$sql = "SELECT * FROM " . STYLES_TABLE . " ORDER BY style_id ASC";
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Could not get style information!", "", __LINE__, __FILE__, $sql);
	}
	$style_row = $db->sql_fetchrowset($result);
	
	$template->set_filenames(array(
		"body" => "admin/styles_list_body.tpl")
	);

	$hidden_fields = '<input type="hidden" name="mode" value="setup" /><input type="hidden" name="confirm" value="yes" />';

	for($i = 0; $i < count($style_row); $i++)
	{
		$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
		$s_id = $i+1;

		$template->assign_block_vars("styles", array(
			"ROW_CLASS" 		=> $row_class,
			"STYLE_NAME" 		=> $style_row[$i]['style_name'],
			"STYLE_ID" 			=> $s_id,
			"U_STYLE_EDIT" 		=> append_sid("admin_styles.$phpEx?mode=edit&amp;style_id=" . $style_row[$i]['style_id']),
			"U_STYLE_DELETE" 	=> append_sid("admin_styles.$phpEx?mode=delete&amp;style_id=" . $style_row[$i]['style_id']))
		);
	}
	
	$installable_styles = array();
	if( $dir = @opendir($phpbb_root_path. "styles/") )
	{
		while( $sub_dir = @readdir($dir) )
		{
			if( !is_file(phpbb_realpath($phpbb_root_path . 'styles/' .$sub_dir)) && !is_link(phpbb_realpath($phpbb_root_path . 'styles/' .$sub_dir)) && $sub_dir != "." && $sub_dir != ".." && $sub_dir != "CVS" )
			{
				if( @file_exists(@phpbb_realpath($phpbb_root_path. "styles/" . $sub_dir . "/install.cfg")) )
				{
					include($phpbb_root_path. "styles/" . $sub_dir . "/install.cfg");
					
					for($i = 0; $i < count($$sub_dir); $i++)
					{
						$working_data = $$sub_dir;
						
						$style_path = $working_data[$i]['style_name'];
												
						$sql = "SELECT style_id 
							FROM " . STYLES_TABLE . " 
							WHERE style_path = '" . str_replace("\'", "''", $style_path) . "'";
						if(!$result = $db->sql_query($sql))
						{
							message_die(GENERAL_ERROR, "Could not query styles table!", "", __LINE__, __FILE__, $sql);
						}

						if(!$db->sql_numrows($result))
						{
							$installable_styles[] = $working_data[$i];
						}
					}
				}
			}
		}
	}
	for($i = 0; $i < count($installable_styles); $i++)
	{
		$row_class = ( !($i % 2) ) ? 'row_hard' : 'row_easy';
		$number = $i+1;
		
		$template->assign_block_vars("not_add_styles", array(
			"ROW_CLASS" => $row_class,
			"NUMBER" => $number,
			"STYLE_NAME" => $installable_styles[$i]['style_name'],

			"U_STYLES_INSTALL" => append_sid("admin_styles.$phpEx?mode=addnew&amp;style=" . urlencode($installable_styles[$i]['style_name']) . "&amp;install_to=" . urlencode($installable_styles[$i]['style_path'])))
		);
	}
				
	$template->assign_vars(array(
		"S_PROFILE_ACTION" 	=> append_sid('admin_styles.'.$phpEx),
		"L_SUBMIT" 			=> $lang['Submit'], 
		"S_HIDDEN_FIELDS" 	=> $hidden_fields)
	);

	$template->pparse("body");

}
include('./page_footer_admin.'.$phpEx);
?>