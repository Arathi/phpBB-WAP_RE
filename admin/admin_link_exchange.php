<?php
/***************************************************
 *		admin_link_exchange.php
 *		--------------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：友链后台模块
 ***************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['General']['友链管理'] = "$file";
	return;
}

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_link_exchange.' . $phpEx);
	
if( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
{
	$mode = (isset($HTTP_GET_VARS['mode'])) ? $HTTP_GET_VARS['mode'] : $HTTP_POST_VARS['mode'];
}
else 
{
	if( isset($HTTP_POST_VARS['add']) )
	{
		$mode = "add";
	}
	else if( isset($HTTP_POST_VARS['save']) )
	{
		$mode = "save";
	}
	else if( isset($HTTP_POST_VARS['change']) )
	{
		$mode = "change";
	}	
	else
	{
		$mode = "";
	}
}


if( $mode!= "")
{
	if( $mode == "edit" || $mode == "add" )
	{
		if( isset($HTTP_POST_VARS['id']) || isset($HTTP_GET_VARS['id']) )
		{
			$link_exchange_id = ( isset($HTTP_POST_VARS['id']) ) ? intval($HTTP_POST_VARS['id']) : intval($HTTP_GET_VARS['id']);
		}
		else
		{
			$link_exchange_id = 0;
		}
		
		$s_hidden_fields = "";
		
		if( $mode == "edit" )
		{
			if( empty($link_exchange_id) )
			{
				message_die(GENERAL_MESSAGE, $lang['Missing_link_exchange_id']);
			}

			$sql = "SELECT * FROM " . LINK_EXCHANGE_TABLE . "
				WHERE link_id = $link_exchange_id";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Couldn't obtain link exchange data", "", __LINE__, __FILE__, $sql);
			}
			
			$link_info = $db->sql_fetchrow($result);
			$s_hidden_fields .= '<input type="hidden" name="id" value="' . $link_exchange_id . '" />';
		}
		else
		{
			$link_info['link_active'] = 1;
		}

		$s_hidden_fields .= '<input type="hidden" name="mode" value="save" />';
		$link_exchange_is_active = ( $link_info['link_active'] ) ? "checked=\"checked\"" : "";
		$link_exchange_is_not_active = ( !$link_info['link_active'] ) ? "checked=\"checked\"" : "";
		
		$template->set_filenames(array(
			'body' => 'admin/link_exchange_edit_body.tpl')
		);
		
		$link_img = $link_info['link_img'];
		if( (0 != strpos($link_img, '.swf')) || (0 != strpos($link_img, '.SWF')) )	
		{ 
		$template->assign_block_vars('switch_flash', array(
			'U_LINK_EXCHANGE_IMG' => ( ereg("http",strtolower($link_info['link_img']))) ? $link_info['link_img'] : '../'.$link_info['link_img'],		
		)); 
		}	
		else 
		{ 
		$template->assign_block_vars('switch_not_flash', array(
			'U_LINK_EXCHANGE_IMG' => ( ereg("http",strtolower($link_info['link_img']))) ? $link_info['link_img'] : '../'.$link_info['link_img'],		
		)); 
		}			

		$template->assign_vars(array(
			'LINK_EXCHANGE_NOT_ACTIVE' => $link_exchange_is_not_active,
			'LINK_EXCHANGE_ACTIVE' => $link_exchange_is_active,
			'LINK_EXCHANGE_NAME' => $link_info['link_name'],			
			'LINK_EXCHANGE_EMAIL' => $link_info['link_email'],
			'LINK_EXCHANGE_WEBSITE' => $link_info['link_website'],
			'LINK_EXCHANGE_IMG' => $link_info['link_img'],			
			'LINK_EXCHANGE_DESCRIPTION' => $link_info['link_desc'],	
			'LINK_EXCHANGE_URL' => $link_info['link_url'],
			'LINK_EXCHANGE_OUT' => $link_info['link_out'],	
			'LINK_EXCHANGE_IN' => $link_info['link_in'],	
			'L_YES' => $lang['Yes'],
			'L_NO' => $lang['No'],		
			'S_LINK_EXCHANGE_ACTION' => append_sid("admin_link_exchange.$phpEx"),
			'S_HIDDEN_FIELDS' => $s_hidden_fields)
		);
	}
	else if( $mode == "save" )
	{
		$link_exchange_id = ( isset($HTTP_POST_VARS['id']) ) ? intval($HTTP_POST_VARS['id']) : 0;
		$link_exchange_name = ( isset($HTTP_POST_VARS['link_exchange_name']) ) ? trim($HTTP_POST_VARS['link_exchange_name']) : "";
		$link_exchange_email = ( isset($HTTP_POST_VARS['link_exchange_email']) ) ? trim($HTTP_POST_VARS['link_exchange_email']) : "";	
		$link_exchange_website = ( isset($HTTP_POST_VARS['link_exchange_website']) ) ? trim($HTTP_POST_VARS['link_exchange_website']) : "";
		$link_exchange_image = ( isset($HTTP_POST_VARS['link_exchange_image']) ) ? trim($HTTP_POST_VARS['link_exchange_image']) : "";					
		$link_exchange_description = ( isset($HTTP_POST_VARS['link_exchange_description']) ) ? trim($HTTP_POST_VARS['link_exchange_description']) : "";
		$link_exchange_url = ( isset($HTTP_POST_VARS['link_exchange_url']) ) ? trim($HTTP_POST_VARS['link_exchange_url']) : "";
		$link_exchange_active = ( $HTTP_POST_VARS['link_exchange_active'] == 1 ) ? TRUE : 0;

		if( $link_exchange_name == "" )
		{
			message_die(GENERAL_MESSAGE, $lang['Missing_link_exchange_name']);
		}
		if( $link_exchange_id )
		{
			$sql = "UPDATE " . LINK_EXCHANGE_TABLE . " SET 	link_active = $link_exchange_active, link_name = '" . str_replace("\'", "''", $link_exchange_name) . "', link_email = '" . str_replace("\'", "''", $link_exchange_email) . "', link_website = '" . str_replace("\'", "''", $link_exchange_website) . "', link_img = '" . str_replace("\'", "''", $link_exchange_image) . "', link_desc = '" . str_replace("\'", "''", $link_exchange_description) . "', link_url = '" . str_replace("\'", "''", $link_exchange_url) . "' 
					WHERE link_id = $link_exchange_id";
					$message = $lang['Link_exchange_updated'];
		}
		else
		{
				$sql = "SELECT MAX(link_id)+1 as link_id FROM " . LINK_EXCHANGE_TABLE; 
				if(!$result = $db->sql_query($sql)) 
				{ 
				message_die(GENERAL_ERROR, "Couldn't obtain link exchange id data", "", __LINE__, __FILE__, $sql); 
				} 
				$link_exchange_nr = $db->sql_fetchrow($result); 
				$sql = "INSERT INTO " . LINK_EXCHANGE_TABLE . " (link_id, link_name, link_email, link_website, link_img, link_desc, link_url, link_active)
					VALUES (".$link_exchange_nr[link_id].", '" . str_replace("\'", "''", $link_exchange_name) . "', '" . str_replace("\'", "''", $link_exchange_email) . "', '" . str_replace("\'", "''", $link_exchange_website) . "', '" . str_replace("\'", "''", $link_exchange_image) . "', '" . str_replace("\'", "''", $link_exchange_description) . "', '" . str_replace("\'", "''", $link_exchange_url) . "', '$link_exchange_active')";
					$message = $lang['Link_exchange_added'];
		}
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't update/insert into link exchange table", "", __LINE__, __FILE__, $sql);
		}
		$message .= "<br /><br />" . sprintf($lang['Click_return_linkexchangeadmin'], "<a href=\"" . append_sid("admin_link_exchange.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
		message_die(GENERAL_MESSAGE, $message);
	}
	else if( $mode == "delete" )
	{
		
		if( isset($HTTP_POST_VARS['id']) || isset($HTTP_GET_VARS['id']) )
		{
			$link_exchange_id = ( isset($HTTP_POST_VARS['id']) ) ? intval($HTTP_POST_VARS['id']) : intval($HTTP_GET_VARS['id']);
		}
		else
		{
			$link_exchange_id = '';
		}
		
		if( $link_exchange_id )
		{
			$sql = "DELETE FROM " . LINK_EXCHANGE_TABLE . "
				WHERE link_id = $link_exchange_id";
			
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't delete link exchange data", "", __LINE__, __FILE__, $sql);
			}
			$message = $lang['Link_exchange_removed'] . "<br /><br />" . sprintf($lang['Click_return_linkexchangeadmin'], "<a href=\"" . append_sid("admin_link_exchange.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['Missing_link_exchange_id']);
		}
	}


}
else
{
	$link_per_page = 5;
	// 初始化分页
	if ( isset($HTTP_POST_VARS['start1']) )
	{
		$start1 = abs(intval($HTTP_POST_VARS['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $link_per_page);
	}
	else
	{
		$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
		$start = ($start < 0) ? 0 : $start;
	}
	
	//选择模版
	$template->set_filenames(array(
		"body" => "admin/link_exchange_list_body.tpl")
	);
	
	// 统计所有链接
	$sql = "SELECT link_id FROM " . LINK_EXCHANGE_TABLE ." 
		WHERE link_id != 0";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Couldn't obtain link exchange data", "", __LINE__, __FILE__, $sql);
	}
	$total_links = $db->sql_numrows($result);

	// 得出链接日志
	$sql = "SELECT * FROM " . LINK_EXCHANGE_TABLE ." 
		WHERE link_id != 0 
		ORDER BY link_id DESC 
		LIMIT $start, $link_per_page";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Couldn't obtain link exchange data", "", __LINE__, __FILE__, $sql);
	}
	$link_exchange_count = $db->sql_numrows($result);
	$link_exchange_rows = $db->sql_fetchrowset($result);
	
	// 循环
	for($i = 0; $i < $link_exchange_count; $i++)
	{
		$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
		$link_exchange_website = $link_exchange_rows[$i]['link_website'];
		$link_exchange_id = $link_exchange_rows[$i]['link_id'];
		$link_exchange_is_active = ( $link_exchange_rows[$i]['link_active'] ) ? $lang['Yes'] : $lang['No'];
		$link_exchange_clicks_out = $link_exchange_rows[$i]['link_out'];
		$link_exchange_clicks_in = $link_exchange_rows[$i]['link_in'];
		$link_exchange_desc = $link_exchange_rows[$i]['link_desc'];
		$link_exchange_link_url = $link_exchange_rows[$i]['link_url'];
		
		if ( preg_match('/http:\/\//', $link_exchange_link_url) )// || preg_match('/https:\/\//', $link_exchange_link_url) 
		{
			$link_exchange_link_url = $link_exchange_link_url;
		}	
		else 
		{
			$link_exchange_link_url = 'http://' . $link_exchange_link_url;
		}
		
		$template->assign_block_vars("links", array(
			'ROW_CLASS' => $row_class,
			'LINK_EXCHANGE_DESCRIPTION' => $link_exchange_desc,
			'LINK_EXCHANGE_IS_ACTIVE' => $link_exchange_is_active,
			'LINK_EXCHANGE_CLICKS_OUT' => $link_exchange_clicks_out,
			'LINK_EXCHANGE_CLICKS_IN' => $link_exchange_clicks_in,
			'LINK_EXCHANGE_WEBSITE' => $link_exchange_website,
			'LINK_EXCHANGE_ID' => $link_exchange_id,
			'LINK_EXCHANGE_LINK_URL' => $link_exchange_link_url,
			'U_LINK_EXCHANGE_EDIT' => append_sid("admin_link_exchange.$phpEx?mode=edit&amp;id=$link_exchange_id"),
			'U_LINK_EXCHANGE_DELETE' => append_sid("admin_link_exchange.$phpEx?mode=delete&amp;id=$link_exchange_id"))
		);
	}
	$template->assign_vars(array(
		"PAGINATION" 				=> generate_pagination("{$phpbb_root_path}admin/admin_link_exchange.$phpEx?per", $total_links, $link_per_page, $start),
		"PAGE_NUMBER" 				=> sprintf($lang['Page_of'], ( floor( $start / $link_per_page ) + 1 ), ceil( $total_links / $link_per_page)))
	);
}

$template->pparse("body");
include('./page_footer_admin.'.$phpEx);
?>