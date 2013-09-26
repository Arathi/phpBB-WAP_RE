<?php
/***************************************
 *		edit_pages.php
 *		---------------
 *		作者：爱疯的云
 *		说明：index.php和page.php排
 *			版使用
 ***************************************/

define('IN_PHPBB', true);

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

// 初始化变量
if( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
{
	$mode = ( isset($HTTP_GET_VARS['mode']) ) ? $HTTP_GET_VARS['mode'] : $HTTP_POST_VARS['mode'];
	$mode = htmlspecialchars($mode);
}
else 
{
	$mode = '';
}

$confirm = ( isset($HTTP_POST_VARS['confirm']) ) ? TRUE : FALSE;

switch( $mode )
{
	case 'index':
		
		if( !$confirm )
		{
			$sql = "SELECT index_content FROM " . INDEX_PAGE_TABLE;
			
			// 执行查询
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain index page data for pages', '', __LINE__, __FILE__, $sql);
			}
			
			// 循环出结果
			if ( !($index_data = $db->sql_fetchrow($result)) )
			{
				message_die(GENERAL_MESSAGE, '无法得到网页的内容！');
			}
			
			$hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="confirm" value="yes" />';
			
			$template->set_filenames(array(
				"body" => "admin/edit_pages.tpl")
			);
			
			$template->assign_vars(array(
				'L_SUBMIT' 					=> $lang['Submit'],
				'U_EDIT_PAGE'				=> append_sid("edit_page.$phpEx"),
				'PAGES_BODY' 				=> $index_data['index_content'],
				'S_HIDDEN_FIELDS'			=> $hidden_fields,
				'S_EDITPAGES_ACTION' 		=> append_sid("edit_page.$phpEx?mode=index"))
			);
			
			$template->pparse("body");
			include('./page_footer_admin.'.$phpEx);
		}
		else
		{
			if ( empty($HTTP_POST_VARS['pages_body']))
			{
				$message = '你总得加点内容吧?<br /><br />点击 <a href="' . append_sid("edit_page.$phpEx?mode=index") . '">这里</a> 返回首页排版！';
				message_die(GENERAL_ERROR, $message);
			}
			
			$index_page_body = htmlspecialchars(trim($HTTP_POST_VARS['pages_body']));
			
			$sql = "UPDATE " . INDEX_PAGE_TABLE . " SET index_content = '" . str_replace("\'", "''", $index_page_body) . "'";
			
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not update style information", "", __LINE__, __FILE__, $sql);
			}
			
			$message = '首页已成功排版！<br /><br />' . sprintf('点击 %s这里%s 返回首页排版！', '<a href="' . append_sid("edit_page.$phpEx?mode=index") . '">', '</a>') . '<br /><br />' . sprintf('点击 %s这里%s 返回首页预览效果！', '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
	break;
	
	case 'pages':
	
		$pages_id = ( isset($HTTP_GET_VARS['id']) ) ? intval($HTTP_GET_VARS['id']) : '';
		
		if ( empty($pages_id) )
		{
			message_die(GENERAL_ERROR,'您没有指定要排版的页面！');
		}
		
		if( !$confirm )
		{
			$sql = "SELECT * 
				FROM " . PAGES_TABLE . " 
				WHERE page_id = " . $pages_id;
		
			// 执行查询
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain pages data for pages', '', __LINE__, __FILE__, $sql);
			}
			
			// 循环出结果
			if ( !($pages_data = $db->sql_fetchrow($result)) )
			{
				message_die(GENERAL_MESSAGE, '无法得到网页的内容！');
			}
			
			$hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="confirm" value="yes" />';
			
			$template->set_filenames(array(
				"body" => "admin/edit_pages.tpl")
			);
			
			$template->assign_vars(array(
				'L_SUBMIT' 					=> $lang['Submit'],
				'U_EDIT_PAGE'				=> append_sid("edit_page.$phpEx"),
				'PAGES_ID'					=> $pages_data['page_id'],
				'PAGES_TITLE' 				=> $pages_data['page_title'],
				'PAGES_BODY' 				=> $pages_data['page_contents'],
				'S_HIDDEN_FIELDS'			=> $hidden_fields,
				'S_EDITPAGES_ACTION' 		=> append_sid("edit_page.$phpEx?mode=pages&amp;id=$pages_id"))
			);
			
			$template->assign_block_vars('switch_pages_page', array() );
			
			$template->pparse("body");
			include('./page_footer_admin.'.$phpEx);
		}
		else
		{
			if ( empty($HTTP_POST_VARS['pages_title']) || empty($HTTP_POST_VARS['pages_body']) )
			{
				$message = '网页排版内容和标题不能为空！<br /><br />点击 <a href="' . append_sid("edit_page.$phpEx?mode=pages&amp;id=$pages_id") . '">这里</a> 返回自定义页面排版！';
				message_die(GENERAL_ERROR, $message);
			}
			
			$page_pages_title	= htmlspecialchars(trim($HTTP_POST_VARS['pages_title']));
			$page_pages_body	= htmlspecialchars(trim($HTTP_POST_VARS['pages_body']));
			
			$sql = "UPDATE " . PAGES_TABLE . " 
				SET page_title = '" . str_replace("\'", "''", $page_pages_title) . "', page_contents = '" . str_replace("\'", "''", $page_pages_body) . "' 
				WHERE page_id = " . $pages_id;
			
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Could not update style information", "", __LINE__, __FILE__, $sql);
			}
			
			$message = '已成功更改自定义网页排版！<br /><br />' . sprintf('点击 %s这里%s 返回上一页面！', '<a href="' . append_sid("edit_page.$phpEx?mode=pages&amp;id=$pages_id") . '">', '</a>') . '<br /><br />' . sprintf('点击 %s这里%s 返回该自定义页面！', '<a href="' . append_sid("{$phpbb_root_path}page.$phpEx?page=$pages_id") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
			
		}
		
	break;

	case 'select':
		
		$sql = "SELECT page_id, page_title FROM " . PAGES_TABLE;
		
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain pages data for pages', '', __LINE__, __FILE__, $sql);
		}
	
		$total_pages_data = $db->sql_numrows($result);
		
		$pages_row = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$pages_row[] = $row;
		}
		
		for ($i = 0; $i < $total_pages_data; $i++)
		{
			$template->assign_block_vars('pages_row', array(
				'PAGE_TITLE' 		=> $pages_row[$i]['page_title'],
				'PAGE_ID' 			=> $pages_row[$i]['page_id'],
				'U_SELECT' 			=> append_sid("edit_page.$phpEx?mode=pages&amp;id=" . $pages_row[$i]['page_id']),
				)
			);
		}
						
		$template->set_filenames(array(
			'body' => 'admin/select_page.tpl')
		);
		
		$template->assign_vars(array('U_EDIT_PAGE' => append_sid("edit_page.$phpEx")));
		
		$template->pparse("body");
		include('./page_footer_admin.'.$phpEx);
		
	break;
	
	case 'add':
	
		if( !$confirm )
		{
			$hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="confirm" value="yes" />';
			
			$template->set_filenames(array(
				"body" => "admin/edit_pages.tpl")
			);
			
			$template->assign_vars(array(
				'L_SUBMIT' 					=> $lang['Submit'],
				'U_EDIT_PAGE'				=> append_sid("edit_page.$phpEx"),
				'PAGES_TITLE' 				=> '',
				'PAGES_BODY' 				=> '',
				'S_HIDDEN_FIELDS'			=> $hidden_fields,
				'S_EDITPAGES_ACTION' 		=> append_sid("edit_page.$phpEx?mode=add"))
			);
			
			$template->assign_block_vars('switch_pages_page', array() );
			
			$template->pparse("body");
			include('./page_footer_admin.'.$phpEx);
		}
		else
		{
			if ( empty($HTTP_POST_VARS['pages_title']) || empty($HTTP_POST_VARS['pages_body']) )
			{
				$message = '网页排版内容和标题不能为空！<br /><br />点击 <a href="' . append_sid("edit_page.$phpEx?mode=add") . '">这里</a> 返回自定义页面排版！';
				message_die(GENERAL_ERROR, $message);
			}
			
			$page_pages_title	= htmlspecialchars(trim($HTTP_POST_VARS['pages_title']));
			$page_pages_body	= htmlspecialchars(trim($HTTP_POST_VARS['pages_body']));
			
			$sql = "INSERT INTO " . PAGES_TABLE . " (page_title, page_contents) 
				VALUES ('" . str_replace("\'", "''", $page_pages_title) . "', page_contents = '" . str_replace("\'", "''", $page_pages_body) . "')";
	
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '无法执行SQL查询', '', __LINE__, __FILE__, $sql);
			}
			
			$message = '自定义页面 “' . $page_pages_title . '” 已成功添加！<br /><br />点击 <a href="' . append_sid("edit_page.$phpEx") . '">这里</a> 返回风格管理页面<br /><br />' . sprintf('点击 %s这里%s 返回超级面板首页！', '<a href="' . append_sid("index.$phpEx") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
	
	break;
	
	default:
	
		$template->set_filenames(array(
			"body" => "admin/edit_pages_body.tpl")
		);
		
		$template->assign_vars(array(
			'ADD_PAGE_PAGES' 			=> append_sid("edit_page.$phpEx?mode=add"),
			'EDIT_PAGE_INDEX' 			=> append_sid("edit_page.$phpEx?mode=index"),
			'EDIT_PAGE_PAGES' 			=> append_sid("edit_page.$phpEx?mode=select"))
		);

		$template->pparse("body");
		include('./page_footer_admin.'.$phpEx);
		
}

?>