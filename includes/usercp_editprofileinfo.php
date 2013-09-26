<?php
/**************************************************
 *		usercp_editprofileinfo.php
 *		--------------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		说明：编辑个人资料
 **************************************************/

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
	isset($HTTP_POST_VARS['submit']))
{
	include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);
	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_post.'.$phpEx);

	$strip_var_list = array('icq' => 'icq', 'number' => 'number', 'aim' => 'aim', 'msn' => 'msn', 'yim' => 'yim', 'website' => 'website', 'location' => 'location', 'occupation' => 'occupation', 'interests' => 'interests', 'user_purse' => 'user_purse', 'signature' => 'signature');

	while( list($var, $param) = @each($strip_var_list) )
	{
		if ( !empty($HTTP_POST_VARS[$param]) )
		{
			$$var = trim(htmlspecialchars($HTTP_POST_VARS[$param]));
		}
	}

	$gender = ( isset($HTTP_POST_VARS['gender']) ) ? intval ($HTTP_POST_VARS['gender']) : 0;

	if (isset($HTTP_POST_VARS['birthday']) )
	{
		$birthday = intval ($HTTP_POST_VARS['birthday']);
		if ( $birthday != 999999 )
		{
			$b_day = realdate('j',$birthday); 
			$b_md = realdate('n',$birthday); 
			$b_year = realdate('Y',$birthday);
		}
	} 
	else
	{
		$b_day = ( isset($HTTP_POST_VARS['b_day']) ) ? intval ($HTTP_POST_VARS['b_day']) : 0;
		$b_md = ( isset($HTTP_POST_VARS['b_md']) ) ? intval ($HTTP_POST_VARS['b_md']) : 0;
		$b_year = ( isset($HTTP_POST_VARS['b_year']) ) ? intval ($HTTP_POST_VARS['b_year']) : 0;
		if ( $b_day && $b_md && $b_year )
		{
			$birthday = mkrealdate($b_day,$b_md,$b_year);
		}
		else
		{
			$birthday = 999999;
		}
	}

	validate_optional_fields($icq, $aim, $msn, $yim, $website, $location, $occupation, $interests, $signature);
}

if ( isset($HTTP_POST_VARS['submit']) )
{
	$user_id = intval($HTTP_POST_VARS['user_id']);
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Wrong_Profile'];
	}

	if ( $website != '' )
	{
		rawurlencode($website);
	}

	if ( $b_day || $b_md || $b_year ) 
	{
		$user_age = ( date('md') >= $b_md.(($b_day <= 9) ? '0':'').$b_day ) ? date('Y') - $b_year : date('Y') - $b_year - 1 ;
		if ( !checkdate($b_md,$b_day,$b_year) )
		{
			$error = TRUE;
			if( isset($error_msg) )
			{
				$error_msg .= "<br />";
			}
			$error_msg .= $lang['Wrong_birthday_format'];
		}
		else if ( $user_age > $board_config['max_user_age'] )
		{
			$error = TRUE;
			if( isset($error_msg) )
			{
				$error_msg .= "<br />";
			}
			$error_msg .= sprintf($lang['Birthday_to_high'],$board_config['max_user_age']);
		} 
		else if ( $user_age < $board_config['min_user_age'] )
		{
			$error = TRUE;
			if( isset($error_msg) )
			{
				$error_msg .= "<br />";
			}
			$error_msg .= sprintf($lang['Birthday_to_low'],$board_config['min_user_age']);
		} 
		else
		{
			$birthday = ( $error ) ? $birthday : mkrealdate($b_day, $b_md, $b_year);
			$next_birthday_greeting = ( date('md') < $b_md . ( ($b_day <= 9) ? '0' : '' ) . $b_day ) ? date('Y') : date('Y') + 1;
		}
	}
	else
	{
		if ($board_config['birthday_required'])
		{
			$error = TRUE;
			if( isset($error_msg) )
			{
				$error_msg .= "<br />";
			}
			$error_msg .= sprintf($lang['Birthday_require']);
		}
		$birthday = 999999;
		$next_birthday_greeting = 0;
	}

	if ( !$error )
	{
			$user_active = 1;
			$user_actkey = '';

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_icq = '" . str_replace("\'", "''", $icq) . "', user_purse = '" . str_replace("\'", "''", $user_purse) . "', user_number = '" . str_replace("\'", "''", $number) . "',user_website = '" . str_replace("\'", "''", $website) . "', user_occ = '" . str_replace("\'", "''", $occupation) . "', user_from = '" . str_replace("\'", "''", $location) . "', user_interests = '" . str_replace("\'", "''", $interests) . "', user_birthday = '$birthday', user_next_birthday_greeting = '$next_birthday_greeting', user_aim = '" . str_replace("\'", "''", $aim) . "', user_yim = '" . str_replace("\'", "''", $yim) . "', user_msnm = '" . str_replace("\'", "''", $msn) . "', user_gender = '$gender', user_sig = '" . str_replace("\'", "''", $signature) . "'
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
	$icq = stripslashes($icq);
	$user_purse = stripslashes($user_purse);
	$aim = stripslashes($aim);
	$msn = stripslashes($msn);
	$yim = stripslashes($yim);

	$website = stripslashes($website);
	$signature = stripslashes($signature);
	$location = stripslashes($location);
	$occupation = stripslashes($occupation);
	$interests = stripslashes($interests);
}
else
{
	$user_id = $userdata['user_id'];
	$user_purse = $userdata['user_purse'];
	$icq = $userdata['user_icq'];
	$aim = $userdata['user_aim'];
	$msn = $userdata['user_msnm'];
	$yim = $userdata['user_yim'];

	$website = $userdata['user_website'];
	$signature = $userdata['user_sig'];
	$location = $userdata['user_from'];
	$occupation = $userdata['user_occ'];
	$interests = $userdata['user_interests'];
	$number = $userdata['user_number'];
            $gender=$userdata['user_gender']; 
	$birthday = $userdata['user_birthday'];
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg = $lang['Wrong_Profile'];
	}

	include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

	if ( !isset($coppa) )
	{
		$coppa = FALSE;
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';

	$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
	$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';

switch ($gender) 
{ 
   case 1: $gender_male_checked="checked=\"checked\"";break; 
   case 2: $gender_female_checked="checked=\"checked\"";break; 
   default:$gender_no_specify_checked="checked=\"checked\""; 
}

if ( $birthday!=999999 )
{
	$b_day = realdate('j', $birthday);
	$b_md = realdate('n', $birthday);
	$b_year = realdate('Y', $birthday);
	$birthday = realdate($lang['Submit_date_format'], $birthday);
} else
{
	$b_day = '';
	$b_md = '';
	$b_year = '';
	$birthday = '';
}

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
		'body' => 'profile_add_info.tpl')
	);

	$s_b_day = $lang['Day'] . '<select name="b_day" size="1" class="gensmall"> 
		<option value="0">-</option> 
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
		<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
		<option value="19">19</option>
		<option value="20">20</option>
		<option value="21">21</option>
		<option value="22">22</option>
		<option value="23">23</option>
		<option value="24">24</option>
		<option value="25">25</option>
		<option value="26">26</option>
		<option value="27">27</option>
		<option value="28">28</option>
		<option value="29">29</option>
		<option value="30">30</option>
		<option value="31">31</option>
	  	</select><br/>';
	$s_b_md = $lang['Month'] . '<select name="b_md" size="1" class="gensmall"> 
     		<option value="0">-</option> 
		<option value="1">'.$lang['datetime']['January'].'</option>
		<option value="2">'.$lang['datetime']['February'].'</option>
		<option value="3">'.$lang['datetime']['March'].'&nbsp;</option>
		<option value="4">'.$lang['datetime']['April'].'</option>
		<option value="5">'.$lang['datetime']['May'].'</option>
		<option value="6">'.$lang['datetime']['June'].'</option>
		<option value="7">'.$lang['datetime']['July'].'</option>
		<option value="8">'.$lang['datetime']['August'].'</option>
		<option value="9">'.$lang['datetime']['September'].'</option>
		<option value="10">'.$lang['datetime']['October'].'</option>
		<option value="11">'.$lang['datetime']['November'].'</option>
		<option value="12">'.$lang['datetime']['December'].'</option>
		</select><br/>';
	$s_b_day= str_replace("value=\"".$b_day."\">", "value=\"".$b_day."\" SELECTED>" ,$s_b_day);
	$s_b_md = str_replace("value=\"".$b_md."\">", "value=\"".$b_md."\" SELECTED>" ,$s_b_md);
	$s_b_year = $lang['Year'] . '<input type="text" style="width: 50px" name="b_year" size="4" maxlength="4" value="' . $b_year . '" />'; 
	$i = 0;
	$s_birthday = '';
	for ($i=0; $i<=strlen($lang['Submit_date_format']); $i++)
	{
		switch ($lang['Submit_date_format'][$i])
		{
			case d:  $s_birthday .= $s_b_day;break;
			case m:  $s_birthday .= $s_b_md;break;
			case Y:  $s_birthday .= $s_b_year;break;
		}
	}

	$template->assign_vars(array(
		'YIM' => $yim,
		'ICQ' => $icq,
		'MSN' => $msn,
		'AIM' => $aim,
		'PURSE' => $user_purse,
		'OCCUPATION' => $occupation,
		'INTERESTS' => $interests,
		'NUMBER' => $number,
		'S_BIRTHDAY' => $s_birthday,
		'BIRTHDAY_REQUIRED' => ($board_config['birthday_required']) ? '*' : '',
		'LOCATION' => $location,
		'WEBSITE' => $website,
		'SIGNATURE' => str_replace('<br />', "\n", $signature),

		'LOCK_GENDER' =>($mode!='register') ? 'DISABLED':'', 
		'GENDER' => $gender, 
		'GENDER_NO_SPECIFY_CHECKED' => $gender_no_specify_checked, 
		'GENDER_MALE_CHECKED' => $gender_male_checked, 
		'GENDER_FEMALE_CHECKED' => $gender_female_checked, 

		'L_SUBMIT' => $lang['Submit'],
		'L_ICQ_NUMBER' => $lang['ICQ'],
		'L_MESSENGER' => $lang['MSNM'],
		'L_YAHOO' => $lang['YIM'],
		'L_WEBSITE' => $lang['Website'],
		'L_AIM' => $lang['AIM'],
		'L_LOCATION' => $lang['Location'],
		'L_OCCUPATION' => $lang['Occupation'],
		'L_YES' => $lang['Yes'],
		'L_NO' => $lang['No'],
		'L_INTERESTS' => $lang['Interests'],
		'L_NUMBER' => $lang['Number'],

		'L_GENDER' =>$lang['Gender'], 
		'L_GENDER_MALE' =>$lang['Male'], 
		'L_GENDER_FEMALE' =>$lang['Female'], 
		'L_GENDER_NOT_SPECIFY' =>$lang['No_gender_specify'], 

		'L_BIRTHDAY' => $lang['Birthday'],

		'S_HIDDEN_FIELDS' => $s_hidden_fields,
		'S_PROFILE_ACTION' => append_sid("profile.$phpEx"))
	);

if ( $board_config['pay_money'] )
{
	$template->assign_block_vars('pay_money', array() );
}

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>