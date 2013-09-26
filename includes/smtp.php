<?php
/***************************************************************************
 *                             smtp.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 ***************************************************************************/

define('SMTP_INCLUDED', 1);


function server_parse($socket, $response, $line = __LINE__) 
{
	$server_response = '';
	while (substr($server_response, 3, 1) != ' ') 
	{
		if (!($server_response = fgets($socket, 256))) 
		{ 
			message_die(GENERAL_ERROR, "Couldn't get mail server response codes", "", $line, __FILE__); 
		} 
	}
	if (!(substr($server_response, 0, 3) == $response)) 
	{ 
		message_die(GENERAL_ERROR, "Ran into problems sending Mail. Response: $server_response", "", $line, __FILE__); 
	} 
}

function smtpmail($mail_to, $subject, $message, $headers = '')
{
	global $board_config;

	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

	if ($headers != '')
	{
		if (is_array($headers))
		{
			if (sizeof($headers) > 1)
			{
				$headers = join("\n", $headers);
			}
			else
			{
				$headers = $headers[0];
			}
		}
		$headers = chop($headers);
		$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);
		$header_array = explode("\r\n", $headers);
		@reset($header_array);

		$headers = '';
		while(list(, $header) = each($header_array))
		{
			if (preg_match('#^cc:#si', $header))
			{
				$cc = preg_replace('#^cc:(.*)#si', '\1', $header);
			}
			else if (preg_match('#^bcc:#si', $header))
			{
				$bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
				$header = '';
			}
			$headers .= ($header != '') ? $header . "\r\n" : '';
		}

		$headers = chop($headers);
		$cc = explode(', ', $cc);
		$bcc = explode(', ', $bcc);
	}

	if (trim($subject) == '')
	{
		message_die(GENERAL_ERROR, "No email Subject specified", "", __LINE__, __FILE__);
	}

	if (trim($message) == '')
	{
		message_die(GENERAL_ERROR, "Email message was blank", "", __LINE__, __FILE__);
	}

	if( !$socket = @fsockopen($board_config['smtp_host'], 25, $errno, $errstr, 20) )
	{
		message_die(GENERAL_ERROR, "Could not connect to smtp host : $errno : $errstr", "", __LINE__, __FILE__);
	}

	server_parse($socket, "220", __LINE__);

	if( !empty($board_config['smtp_username']) && !empty($board_config['smtp_password']) )
	{ 
		fputs($socket, "EHLO " . $board_config['smtp_host'] . "\r\n");
		server_parse($socket, "250", __LINE__);

		fputs($socket, "AUTH LOGIN\r\n");
		server_parse($socket, "334", __LINE__);

		fputs($socket, base64_encode($board_config['smtp_username']) . "\r\n");
		server_parse($socket, "334", __LINE__);

		fputs($socket, base64_encode($board_config['smtp_password']) . "\r\n");
		server_parse($socket, "235", __LINE__);
	}
	else
	{
		fputs($socket, "HELO " . $board_config['smtp_host'] . "\r\n");
		server_parse($socket, "250", __LINE__);
	}

	fputs($socket, "MAIL FROM: <" . $board_config['board_email'] . ">\r\n");
	server_parse($socket, "250", __LINE__);

	$to_header = '';
	
	$mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);
	if (preg_match('#[^ ]+\@[^ ]+#', $mail_to))
	{
		fputs($socket, "RCPT TO: <$mail_to>\r\n");
		server_parse($socket, "250", __LINE__);
	}

	@reset($bcc);
	while(list(, $bcc_address) = each($bcc))
	{
		$bcc_address = trim($bcc_address);
		if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address))
		{
			fputs($socket, "RCPT TO: <$bcc_address>\r\n");
			server_parse($socket, "250", __LINE__);
		}
	}

	@reset($cc);
	while(list(, $cc_address) = each($cc))
	{
		$cc_address = trim($cc_address);
		if (preg_match('#[^ ]+\@[^ ]+#', $cc_address))
		{
			fputs($socket, "RCPT TO: <$cc_address>\r\n");
			server_parse($socket, "250", __LINE__);
		}
	}

	
	fputs($socket, "DATA\r\n");
	server_parse($socket, "354", __LINE__);
	fputs($socket, "Subject: $subject\r\n");
	fputs($socket, "To: $mail_to\r\n");
	fputs($socket, "$headers\r\n\r\n");
	fputs($socket, "$message\r\n");
	fputs($socket, ".\r\n");
	server_parse($socket, "250", __LINE__);
	fputs($socket, "QUIT\r\n");
	fclose($socket);

	return TRUE;
}

?>