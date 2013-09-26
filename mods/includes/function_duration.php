<?php
/****************
*	彩票游戏函数
*	爱疯的云
*****************/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}


if ( !(function_exists(duration)) )
{
	/**
	* 计时函数
	**/
	function duration($seconds)
	{
		global $lang;

		if ( $seconds > 86399 )
		{
			$days = floor($seconds / 86400);
			$seconds = ($seconds - ($days * 86400));
			$string .= ( $days > 1 ) ? $days .' ' . $lang['lottery_days'] . ', ' : $days .' ' . $lang['lottery_day'] . ', ';
		}
		if ( $seconds > 3599 )
		{
			$hours = floor($seconds / 3600);

			if ( $seconds != 0 )
			{
				$string .= ( $hours > 1 ) ? $hours .' ' . $lang['lottery_hours'] . ', ' : $hours .' ' . $lang['lottery_hour'] . ', ';
			}

			$seconds = ( $days > 0 ) ? 0 : ( $seconds - ($hours * 3600) );
		}
		if ( $seconds > 59 )
		{
			$minutes = floor($seconds / 60);
			if ( $seconds != 0 )
			{
				$string .= ( $minutes > 1) ? $minutes .' ' . $lang['lottery_minutes'] . ', ' : $minutes .' ' . $lang['lottery_minute'] . ', ';
			}

			$seconds = ( $hours > 0 ) ? 0 : ($seconds - ($minutes * 60));
		}
		if ( $seconds > 0 )
		{
			$string .= ( $seconds > 1 ) ? $seconds . ' ' . $lang['lottery_seconds'] . ', ' : $seconds . ' ' . $lang['lottery_second'] . ', ';
		}

		$string = substr($string, 0, -2);
		return $string;
	}
}
?>