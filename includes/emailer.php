<?php
/***************************************************
 *		emailer.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************/

/**
* emailer 类支持添加附件，虽然在当前版本中没有实现该功能
* 但有可能我们在以后的版本中实现
**/
class emailer
{
	// 声明变量
	var $msg, $subject, $extra_headers;
	var $addresses, $reply_to, $from;
	var $use_smtp;

	var $tpl_msg = array();

	function emailer($use_smtp)
	{
		$this->reset();
		$this->use_smtp = $use_smtp;
		$this->reply_to = $this->from = '';
	}

	// 重置所有数组，地址等默认模版文件
	function reset()
	{
		$this->addresses = array();
		$this->vars = $this->msg = $this->extra_headers = '';
	}

	// 设置发送地址
	function email_address($address)
	{
		$this->addresses['to'] = trim($address);// trim() 函数从字符串的两端删除空白字符和其他预定义字符
	}

	// 抄送
	function cc($address)
	{
		$this->addresses['cc'][] = trim($address);
	}

	// 密件抄送
	function bcc($address)
	{
		$this->addresses['bcc'][] = trim($address);
	}

	// 回复
	function replyto($address)
	{
		$this->reply_to = trim($address);
	}

	// 
	function from($address)
	{
		$this->from = trim($address);
	}

	// 设置邮件的标题
	function set_subject($subject = '')
	{
		$subject = u2w($subject);// u2w() 内建函数
		$this->subject = trim(preg_replace('#[\n\r]+#s', '', $subject));
	}

	// 附上Email的 header
	function extra_headers($headers)
	{
		$this->extra_headers .= trim($headers) . "\n";
	}

	// 使用邮件模版
	function use_template($template_file, $template_lang = '')
	{
		global $board_config, $phpbb_root_path;

		if (trim($template_file) == '')
		{
			message_die(GENERAL_ERROR, 'No template file set', '', __LINE__, __FILE__);
		}

		if (trim($template_lang) == '')
		{
			$template_lang = $board_config['default_lang'];
		}

		if (empty($this->tpl_msg[$template_lang . $template_file]))
		{
			$tpl_file = $phpbb_root_path . 'language/lang_' . $template_lang . '/email/' . $template_file . '.tpl';

			if (!@file_exists(@phpbb_realpath($tpl_file)))// file_exists() 函数检查文件或目录是否存在
			{
				$tpl_file = $phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/email/' . $template_file . '.tpl';

				if (!@file_exists(@phpbb_realpath($tpl_file)))
				{
					message_die(GENERAL_ERROR, 'Could not find email template file :: ' . $template_file, '', __LINE__, __FILE__);
				}
			}

			if (!($fd = @fopen($tpl_file, 'r')))// fopen() 函数用来打开文件
			{
				message_die(GENERAL_ERROR, 'Failed opening template file :: ' . $tpl_file, '', __LINE__, __FILE__);
			}

			// fread() 函数读取文件（可安全用于二进制文件）
			// filesize() 函数返回指定文件的大小
			$this->tpl_msg[$template_lang . $template_file] = fread($fd, filesize($tpl_file));
			fclose($fd);
		}

		$this->msg = $this->tpl_msg[$template_lang . $template_file];

		return true;
	}

	// 分配变量
	function assign_vars($vars)
	{
		$this->vars = (empty($this->vars)) ? $vars : $this->vars . $vars;
	}

	// 发送邮件的收件人以前在 var $this->address
	function send()
	{
		global $board_config, $lang, $phpEx, $phpbb_root_path, $db;

		// 避开引号, 否则会 eval 失败
		$this->msg = str_replace ("'", "\'", $this->msg);
		$this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

		// 重置变量 。 reset() 函数把数组的内部指针指向第一个元素，并返回这个元素的值
		reset ($this->vars);
		// each() 函数生成一个由数组当前内部指针所指向的元素的键名和键值组成的数组，并把内部指针向前移动
		while (list($key, $val) = each($this->vars)) 
		{
			$$key = u2w($val);
		}

		// eval() 函数把字符串按照 PHP 代码来计算
		eval("\$this->msg = '$this->msg';");

		reset ($this->vars);
		while (list($key, $val) = each($this->vars))// list() 函数用数组中的元素为一组变量赋值
		{
			unset($$key);// unset() 函数用于销毁指定的变量
		}

		$drop_header = '';
		$match = array();
		if (preg_match('#^(Subject:(.*?))$#m', $this->msg, $match))
		{
			$this->subject = (trim($match[2]) != '') ? trim($match[2]) : (($this->subject != '') ? $this->subject : 'No Subject');
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->subject = (($this->subject != '') ? $this->subject : 'No Subject');
		}

		if (preg_match('#^(Charset:(.*?))$#m', $this->msg, $match))
		{
			$this->encoding = (trim($match[2]) != '') ? trim($match[2]) : trim($lang['ENCODING']);
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->encoding = trim($lang['ENCODING']);
		}

		if ($drop_header != '')
		{
			$this->msg = trim(preg_replace('#' . $drop_header . '#s', '', $this->msg));
		}

		$to = $this->addresses['to'];

		$cc = (count($this->addresses['cc'])) ? implode(', ', $this->addresses['cc']) : '';
		$bcc = (count($this->addresses['bcc'])) ? implode(', ', $this->addresses['bcc']) : '';

		// 创建 header
		$this->extra_headers = (($this->reply_to != '') ? "Reply-to: $this->reply_to\n" : '') . (($this->from != '') ? "From: $this->from\n" : "From: " . $board_config['board_email'] . "\n") . "Return-Path: " . $board_config['board_email'] . "\nMessage-ID: <" . md5(uniqid(time())) . "@" . $board_config['server_name'] . ">\nMIME-Version: 1.0\nContent-type: text/plain; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . date('r', time()) . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\nX-MimeOLE: Produced By phpBB-WAP\n" . $this->extra_headers . (($cc != '') ? "Cc: $cc\n" : '')  . (($bcc != '') ? "Bcc: $bcc\n" : ''); 

		// 发送消息 ... 删除 $this->encode() 当时的标题
		if ( $this->use_smtp )
		{
			if ( !defined('SMTP_INCLUDED') ) 
			{
				include($phpbb_root_path . 'includes/smtp.' . $phpEx);
			}

			$result = smtpmail($to, $this->subject, $this->msg, $this->extra_headers);// 内建函数
		}
		else
		{
			$empty_to_header = ($to == '') ? TRUE : FALSE;
			$to = ($to == '') ? (($board_config['sendmail_fix']) ? ' ' : 'Undisclosed-recipients:;') : $to;
	
			// mail() 函数允许您从脚本中直接发送电子邮件
			$result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
			
			if (!$result && !$board_config['sendmail_fix'] && $empty_to_header)
			{
				$to = ' ';

				$sql = "UPDATE " . CONFIG_TABLE . " 
					SET config_value = '1'
					WHERE config_name = 'sendmail_fix'";
				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Unable to update config table', '', __LINE__, __FILE__, $sql);
				}

				$board_config['sendmail_fix'] = 1;
				$result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
			}
		}

		/**
		*if (!$result)
		*{
		*	message_die(GENERAL_ERROR, 'Failed sending email :: ' . (($this->use_smtp) ? 'SMTP' : 'PHP') . ' :: ' . $result, '', __LINE__, __FILE__);
		*}
		**/

		return true;
	}

	// 编码
	function encode($str)
	{
		if ($this->encoding == '')
		{
			return $str;
		}

		$end = "?=";
		$start = "=?$this->encoding?B?";
		$spacer = "$end\r\n $start";
		$length = 75 - strlen($start) - strlen($end);
		$length = floor($length / 2) * 2;// floor() 函数向下舍入为最接近的整数
		// chunk_split() 函数把字符串分割为一连串更小的部分
		// base64_encode() 函数将字符串以 MIME BASE64 编码
		$str = chunk_split(base64_encode($str), $length, $spacer);
		$str = preg_replace('#' . preg_quote($spacer, '#') . '$#', '', $str);

		return $start . $str . $end;
	}

	// 附件
	function attachFile($filename, $mimetype = "application/octet-stream", $szFromAddress, $szFilenameToDisplay)
	{
		global $lang;
		$mime_boundary = "--==================_846811060==_";

		$this->msg = '--' . $mime_boundary . "\nContent-Type: text/plain;\n\tcharset=\"" . $lang['ENCODING'] . "\"\n\n" . $this->msg;

		if ($mime_filename)
		{
			$filename = $mime_filename;
			$encoded = $this->encode_file($filename);
		}

		$fd = fopen($filename, "r");
		$contents = fread($fd, filesize($filename));

		$this->mimeOut = "--" . $mime_boundary . "\n";
		$this->mimeOut .= "Content-Type: " . $mimetype . ";\n\tname=\"$szFilenameToDisplay\"\n";
		$this->mimeOut .= "Content-Transfer-Encoding: quoted-printable\n";
		$this->mimeOut .= "Content-Disposition: attachment;\n\tfilename=\"$szFilenameToDisplay\"\n\n";

		if ( $mimetype == "message/rfc822" )
		{
			$this->mimeOut .= "From: ".$szFromAddress."\n";
			$this->mimeOut .= "To: ".$this->emailAddress."\n";
			$this->mimeOut .= "Date: ".date("D, d M Y H:i:s") . " UT\n";
			$this->mimeOut .= "Reply-To:".$szFromAddress."\n";
			$this->mimeOut .= "Subject: ".$this->mailSubject."\n";
			$this->mimeOut .= "X-Mailer: PHP/".phpversion()."\n";
			$this->mimeOut .= "MIME-Version: 1.0\n";
		}

		$this->mimeOut .= $contents."\n";
		$this->mimeOut .= "--" . $mime_boundary . "--" . "\n";

		return $out;
	}

	// 取得 MIME 的 header
	function getMimeHeaders($filename, $mime_filename="")
	{
		$mime_boundary = "--==================_846811060==_";

		if ($mime_filename)
		{
			$filename = $mime_filename;
		}

		$out = "MIME-Version: 1.0\n";
		$out .= "Content-Type: multipart/mixed;\n\tboundary=\"$mime_boundary\"\n\n";
		$out .= "This message is in MIME format. Since your mail reader does not understand\n";
		$out .= "this format, some or all of this message may not be legible.";

		return $out;
	}

	// 分割模块
	function myChunkSplit($str)
	{
		$stmp = $str;
		$len = strlen($stmp);
		$out = "";

		while ($len > 0)
		{
			if ($len >= 76)
			{
				$out .= substr($stmp, 0, 76) . "\r\n";
				$stmp = substr($stmp, 76);
				$len = $len - 76;
			}
			else
			{
				$out .= $stmp . "\r\n";
				$stmp = "";
				$len = 0;
			}
		}
		return $out;
	}

	// 对文件进行编码
	function encode_file($sourcefile)
	{
		if (is_readable(phpbb_realpath($sourcefile)))
		{
			$fd = fopen($sourcefile, "r");
			$contents = fread($fd, filesize($sourcefile));
	      $encoded = $this->myChunkSplit(base64_encode($contents));
	      fclose($fd);
		}

		return $encoded;
	}

} 

?>