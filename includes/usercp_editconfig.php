<?php
/***************************************************************************
 *		usercp_editconfig.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 *		说明：用户个人设置
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}

$unhtml_specialchars_match = array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace = array('>', '<', '"', '&');

$error = FALSE;
$error_msg = '';

if (
	isset($HTTP_POST_VARS['submit']) ||
	isset($HTTP_POST_VARS['avatargallery']) ||
	isset($HTTP_POST_VARS['submitavatar']) ||
	isset($HTTP_POST_VARS['cancelavatar']) )
{
	include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);
	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_post.'.$phpEx);

	if (isset($HTTP_POST_VARS['topics_per_page']) )
	{
		$user_topics_per_page = abs(( intval($HTTP_POST_VARS['topics_per_page']) == 0 ) ? $board_config['topics_per_page'] : intval($HTTP_POST_VARS['topics_per_page']));
		$user_topics_per_page = abs(( $user_topics_per_page > $board_config['max_user_topics_per_page'] ) ? $board_config['topics_per_page'] : $user_topics_per_page);
	}
	if (isset($HTTP_POST_VARS['posts_per_page']) )
	{
		$user_posts_per_page = abs(( intval($HTTP_POST_VARS['topics_per_page']) == 0 ) ? $board_config['posts_per_page'] : intval($HTTP_POST_VARS['posts_per_page']));
		$user_posts_per_page = abs(( $user_topics_per_page > $board_config['max_user_posts_per_page'] ) ? $board_config['posts_per_page'] : $user_posts_per_page);
	}

	$viewemail = ( isset($HTTP_POST_VARS['viewemail']) ) ? ( ($HTTP_POST_VARS['viewemail']) ? TRUE : 0 ) : 0;
	$allowviewonline = ( isset($HTTP_POST_VARS['hideonline']) ) ? ( ($HTTP_POST_VARS['hideonline']) ? 0 : TRUE ) : TRUE;
	$notifyreply_to_email = ( isset($HTTP_POST_VARS['notifyreply_to_email']) ) ? ( ($HTTP_POST_VARS['notifyreply_to_email']) ? TRUE : 0 ) : 0;
	$notifyreply_to_pm = ( isset($HTTP_POST_VARS['notifyreply_to_pm']) ) ? ( ($HTTP_POST_VARS['notifyreply_to_pm']) ? TRUE : 0 ) : 0;
	$notifypm = ( isset($HTTP_POST_VARS['notifypm']) ) ? ( ($HTTP_POST_VARS['notifypm']) ? TRUE : 0 ) : TRUE;
	$popup_pm = ( isset($HTTP_POST_VARS['popup_pm']) ) ? ( ($HTTP_POST_VARS['popup_pm']) ? TRUE : 0 ) : TRUE;
	$user_email_new_gb = ( isset($HTTP_POST_VARS['user_email_new_gb']) ) ? ( ($HTTP_POST_VARS['user_email_new_gb']) ? TRUE : 0 ) : 1;

	$attachsig = ( isset($HTTP_POST_VARS['attachsig']) ) ? ( ($HTTP_POST_VARS['attachsig']) ? TRUE : 0 ) : $userdata['user_attachsig'];
	$allowhtml = ( isset($HTTP_POST_VARS['allowhtml']) ) ? ( ($HTTP_POST_VARS['allowhtml']) ? TRUE : 0 ) : $userdata['user_allowhtml'];
	$allowbbcode = ( isset($HTTP_POST_VARS['allowbbcode']) ) ? ( ($HTTP_POST_VARS['allowbbcode']) ? TRUE : 0 ) : $userdata['user_allowbbcode'];
	$allowsmilies = ( isset($HTTP_POST_VARS['allowsmilies']) ) ? ( ($HTTP_POST_VARS['allowsmilies']) ? TRUE : 0 ) : $userdata['user_allowsmile'];
	$on_off = ( isset($HTTP_POST_VARS['on_off']) ) ? ( ($HTTP_POST_VARS['on_off']) ? TRUE : 0 ) : 1;
	$attach_on = ( isset($HTTP_POST_VARS['attach_on']) ) ? ( ($HTTP_POST_VARS['attach_on']) ? TRUE : 0 ) : 1;
	
	$quick_answer = ( isset($HTTP_POST_VARS['quick_answer']) ) ? ( ($HTTP_POST_VARS['quick_answer']) ? TRUE : 0 ) : 1;
	$bb_panel = ( isset($HTTP_POST_VARS['bb_panel']) ) ? ( ($HTTP_POST_VARS['bb_panel']) ? TRUE : 0 ) : 1;
	$view_latest_post = ( isset($HTTP_POST_VARS['view_latest_post']) ) ? ( ($HTTP_POST_VARS['view_latest_post']) ? 1 : 0 ) : 0;
	$java_otv = ( isset($HTTP_POST_VARS['java_otv']) ) ? ( ($HTTP_POST_VARS['java_otv']) ? TRUE : 0 ) : 1;
	$message_quote = ( isset($HTTP_POST_VARS['message_quote']) ) ? ( ($HTTP_POST_VARS['message_quote']) ? 1 : 0 ) : 1;
	$icq_send = ( isset($HTTP_POST_VARS['icq_send']) ) ? ( ($HTTP_POST_VARS['icq_send']) ? TRUE : 0 ) : 1;
	$index_spisok = ( isset($HTTP_POST_VARS['index_spisok']) ) ? ( ($HTTP_POST_VARS['index_spisok']) ? TRUE : 0 ) : 1;
	$posl_red = ( isset($HTTP_POST_VARS['posl_red']) ) ? ( ($HTTP_POST_VARS['posl_red']) ? TRUE : 0 ) : 1;
	$post_leng = ( isset($HTTP_POST_VARS['post_leng']) ) ? intval($HTTP_POST_VARS['post_leng']) : $userdata['user_post_leng'];

	$user_timezone = ( isset($HTTP_POST_VARS['timezone']) ) ? doubleval($HTTP_POST_VARS['timezone']) : $board_config['board_timezone'];

	$sql = "SELECT config_value
		FROM " . CONFIG_TABLE . "
		WHERE config_name = 'default_dateformat'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select default dateformat', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);
	$board_config['default_dateformat'] = $row['config_value'];
	$user_dateformat = ( !empty($HTTP_POST_VARS['dateformat']) ) ? trim(htmlspecialchars($HTTP_POST_VARS['dateformat'])) : $board_config['default_dateformat'];

	$user_avatar_local = ( isset($HTTP_POST_VARS['avatarselect']) && !empty($HTTP_POST_VARS['submitavatar']) && $board_config['allow_avatar_local'] ) ? htmlspecialchars($HTTP_POST_VARS['avatarselect']) : ( ( isset($HTTP_POST_VARS['avatarlocal'])  ) ? htmlspecialchars($HTTP_POST_VARS['avatarlocal']) : '' );
	$user_avatar_category = ( isset($HTTP_POST_VARS['avatarcatname']) && $board_config['allow_avatar_local'] ) ? htmlspecialchars($HTTP_POST_VARS['avatarcatname']) : '' ;

	$user_avatar_remoteurl = ( !empty($HTTP_POST_VARS['avatarremoteurl']) ) ? trim(htmlspecialchars($HTTP_POST_VARS['avatarremoteurl'])) : '';

	if ( $result_ua )
	{
		$opera_mini = "./opera_mini";
		$uploadedfile = $HTTP_POST_VARS['fileupload'];

		if ( strlen($uploadedfile) ) 
		{
			$array = explode('file=', $uploadedfile);
			$tmp_name = $array[0];
			$filebase64 = $array[1]; 
		}

		$tmp_name = basename($tmp_name);

		if ( strlen($filebase64) ) 
		{
			$filedata = base64_decode($filebase64);
		}

		$fileom = @fopen($opera_mini . "/" . $tmp_name, "wb");

		if ( $fileom ) 
		{
			if ( flock($fileom, LOCK_EX) ) 
			{
				fwrite($fileom, $filedata);
				flock($fileom, LOCK_UN); 
			}
			fclose($fileom); 
		}

		$file = $opera_mini . "/" . $tmp_name;
		$size = @filesize($file);
		$tmp_name_type = strrchr($tmp_name, '.');
		$repl=array("."=>"");
		$type = strtr($tmp_name_type, $repl);
		$user_avatar_upload = ( !empty($HTTP_POST_VARS['avatarurl']) ) ? trim($HTTP_POST_VARS['avatarurl']) : ( ( $file != $opera_mini . "/") ? $file : '' );
		$user_avatar_name = ( !empty($tmp_name) ) ? $tmp_name : '';
		$user_avatar_size = ( !empty($size) ) ? $size : 0;
		$user_avatar_filetype = ( !empty($type) ) ? 'image/'.$type : '';
	} else {
		$user_avatar_upload = ( !empty($HTTP_POST_VARS['avatarurl']) ) ? trim($HTTP_POST_VARS['avatarurl']) : ( ( $HTTP_POST_FILES['avatar']['tmp_name'] != "none") ? $HTTP_POST_FILES['avatar']['tmp_name'] : '' );
		$user_avatar_name = ( !empty($HTTP_POST_FILES['avatar']['name']) ) ? $HTTP_POST_FILES['avatar']['name'] : '';
		$user_avatar_size = ( !empty($HTTP_POST_FILES['avatar']['size']) ) ? $HTTP_POST_FILES['avatar']['size'] : 0;
		$user_avatar_filetype = ( !empty($HTTP_POST_FILES['avatar']['type']) ) ? $HTTP_POST_FILES['avatar']['type'] : '';
	}

	$user_avatar = ( empty($user_avatar_local) ) ? $userdata['user_avatar'] : '';
	$user_avatar_type = ( empty($user_avatar_local) ) ? $userdata['user_avatar_type'] : '';

	if ( (isset($HTTP_POST_VARS['avatargallery']) || isset($HTTP_POST_VARS['submitavatar']) || isset($HTTP_POST_VARS['cancelavatar'])) && (!isset($HTTP_POST_VARS['submit'])) )
	{
		$user_dateformat = stripslashes($user_dateformat);

		if ( !isset($HTTP_POST_VARS['cancelavatar']))
		{
			$user_avatar = $user_avatar_category . '/' . $user_avatar_local;
			$user_avatar_type = USER_AVATAR_GALLERY;
		}
	}
}

if ( isset($HTTP_POST_VARS['submit']) )
{
	include($phpbb_root_path . 'includes/usercp_avatar.'.$phpEx);

	$user_id = intval($HTTP_POST_VARS['user_id']);
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Wrong_Profile'];
	}

	$avatar_sql = '';

	if ( isset($HTTP_POST_VARS['avatardel']) )
	{
		$avatar_sql = user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
	}
	else
	if ( ( !empty($user_avatar_upload) || !empty($user_avatar_name) ) && $board_config['allow_avatar_upload'] )
	{
		if ( !empty($user_avatar_upload) )
		{
			$avatar_mode = (empty($user_avatar_name)) ? 'remote' : 'local';
			$avatar_sql = user_avatar_upload($mode, $avatar_mode, $userdata['user_avatar'], $userdata['user_avatar_type'], $error, $error_msg, $user_avatar_upload, $user_avatar_name, $user_avatar_size, $user_avatar_filetype);
		}
		else if ( !empty($user_avatar_name) )
		{
			$l_avatar_size = sprintf($lang['Avatar_filesize'], round($board_config['avatar_filesize'] / 1024));

			$error = true;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $l_avatar_size;
		}
	}
	else if ( $user_avatar_remoteurl != '' && $board_config['allow_avatar_remote'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_url($mode, $error, $error_msg, $user_avatar_remoteurl);
	}
	else if ( $user_avatar_local != '' && $board_config['allow_avatar_local'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_gallery($mode, $error, $error_msg, $user_avatar_local, $user_avatar_category);
	}

	if ( !$error )
	{
			$user_active = 1;
			$user_actkey = '';

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_topics_per_page = '$user_topics_per_page', user_posts_per_page = '$user_posts_per_page', user_viewemail = $viewemail, user_on_off = '$on_off', user_attachsig = $attachsig, user_allowsmile = $allowsmilies, user_allowhtml = $allowhtml, user_allowbbcode = $allowbbcode, user_allow_viewonline = $allowviewonline, user_notify_to_email = $notifyreply_to_email, user_notify_to_pm = $notifyreply_to_pm, user_notify_pm = $notifypm, user_popup_pm = $popup_pm, user_timezone = $user_timezone, user_dateformat = '" . str_replace("\'", "''", $user_dateformat) . "', user_lang = 'chinese', user_attach_mod = '$attach_on', user_quick_answer = '$quick_answer', user_bb_panel = '$bb_panel', user_java_otv = '$java_otv', user_message_quote = '$message_quote', user_view_latest_post = '$view_latest_post', user_icq_send = '$icq_send', user_index_spisok = '$index_spisok', user_email_new_gb = '$user_email_new_gb', user_posl_red = '$posl_red', user_post_leng = '$post_leng', user_active = $user_active, user_actkey = '" . str_replace("\'", "''", $user_actkey) . "'" . $avatar_sql . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}

			$message = $lang['Profile_updated'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

			$template->assign_vars(array(
				"META" => '<meta http-equiv="refresh" content="0;url=' . append_sid("index.$phpEx") . '">')
			);

			message_die(GENERAL_MESSAGE, $message);
	}
}


if ( $error )
{
	$user_dateformat = stripslashes($user_dateformat);
	$user_topics_per_page = $userdata['user_topics_per_page'];
	$user_posts_per_page = $userdata['user_posts_per_page'];
	$post_leng = $userdata['user_post_leng'];
}
else if ( !isset($HTTP_POST_VARS['avatargallery']) && !isset($HTTP_POST_VARS['submitavatar']) && !isset($HTTP_POST_VARS['cancelavatar']) )
{
	$user_id = $userdata['user_id'];

	
	$viewemail = $userdata['user_viewemail'];
	$notifypm = $userdata['user_notify_pm'];
	$popup_pm = $userdata['user_popup_pm'];
	$user_email_new_gb = $userdata['user_email_new_gb'];
	$notifyreply_to_email = $userdata['user_notify_to_email'];
	$notifyreply_to_pm = $userdata['user_notify_to_pm'];
	$attachsig = $userdata['user_attachsig'];
	$allowhtml = $userdata['user_allowhtml'];
	$allowbbcode = $userdata['user_allowbbcode'];
	$allowsmilies = $userdata['user_allowsmile'];
	$allowviewonline = $userdata['user_allow_viewonline'];

	$user_avatar = ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar'] : '';
	$user_avatar_type = ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar_type'] : USER_AVATAR_NONE;

	$user_timezone = $userdata['user_timezone'];
	$user_dateformat = $userdata['user_dateformat'];
	$user_topics_per_page = $userdata['user_topics_per_page'];
	$user_posts_per_page = $userdata['user_posts_per_page'];
	$post_leng = $userdata['user_post_leng'];
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$user_id = ( !empty($HTTP_POST_VARS['user_id']) ) ? intval($HTTP_POST_VARS['user_id']) : $user_id;
if ( $user_id != $userdata['user_id'] )
{
	$error = TRUE;
	$error_msg = $lang['Wrong_Profile'];
}

if( isset($HTTP_POST_VARS['avatargallery']) && !$error )
{
	include($phpbb_root_path . 'includes/usercp_avatar.'.$phpEx);

	$avatar_category = ( !empty($HTTP_POST_VARS['avatarcategory']) ) ? htmlspecialchars($HTTP_POST_VARS['avatarcategory']) : '';

	$template->set_filenames(array(
		'body' => 'profile_avatar_gallery.tpl')
	);

	$allowviewonline = !$allowviewonline;

	display_avatar_gallery($mode, $avatar_category, $user_id, $viewemail, $notifypm, $popup_pm, $user_email_new_gb, $notifyreply_to_email, $notifyreply_to_pm, $attachsig, $allowhtml, $allowbbcode, $allowsmilies, $allowviewonline, $user_style, $user_lang, $user_timezone, $user_dateformat, $userdata['session_id'], $user_topics_per_page, $user_posts_per_page, $birthday, $gender);
}
else
{
	include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

	if ( !isset($coppa) )
	{
		$coppa = FALSE;
	}

	if ( !isset($user_style) )
	{
		$user_style = $board_config['default_style'];
	}

	$avatar_img = '';
	if ( $user_avatar_type )
	{
		switch( $user_avatar_type )
		{
			case USER_AVATAR_UPLOAD:
				$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $user_avatar . '" alt="" /><br/><input type="checkbox" name="avatardel" /> '.$lang['Delete_Image'].'<br/>' : '';
				break;
			case USER_AVATAR_REMOTE:
				$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $user_avatar . '" alt="" /><br/><input type="checkbox" name="avatardel" /> '.$lang['Delete_Image'].'<br/>' : '';
				break;
			case USER_AVATAR_GALLERY:
				$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $user_avatar . '" alt="" /><br/><input type="checkbox" name="avatardel" /> '.$lang['Delete_Image'].'<br/>' : '';
				break;
		}
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';
	$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
	$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';

	if ( !empty($user_avatar_local) )
	{
		$s_hidden_fields .= '<input type="hidden" name="avatarlocal" value="' . $user_avatar_local . '" /><input type="hidden" name="avatarcatname" value="' . $user_avatar_category . '" />';
	}

	$html_status =  ( $userdata['user_allowhtml'] && $board_config['allow_html'] ) ? $lang['HTML_is_ON'] : $lang['HTML_is_OFF'];
	$bbcode_status = ( $userdata['user_allowbbcode'] && $board_config['allow_bbcode']  ) ? $lang['BBCode_is_ON'] : $lang['BBCode_is_OFF'];
	$smilies_status = ( $userdata['user_allowsmile'] && $board_config['allow_smilies']  ) ? $lang['Smilies_are_ON'] : $lang['Smilies_are_OFF'];

	if ( $error )
	{
		$template->set_filenames(array(
			'reg_header' => 'error_body.tpl')
		);
		$template->assign_vars(array(
			'ERROR_MESSAGE' => $error_msg)
		);
		$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
	}

	$template->set_filenames(array(
		'body' => 'profile_add_config.tpl')
	);

	$ini_val = ( phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
	$form_enctype = ( @$ini_val('file_uploads') == '0' || strtolower(@$ini_val('file_uploads') == 'off') || phpversion() == '4.0.4pl1' || !$board_config['allow_avatar_upload'] || ( phpversion() < '4.0.3' && @$ini_val('open_basedir') != '' ) ) ? '' : 'enctype="multipart/form-data"';

	$template->assign_vars(array(
		
		'VIEW_EMAIL_YES' => ( $viewemail ) ? 'checked="checked"' : '',
		'VIEW_EMAIL_NO' => ( !$viewemail ) ? 'checked="checked"' : '',
		'HIDE_USER_YES' => ( !$allowviewonline ) ? 'checked="checked"' : '',
		'HIDE_USER_NO' => ( $allowviewonline ) ? 'checked="checked"' : '',
		'NOTIFY_PM_YES' => ( $notifypm ) ? 'checked="checked"' : '',
		'NOTIFY_PM_NO' => ( !$notifypm ) ? 'checked="checked"' : '',
		'POPUP_PM_YES' => ( $popup_pm ) ? 'checked="checked"' : '',
		'POPUP_PM_NO' => ( !$popup_pm ) ? 'checked="checked"' : '',
		'GB_EMAIL_YES' => ( $user_email_new_gb ) ? 'checked="checked"' : '',
		'GB_EMAIL_NO' => ( !$user_email_new_gb ) ? 'checked="checked"' : '',
		'ALWAYS_ADD_SIGNATURE_YES' => ( $attachsig ) ? 'checked="checked"' : '',
		'ALWAYS_ADD_SIGNATURE_NO' => ( !$attachsig ) ? 'checked="checked"' : '',
		'NOTIFY_REPLY_TO_EMAIL' => ( $notifyreply_to_email ) ? ' checked="checked"' : '',
		'NOTIFY_REPLY_TO_PM' => ( $notifyreply_to_pm ) ? ' checked="checked"' : '',
		'ALWAYS_ALLOW_BBCODE_YES' => ( $allowbbcode ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_BBCODE_NO' => ( !$allowbbcode ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_HTML_YES' => ( $allowhtml ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_HTML_NO' => ( !$allowhtml ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_SMILIES_YES' => ( $allowsmilies ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_SMILIES_NO' => ( !$allowsmilies ) ? 'checked="checked"' : '',
		'ON_OFF_YES' => ( $userdata['user_on_off'] ) ? 'checked="checked"' : '',
		'ON_OFF_NO' => ( !$userdata['user_on_off'] ) ? 'checked="checked"' : '',
		'ATTACH_ON_NO' => ( !$userdata['user_attach_mod'] ) ? 'checked="checked"' : '',
		'ATTACH_ON_YES' => ( $userdata['user_attach_mod'] ) ? 'checked="checked"' : '',
		'BB_PANEL_NO' => ( !$userdata['user_bb_panel'] ) ? 'checked="checked"' : '',
		'BB_PANEL_YES' => ( $userdata['user_bb_panel'] ) ? 'checked="checked"' : '',
		'JAVA_OTV_NO' => ( !$userdata['user_java_otv'] ) ? 'checked="checked"' : '',
		'JAVA_OTV_YES' => ( $userdata['user_java_otv'] ) ? 'checked="checked"' : '',
		'MESSAGE_QUOTE_NO' => ( !$userdata['user_message_quote'] ) ? 'checked="checked"' : '',
		'MESSAGE_QUOTE_YES' => ( $userdata['user_message_quote'] ) ? 'checked="checked"' : '',
		'VIEW_LATEST_POST_NO' => ( !$userdata['user_view_latest_post'] ) ? 'checked="checked"' : '',
		'VIEW_LATEST_POST_YES' => ( $userdata['user_view_latest_post'] ) ? 'checked="checked"' : '',
		'QUICK_ANSWER_NO' => ( !$userdata['user_quick_answer'] ) ? 'checked="checked"' : '',
		'QUICK_ANSWER_YES' => ( $userdata['user_quick_answer'] ) ? 'checked="checked"' : '',
		'ICQ_SEND_NO' => ( !$userdata['user_icq_send'] ) ? 'checked="checked"' : '',
		'ICQ_SEND_YES' => ( $userdata['user_icq_send'] ) ? 'checked="checked"' : '',
		'INDEX_SPISOK_NO' => ( !$userdata['user_index_spisok'] ) ? 'checked="checked"' : '',
		'INDEX_SPISOK_YES' => ( $userdata['user_index_spisok'] ) ? 'checked="checked"' : '',
		'POSL_RED_NO' => ( !$userdata['user_posl_red'] ) ? 'checked="checked"' : '',
		'POSL_RED_YES' => ( $userdata['user_posl_red'] ) ? 'checked="checked"' : '',
		'ALLOW_AVATAR' => $board_config['allow_avatar_upload'],
		'AVATAR' => $avatar_img,
		'AVATAR_SIZE' => $board_config['avatar_filesize'],
		'LANGUAGE_SELECT' => language_select($user_lang, 'language'),
		'STYLE_SELECT' => style_select($user_style, 'style'),
		'TIMEZONE_SELECT' => tz_select($user_timezone, 'timezone'),
		'DATE_FORMAT' => select_dateformat($user_dateformat, 'dateformat'),
		'HTML_STATUS' => $html_status,
		'SMILIES_STATUS' => $smilies_status,
		'TOPICS_PER_PAGE' => $user_topics_per_page,
		'POSTS_PER_PAGE' => $user_posts_per_page,
		'POST_LENG' => $post_leng,

		'L_SUBMIT' => $lang['Submit'],
		'L_BOARD_LANGUAGE' => $lang['Board_lang'],
		'L_TIMEZONE' => $lang['Timezone'],
		'L_DATE_FORMAT' => $lang['Date_format'],
		'L_DATE_FORMAT_EXPLAIN' => $lang['Date_format_explain'],
		'L_YES' => $lang['Yes'],
		'L_NO' => $lang['No'],

		'L_GENDER' =>$lang['Gender'], 
		'L_GENDER_MALE' =>$lang['Male'], 
		'L_GENDER_FEMALE' =>$lang['Female'], 
		'L_GENDER_NOT_SPECIFY' =>$lang['No_gender_specify'], 
		'L_BIRTHDAY' => $lang['Birthday'],

		'L_ALWAYS_ALLOW_SMILIES' => $lang['Always_smile'],
		'L_ALWAYS_ALLOW_BBCODE' => $lang['Always_bbcode'],
		'L_ALWAYS_ALLOW_HTML' => $lang['Always_html'],
		'L_HIDE_USER' => $lang['Hide_user'],
		'L_ALWAYS_ADD_SIGNATURE' => $lang['Always_add_sig'],

		'L_AVATAR_PANEL' => $lang['Avatar_panel'],
		'L_AVATAR_EXPLAIN' => sprintf($lang['Avatar_explain'], $board_config['avatar_max_width'], $board_config['avatar_max_height'], (round($board_config['avatar_filesize'] / 1024))),
		'L_UPLOAD_AVATAR_FILE' => $lang['Upload_Avatar_file'],
		'L_UPLOAD_AVATAR_URL' => $lang['Upload_Avatar_URL'],
		'L_UPLOAD_AVATAR_URL_EXPLAIN' => $lang['Upload_Avatar_URL_explain'],
		'L_AVATAR_GALLERY' => $lang['Select_from_gallery'],
		'L_SHOW_GALLERY' => $lang['View_avatar_gallery'],
		'L_LINK_REMOTE_AVATAR' => $lang['Link_remote_Avatar'],
		'L_LINK_REMOTE_AVATAR_EXPLAIN' => $lang['Link_remote_Avatar_explain'],
		'L_CURRENT_IMAGE' => $lang['Current_Image'],

		'L_NOTIFY_ON_REPLY' => $lang['Always_notify'],
		'L_NOTIFY_ON_REPLY_TO_EMAIL' => $lang['Always_notify_to_email'],
		'L_NOTIFY_ON_REPLY_TO_PM' => $lang['Always_notify_to_pm'],
		'L_NOTIFY_ON_REPLY_EXPLAIN' => $lang['Always_notify_explain'],
		'L_NOTIFY_ON_PRIVMSG' => $lang['Notify_on_privmsg'],
		'L_POPUP_ON_PRIVMSG' => $lang['Popup_on_privmsg'],
		'L_POPUP_ON_PRIVMSG_EXPLAIN' => $lang['Popup_on_privmsg_explain'],
		'L_PREFERENCES' => $lang['Preferences'],
		'L_PUBLIC_VIEW_EMAIL' => $lang['Public_view_email'],
		'L_ITEMS_REQUIRED' => $lang['Items_required'],
		'L_TOPICS_PER_PAGE' => $lang['Topics_per_page'],
		'L_POSTS_PER_PAGE' => $lang['Posts_per_page'],

		'S_ALLOW_AVATAR_UPLOAD' => $board_config['allow_avatar_upload'],
		'S_ALLOW_AVATAR_LOCAL' => $board_config['allow_avatar_local'],
		'S_ALLOW_AVATAR_REMOTE' => $board_config['allow_avatar_remote'],
		'S_HIDDEN_FIELDS' => $s_hidden_fields,
		'S_FORM_ENCTYPE' => $form_enctype,
		'S_PROFILE_ACTION' => append_sid("profile.$phpEx"))
	);

	if ( $board_config['message_quote'] )
	{
		$template->assign_block_vars('switch_message_quote', array());
	}

	if ( $userdata['user_allowavatar'] && ( $board_config['allow_avatar_upload'] || $board_config['allow_avatar_local'] || $board_config['allow_avatar_remote'] ) )
	{
		$template->assign_block_vars('switch_avatar_block', array() );

		if ( $board_config['allow_avatar_upload'] && file_exists(@phpbb_realpath('./' . $board_config['avatar_path'])) )
		{
			if ( !$result_ua && $form_enctype != '' )
			{
				$template->assign_block_vars('switch_avatar_block.switch_avatar_local_upload', array() );
			}
			elseif ( $result_ua && $form_enctype != '' )
			{
				$template->assign_block_vars('switch_avatar_block.switch_avatar_local_upload_om', array() );
			}
			$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_upload', array() );
		}

		if ( $board_config['allow_avatar_remote'] )
		{
			$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_link', array() );
		}

		if ( $board_config['allow_avatar_local'] && file_exists(@phpbb_realpath('./' . $board_config['avatar_gallery_path'])) )
		{
			$template->assign_block_vars('switch_avatar_block.switch_avatar_local_gallery', array() );
		}
	}
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>