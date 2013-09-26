<?php
/**************************************************
 *		admin_template.php  
 *		------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2012 год
 *		简体中文：爱疯的云
 *************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Edit_Page']['Edit_Page_Direct_Select'] = $file;
	return;
}

$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

if ( isset($HTTP_POST_VARS['choose']) )
{
	if ( is_dir($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/') )
	{
		$template->set_filenames(array(
			'files' => 'admin/admin_template_select.tpl')
		);
				
		$templates = '';
		
		// 列出该目录的所有文件夹以及文件
		$dir = @opendir($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/');
		while( $file = @readdir($dir) )
		{
			if( $file != '.' && $file != '..' && $file != 'images' && !is_file($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $file) && !is_link($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $file) )
			{
				$sub_dir = @opendir($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $file);

				while( $sub_file = @readdir($sub_dir) )
				{
					if( $sub_file != '.' && $sub_file != '..' && is_file($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $file . '/' . $sub_file) && !is_link($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $file . '/' . $sub_file) )
					{
						$templates .=  '<option value="' . $file . '/' . $sub_file . '">' . $file . '/' . $sub_file . '</option>';
					}
				}
			}
		}
		$file = '';
		@closedir($dir);
		
		$dir = dir($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/');
		while ( $tpl = $dir->read() )
		{
			if ( is_file($phpbb_root_path . '/' . $HTTP_POST_VARS['template'] . '/' . $tpl) )
			{
				$templates .=  '<option value="' . $tpl . '">' . $tpl . '</option>';
			}
		}
		@closedir($dir);
	
	  $template->assign_vars(array(
			'S_ACTION' 			=> append_sid('admin_template.' . $phpEx),
			'SUBMIT_NAME' 		=> 'file',
			'FILE_NAME' 		=> $HTTP_POST_VARS['template'] . '/',
			'HIDDEN_THINGS' 	=> '<input type="hidden" name="directory" value="' . $HTTP_POST_VARS['template'] . '" />',
			'L_CHOOSE_TEMPLATE'	=> $lang['Template_Edit_Choose'],
			'L_SUBMIT' 			=> $lang['Submit'],
			
			'S_TEMPLATES' 		=> $templates
		));
	
		$template->pparse('files');
		include('./page_footer_admin.'.$phpEx);
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Template_Edit_No_Template']);
	}
}
else if ( isset($HTTP_POST_VARS['file']) )
{
	if ( is_file($phpbb_root_path . '/' . $HTTP_POST_VARS['directory'] . '/' . $HTTP_POST_VARS['template']) )
	{
		$file_data = @implode('', @file($phpbb_root_path . '/' . $HTTP_POST_VARS['directory'] . '/' . $HTTP_POST_VARS['template']));
		if ( !empty($file_data) )
		{
			$template->set_filenames(array(
				'edit_file' => 'admin/admin_template_edit.tpl')
			);
			$template->assign_vars(array(
				'S_ACTION' 			=> append_sid('admin_template.' . $phpEx),
				'HIDDEN_THINGS' 	=> '<input type="hidden" name="directory" value="' . $HTTP_POST_VARS['directory'] . '" /><input type="hidden" name="file_name" value="' . $HTTP_POST_VARS['template'] . '" />',
				'SUBMIT_NAME' 		=> 'edit',
				'FILE_NAME' 		=> $HTTP_POST_VARS['directory'] . '/' . $HTTP_POST_VARS['template'],
				'FILE_DATA' 		=> htmlspecialchars(trim($file_data)),
				'L_EDIT_TEMPLATE' 	=> $lang['Template_Edit'],
				'L_SUBMIT' 			=> $lang['Submit'],
				'L_RESET' 			=> $lang['Reset']
			));
			$template->pparse('edit_file');
			include('./page_footer_admin.'.$phpEx);
		}
		else
		{
			message_die(GENERAL_ERROR, $lang['Template_Edit_No_Open']);
		}
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Template_Edit_No_Files']);
	}
}
else if ( isset($HTTP_POST_VARS['edit']) )
{
	if ( is_file($phpbb_root_path . '/' . $HTTP_POST_VARS['directory'] . '/' . $HTTP_POST_VARS['file_name']) )
	{
		$fp = fopen($phpbb_root_path . '/' . $HTTP_POST_VARS['directory'] . '/' . $HTTP_POST_VARS['file_name'], 'w');
		if ( $fp )
		{
      $file_data = stripslashes(trim($HTTP_POST_VARS['file_data']));
      $file_data = str_replace ("\r", "", $file_data);

      fwrite($fp, $file_data, strlen($file_data));
			fclose($fp);
			$message = $lang['Template_Edit_Yes_Write'] . "<br /><br />" . sprintf($lang['Click_return_template_edit'], "<a href=\"" . append_sid("admin_template.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

      message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			message_die(GENERAL_ERROR, $lang['Template_Edit_No_Write']);
		}
	}
}
		
$template->set_filenames(array(
	'template' => 'admin/admin_template_select.tpl')
);
$themes = '';
$dir = dir($phpbb_root_path . '/');
while ( $tpl = $dir->read() )
{
	if ( !strstr($tpl, '.') )
	{
		$themes .= '<option value="' . $tpl . '">' . $tpl . '</option>';
	}
}
$template->assign_vars(array(
	'S_ACTION' 			=> append_sid('admin_template.' . $phpEx),
	'SUBMIT_NAME' 		=> 'choose',
	'L_CHOOSE_TEMPLATE'	=> $lang['Template_Edit_Choose'],
	'L_SUBMIT' 			=> $lang['Submit'],
	
	'S_TEMPLATES' 		=> $themes
));
$dir->close();
$template->pparse('template');
include('./page_footer_admin.'.$phpEx);
?>
