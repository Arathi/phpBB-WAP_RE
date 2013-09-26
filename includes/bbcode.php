<?php
/***************************************************************************
 *                              bbcode.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************************************/

if ( !defined('IN_PHPBB') )// define() 函数定义一个常量
{
	die("Hacking attempt");// die() 函数输出一条消息,并退出当前脚本
}

define("BBCODE_UID_LEN", 10);// 常量

$bbcode_tpl = null;

/**
* 这个函数用于加载 bbcede.tpl 模版
**/
function load_bbcode_template()
{
	global $template;// 全局变量，引用函数外的变量必须声明全局变量
	$tpl_filename = $template->make_filename('bbcode.tpl');// $template->make_filename() 类与对象。。。用于加载模版文件的
	/** 
	* fread() 函数读取文件（可安全用于二进制文件）
	* fopen() 函数用于打开文件
	* filesize() 函数返回指定文件的大小
	**/
	$tpl = fread(fopen($tpl_filename, 'r'), filesize($tpl_filename));
	// 下面几个都是正则表达式匹配函数
	$tpl = str_replace('\\', '\\\\', $tpl);
	$tpl  = str_replace('\'', '\\\'', $tpl);
	$tpl  = str_replace("\n", '', $tpl);
	$tpl = preg_replace('#<!-- BEGIN (.*?) -->(.*?)<!-- END (.*?) -->#', "\n" . '$bbcode_tpls[\'\\1\'] = \'\\2\';', $tpl);

	$bbcode_tpls = array(); //数组

	eval($tpl);// eval() 函数把字符串按照 PHP 代码来计算

	return $bbcode_tpls;// return 是执行的意思，作用与 echo 有点相似
}

/**
* 准备解析 BBCode 模版
**/
function prepare_bbcode_template($bbcode_tpl)
{
	global $lang;

	// <ol> 的Bbcede
	$bbcode_tpl['olist_open'] = str_replace('{LIST_TYPE}', '\\1', $bbcode_tpl['olist_open']);

	// 颜色
	$bbcode_tpl['color_open'] = str_replace('{COLOR}', '\\1', $bbcode_tpl['color_open']);

	// 字体大小
	$bbcode_tpl['size_open'] = str_replace('{SIZE}', '\\1', $bbcode_tpl['size_open']);

	// 引用
	$bbcode_tpl['quote_open'] = str_replace('{L_QUOTE}', $lang['Quote'], $bbcode_tpl['quote_open']);

	// 引用用户名
	$bbcode_tpl['quote_username_open'] = str_replace('{L_QUOTE}', $lang['Quote'], $bbcode_tpl['quote_username_open']);
	$bbcode_tpl['quote_username_open'] = str_replace('{L_WROTE}', $lang['wrote'], $bbcode_tpl['quote_username_open']);
	$bbcode_tpl['quote_username_open'] = str_replace('{USERNAME}', '\\1', $bbcode_tpl['quote_username_open']);

	// Code 代码
	$bbcode_tpl['code_open'] = str_replace('{L_CODE}', $lang['Code'], $bbcode_tpl['code_open']);

	//图片
	$bbcode_tpl['img'] = str_replace('{URL}', '\\1', $bbcode_tpl['img']);

	// URL
	$bbcode_tpl['url1'] = str_replace('{URL}', '\\1', $bbcode_tpl['url']);
	$bbcode_tpl['url1'] = str_replace('{DESCRIPTION}', '\\1', $bbcode_tpl['url1']);

	$bbcode_tpl['url2'] = str_replace('{URL}', 'http://\\1', $bbcode_tpl['url']);
	$bbcode_tpl['url2'] = str_replace('{DESCRIPTION}', '\\1', $bbcode_tpl['url2']);

	$bbcode_tpl['url3'] = str_replace('{URL}', '\\1', $bbcode_tpl['url']);
	$bbcode_tpl['url3'] = str_replace('{DESCRIPTION}', '\\2', $bbcode_tpl['url3']);

	$bbcode_tpl['url4'] = str_replace('{URL}', 'http://\\1', $bbcode_tpl['url']);
	$bbcode_tpl['url4'] = str_replace('{DESCRIPTION}', '\\3', $bbcode_tpl['url4']);

	// 电子邮件
	$bbcode_tpl['email'] = str_replace('{EMAIL}', '\\1', $bbcode_tpl['email']);
	
	// 登录可见
	$bbcode_tpl['login'] = str_replace('{LOGIN}', '\\1', $bbcode_tpl['login']);
	
	// QQ在线状态
	$bbcode_tpl['qq'] = str_replace('{QQ}', '\\1', $bbcode_tpl['qq']);
	$bbcode_tpl['qq_b'] = str_replace('{QQ}', '\\1', $bbcode_tpl['qq_b']);

	
	
	define("BBCODE_TPL_READY", true);

	// 这里得出的结果不是 prepare_bbcode_template($bbcode_tpl) 的结果，而是函数内部的$bbcode_tpl
	return $bbcode_tpl;
}

/**
* 二次解析
**/
function bbencode_second_pass($text, $uid)
{
	global $lang, $bbcode_tpl, $userdata, $HTTP_SERVER_VARS, $phpEx;

	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
	$text = " " . $text;

	// strpos() 函数返回字符串在另一个字符串中第一次出现的位置
	if (! (strpos($text, "[") && strpos($text, "]")) )
	{
		// 删除填充，返回 $text
		$text = substr($text, 1);// substr() 函数返回字符串的一部分
		return $text;
	}

	if (!defined("BBCODE_TPL_READY"))
	{
		$bbcode_tpl = load_bbcode_template();// 内建函数
		$bbcode_tpl = prepare_bbcode_template($bbcode_tpl);// 内建函数
	}

	$text = bbencode_second_pass_code($text, $uid, $bbcode_tpl);// 内建函数
	
	$text = str_replace("[quote:$uid]", $bbcode_tpl['quote_open'], $text);
	$text = str_replace("[/quote:$uid]", $bbcode_tpl['quote_close'], $text);
	$text = preg_replace("/\[quote:$uid=\"(.*?)\"\]/si", $bbcode_tpl['quote_username_open'], $text);
	$text = str_replace("[list:$uid]", $bbcode_tpl['ulist_open'], $text);
	$text = str_replace("[*:$uid]", $bbcode_tpl['listitem'], $text);
	$text = str_replace("[/list:u:$uid]", $bbcode_tpl['ulist_close'], $text);
	$text = str_replace("[/list:o:$uid]", $bbcode_tpl['olist_close'], $text);
	$text = preg_replace("/\[list=([a1]):$uid\]/si", $bbcode_tpl['olist_open'], $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+):$uid\]/si", $bbcode_tpl['color_open'], $text);
	$text = str_replace('<span style="color: transparent">', '<span style="color: white">', $text);
	$text = str_replace("[/color:$uid]", $bbcode_tpl['color_close'], $text);
	$text = preg_replace("/\[size=([1-2]?[0-9]):$uid\]/si", $bbcode_tpl['size_open'], $text);
	$text = str_replace("[/size:$uid]", $bbcode_tpl['size_close'], $text);
	$text = str_replace("[b:$uid]", $bbcode_tpl['b_open'], $text);
	$text = str_replace("[/b:$uid]", $bbcode_tpl['b_close'], $text);
	$text = str_replace("[u:$uid]", $bbcode_tpl['u_open'], $text);
	$text = str_replace("[/u:$uid]", $bbcode_tpl['u_close'], $text);
	$text = str_replace("[i:$uid]", $bbcode_tpl['i_open'], $text);
	$text = str_replace("[/i:$uid]", $bbcode_tpl['i_close'], $text);

		$ampm = date("H");
	if ( $ampm < 6)
	{
		$timer = '凌晨好';
	}
	elseif ( $ampm < 10)
	{
		$timer = '早上好';
	}
	elseif ( $ampm < 12)
	{
		$timer = '上午好';
	}
	elseif ( $ampm == 12)
	{
		$timer = '中午好';
	}
	elseif ( $ampm < 17)
	{
		$timer = '下午好';
	}
	elseif ( $ampm < 19)
	{
		$timer = '傍晚好';
	}
	elseif ( $ampm < 23)
	{
		$timer = '晚上好';
	}
	elseif ( $ampm == 23)
	{
		$timer = '午夜好';
	}
	else
	{
		$timer = '未知时间';
	}
	$timer_hello = $timer;
	$text = str_replace("[hello]", $timer_hello, $text);

	$weeks = date("D");
	if ( $weeks == 'Mon' )
	{
		$week = '一';
	}
	elseif ( $weeks == 'Tue')
	{
		$week = '二';
	}
	elseif ( $weeks == 'Wed')
	{
		$week = '三';
	}
	elseif ( $weeks == 'Thu' )
	{
		$week = '四';
	}
	elseif ( $weeks == 'Fri' )
	{
		$week = '五';
	}
	elseif ( $weeks == 'Sat')
	{
		$week = '六';
	}
	elseif ( $weeks == 'Sun')
	{
		$week = '日';
	}
	else
	{
		$week = '忘记了？';
	}
	$text = str_replace("[week]", $week, $text);
	
	$text = str_replace("[TAB]", "&nbsp;&nbsp;", $text);
	$text = str_replace("[tab]", "&nbsp;", $text);
	$text = str_replace("[br]", "<br />", $text);
	$text = str_replace("///", "<br />", $text);
	$text = str_replace("[hr]", "<hr />", $text);

	$text = str_replace("[now]",date("Y年m月d日H点i分s秒"),$text);
	$text = str_replace("[date]",date($lang['DATE_FORMAT']),$text);// 日期
	$text = str_replace("[time]",date('G:i'),$text);
	$text = str_replace("[day]",date("d"),$text);
	$text = str_replace("[HOUR]",date('G'),$text);// 小时（24小时制）
	$text = str_replace("[hour]",date('g'),$text);// 小时
	$text = str_replace("[minute]",date('i'),$text);//分
	$text = str_replace("[second]",date('s'),$text);// 秒
	$text = str_replace("[username]", $userdata['username'], $text);// 用户名
	$text = str_replace("[userid]", $userdata['user_id'], $text);// 用户ID
	$text = str_replace("[email]", $userdata['user_email'], $text);// 用户E-mail
	$text = str_replace("[posts]", $userdata['user_posts'], $text);// 用户帖子数量
	$text = str_replace("[usertime]", date($userdata['user_dateformat']), $text);// 时间，用户自己设置的时间
	$text = str_replace("[money]", $userdata['user_points'], $text);// 显示用户积分数量
	
	$patterns = array();
	$replacements = array();

	// 图片
	$patterns[] = "#\[img:$uid\]([^?](?:[^\[]+|\[(?!url))*?)\[/img:$uid\]#i";
	$replacements[] = $bbcode_tpl['img'];

	// URL
	$patterns[] = "#\[url\]([\w]+?://[^[:space:]]*?)\[/url\]#is";
	$replacements[] = $bbcode_tpl['url1'];
	$patterns[] = "#\[url\]((www|ftp)\.[^[:space:]]*?)\[/url\]#is";
	$replacements[] = $bbcode_tpl['url2'];
	$patterns[] = "#\[url=([\w]+?://[^[:space:]]*?)\]([^?\n\r\t].*?)\[/url\]#is";
	$replacements[] = $bbcode_tpl['url3'];
	$patterns[] = "#\[url=((www|ftp)\.[^[:space:]]*?)\]([^?\n\r\t].*?)\[/url\]#is";
	$replacements[] = $bbcode_tpl['url4'];

	// E-mail
	$patterns[] = "#\[email\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si";
	$replacements[] = $bbcode_tpl['email'];

	// QQ
	$patterns[] = "#\[qq](\d{5,11})\[/qq\]#";
	$replacements[] = $bbcode_tpl['qq'];
	$patterns[] = "#\[QQ](\d{5,11})\[/QQ\]#";
	$replacements[] = $bbcode_tpl['qq_b'];
	
	// 登录可见
	$replacer = sprintf($lang['Links_Allowed_For_Registered_Only'], '<a href="' . append_sid("{$phpbb_root_path}login.$phpEx?redirect=" . $HTTP_SERVER_VARS['REQUEST_URI']) . '">', $lang['Login'], '</a>');
	if ( !$userdata['session_logged_in'] ) 
	{ 	
		$patterns[] = "#\[login\](.*?)\[/login\]#si";
		$replacements[] = $replacer;
	} 
	else 
	{
		$patterns[] = "#\[login\](.*?)\[/login\]#si";
		$replacements[] = $bbcode_tpl['login']; 
	}
	
	$text = preg_replace($patterns, $replacements, $text);
	$text = substr($text, 1);

	return $text;

}

// mt_srand() 播种 Mersenne Twister 随机数生成器
// microtime() 函数返回当前 Unix 时间戳和微秒数
mt_srand( (double) microtime() * 1000000);

/**
* 得出 BBCode 的 uid
**/
function make_bbcode_uid()
{
	$uid = dss_rand();// 内建函数
	$uid = substr($uid, 0, BBCODE_UID_LEN);

	return $uid;
}

function bbencode_first_pass($text, $uid)
{
	$text = " " . $text;
	$text = bbencode_first_pass_pda($text, $uid, '[code]', '[/code]', '', true, '');
	$text = bbencode_first_pass_pda($text, $uid, '[quote]', '[/quote]', '', false, '');
	$text = bbencode_first_pass_pda($text, $uid, '/\[quote=\\\\&quot;(.*?)\\\\&quot;\]/is', '[/quote]', '', false, '', "[quote:$uid=\\\"\\1\\\"]");

	$open_tag = array();
	$open_tag[0] = "[list]";

	$text = bbencode_first_pass_pda($text, $uid, $open_tag, "[/list]", "[/list:u]", false, 'replace_listitems');

	$open_tag[0] = "[list=1]";
	$open_tag[1] = "[list=a]";

	$text = bbencode_first_pass_pda($text, $uid, $open_tag, "[/list]", "[/list:o]",  false, 'replace_listitems');
	$text = preg_replace("#\[color=(\#[0-9A-F]{6}|[a-z\-]+)\](.*?)\[/color\]#si", "[color=\\1:$uid]\\2[/color:$uid]", $text);
	$text = preg_replace("#\[size=([1-2]?[0-9])\](.*?)\[/size\]#si", "[size=\\1:$uid]\\2[/size:$uid]", $text);
	$text = preg_replace("#\[b\](.*?)\[/b\]#si", "[b:$uid]\\1[/b:$uid]", $text);
	$text = preg_replace("#\[u\](.*?)\[/u\]#si", "[u:$uid]\\1[/u:$uid]", $text);
	$text = preg_replace("#\[i\](.*?)\[/i\]#si", "[i:$uid]\\1[/i:$uid]", $text);
	$text = preg_replace("#\[img\]((http|ftp|https|ftps)://)([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png)))\[/img\]#sie", "'[img:$uid]\\1' . str_replace(' ', '%20', '\\3') . '[/img:$uid]'", $text);

	return substr($text, 1);;

}

function bbencode_first_pass_pda($text, $uid, $open_tag, $close_tag, $close_tag_new, $mark_lowest_level, $func, $open_regexp_replace = false)
{
	$open_tag_count = 0;

	if (!$close_tag_new || ($close_tag_new == ''))
	{
		$close_tag_new = $close_tag;
	}

	$close_tag_length = strlen($close_tag);
	$close_tag_new_length = strlen($close_tag_new);
	$uid_length = strlen($uid);

	$use_function_pointer = ($func && ($func != ''));

	$stack = array();

	// is_array() 函数若变量为数组类型则返回 true，否则返回 false
	if (is_array($open_tag))
	{
		if (0 == count($open_tag))// count() 函数计算数组中的单元数目或对象中的属性个数
		{
			return $text;
		}
		$open_tag_count = count($open_tag);
	}
	else
	{
		$open_tag_temp = $open_tag;
		$open_tag = array();
		$open_tag[0] = $open_tag_temp;
		$open_tag_count = 1;
	}

	$open_is_regexp = false;

	if ($open_regexp_replace)
	{
		$open_is_regexp = true;
		if (!is_array($open_regexp_replace))
		{
			$open_regexp_temp = $open_regexp_replace;
			$open_regexp_replace = array();
			$open_regexp_replace[0] = $open_regexp_temp;
		}
	}

	if ($mark_lowest_level && $open_is_regexp)
	{
		message_die(GENERAL_ERROR, "Unsupported operation for bbcode_first_pass_pda()."); //内建函数
	}

	$curr_pos = 1;
	while ($curr_pos && ($curr_pos < strlen($text)))// strlen() 函数返回字符串的长度
	{
		$curr_pos = strpos($text, "[", $curr_pos);

		if ($curr_pos)
		{
			$found_start = false;
			$which_start_tag = "";
			$start_tag_index = -1;

			for ($i = 0; $i < $open_tag_count; $i++)
			{
				$possible_start = substr($text, $curr_pos, strpos($text, ']', $curr_pos + 1) - $curr_pos + 1);

				if( preg_match('#\[quote=\\\&quot;#si', $possible_start, $match) && !preg_match('#\[quote=\\\&quot;(.*?)\\\&quot;\]#si', $possible_start) )
				{
					if ($close_pos = strpos($text, '&quot;]', $curr_pos + 14))
					{
						if (strpos(substr($text, $curr_pos + 14, $close_pos - ($curr_pos + 14)), '[quote') === false)
						{
							$possible_start = substr($text, $curr_pos, $close_pos - $curr_pos + 7);
						}
					}
				}

				if ($open_is_regexp)
				{
					$match_result = array();
					if (preg_match($open_tag[$i], $possible_start, $match_result))
					{
						$found_start = true;
						$which_start_tag = $match_result[0];
						$start_tag_index = $i;
						break;
					}
				}
				else
				{
					if (0 == strcasecmp($open_tag[$i], $possible_start))
					{
						$found_start = true;
						$which_start_tag = $open_tag[$i];
						$start_tag_index = $i;
						break;
					}
				}
			}

			if ($found_start)
			{
				$match = array("pos" => $curr_pos, "tag" => $which_start_tag, "index" => $start_tag_index);
				// array_push() 函数向第一个参数的数组尾部添加一个或多个元素（入栈），然后返回新数组的长度
				array_push($stack, $match);
				$curr_pos += strlen($possible_start);
			}
			else
			{
				$possible_end = substr($text, $curr_pos, $close_tag_length);
				if (0 == strcasecmp($close_tag, $possible_end))// strcasecmp() 函数比较两个字符串
				{
					if (sizeof($stack) > 0)// sizeof() 函数计算数组中的单元数目或对象中的属性个数
					{
						$curr_nesting_depth = sizeof($stack);
						$match = array_pop($stack);// array_pop() 函数删除数组中的最后一个元素
						$start_index = $match['pos'];
						$start_tag = $match['tag'];
						$start_length = strlen($start_tag);
						$start_tag_index = $match['index'];

						if ($open_is_regexp)
						{
							$start_tag = preg_replace($open_tag[$start_tag_index], $open_regexp_replace[$start_tag_index], $start_tag);
						}

						$before_start_tag = substr($text, 0, $start_index);
						$between_tags = substr($text, $start_index + $start_length, $curr_pos - $start_index - $start_length);

						if ($use_function_pointer)
						{
							$between_tags = $func($between_tags, $uid);
						}

						$after_end_tag = substr($text, $curr_pos + $close_tag_length);

						if ($mark_lowest_level && ($curr_nesting_depth == 1))
						{
							if ($open_tag[0] == '[code]')
							{
								$code_entities_match = array('#<#', '#>#', '#"#', '#:#', '#\[#', '#\]#', '#\(#', '#\)#', '#\{#', '#\}#');
								$code_entities_replace = array('&lt;', '&gt;', '&quot;', '&#58;', '&#91;', '&#93;', '&#40;', '&#41;', '&#123;', '&#125;');
								$between_tags = preg_replace($code_entities_match, $code_entities_replace, $between_tags);
							}
							$text = $before_start_tag . substr($start_tag, 0, $start_length - 1) . ":$curr_nesting_depth:$uid]";
							$text .= $between_tags . substr($close_tag_new, 0, $close_tag_new_length - 1) . ":$curr_nesting_depth:$uid]";
						}
						else
						{
							if ($open_tag[0] == '[code]')
							{
								$text = $before_start_tag . '&#91;code&#93;';
								$text .= $between_tags . '&#91;/code&#93;';
							}
							else
							{
								if ($open_is_regexp)
								{
									$text = $before_start_tag . $start_tag;
								}
								else
								{
									$text = $before_start_tag . substr($start_tag, 0, $start_length - 1) . ":$uid]";
								}
								$text .= $between_tags . substr($close_tag_new, 0, $close_tag_new_length - 1) . ":$uid]";
							}
						}

						$text .= $after_end_tag;

						if (sizeof($stack) > 0)
						{
							$match = array_pop($stack);
							$curr_pos = $match['pos'];
						}
						else
						{
							$curr_pos = 1;
						}
					}
					else
					{
						++$curr_pos;
					}
				}
				else
				{
					++$curr_pos;
				}
			}
		}
	}

	return $text;

}

function bbencode_second_pass_code($text, $uid, $bbcode_tpl)
{
	global $lang;

	$code_start_html = $bbcode_tpl['code_open'];
	$code_end_html =  $bbcode_tpl['code_close'];
	$match_count = preg_match_all("#\[code:1:$uid\](.*?)\[/code:1:$uid\]#si", $text, $matches);

	for ($i = 0; $i < $match_count; $i++)
	{
		$before_replace = $matches[1][$i];
		$after_replace = $matches[1][$i];
		$after_replace = str_replace("  ", "&nbsp; ", $after_replace);
		$after_replace = str_replace("  ", " &nbsp;", $after_replace);
		$after_replace = str_replace("\t", "&nbsp; &nbsp;", $after_replace);
		$after_replace = preg_replace("/^ {1}/m", '&nbsp;', $after_replace);

		$str_to_match = "[code:1:$uid]" . $before_replace . "[/code:1:$uid]";

		$replacement = $code_start_html;
		$replacement .= $after_replace;
		$replacement .= $code_end_html;

		$text = str_replace($str_to_match, $replacement, $text);
	}

	$text = str_replace("[code:$uid]", $code_start_html, $text);
	$text = str_replace("[/code:$uid]", $code_end_html, $text);

	return $text;

}

/**
* 生成链接的 BBCode
**/
function make_clickable($text)
{
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);

	$ret = ' ' . $text;
	$ret = preg_replace("#(^|[\n ])([\w]+?://[^[:space:]]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^[:space:]]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
	$ret = substr($ret, 1);

	return($ret);
}

/**
* 还原链接
**/
function undo_make_clickable($text)
{
	$text = preg_replace("#<!-- BBCode auto-link start --><a href=\"(.*?)\" target=\"_blank\">.*?</a><!-- BBCode auto-link end -->#i", "\\1", $text);
	$text = preg_replace("#<!-- BBcode auto-mailto start --><a href=\"mailto:(.*?)\">.*?</a><!-- BBCode auto-mailto end -->#i", "\\1", $text);

	return $text;

}

/**
* 将 HTML 实体还原成 HTML 字符串
**/
function undo_htmlspecialchars($input)
{
	$input = preg_replace("/&gt;/i", ">", $input);
	$input = preg_replace("/&lt;/i", "<", $input);
	$input = preg_replace("/&quot;/i", "\"", $input);
	$input = preg_replace("/&amp;/i", "&", $input);

	return $input;
}

/**
* 取代列表项目
**/
function replace_listitems($text, $uid)
{
	$text = str_replace("[*]", "[*:$uid]", $text);

	return $text;
}

/**
* 去除斜杠
**/
function escape_slashes($input)
{
	$output = str_replace('/', '\/', $input);
	return $output;
}

/**
* 推送数组
**/
function bbcode_array_push(&$stack, $value)
{
   $stack[] = $value;
   return(sizeof($stack));
}


function bbcode_array_pop(&$stack)
{
	$arrSize = count($stack);
	$x = 1;

	while(list($key, $val) = each($stack))// each() 函数生成一个由数组当前内部指针所指向的元素的键名和键值组成的数组，并把内部指针向前移动
	{
		if($x < count($stack))
		{
	 		$tmpArr[] = $val;
		}
		else
		{
	 		$return_val = $val;
		}
		$x++;
	}

	$stack = $tmpArr;

	return($return_val);
}

/**
* 解析表情代码
**/
function smilies_pass($message)
{
	static $orig, $repl;// 静态变量
	global $board_config;

	if (!isset($orig))
	{
		global $db, $board_config, $phpbb_root_path;
		$orig = $repl = array();

		$sql = 'SELECT * FROM ' . SMILIES_TABLE;
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't obtain smilies data", "", __LINE__, __FILE__, $sql);
		}
		$smilies = $db->sql_fetchrowset($result);

		if (count($smilies))
		{
			usort($smilies, 'smiley_sort');// usort() 函数使用用户自定义的函数对数组排序
		}

		for ($i = 0; $i < count($smilies); $i++)
		{
			$orig[] = "/(?<=.\W|\W.|^\W)" . preg_quote($smilies[$i]['code'], "/") . "(?=.\W|\W.|\W$)/";
			$repl[] = '<img src="' . $phpbb_root_path . $board_config['smilies_path'] . '/' . $smilies[$i]['smile_url'] . '" alt="' . $smilies[$i]['emoticon'] . '" border="0" />';
		}
	}

	if (count($orig))
	{
		$max_smiles = abs(intval($board_config['max_smiles_in_message']));// abs() 函数返回一个数的绝对值
		$message = preg_replace($orig, $repl, ' ' . $message . ' ', $max_smiles);
	}
	
	return $message;
}

/**
* 
**/
function smiley_sort($a, $b)
{
	if ( strlen($a['code']) == strlen($b['code']) )
	{
		return 0;
	}

	return ( strlen($a['code']) > strlen($b['code']) ) ? -1 : 1;
}

?>