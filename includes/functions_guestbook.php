<?php
/***************************************************
 *		functions_guestbook.php
 *		-----------------------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		简体中文：爱疯的云
 *		说明：留言板函数
 **************************************************/

if (!defined('IN_PHPBB'))
{
	die("Hacking attempt");
}

class guestbook{
	var $userdata;
	var $uid;
	var $url;
	var $url_intern;
	
	function guestbook(&$uid,$mode = false, $url = false)
	{
		global $phpEx;
		if(is_array($uid))
		{
			$this->userdata = $uid;
		}
		else
		{
			$this->userdata = get_userdata($uid);
		}
		$this->uid = $this->userdata['user_id'];
		$this->version = '1.0.8';

		if($url !== false)
		{
			$tmp = explode("?", $url);
			if(!count($tmp))
			{
				//No valid url, missing ?.
				$url = false;
			}
			else
			{
				if(!file_exists($tmp[0]))
				{
					//File doesn't exists
					$url = false;
				}
			}
		}

		if($url === false )
		{
			$this->url_intern = "profile." . $phpEx . "?mode=guestbook&amp;" . POST_USERS_URL . "=" . $this->uid;
		}
		else
		{
			$this->url_intern = $url;
		}

		if(!$mode)
		{
			return true;
		}
		else
		{
			return $this->mode($mode);
		}
	}
	
	function mode($mode)
	{
		global $userdata,$board_config,$lang;
		global $HTTP_GET_VARS, $template, $HTTP_POST_VARS, $phpbb_root_path, $phpEx, $db;
		
		if(!$this->userdata['user_can_gb'] || !$userdata['user_can_gb'] || (!$board_config['allow_guests_gb'] && !$userdata['session_logged_in']))
		{
			return false;
		}
		else
		{
			if ( isset($HTTP_POST_VARS['cancel']) )
			{
				$redirect = "profile.$phpEx?mode=guestbook&" . POST_USERS_URL . '=' . $this->uid;
				redirect(append_sid($redirect, true));
			}
			$confirm = ( $HTTP_POST_VARS['confirm'] ) ? TRUE : 0;
			if(($mode == 'delete' || $mode == 'deleteall') && !$confirm)
			{
				$hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="gb_id" value="' . intval($HTTP_GET_VARS['gb_id']) . '" />';
				
				$template->set_filenames(array(
					'confirm' => 'confirm_body.tpl')
				);

				$template->assign_vars(array(
					'MESSAGE_TITLE' 	=> $lang['Confirm'],
					'MESSAGE_TEXT' 		=> $lang['Confirm_delete_gbpost'],

					'L_YES' 	=> $lang['Yes'],
					'L_NO' 		=> $lang['No'],

					'S_CONFIRM_ACTION' 	=> $this->append_sid("gb=" . $mode),
					'S_HIDDEN_FIELDS' 	=> $hidden_fields)
				);

				$template->pparse('confirm');

				include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
			}
			switch($mode){
				case "view":
					$this->view();
				break;
				case "quote":
				case "post":
				case "edit":
						$this->post($mode);
				break;
				case "delete":
					if($userdata['user_level'] == ADMIN || $userdata['user_id'] == $this->uid)
					{
						$this->delete();
					}
					else
					{
						message_die(GENERAL_MESSAGE,sprintf($lang['gb_no_per'],$lang['delete_pro']));
					}
				break;
				case "deleteall":
					if($userdata['user_level'] == ADMIN || $userdata['user_id'] == $this->uid)
					{
						$this->deleteall();
					}
					else
					{
						message_die(GENERAL_MESSAGE,sprintf($lang['gb_no_per'],$lang['delete_all_pro']));
					}
				break;
				default:
					return false;
			}
		}
		return true;
	}
	
	/**
	* gb_view 页面
	**/
	function view()
	{
		global $db,$HTTP_GET_VARS;
		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;
		$start = ($start < 0) ? 0 : $start; 
		
		$sql = "SELECT * FROM " . PROFILE_GUESTBOOK_TABLE . " g, " . USERS_TABLE . " u 
			WHERE g.user_id = " . $this->uid . " AND g.poster_id = u.user_id
			ORDER BY g.gb_time DESC
			LIMIT $start, 10";
			
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR,"Could not query guestbook","",__LINE__,__FILE__,$sql);
		}
		if( !$db->sql_numrows($result) )
		{
			if($start == 0)
			{
				$this->maak_view($result,'nores',0);
			}
			else
			{
				$this->maak_view($result,'nopag',0);
			}
		}
		else
		{
			$this->maak_view($result,'',0);
		}
	}
	
	/**
	* gb_view 页面
	**/
	function maak_view($result,$fout = '',$tot)
	{
		global $phpbb_root_path,$phpEx,$template,$lang,$profiledata,$userdata,$images,$board_config;
		global $db,$theme,$HTTP_GET_VARS;
		include_once($phpbb_root_path."/includes/bbcode.".$phpEx);
		$template->set_filenames(array(
			'gb_body' => 'gb_view.tpl')
		);
		$txt = sprintf($lang['gb_text'],$profiledata['username']);
		if($userdata['user_level'] == ADMIN)
		{
			$delete = sprintf($lang['gb_text2'],$this->append_sid("gb=deleteall"));
		}
		elseif($userdata['user_id'] == $this->uid || $userdata['user_level'] == MOD)
		{
			$delete = sprintf($lang['gb_text2'],$this->append_sid("gb=deleteall"));
		}
		$template->assign_vars(array(
			"L_GUESTBOOK" 	=> $lang['gb_txt'],
			"L_TXT" 		=> $txt,
			'L_DELETE'		=> $delete,
			"L_DIS" 		=> $lang['dis'],
			"L_EN" 			=> $lang['en'],
			"L_BACK_TO_TOP"	=> $lang['Back_to_top'],
			"L_NUMBER_URL" 	=> $lang['number_url'],
			"MINI_POST_IMG"	=> $images['icon_minipost'],
			"UID" 			=> $this->uid,
			"U" 			=> POST_USERS_URL,
			"URL" 			=> $board_config['server_name'],
			"PAD" 			=> $board_config['script_path'],
			"SECURE" 		=> ($board_config['cookie_secure']) ? "s" : '',
			"PHPEX" 		=> $phpEx,
		));
		if($fout != '')
		{
			$reply_img = $images['reply_new'];
			$reply_alt = $lang['gb_reply'];
			$reply_topic_url = $this->append_sid("gb=post");
			$template->assign_vars(array(
				'REPLY_IMG' 			=> $reply_img,
				'U_POST_REPLY_TOPIC' 	=> $reply_topic_url)
			);
			switch($fout)
			{
				case "nores":
					$template->assign_block_vars("error",array(
						"L_GUESTBOOK_ERROR" 	=> $lang['gb_error2'],
						"ERROR" 				=> $lang['gb_nores']
					));
				break;
				case "nopag":
					$template->assign_block_vars("error",array(
						"L_GUESTBOOK_ERROR" 	=> $lang['gb_error'],
						"ERROR" 				=> $lang['gb_nopag']
					));
				break;
			}
		}
		else
		{
			$postrow = array();
			$postrow = $db->sql_fetchrowset($result);
			$total_posts = count($postrow);

			global $ranksrow;
			
			$orig_word = array();
			$replacement_word = array();
			obtain_word_list($orig_word, $replacement_word);
			$reply_img = $images['reply_new'];
			$reply_alt = $lang['gb_reply'];
			$reply_topic_url = $this->append_sid('gb=post');
			
			$sql2 = "SELECT * FROM " . PROFILE_GUESTBOOK_TABLE . " 
				WHERE user_id = " . $this->uid;
				
			$result2 = $db->sql_query($sql2);
			if(!$result2)
			{
				message_die(GENERAL_ERROR,"Could not get total of guestbook posts!","",__LINE__,__FILE__,$sql2);
			}
			$total_replies = $db->sql_numrows($result2);
			$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;
			$pagination = generate_pagination('profile.'.$phpEx.'?mode=guestbook&gb=view&'.POST_USERS_URL.'='.$this->uid, $total_replies, 10, $start);
	
			$template->assign_block_vars('main',array(
				'PAGINATION' 	=> $pagination,
				'PAGE_NUMBER' 	=> sprintf($lang['Page_of'], ( floor( $start / intval(10) ) + 1 ), ceil( $total_replies / intval(10) )),

				'REPLY_IMG' 	=> $reply_img,

				'L_AUTHOR' 			=> $lang['Author'],
				'POSTER_STATUS'		=> $poster_status,
				'L_MESSAGE' 		=> $lang['Message'],
				'L_POSTED' 			=> $lang['Posted'],
				
				'L_POST_SUBJECT' 		=> $lang['gb_title'],
				'L_POST_REPLY_TOPIC' 	=> $reply_alt,
				'L_GOTO_PAGE'			=> $lang['Goto_page'],

				'U_POST_REPLY_TOPIC' 	=> $reply_topic_url)
			);
			$template->assign_vars(array(
				'PAGINATION' 	=> $pagination,
				'PAGE_NUMBER' 	=> sprintf($lang['Page_of'], ( floor( $start / intval(10) ) + 1 ), ceil( $total_replies / intval(10) )),
				'REPLY_IMG' 	=> $reply_img,

				'U_POST_REPLY_TOPIC' 	=> $reply_topic_url)
			);


			$post_nr = 0;
			if($start != 0)	{
				$post_nr += (int)$start;
			}
			for($i = 0; $i < $total_posts; $i++)
			{
				$post_nr++;

				$poster_id = $postrow[$i]['poster_id'];
				$poster = ( $poster_id == ANONYMOUS ) ? (!empty($postrow[$i]['user_guest_name'])) ? $postrow[$i]['user_guest_name']."(".$lang['Guest'].")": $lang['Guest'] : $postrow[$i]['username'];
				

				$post_date = create_date($board_config['default_dateformat'], $postrow[$i]['gb_time'], $board_config['board_timezone']);

				$poster_posts = ( $postrow[$i]['user_id'] != ANONYMOUS ) ? $lang['Posts'] . '[' . $postrow[$i]['user_posts'].']' : '';

				$poster_from = ( $postrow[$i]['user_from'] && $postrow[$i]['user_id'] != ANONYMOUS ) ? $lang['Location'] . ': ' . $postrow[$i]['user_from'] : '';
				$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : $lang['Guest'] ) : '<a href="' . append_sid("profile.$phpEx?mode=guestbook&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '" style="color:#000000">' . $postrow[$i]['username'] . '</a>';

				if ( $postrow[$i]['user_level'] == ADMIN )
				{
					$poster_status = '<b>管</b>';
				} 
				elseif ( $postrow[$i]['user_level'] == MOD ) 
				{
					$poster_status = '<b>版</b>';
				}
				else 
				{
					$poster_status = '';
				}
				
				if ( $poster_id == ANONYMOUS && $postrow[$i]['post_username'] != '' )
				{
					$poster = $postrow[$i]['post_username'];
					$poster_rank = $lang['Guest'];
				}

				$temp_url = '';

				if ( $poster_id != ANONYMOUS )
				{
					$temp_url = append_sid("profile.$phpEx?mode=guestbook&amp;" . POST_USERS_URL . "=$poster_id");
				    $poster = '<a href="' . $temp_url . '">'.$poster.'</a>';

					$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=$poster_id");
					$pm_img = '<a href="' . $temp_url . '">发信息</a>|';

					if ( !empty($postrow[$i]['user_viewemail']) || $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD )
					{
						$email_uri = ( $board_config['board_email_form'] ) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL .'=' . $poster_id) : 'mailto:' . $postrow[$i]['user_email'];

						$email_img = '<a href="' . $email_uri . '">E-mail</a>';
					}
					else
					{
						$email_img = '';
						$email = '';
					}

					$www_img = ( $postrow[$i]['user_website'] ) ? '<a href="' . $postrow[$i]['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0" /></a>' : '';
					$www = ( $postrow[$i]['user_website'] ) ? '<a href="' . $postrow[$i]['user_website'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';

					if ( !empty($postrow[$i]['user_icq']) )
					{
						$icq_status_img = '<a href="http://wwp.icq.com/' . $postrow[$i]['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $postrow[$i]['user_icq'] . '&img=5" width="18" height="18" border="0" /></a>';
						$icq_img = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $postrow[$i]['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0" /></a>';
						$icq =  '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $postrow[$i]['user_icq'] . '">' . $lang['ICQ'] . '</a>';
					}
					else
					{
						$icq_status_img = '';
						$icq_img = '';
						$icq = '';
					}
				}
				else
				{
					$profile_img = '';
					$profile = '';
					$pm_img = '';
					$pm = '';
					$email_img = '';
					$email = '';
					$www_img = '';
					$www = '';
					$icq_status_img = '';
					$icq_img = '';
					$icq = '';
					$aim_img = '';
					$aim = '';
					$msn_img = '';
					$msn = '';
					$yim_img = '';
					$yim = '';
				}

				$temp_url = $this->append_sid("gb=quote&amp;gb_id=" . $postrow[$i]['gb_id']);
				$quote_img = '<a href="' . $temp_url . '">引用</a>|';

				$temp_url = append_sid("search.$phpEx?search_author=" . urlencode($postrow[$i]['username']) . "&amp;showresults=posts");
				$search_img = '<a href="' . $temp_url . '">搜索</a>';

				if (($poster_id != ANONYMOUS && $userdata['user_id'] == $poster_id) || $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_id'] == $this->uid)
		            {
		               $temp_url = $this->append_sid("gb=edit&amp;gb_id=".$postrow[$i]['gb_id']);
		               $edit_img = '<a href="' . $temp_url . '">编辑</a>|';
		            }
		            else
		            {
		               $edit_img = '';
		               $edit = '';
		            }

		            if ( $userdata['user_id'] == $postrow['poster_id'] || $userdata['user_level'] == ADMIN || $userdata['user_level'] == MOD || $userdata['user_id'] == $this->uid)
		            {
		               $temp_url = $this->append_sid("gb=delete&amp;gb_id=" . $postrow[$i]['gb_id']);
		               $delpost_img = '<a href="' . $temp_url . '">删除</a>';
		            }
		            else
		            {
		               $delpost_img = '';
		               $delpost = '';
		            }

				$post_title = ( $postrow[$i]['title'] != '' ) ? $postrow[$i]['title'] : '';

				$message = stripslashes($postrow[$i]['message']);
				$bbcode_uid = $postrow[$i]['bbcode'];

				$user_sig = ( $postrow[$i]['enable_sig'] && $postrow[$i]['user_sig'] != '' && $board_config['allow_sig'] ) ? $postrow[$i]['user_sig'] : '';
				$user_sig_bbcode_uid = $postrow[$i]['user_sig_bbcode_uid'];
				
				if ( $user_sig != '' && $user_sig_bbcode_uid != '' )
				{
					$user_sig = ( $board_config['allow_bbcode'] ) ? bbencode_second_pass($user_sig, $user_sig_bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $user_sig);
				}

				if ( $bbcode_uid != '' )
				{
					$message = ( $board_config['allow_bbcode'] ) ? bbencode_second_pass($message, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);
					$post_title = ( $board_config['allow_bbcode'] ) ? bbencode_second_pass($post_title, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $post_title);
				}

				if ( $user_sig != '' )
				{
					$user_sig = make_clickable($user_sig);
				}
				$message = make_clickable($message);

				if ( $postrow[$i]['user_allowsmile'] && $user_sig != '' )
				{
					$user_sig = smilies_pass($user_sig);
				}
				$message = smilies_pass($message);
				$post_title = smilies_pass($post_title);

				if ( $user_sig != '' )
				{
					$user_sig = '<br />_________________<br />' . str_replace("\n", "\n<br />\n", $user_sig);
				}

				$message = str_replace("\n", "\n<br />\n", $message);
				
				$template->assign_block_vars('main.postrow', array(
					'U_POST_ID' 	=> $postrow[$i]['gb_id'],
					'ROW_COLOR' 	=> '#' . $row_color,
					'ROW_CLASS' 	=> $row_class,
					'POSTER_NAME' 	=> $poster,
					'POSTER_RANK' 	=> $poster_rank,
					'RANK_IMAGE' 	=> $rank_image,
					
					'POSTER_JOINED' 	=> $poster_joined,
					'POSTER_POSTS' 		=> $poster_posts,
					'POSTER_STATUS' 	=> $poster_status,
					'POSTER_FROM' 		=> $poster_from,
					'POSTER_AVATAR' 	=> $poster_avatar,
					'POST_DATE' 		=> $post_date,
					'POST_SUBJECT' 		=> $post_title,
					'MESSAGE' 			=> $message,
					'SIGNATURE' 		=> $user_sig,

					'PROFILE_IMG' 	=> $profile_img,
					'PROFILE' 		=> $profile,
					'SEARCH_IMG' 	=> $search_img,
					'SEARCH' 		=> $search,
					'PM_IMG' 		=> $pm_img,
					'PM' 			=> $pm,
					'EMAIL_IMG' 	=> $email_img,
					'EMAIL' 		=> $email,
					'WWW_IMG' 		=> $www_img,
					'WWW' 			=> $www,
					
					'ICQ_STATUS_IMG' 	=> $icq_status_img,
					'ICQ_IMG' 			=> $icq_img,
					'ICQ' 				=> $icq,
					'AIM_IMG' 			=> $aim_img,
					'AIM' 				=> $aim,
					'MSN_IMG' 			=> $msn_img,
					'MSN' 				=> $msn,
					'YIM_IMG' 			=> $yim_img,
					'YIM'			 	=> $yim,
					'EDIT_IMG' 			=> $edit_img,
					'EDIT' 				=> $edit,
					'QUOTE_IMG' 		=> $quote_img,
					'QUOTE' 			=> $quote,
					'DELETE_IMG' 		=> $delpost_img,
					'DELETE' 			=> $delpost,
					'NUMBER' 			=> $post_nr)
					);
			}
		}
		
		include_once($phpbb_root_path."/includes/functions_post.".$phpEx);

		$quick_valid = true;

		if($board_config['gb_posts'] > 0 && $userdata['user_posts'] <= $board_config['gb_posts'])
		{
			$quick_valid = false;
		}

		if($board_config['gb_quick'] && $quick_valid)
		{
			$template->assign_block_vars('quick',array());
		}

		if(!$userdata['session_logged_in'])
		{
			$template->assign_block_vars('quick.username',array());
		}
		
		$action = $this->append_sid("gb=post");
		
		$template->assign_vars(array(
			'L_POST_QUICK' 		=> $lang['gb_quick_reply'],
			'L_GB_POST' 		=> $lang['gb_post2'],
			'L_TITLE' 			=> $lang['gb_title'],
			'L_MESSAGE_BODY' 	=> $lang['Message_body'],
			'L_SUBMIT' 			=> $lang['Submit'],
			'L_USERNAME' 		=> $lang['Username'],
			'U_PROFILE' 		=> append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '=' . $this->uid),

			'S_POST_ACTION' 	=> $action
			)
		);
		$template->assign_var_from_handle('GUESTBOOK', 'gb_body');
	}
	
	/**
	* 表单处理
	**/
	function post($mode)
	{
		global $board_config, $userdata, $lang, $HTTP_POST_VARS, $phpbb_root_path, $phpEx, $db;
		global $HTTP_GET_VARS, $unhtml_specialchars_replace, $unhtml_specialchars_match, $html_entities_match, $html_entities_replace;
		
		if($board_config['allow_guests_gb'] == 0 && !$userdata['session_logged_in'])
		{
			message_die(GENERAL_MESSAGE,sprintf($lang['gb_no_per'],$lang['post_pro']));
		}
		elseif($board_config['gb_posts'] > 0 && $userdata['user_posts'] <= $board_config['gb_posts'] && $userdata['user_id'] != ANONYMOUS)
		{
			message_die(GENERAL_MESSAGE,sprintf($lang['gb_posts_not'],$board_config['gb_posts']));
		}
		if(isset($HTTP_POST_VARS['message']))
		{
			$me = $HTTP_POST_VARS['message'];
			$ti = $HTTP_POST_VARS['subject'];
			include_once($phpbb_root_path."/includes/bbcode.".$phpEx);
			include_once($phpbb_root_path."/includes/functions_post.".$phpEx);

			$bbcode = make_bbcode_uid();
			$me = prepare_message($me,$board_config['allow_html'],true,true,$bbcode);
			$ti = prepare_message($ti,false,true,true,$bbcode);//No HTML in titles. BBcode and smilies are allowed.
			$err = false;
			$errmsg = array();
			if(empty($me))
			{
				$errmsg[] = $lang['gb_no_me'];
				$err = true;
			}
			if(!$userdata['session_logged_in'])
			{
				if(!empty($HTTP_POST_VARS['username']))
				{
					$username = phpbb_clean_username($HTTP_POST_VARS['username']);
				}
				else
				{
					$username = '';
				}
			}
			else
			{
				$username = '';
			}
			if($err)
			{
				$id = intval($HTTP_GET_VARS['gb_id']);

				$action = $this->append_sid("gb=$mode&amp;id=$id");
				$this->post_table($me,$ti,$action,$username,$errmsg);
				return;
			}

			$pid = $userdata['user_id'];
			if($mode != 'edit')
			{

				$sql = "INSERT INTO ".PROFILE_GUESTBOOK_TABLE." (user_id,poster_id,bbcode,title,message,gb_time,user_guest_name) VALUES
				(".$this->uid.",$pid,'$bbcode','$ti','$me','".time()."','$username');";
			}
			else
			{
				$id = intval($HTTP_GET_VARS['gb_id']);
				if(empty($id))
				{
					message_die(GENERAL_ERROR,$lang['gb_no_id'],"",__LINE__,__FILE__);
				}
				$sql = "UPDATE ".PROFILE_GUESTBOOK_TABLE." SET
				bbcode = '$bbcode', title = '$ti', message = '$me' WHERE gb_id = $id";
			}
			$result = $db->sql_query($sql);
			if(!$result)
			{
				message_die(GENERAL_ERROR,"Could not insert or update user guestbook!","",__LINE__,__FILE__,$sql);
			}

			$id = $db->sql_nextid();
			$msg = '<br />点击 <a href="' . $this->append_sid("gb=view") . '#' . $id . '">'.$lang['back_pro'] . '</a> 到留言板！';	

			if($mode == 'edit')
			{
				message_die(GENERAL_MESSAGE,$lang['gb_edit'].$msg);
			}
			else
			{
				$this->email($id);
				message_die(GENERAL_MESSAGE,$lang['gb_post'].$msg);
			}
		}
		else
		{
			if($mode == 'edit')
			{
				$id = intval($HTTP_GET_VARS['gb_id']);
				if(empty($id))
				{
					message_die(GENERAL_ERROR,$lang['gb_no_id'],"",__LINE__,__FILE__);
				}
				$action = $this->append_sid("gb=edit&amp;gb_id=" . $id);
				$sql = "SELECT * FROM ".PROFILE_GUESTBOOK_TABLE." WHERE gb_id = $id";;
				$r = $db->sql_query($sql);
				if(!$r)
				{
					message_die(GENERAL_ERROR,"Could not select edit information!",__LINE__,__FILE__,$sql);
				}
				$row = $db->sql_fetchrow($r);
				if($userdata['user_level'] != ADMIN && $userdata['user_level'] != MOD && $userdata['user_id'] != $this->uid && $row['poster_id'] != $userdata['user_id'])
				{
					message_die(GENERAL_MESSAGE,sprintf($lang['gb_no_per'],$lang['edit_pro']));
				}
				$me = str_replace(':'.$row['bbcode'],'',$row['message']);
				$ti = str_replace(':'.$row['bbcode'],'',$row['title']);

			}
			elseif($mode == 'quote')
			{
				$action = $this->append_sid("gb=post");
				$id = intval($HTTP_GET_VARS['gb_id']);
				if(empty($id)){
					message_die(GENERAL_ERROR,$lang['gb_no_id'],"",__LINE__,__FILE__);
				}
				$sql = "SELECT * FROM ".PROFILE_GUESTBOOK_TABLE." g, ".USERS_TABLE." u WHERE g.gb_id = $id AND u.user_id = g.poster_id";
				$result = $db->sql_query($sql);
				if(!$result)
				{
					message_die(GENERAL_ERROR,"Could not select edit information!",__LINE__,__FILE__,$sql);
				}
				$row = $db->sql_fetchrow($result);
				$me = str_replace(':'.$row['bbcode'],'',$row['message']);
				$ti = str_replace(':'.$row['bbcode'],'',$row['title']);
				if($row['user_id'] != ANONYMOUS)
				{
					$me = '[quote="' . $row['username'] . '"]' . $me . '[/quote]';
				}
				else
				{
					if(!empty($row['user_guest_name']))
					{
						$me = '[quote="' . $row['user_guest_name'] . '"]' . $me . '[/quote]';
					}
					else
					{
						$me = '[quote]' . $me . '[/quote]';
					}
				}
				$ti = $lang['re'] . ":".$ti;
			}
			else
			{
				$action = $this->append_sid("gb=post");
				$me = '';
				$ti = '';
			}
			$this->post_table($me,$ti,$action);
			return;
		}
	}
	
	/**
	* 邮件
	**/
	function email($post_id)
	{
		global $db,$board_config,$phpbb_root_path,$phpEx,$lang;

		if($this->userdata['user_email_new_gb'] == 0)
		{
			return;
		}
		$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
		$script_name = ( $script_name != '' ) ? $script_name . '/profile.'.$phpEx : 'profile.'.$phpEx;
		$server_name = trim($board_config['server_name']);
		$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
		$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
		include_once($phpbb_root_path . 'includes/emailer.'.$phpEx);
		$emailer = new emailer($board_config['smtp_delivery']);

		$emailer->from($board_config['board_email']);
		$emailer->replyto($board_config['board_email']);

		$emailer->use_template('guestbook', $this->userdata['user_lang']);
		$emailer->email_address($this->userdata['user_email']);
		$emailer->set_subject($lang['gb_email']);

		$emailer->assign_vars(array(
			"U_ACT" 		=> $server_protocol . $server_name . $server_port . $script_name."?mode=guestbook&".POST_USERS_URL."=".$this->userdata['user_id'] . "#" . $post_id,
			"USERNAME" 		=> $this->userdata['username'],
			'EMAIL_SIG' 	=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),
		));
		$emailer->send();
		$emailer->reset();
		return;
	}

	/**
	* 编辑、发表页面
	**/
	function post_table($me,$ti,$action,$username = '',$errmsg = array())
	{
		global $phpbb_root_path, $phpEx,$template, $mode, $userdata, $lang, $db;
		global $unhtml_specialchars_replace, $unhtml_specialchars_match, $html_entities_match, $html_entities_replace;


		include_once($phpbb_root_path."/includes/bbcode.".$phpEx);
		include_once($phpbb_root_path."/includes/functions_post.".$phpEx);
		$template->set_filenames(array(
			'body' => 'gb_post.tpl')
		);
		if(count($errmsg) > 0)
		{
			$template->set_filenames(array(
				'reg_header' => 'error_body.tpl')
			);
			for($i = 0;$i<count($errmsg);$i++)
			{
				$error_msg = $errmsg[$i];
			}
			$template->assign_vars(array(
				'ERROR_MESSAGE' => $error_msg)
			);
			$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
		}
		if(!$userdata['session_logged_in'] && $mode != 'edit')
		{
			$template->assign_block_vars('username',array(
				'USERNAME' => $username
			));
		}
		$me = unprepare_message($me);
		$ti = unprepare_message($ti);
		$template->assign_vars(array(
			'TITLE' 		=> stripslashes($ti),
			'MESSAGE' 		=> stripslashes($me),

			'L_GB_POST' 		=> $lang['gb_post2'],
			'L_TITLE' 			=> $lang['gb_title'],
			'L_MESSAGE_BODY' 	=> $lang['Message_body'],
			'L_SUBMIT' 			=> $lang['Submit'],
			'L_USERNAME' 		=> $lang['Username'],
			'L_EMPTY_MESSAGE' 	=> $lang['Empty_message'],

			'U_PROFILE' 		=> append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '=' . $this->uid),
			'U_GUESTBOOK'		=> append_sid("profile.$phpEx?mode=guestbook&amp;" . POST_USERS_URL . '=' . $this->uid),
			
			'S_POST_ACTION' 	=> $action)
		);
		$template->pparse('body');
		include_once($phpbb_root_path."/includes/page_tail.".$phpEx);
	}
	
	/**
	* 清空留言板
	**/
	function deleteall()
	{
		global $db,$lang,$phpEx;
		
		$sql = "DELETE FROM " . PROFILE_GUESTBOOK_TABLE . " WHERE user_id = " . $this->uid;
		
		if(!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR,"Could not delete guestbook posts!","",__LINE__,__FILE__,$sql);
		}
		
		$msg = '<br /><a href="' . $this->append_sid("gb=view") . '">' . $lang['back_pro'] . '</a>';
		
		message_die(GENERAL_MESSAGE,$lang['gb_all_del'] . $msg);
	}
	
	/**
	* 删除留言
	**/
	function delete()
	{
		global $lang,$HTTP_POST_VARS,$db,$phpEx;
		$id = intval($HTTP_POST_VARS['gb_id']);
		if(empty($id))
		{
			message_die(GENERAL_ERROR,$lang['gb_no_id'],"",__LINE__,__FILE__);
		}
		$sql = "DELETE FROM ".PROFILE_GUESTBOOK_TABLE." WHERE user_id = ".$this->uid." AND gb_id = $id";
		if(!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR,"Could not delete guestbook posts!","",__LINE__,__FILE__,$sql);
		}
		$msg = '<br /><a href="' . $this->append_sid("gb=view") . '">'.$lang['back_pro'].'</a>';
		message_die(GENERAL_MESSAGE,$lang['gb_del'] . $msg);
	}
	
	/**
	* append_sid()
	**/
	function append_sid($url)
	{

		$url = $this->url_intern . '&amp;' . $url;
		$url = append_sid($url);
		return $url;
	}
	
	/**
	* 版本检测
	**/
	function version_check()
	{
		global $phpbb_root_path,$lang,$board_config,$phpEx;
		$text = "";
		if(is_writable($phpbb_root_path."/cache/"))
		{
			if(file_exists($phpbb_root_path."/cache/profilemod.".$phpEx))
			{
				include($phpbb_root_path."/cache/profilemod.".$phpEx);
				define('VERSION_CHECK_DELAY', 86400);
				$now = time();
				$version_check_delay = intval($last_check);
				$check = empty($version_check_delay) || (($now - $version_check_delay) > VERSION_CHECK_DELAY);
				if(!$check){
				 	$this->url = $url;
				}
			}
			else
			{
				$check = true;
			}
		}
		
		if($check)
		{
			$current_version = explode('.', $this->version);
			$minor_revision = intval( $current_version[2]);

			$errno = 0;
			$errstr = $version_info = '';

			if ($fsock = @fsockopen('www.paulscripts.nl', 80, $errno, $errstr))
			{
				@fputs($fsock, "GET /profile.txt HTTP/1.1\r\n");
				@fputs($fsock, "HOST: www.paulscripts.nl\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");

				$get_info = false;
				while (!@feof($fsock))
				{
					if ($get_info)
					{
						$version_info .= @fread($fsock, 1024);
					}
					else
					{
						if (@fgets($fsock, 1024) == "\r\n")
						{
							$get_info = true;
						}
					}
				}
				@fclose($fsock);

				$version_info = explode("\n", $version_info);
				$latest_head_revision = intval( $version_info[0]);
				$latest_minor_revision = intval($version_info[2]);
				$latest_version = intval($version_info[0]) . '.' . intval($version_info[1]) . '.' . intval($version_info[2]);
				$this->url = $version_info[3];

				if ($current_version[0] == intval($version_info[0]) && $current_version[1] == intval($version_info[1]) && $current_version[2] >= intval($version_info[2]))
				{
					$version_info = sprintf($lang['ok_check'],$this->version,$this->url,$this->url);
				}
				else
				{
					$version_info = sprintf($lang['not_ok_check'],$this->version,$latest_version,$this->url,$this->url);
				}
			}
			else
			{
				if ($errstr)
				{
					$version_info = sprintf($lang['gb_error_check'], $errstr);
				}
				else
				{
					$version_info = $lang['Socket_functions_disabled'];
				}
			}
			$text .= $version_info;
			if(is_writable($phpbb_root_path."/cache/")){
				@unlink($phpbb_root_path."/cache/profilemod.".$phpEx);
				$o = fopen($phpbb_root_path."/cache/profilemod.".$phpEx,"w");
				if(!$o)
				{
					message_die(GENERAL_ERROR,"Could not open $phpbb_root_path/cache/profilemod.".$phpEx."!","",__LINE__,__FILE__);
				}
				fwrite($o,"<"."?php\n\$last_check = ".time().";\n\$url = '".$this->url."';\n?".">");
				fclose($o);
			}
		}
		else
		{
			$text .= sprintf($lang['not_check'],$this->version,$this->url,$this->url);
		}
		return $text;
	}
}

?>