<?php
/*************************************************
 *		functions.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 **************************************************/

 /**
 * 用于统计数据
 **/
function get_db_stat($mode)
{
	global $db;
	// switch() 语句，详情请见 PHP 手册
	switch( $mode )
	{
		case 'usercount':
			$sql = "SELECT COUNT(user_id) AS total
				FROM " . USERS_TABLE . "
				WHERE user_id <> " . ANONYMOUS;
			break;

		case 'newestuser':
			$sql = "SELECT user_id, username
				FROM " . USERS_TABLE . "
				WHERE user_id <> " . ANONYMOUS . "
				ORDER BY user_id DESC
				LIMIT 1";
			break;

		case 'postcount':
		case 'topiccount':
			$sql = "SELECT SUM(forum_topics) AS topic_total, SUM(forum_posts) AS post_total
				FROM " . FORUMS_TABLE;
			break;
		case 'attachcount':
			$sql = "SELECT count(*) AS total FROM " . ATTACHMENTS_DESC_TABLE;
			break;
	}
	// $db->sql_query($sql) 是类与对象, 在db.php中创建的
	if ( !($result = $db->sql_query($sql)) )
	{
		return false;// return 表示执行，false = 真，true = 假
	}

	$row = $db->sql_fetchrow($result);

	switch ( $mode )
	{
		case 'usercount':
			return $row['total'];
			break;
		case 'newestuser':
			return $row;
			break;
		case 'postcount':
			return $row['post_total'];
			break;
		case 'topiccount':
			return $row['topic_total'];
			break;
		case 'attachcount':
			return $row['total'];
			break;
	}

	return false;
}

/**
* 该函数用户注册、游客发帖输入的用户名是否合法
* 不能包含 ' 会导致 SQL 错误的字符
**/
function phpbb_clean_username($username)
{
	// substr() 函数返回字符串的一部分,目前为25个字符串
	// htmlspecialchars()将特殊字符转成 HTML 格式
	$username = substr(htmlspecialchars(str_replace("\'", "'", trim($username))), 0, 25);
	$username = phpbb_rtrim($username, "\\");
	$username = str_replace("'", "\'", $username);// 安全起见，将 ' 替换成 \' 

	return $username;
}

/**
* 检测E-mail是否合法
**/
function phpbb_clean_email($email)
{
   $email = substr(htmlspecialchars(str_replace("\'", "'", trim($email))), 0, 255);
   $email = phpbb_rtrim($email, "\\");
   $email = str_replace("'", "\'", $email);

   return $email;
} 

/**
* 检测用户ID是否合法
**/
function phpbb_clean_uid($email)
{
   $uid = substr(htmlspecialchars(str_replace("\'", "'", trim($uid))), 0, 12);
   $uid = phpbb_rtrim($uid, "\\");
   $uid = str_replace("'", "\'", $uid);
   return $uid;
} 

/**
* 处理文件的文件名，不能包含非法字符
* 例如头像、附件的文件名
* =：赋值，在逻辑运算时也有效；
* ==：等于运算，但是不比较值的类型；
* ===：完全等于运算，不仅比较值，而且还比较值的类型，只有两者一致才为真。
**/
function phpbb_ltrim($str, $charlist = false)
{
	if ($charlist === false)
	{
		return ltrim($str);// ltrim() 函数从字符串左侧删除空格或其他预定义字符。
	}

	$php_version = explode('.', PHP_VERSION);// 把 PHP 的版本号码分割为数组，分割为 . 号

	if ((int) $php_version[0] < 4 || ((int) $php_version[0] == 4 && (int) $php_version[1] < 1))
	{
		while ($str{0} == $charlist)
		{
			$str = substr($str, 1);
		}
	}
	else
	{
		$str = ltrim($str, $charlist);
	}

	return $str;
}

/**
* 使用方法同于 phpbb_ltrim() 函数
* 在 phpBB-WAP 中用于处理文件夹和用户名是否非法
* 这个函数亦应用于 phpbb_clean_username() 函数
**/
function phpbb_rtrim($str, $charlist = false)
{
	if ($charlist === false)
	{
		return rtrim($str);
	}
	
	$php_version = explode('.', PHP_VERSION);

	if ((int) $php_version[0] < 4 || ((int) $php_version[0] == 4 && (int) $php_version[1] < 1))
	{
		while ($str{strlen($str)-1} == $charlist)
		{
			$str = substr($str, 0, strlen($str)-1);
		}
	}
	else
	{
		$str = rtrim($str, $charlist);
	}

	return $str;
}

/**
* 得出随机值
* 用于 session 、 BBCode
**/ 
function dss_rand()
{
	global $db, $board_config, $dss_seeded;

	$val = $board_config['rand_seed'] . microtime();// microtime() 函数返回当前 Unix 时间戳和微秒数。
	$val = md5($val);// MD5加密
	$board_config['rand_seed'] = md5($board_config['rand_seed'] . $val . 'a');
   
	if($dss_seeded !== true)
	{
		$sql = "UPDATE " . CONFIG_TABLE . " SET
			config_value = '" . $board_config['rand_seed'] . "'
			WHERE config_name = 'rand_seed'";
		
		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Unable to reseed PRNG", "", __LINE__, __FILE__, $sql);
		}

		$dss_seeded = true;
	}

	return substr($val, 4, 16);
}

/**
* 取得userdata
**/
function get_userdata($user,$postuser = false, $force_str = false)
{
global $db;
if ($postuser != NULL)
{
$sql = "SELECT *
FROM " . USERS_TABLE . " 
WHERE username='".$postuser."';";
if ( !($result = $db->sql_query($sql)) )
{
message_die(GENERAL_ERROR, 'Tried obtaining data for a non-existent user', '', __LINE__, __FILE__, $sql);
}

return ( $row = $db->sql_fetchrow($result) ) ? $row : false;
}else
{
if (!is_numeric($user) || $force_str)
{
$user = phpbb_clean_username($user);
}
else
{
$user = intval($user);
}

$sql = "SELECT *
FROM " . USERS_TABLE . " 
WHERE ";
$sql .= ( ( is_integer($user) ) ? "user_id = $user" : "username = '" .  str_replace("\'", "''", $user) . "'" ) . " AND user_id <> " . ANONYMOUS;
if ( !($result = $db->sql_query($sql)) )
{
message_die(GENERAL_ERROR, 'Tried obtaining data for a non-existent user', '', __LINE__, __FILE__, $sql);
}

return ( $row = $db->sql_fetchrow($result) ) ? $row : false;
}
}

/**
* 初始化 uaerdata
**/
function init_userprefs($userdata)
{
	global $board_config;
	global $template, $lang, $phpEx, $phpbb_root_path, $db;

	if ( $userdata['user_id'] != ANONYMOUS )
	{
		// empty() 检查变量是否为空
		if ( !empty($userdata['user_lang']))// 这里表示不为空
		{
			// basename() 函数返回路径中的文件名部分
			$default_lang = phpbb_ltrim(basename(phpbb_rtrim($userdata['user_lang'])), "'");
		}

		if ( !empty($userdata['user_dateformat']) )
		{
			$board_config['default_dateformat'] = $userdata['user_dateformat'];
		}
		// isset() 检测变量是否设置
		if ( isset($userdata['user_timezone']) )
		{
			$board_config['board_timezone'] = $userdata['user_timezone'];
		}
		if ( isset($userdata['user_topics_per_page']) )
		{
			$board_config['topics_per_page'] = $userdata['user_topics_per_page'];
		}
		if ( isset($userdata['user_posts_per_page']) )
		{
			$board_config['posts_per_page'] = $userdata['user_posts_per_page'];
		}
	}
	else
	{
		$default_lang = phpbb_ltrim(basename(phpbb_rtrim($board_config['default_lang'])), "'");
	}
	
	// file_exists() 函数检查文件或目录是否存在，realpath() 函数返回绝对路径
	if ( !file_exists(@phpbb_realpath($phpbb_root_path . 'language/lang_' . $default_lang . '/lang_main.'.$phpEx)) )
	{
		if ( $userdata['user_id'] != ANONYMOUS )
		{
			$default_lang = phpbb_ltrim(basename(phpbb_rtrim($board_config['default_lang'])), "'");
		}
		else
		{

			$default_lang = 'chinese';
		}

		if ( !file_exists(@phpbb_realpath($phpbb_root_path . 'language/lang_' . $default_lang . '/lang_main.'.$phpEx)) )
		{
			message_die(CRITICAL_ERROR, 'Could not locate valid language pack');
		}
	}

	if ( $userdata['user_id'] != ANONYMOUS && $userdata['user_lang'] !== $default_lang )
	{
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_lang = '" . $default_lang . "'
			WHERE user_lang = '" . $userdata['user_lang'] . "'";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(CRITICAL_ERROR, 'Could not update user language info');
		}

		$userdata['user_lang'] = $default_lang;
	}
	elseif ( $userdata['user_id'] == ANONYMOUS && $board_config['default_lang'] !== $default_lang )
	{
		$sql = 'UPDATE ' . CONFIG_TABLE . "
			SET config_value = '" . $default_lang . "'
			WHERE config_name = 'default_lang'";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(CRITICAL_ERROR, 'Could not update user language info');
		}
	}

	$board_config['default_lang'] = $default_lang;

	include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_main.' . $phpEx);
	// 留言板
	include($phpbb_root_path . "language/lang_".$board_config['default_lang'] . "/lang_guestbook.php");
	
	// defined() 函数检查某常量是否存在
	if ( defined('IN_ADMIN') )
	{
		if( !file_exists(@phpbb_realpath($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_admin.'.$phpEx)) )
		{
			$board_config['default_lang'] = 'chinese';
		}
		// include() 包含文件
		include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_admin.' . $phpEx);
	}
	// phpBB-WAP内建函数，用于包含附件的语言包
	include_attach_lang();

	//
	// 设置风格
	//
		// 非游客
	if ( $userdata['user_id'] != ANONYMOUS && $userdata['user_style'] > 0 )
	{
		// setup_style() phpBB-WAP内建函数，用于启动风格
		if ( $theme = setup_style($userdata['user_style']) )
		{
			return;
		}
	}
	
	$theme = setup_style($board_config['default_style']);
	
	$lang['View_latest_post'] = ( $userdata['user_view_latest_post'] && ($userdata['user_id'] != ANONYMOUS) ) ? '&gt;&gt;&gt;' : $lang['View_latest_post'];

	return;
}

/**
* 启动风格
**/
function setup_style($style)
{
	global $db, $board_config, $template, $images, $phpbb_root_path, $userdata;
	
	$sql = "SELECT * 
		FROM " . STYLES_TABLE . " 
		WHERE style_id = " . $style;
	
	if ( $result = $db->sql_query($sql) )
	{
		if ( !$row = $db->sql_fetchrow($result) )
		{
			message_die(CRITICAL_ERROR, 'Could not query database for user style id');
		}
	}
	else
	{
		$sql = "SELECT * 
			FROM " . STYLES_TABLE . " 
			WHERE style_id = " . $style;
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(CRITICAL_ERROR, 'Could not query database for user style id');
		}
		if ( !$row = $db->sql_fetchrow($result) )
		{
			message_die(CRITICAL_ERROR, 'Could not query database for user style id');
		}
	}
	
	$template_path = 'styles/' ;
	//$template_name = 'prosilver';
	$template_name = $row['style_path'];
	// 创建新对象
	$template = new Template($phpbb_root_path . $template_path . $template_name);

	if ( $template )
	{
		$current_template_path = $template_path . $template_name;
		// @ 用于屏蔽错误输出
		@include($phpbb_root_path . $template_path . $template_name . '/' . $template_name . '.cfg');

		if ( !defined('TEMPLATE_CONFIG') )
		{
			message_die(CRITICAL_ERROR, "无法打开模版的cfg配置文件！", '', __LINE__, __FILE__);
		}
		
		$img_lang = ( file_exists(@phpbb_realpath($phpbb_root_path . $current_template_path . '/images/lang_' . $board_config['default_lang'])) ) ? $board_config['default_lang'] : 'english';

		while( list($key, $value) = @each($images) )
		{
			if ( !is_array($value) )
			{
				$images[$key] = str_replace('{LANG}', 'lang_' . $img_lang, $value);
			}
		}
	}

	return $row;
}

/**
* 校正 IP 地址
**/
function encode_ip($dotquad_ip)
{
	// preg_match() 正则表达式匹配，匹配 IP
	if( !preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $dotquad_ip) )
	{
		message_die(CRITICAL_ERROR, 'IP地址错误！');
	}
	$ip_sep = explode('.', $dotquad_ip);
	// sprintf() 函数把格式化的字符串写写入一个变量中，在语言包中可以经常见到
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

/**
* 格式化 IP
**/
function decode_ip($int_ip)
{
	// chunk_split() 目前为没两个字符添加一个 . 
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	// hexdec() 十六进制转换为十进制
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

/**
* 创建日期
**/
function create_date($format, $gmepoch, $tz)
{
	global $board_config, $lang;
	static $translate;

	if ( empty($translate) && $board_config['default_lang'] = 'chinese' )
	{
		// reset() 函数把数组的内部指针指向第一个元素，并返回这个元素的值
		@reset($lang['datetime']);
		while ( list($match, $replace) = @each($lang['datetime']) )// 遍历数组
		{
			$translate[$match] = $replace;
		}
	}
	// strtr() 函数转换字符串中特定的字符, gmdate() 函数格式化 GMT/UTC 日期/时间
	return ( !empty($translate) ) ? strtr(@gmdate($format, $gmepoch + (3600 * $tz)), $translate) : @gmdate($format, $gmepoch + (3600 * $tz));
}

/**
* 创建分页处理
**/
function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = TRUE)
{
	global $lang;
	// ceil() 函数向上舍入为最接近的整数
	$total_pages = ceil($num_items/$per_page);

	if ( $total_pages == 1 )
	{
		return '';
	}
	// floor() 函数向下舍入为最接近的整数
	$on_page = floor($start_item / $per_page) + 1;

	$page_string = '';
	if ( $on_page == 1 )
	{
		// append_sid() 为phpBB-WAP内建函数，用于创建 SID 用
		$page_string = '<br/>' . $lang['Previous'] . ' | <a href="' . append_sid($base_url . "&amp;start=" . ( $on_page * $per_page ) ) . '">' . $lang['Next'] . '</a><br/>';
	}
	if ( $on_page == $total_pages )
	{
		$page_string = '<br/><a href="' . append_sid($base_url . "&amp;start=" . ( ( $on_page - 2 ) * $per_page ) ) . '">' . $lang['Previous'] . '</a> | ' . $lang['Next'] . '<br/>';
	}
	if ( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

		for($i = 1; $i < $init_page_max + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
			if ( $i <  $init_page_max )
			{
				$page_string .= ",";
			}
		}

		if ( $total_pages > 3 )
		{
			if ( $on_page > 1  && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? '...' : ',';

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
				{
					$page_string .= ($i == $on_page) ? '<b>' . $i . '</b>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
					if ( $i <  $init_page_max + 1 )
					{
						$page_string .= ',';
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? '...' : ',';
			}
			else
			{
				$page_string .= '...';
			}

			for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
			{
				$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>'  : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
				if( $i <  $total_pages )
				{
					$page_string .= ",";
				}
			}
		}
	}
	else
	{
		for($i = 1; $i < $total_pages + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . append_sid($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
			if ( $i <  $total_pages )
			{
				$page_string .= ',';
			}
		}
	}

	if ( $add_prevnext_text )
	{
		if ( $on_page > 1  && $on_page < $total_pages )
		{
			$page_string = '<br/><a href="' . append_sid($base_url . "&amp;start=" . ( ( $on_page - 2 ) * $per_page ) ) . '">' . $lang['Previous'] . '</a> | <a href="' . append_sid($base_url . "&amp;start=" . ( $on_page * $per_page ) ) . '">' . $lang['Next'] . '</a><br/>' . $page_string;
		}

		if ( $on_page < $total_pages )
		{
			$page_string .= '';
		}
	}

	if ( $total_pages > 6 )
	{	
		$select_list = '<form action="' . append_sid($base_url) . '" method="post">跳转到第 <input type="text" name="start1" size="3" value="' . $on_page . '"/> 页<input type="submit" value="&gt;&gt;"/></form>';
	}
	else
	{
		$select_list = '';
	}

	$page_string = $page_string . $select_list;

	return $page_string;
}

/**
* 用来干嘛的？？
**/
function phpbb_preg_quote($str, $delimiter)
{
	$text = preg_quote($str);// 匹配正则表达式
	$text = str_replace($delimiter, '\\' . $delimiter, $text);// str_replace() 函数使用一个字符串替换字符串中的另一些字符
	
	return $text;
}

/**
* 取得 word_list
**/
function obtain_word_list(&$orig_word, &$replacement_word)
{
	global $db;

	$sql = "SELECT word, replacement
		FROM  " . WORDS_TABLE;
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get censored words from database', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		do 
		{
			
			$orig_word[] = $row['word'];
			$replacement_word[] = $row['replacement'];
		}
		while ( $row = $db->sql_fetchrow($result) );
	}

	return true;
}

// 函数输出一条消息，并退出当前脚本
function message_die($msg_code, $msg_text = '', $msg_title = '', $err_line = '', $err_file = '', $sql = '')
{
	global $db, $template, $board_config, $lang, $phpEx, $phpbb_root_path, $gen_simple_header, $opera_mini, $tmp_name;
	global $userdata, $user_ip, $session_length;
	global $starttime;

	$sql_store = $sql;
	// DEBUG 、GENERAL_ERROR 、CRITICAL_ERROR 是一些常量，请参见 constants.php
	if ( DEBUG && ( $msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR ) )
	{
		$sql_error = $db->sql_error();

		$debug_text = '';

		if ( $sql_error['message'] != '' )
		{
			$debug_text .= '<br /><br />SQL Error : ' . $sql_error['code'] . ' ' . $sql_error['message'];
		}

		if ( $sql_store != '' )
		{
			$debug_text .= "<br /><br />$sql_store";
		}

		if ( $err_line != '' && $err_file != '' )
		{
			$debug_text .= '<br /><br />Line : ' . $err_line . '<br />File : ' . basename($err_file);
		}
	}

	if( empty($userdata) && ( $msg_code == GENERAL_MESSAGE || $msg_code == GENERAL_ERROR ) )
	{
		// session_pagestart() 是phpBB-WAP内建函数
		$userdata = session_pagestart($user_ip, PAGE_INDEX);
		init_userprefs($userdata);// phpBB-WAP内建函数
	}

	if ( !defined('HEADER_INC') && $msg_code != CRITICAL_ERROR )
	{
		if ( empty($lang) )
		{
			if ( !empty($board_config['default_lang']) )
			{
				include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_main.'.$phpEx);
			}
			else
			{
				include($phpbb_root_path . 'language/lang_chinese/lang_main.'.$phpEx);
			}
		}

		$page_title = '系统提示';

		if ( !defined('IN_ADMIN') )
		{
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		}
		else
		{
			include($phpbb_root_path . 'admin/page_header_admin.'.$phpEx);
		}
	}

	switch($msg_code)
	{
		case GENERAL_MESSAGE:
			if ( $msg_title == '' )
			{
				$msg_title = $lang['Information'];
			}
			break;

		case CRITICAL_MESSAGE:
			if ( $msg_title == '' )
			{
				$msg_title = $lang['Critical_Information'];
			}
			break;

		case GENERAL_ERROR:
			if ( $msg_text == '' )
			{
				$msg_text = $lang['An_error_occured'];
			}

			if ( $msg_title == '' )
			{
				$msg_title = $lang['General_Error'];
			}
			break;

		case CRITICAL_ERROR:

			include($phpbb_root_path . 'language/lang_chinese/lang_main.'.$phpEx);

			if ( $msg_text == '' )
			{
				$msg_text = $lang['A_critical_error'];
			}

			if ( $msg_title == '' )
			{
				$msg_title = 'phpBB-WAP : <b>' . $lang['Critical_Error'] . '</b>';
			}
			break;
	}

	if ( DEBUG && ( $msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR ) )
	{
		if ( $debug_text != '' )
		{
			$msg_text = $msg_text . '<br /><br /><b><u>DEBUG MODE</u></b>' . $debug_text;
		}
	}

	if ( $msg_code != CRITICAL_ERROR )
	{
		if ( !empty($lang[$msg_text]) )
		{
			$msg_text = $lang[$msg_text];
		}

		if ( !defined('IN_ADMIN') )
		{
			$template->set_filenames(array(
				'message_body' => 'message_body.tpl')
			);
		}
		else
		{
			$template->set_filenames(array(
				'message_body' => 'admin/admin_message_body.tpl')
			);
		}

		$template->assign_vars(array(
			'MESSAGE_TITLE' => $msg_title,
			'MESSAGE_TEXT' => $msg_text)
		);
		$template->pparse('message_body');

		if ( !defined('IN_ADMIN') )
		{
			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
		else
		{
			include($phpbb_root_path . 'admin/page_footer_admin.'.$phpEx);
		}
	}
	else
	{
		echo "<html>\n<body>\n" . $msg_title . "\n<br /><br />\n" . $msg_text . "</body>\n</html>";
	}
	exit;
}

/**
* 返回绝对路径
**/
function phpbb_realpath($path)
{
	global $phpbb_root_path, $phpEx;
	// function_exists() 用来检查指定的函数是否已经定义
	return (!@function_exists('realpath') || !@realpath($phpbb_root_path . 'includes/functions.'.$phpEx)) ? $path : @realpath($path);
}

/**
* URL 重定向
**/
function redirect($url)
{
	global $db, $board_config;

	if (!empty($db))
	{
		$db->sql_close();
	}
	// urldecode() 将 URL 编码后字符串还原成未编码的样子。
	if (strstr(urldecode($url), "\n") || strstr(urldecode($url), "\r") || strstr(urldecode($url), ';url'))
	{
		message_die(GENERAL_ERROR, 'Tried to redirect to potentially insecure url.');
	}

	$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://';
	$server_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['server_name']));
	$server_port = ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
	$script_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['script_path']));
	$script_name = ($script_name == '') ? $script_name : '/' . $script_name;
	$url = preg_replace('#^\/?(.*?)\/?$#', '/\1', trim($url));
	// getenv() 用来取得参数name环境变量的内容
	if (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')))
	{
		// header() 函数向客户端发送原始的 HTTP 报头
		header('Refresh: 0; URL=' . $server_protocol . $server_name . $server_port . $script_name . $url);
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0; url=' . $server_protocol . $server_name . $server_port . $script_name . $url . '"><title>Redirect</title></head><body><div align="center">If your browser does not support meta redirection please click <a href="' . $server_protocol . $server_name . $server_port . $script_name . $url . '">HERE</a> to be redirected</div></body></html>';
		exit;
	}

	header('Location: ' . $server_protocol . $server_name . $server_port . $script_name . $url);
	exit;
}

/**
* 数据库查询
**/
function db_query($sql)
{
	global $db;
	// 正则表达式匹配
	$sql = preg_replace_callback('#{(\w+)}#', 'const_subst', $sql); 

	// func_num_args() 函数返回的是当前函数的参数数量，返回的是数字
	if (func_num_args() > 1)
	{
		// 返回的是一个数组,这个数组内的每一项都是函数的一个参数
		$args = func_get_args();
		// array_map() 函数返回用户自定义函数作用后的数组
		$args = array_map('addslashes', $args);
		$args[0] = $sql;
		//call_user_func_array() 函数返回一个用户函数与特定的参数数组
		$sql = call_user_func_array('sprintf', $args);
	}

	if ($result = $db->sql_query($sql))
	{
		return $result;
	}
	else
	{
		message_die(GENERAL_ERROR, 'SQL query failed', '', __LINE__, __FILE__, $sql);
	}
}

/**
* 常量匹配
**/
function const_subst($match)
{
	// constant() 函数返回常量的值
	return constant($match[1]);
}

/**
* 数据和解
**/
function db_transaction($command)
{
	global $db;

	$db->sql_query('SELECT 0', $command);
}


/**
* 判断用户年龄是否正确
**/
function mkrealdate($day,$month,$birth_year)
{
	if ($month<1 || $month>12) return "error";
	switch ($month)
	{
		case 1: if ($day>31) return "error";break;
		case 2: if ($day>29) return "error";
			$epoch=$epoch+31;break;
		case 3: if ($day>31) return "error";
			$epoch=$epoch+59;break;
		case 4: if ($day>30) return "error" ;
			$epoch=$epoch+90;break;
		case 5: if ($day>31) return "error";
			$epoch=$epoch+120;break;
		case 6: if ($day>30) return "error";
			$epoch=$epoch+151;break;
		case 7: if ($day>31) return "error";
			$epoch=$epoch+181;break;
		case 8: if ($day>31) return "error";
			$epoch=$epoch+212;break;
		case 9: if ($day>30) return "error";
			$epoch=$epoch+243;break;
		case 10: if ($day>31) return "error";
			$epoch=$epoch+273;break;
		case 11: if ($day>30) return "error";
			$epoch=$epoch+304;break;
		case 12: if ($day>31) return "error";
			$epoch=$epoch+334;break;
	}
	$epoch=$epoch+$day;
	$epoch_Y=sqrt(($birth_year-1970)*($birth_year-1970));// sqrt() 计算平方根
	$leapyear=round((($epoch_Y+2) / 4)-.5);// round() 函数对浮点数进行四舍五入
	if (($epoch_Y+2)%4==0)
	{
		$leapyear--;
		if ($birth_year >1970 && $month>=3) $epoch=$epoch+1;
		if ($birth_year <1970 && $month<3) $epoch=$epoch-1;
	} else if ($month==2 && $day>28) return "error";
	if ($birth_year>1970)
		$epoch=$epoch+$epoch_Y*365-1+$leapyear;
	else
		$epoch=$epoch-$epoch_Y*365-1-$leapyear;
	return $epoch;
}

/**
* 取得实际时间
**/
function realdate($date_syntax="Ymd",$date=0)
{
	global $lang;
	$i=2;
	if ($date>=0)
	{
		// phpBB-WAP内建函数
	 	return create_date($date_syntax,$date*86400+1,0);
	} else
	{
		$year= -(date%1461);
		$days = $date + $year*1461;
		while ($days<0)
		{
			$year--;
			$days+=365;
			if ($i++==3)
			{
				$i=0;
				$days++;
			}
		}
	}
	$leap_year = ($i==0) ? TRUE : FALSE;
	$months_array = ($i==0) ?
		array (0,31,60,91,121,152,182,213,244,274,305,335,366) :
		array (0,31,59,90,120,151,181,212,243,273,304,334,365);
	for ($month=1;$month<12;$month++)
	{
		if ($days<$months_array[$month]) break; // break 结束当前 if，for，foreach，while，do-while 或者 switch 结构的执行
	}

	$day=$days-$months_array[$month-1]+1;
	return strtr ($date_syntax, array(
		'a' => '',
		'A' => '',
		'\\d' => 'd',
		'd' => ($day>9) ? $day : '0'.$day,
		'\\D' => 'D',
		'D' => $lang['day_short'][($date-3)%7],
		'\\F' => 'F',
		'F' => $lang['month_long'][$month-1],
		'g' => '',
		'G' => '',
		'H' => '',
		'h' => '',
		'i' => '',
		'I' => '',
		'\\j' => 'j',
		'j' => $day,
		'\\l' => 'l',
		'l' => $lang['day_long'][($date-3)%7],
		'\\L' => 'L',
		'L' => $leap_year,
		'\\m' => 'm',
		'm' => ($month>9) ? $month : '0'.$month,
		'\\M' => 'M',
		'M' => $lang['month_short'][$month-1],
		'\\n' => 'n',
		'n' => $month,
		'O' => '',
		's' => '',
		'S' => '',
		'\\t' => 't',
		't' => $months_array[$month]-$months_array[$month-1],
		'w' => '',
		'\\y' => 'y',
		'y' => ($year>29) ? $year-30 : $year+70,
		'\\Y' => 'Y',
		'Y' => $year+1970,
		'\\z' => 'z',
		'z' => $days,
		'\\W' => '',
		'W' => '') );
}

/**
* 检查奖的 MOD
**/
function check_medal_mod($medal_id)
{
	global $db, $userdata;
	
	$sql = "SELECT *
	FROM " . MEDAL_MOD_TABLE . "  
	WHERE medal_id =" . $medal_id;
	
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain user and medal information', '', __LINE__, __FILE__, $sql);
	}

	$medal_info = array();
	$found = FALSE;
	while ( $medal_info = $db->sql_fetchrow($result) )
	{

		$medal_moderator = $medal_info['user_id'];

		if ( $medal_moderator == $userdata['user_id'] )
		{
			$found = TRUE;
		}
	}
	$db->sql_freeresult($result);
	
	return $found;
}
//使用道具 类型可以设置为stick highlight qianglou 三种
function use_daoju($type,$user_id){
		global $db;
	if($type=="stick"){
		$sql='UPDATE '.USERS_TABLE.' SET user_stick = user_stick - 1 WHERE user_id = '.$user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '扣除道具卡-置顶出错', '', __LINE__, __FILE__, $sql);
			}
		}else if($type=="highlight"){
			$sql='UPDATE '.USERS_TABLE.' SET user_highlight = user_highlight - 1 WHERE user_id = '.$user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '扣除道具卡-高亮出错', '', __LINE__, __FILE__, $sql);
			}
		}else if($type=="qianglou"){
			$sql='UPDATE '.USERS_TABLE.' SET user_qianglou = user_qianglou - 1 WHERE user_id = '.$user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '扣除道具卡-抢楼出错', '', __LINE__, __FILE__, $sql);
			}
		}

}
//购买道具 类型可以设置为stick highlight qianglou 三种 num是购买数量
function buy_daoju($type,$num,$user_id){
 global $db, $board_config;
	if(!preg_match("/^\+?[1-9][0-9]*$/",$num)){
		message_die(GENERAL_ERROR, '输入不合法，请输入数字');
	}
		$sql="UPDATE " . USERS_TABLE . " SET user_".$type." = user_".$type." + 1 WHERE user_id = " . $user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '购买道具卡出错', '', __LINE__, __FILE__, $sql);
			}
	$type.='_price';
    $money=$num*$board_config[$type];
		$sql="UPDATE " . USERS_TABLE . " SET user_points = user_points - ".$money." WHERE user_id = " . $user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '购买道具卡出错', '', __LINE__, __FILE__, $sql);
			}

}
function set_stick($topic_id){
	global $db;

	$sql="UPDATE " . TOPICS_TABLE . " SET topic_type = 1 WHERE topic_id = " . $topic_id; 

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '设置道具出错', '', __LINE__, __FILE__, $sql);
			}

}
function cancel_stick($topic_id){
	global $db;

	$sql="UPDATE " . TOPICS_TABLE . " SET topic_type = 0 WHERE topic_id = " . $topic_id; 

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '设置道具出错', '', __LINE__, __FILE__, $sql);
			}

}
function phpbb_message_at($message)
{

global $db, $userdata;
$message = $message;
$path = $_SERVER['PHP_SELF'];
$path = parse_url($path);
$at_url = $path['path'];
$at_url = basename($at_url);
if ($at_url == 'posting.php')
{
$at_url = 'viewtopic.php?t=';
}
$at_p = $_POST['t'];
preg_match_all("!(@|＠)([\\x{4e00}-\\x{9fa5}A-Za-z0-9_\\-]{1,})(\x20|&nbsp;|<|\xC2\xA0|\r|\n|\x03|\t|,|\\?|\\!|:|;|，|。|？|！|：|；|、|…|$)!ue",$message,$matches);
$atuser = $matches[2];
for ($i = 0;$i < count($atuser);$i++)
{
  
  $sql = "SELECT * FROM phpbb_users WHERE username='".$atuser[$i]."';";
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $to_userdata = $row['user_id'];
  $msg_time = time();
  $sql_info = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip, privmsgs_enable_html, privmsgs_enable_bbcode, privmsgs_enable_smilies, privmsgs_attach_sig) VALUES (" . PRIVMSGS_NEW_MAIL . ",'@消息', " . $userdata['user_id'] . ", " . $row['user_id'] . ", $msg_time, '$user_ip', 0, 0, 0, '@系统消息')";
  $db->sql_query($sql_info);
  $sql = "SELECT * FROM phpbb_privmsgs WHERE privmsgs_date='".$msg_time."';";
  $result = $db->sql_query($sql);
  $msgrow = $db->sql_fetchrow($result);
  $sql = "UPDATE phpbb_users SET user_new_privmsg=user_new_privmsg+1 WHERE user_id='".$row['user_id']."';";
  
  $db->sql_query($sql);
  $privmsg_sent_id = $msgrow['privmsgs_id'];
  $bbcode_uid = make_bbcode_uid();
  
  $at_message = "<a href=\"".$phpbb_root_path.$at_url.$at_p."\">点击查看</a>";
  
  $sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_bbcode_uid, privmsgs_text)
            VALUES ($privmsg_sent_id, '" . $bbcode_uid . "', '" . str_replace("\'", "''", $at_message) . "')";
  $db->sql_query($sql);
}
return $message;
}//at mod

?>