<?php
/***************************************************
 *		links/index.php
 *		---------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		描述：友情链接主文件
 ***************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_link_exchange.' . $phpEx);

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start = (($start1 - 1) * $board_config['posts_per_page']);
} 
else 
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$userdata = session_pagestart($user_ip, PAGE_LINKS);
init_userprefs($userdata);

if ( isset($HTTP_GET_VARS['id']) )
{
	$link_id = intval($HTTP_GET_VARS['id']);
}

if ( isset($link_id) )
{	
	if ( empty($link_id) )
	{
		redirect(append_sid("links/index.$phpEx"));
	}
	else
	{
		if ( isset($HTTP_GET_VARS['out']) )
		{
			$sql = "SELECT * FROM " . LINK_EXCHANGE_TABLE . "
				WHERE link_id = $link_id";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(CRITICAL_ERROR, "无法查询友链信息！", "", __LINE__, __FILE__, $sql);
			}	
			$link_data = $db->sql_fetchrow($result);
			$link_url = $link_data['link_url'];

			if ( preg_match('/http:\/\//', $link_url) || preg_match('/https:\/\//', $link_url) ) 
			{
				$link_url = $link_url;
			}	
			else 
			{
				$link_url = 'http://' . $link_url;
			}
			
			$link_out_count = $link_data['link_out'];
			$link_out_count = $link_out_count + '1';

			$sql = "UPDATE " . LINK_EXCHANGE_TABLE . "
				SET link_out = $link_out_count
				WHERE link_id = $link_id";	
				
			if( !$result = $db->sql_query($sql) )
			{	
				message_die(CRITICAL_ERROR, "无法更新链出信息", "", __LINE__, __FILE__, $sql);
			}		

			$template->set_filenames(array( 
				  'body' => 'links/redirect.tpl')
			); 
			$template->assign_vars(array( 
				'LINK_URL' => $link_url,
				'LINK_MESSAGE' => sprintf($lang['Link_out_message'], $link_url))
				);
			$template->pparse('body'); 
			exit;
		}
		else
		{
			$sql = "SELECT * FROM " . LINK_EXCHANGE_TABLE . "
				WHERE link_id = $link_id";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(CRITICAL_ERROR, "无法查询友链信息！", "", __LINE__, __FILE__, $sql);
			}	
			$link_data = $db->sql_fetchrow($result);
			
			$link_in_count = $link_data['link_in'];
			$link_in_count = $link_in_count + '1';

			$sql = "UPDATE " . LINK_EXCHANGE_TABLE . "
				SET link_in = $link_in_count
				WHERE link_id = $link_id";	
				
			if( !$result = $db->sql_query($sql) )
			{	
				message_die(CRITICAL_ERROR, "无法更新链入信息", "", __LINE__, __FILE__, $sql);
			}
			
			$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
			$server_name = trim($board_config['server_name']);
			$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
			$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
			$server_url = $server_protocol . $server_name . $server_port . $script_name;
			
			$link_in_url = $server_url;
			
			$template->set_filenames(array( 
				  'body' => 'links/redirect.tpl')
			); 
			$template->assign_vars(array( 
				'LINK_URL' => $link_in_url,
				'LINK_MESSAGE' => sprintf($lang['Link_out_message'], $link_url))
				);
			$template->pparse('body'); 
			exit;		
		}
	}
}
else
{
	
	$sort_by = ( isset($HTTP_POST_VARS['sort_by']) ) ? intval($HTTP_POST_VARS['sort_by']) : 0;
	
	$order_sql = " ORDER BY ";
	switch ( $sort_by )
	{
		case 1:
			$order_sql .= 'link_out';
			break;
		case 2:
			$order_sql .= 'link_website';
			break;
		case 3:
			$order_sql .= 'link_id';
			break;
		default:
			$order_sql .= 'link_in';
			break;
	}
	
	if ( isset($HTTP_POST_VARS['sort_dir']) )
	{
		$order_sql .= ( $HTTP_POST_VARS['sort_dir'] == 'DESC' ) ? ' DESC ' : ' ASC ';
	}
	else
	{
		$order_sql .=  ' DESC ';
	}

	$template->set_filenames(array( 
		"body" => "links/links_body.tpl") 
	); 

	$sql = "SELECT * FROM " . LINK_EXCHANGE_TABLE . "
		WHERE link_active != '0'
		$order_sql
		LIMIT $start, " . $board_config['topics_per_page'];
	if( !$result = $db->sql_query($sql) )
	{
		message_die(CRITICAL_ERROR, "无法查询友链信息！", "", __LINE__, __FILE__, $sql);
	}

	$link_count = $db->sql_numrows($result);
	$link_data = $db->sql_fetchrowset($result); 

	for ($i = 0; $i < $link_count; $i++) 
	{ 
		$link_url[$i] = $phpbb_root_path . 'links/index.' . $phpEx;
		$link_id[$i] = $link_data[$i]['link_id'];
		$link_url[$i] = $link_url[$i] . '?out&amp;id=' . $link_id[$i];

		$template->assign_block_vars("link_exch", array(    
			"LINKNAME" => $link_data[$i]['link_website'], 
			"LINKDESC" => $link_data[$i]['link_desc'], 
			"LINKIMG" => $link_data[$i]['link_img'],
			"LINKURL" => $link_url[$i],
			"LINKOUT" => $link_data[$i]['link_out'],
			"LINKIN" => $link_data[$i]['link_in'])
		);
		$link_img[$i] = $link_data[$i]['link_img'];
		if( (0 != strpos($link_img[$i], '.swf')) || (0 != strpos($link_img[$i], '.SWF')) )	
		{ 
			$template->assign_block_vars('link_exch.switch_flash', array(
			"LINKDESC" => $link_data[$i]['link_desc'], 
			"LINKIMG" => $link_data[$i]['link_img'])
			); 
		}	
		else 
		{ 
			$template->assign_block_vars('link_exch.switch_not_flash', array(
				"LINKDESC" => $link_data[$i]['link_desc'], 
				"LINKIMG" => $link_data[$i]['link_img'])
			); 
		}			
	}	
		
	$sql = "SELECT count(*) AS total 
	   FROM " . LINK_EXCHANGE_TABLE . " 
	   WHERE link_active != '0'"; 
	if ( !($result = $db->sql_query($sql)) ) 
	{ 
		message_die(GENERAL_ERROR, 'Error getting total links', '', __LINE__, __FILE__, $sql); 
	} 
	if ( $total = $db->sql_fetchrow($result) ) 
	{ 
		$total_links = $total['total'];          
	} 

	$page_title = $lang['Link_exchange'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->assign_vars(array(
		"U_LINK_EXCHANGE_SUBMIT"  	=> append_sid('submit.'.$phpEx),
		"TOTAL_LINKS"				=> $total_links,
		"PAGINATION" 				=> generate_pagination("{$phpbb_root_path}links/index.$phpEx?order=$sort_order", $total_links, $board_config['topics_per_page'], $start),
		"PAGE_NUMBER" 				=> sprintf($lang['Page_of'], ( floor( $start / $board_config['topics_per_page'] ) + 1 ), ceil( $total_links / $board_config['topics_per_page'] )))	
	);

	$template->pparse("body");
	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}
?>
