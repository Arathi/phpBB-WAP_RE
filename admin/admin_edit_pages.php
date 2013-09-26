<?php
/*****************************
 *		admin_edit_pages.php
 *		--------------------
 *		作者：爱疯的云
 *		说明：网页编辑
 *****************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Edit_Page']['Edit_Page_Enter_Select'] = $file;
	return;
}

$phpbb_root_path = './../';
include($phpbb_root_path . 'extension.inc');
include('./pagestart.'.$phpEx);

if ( isset($HTTP_GET_VARS['mode']) && !empty($HTTP_GET_VARS['mode']) )
{
	$mode = $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
}

if ( $mode == 'openedit' )
{
	if ( isset($HTTP_POST_VARS['file']) )
	{
		$get_file = $HTTP_POST_VARS['file'];
	}
	else
	{
		message_die(GENERAL_ERROR, "请指定路径、文件名！");
	}
	
	$file = $phpbb_root_path . $get_file;
	
	if ( !is_file($file) )
	{
		message_die(GENERAL_ERROR, "文件名和路径不存在或不正确！");
	}

	$page_body_text = file_get_contents($file);
	
	$page_body_text = htmlentities($page_body_text, ENT_QUOTES, 'UTF-8');
	
	$template->set_filenames(array(
		'body' => 'admin/admin_page_edit.tpl')
	);
	$hidden_fields = '<input type="hidden" name="file" value="' . $get_file . '" />';
	$template->assign_vars(array(
		'PAGE_BODY_TEXT'		=> $page_body_text,
		'U_SELECT_PAGES_ADMIN'	=> append_sid("admin_edit_pages.$phpEx"),
		'S_HIDDEN_FORM_FIELDS' 	=> $hidden_fields,
		'S_ACTION' 				=> append_sid("admin_edit_pages.$phpEx?mode=edited"))
	);
	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);
}
else if ( $mode == 'edited' )
{
	if ( isset($HTTP_POST_VARS['file']) )
	{
		$get_file = $HTTP_POST_VARS['file'];
	}
	else
	{
		message_die(GENERAL_ERROR, "请指定路径、文件名！");
	}
	
	$file = $phpbb_root_path . $get_file;
	
	if ( !is_file($file) )
	{
		message_die(GENERAL_ERROR, "文件名和路径不存在或不正确！");
	}
	
	$page_body_text = $HTTP_POST_VARS['text'];
	
	$page_body_text = str_replace('\"','"',$page_body_text);
	
	$fopen = fopen($file,"w");
    fwrite($fopen,$page_body_text);
    fclose($fopen);
	
	$template->set_filenames(array(
		'body' => 'admin/admin_page_edit.tpl')
	);

	$hidden_fields = '<input type="hidden" name="file" value="' . $get_file . '" />';
	$template->assign_vars(array(
		'PAGE_BODY_TEXT'		=> $page_body_text,
		'U_SELECT_PAGES_ADMIN'	=> append_sid("admin_edit_pages.$phpEx"),
		'S_HIDDEN_FORM_FIELDS' 	=> $hidden_fields,
		'S_ACTION' 				=> append_sid("admin_edit_pages.$phpEx?mode=edited"))
	);
	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);
}
else
{
	$template->set_filenames(array(
		'body' => 'admin/admin_page_select.tpl')
	);

	$template->assign_vars(array(
		'S_ACTION' => append_sid("admin_edit_pages.$phpEx?mode=openedit"))
	);
	
	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);
}


?>