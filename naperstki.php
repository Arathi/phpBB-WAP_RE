<?php
//adapt. KasP
//Оватар лососни тунца :D

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);

$page_title = '挖宝';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
if (isset($_GET['act'])) 
{
    $act = $_GET['act'];
} else {
    $act = 'index';
} 

$rand = mt_rand(100, 999);
echo '<div class="nav"><a href="index.php">首页</a>>挖豆</div>';
echo '<table width="100%" cellspacing="0" border="0" >';
echo '<div class="catSides" >';
echo '挖豆';
echo '</div>';
if ( $userdata['session_logged_in'] )
	{
    switch ($act):
    case "index":

        echo '<div class="row1"><img src="images/naperstki/1.png" alt="image" /></div>';
        echo '<div class="row1">你有红豆: ' .$userdata['user_points']. ' 红豆.</div>';
		 echo '<div class="row1">失败会扣取50个红豆.</div>';
        echo '<div class="row1">成功可以获得得500个红豆.</div>';
		echo '<div class="nav"><a href="naperstki.php?act=choice">我要挖豆</a></div>';

        break;
    case "choice":

        if (isset($_SESSION['naperstki'])) {
            $_SESSION['naperstki'] = "";
            unset($_SESSION['naperstki']);
        } 

        echo '<a href="naperstki.php?act=go&amp;thimble=1&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
        echo '<a href="naperstki.php?act=go&amp;thimble=2&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
		echo '<a href="naperstki.php?act=go&amp;thimble=3&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> <br/>';
		echo '<a href="naperstki.php?act=go&amp;thimble=4&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
        echo '<a href="naperstki.php?act=go&amp;thimble=5&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
        echo '<a href="naperstki.php?act=go&amp;thimble=6&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> <br/>';
		echo '<a href="naperstki.php?act=go&amp;thimble=7&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
        echo '<a href="naperstki.php?act=go&amp;thimble=8&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> ';
        echo '<a href="naperstki.php?act=go&amp;thimble=9&amp;rand=' . $rand . '"><img src="images/naperstki/2.png" alt="image" /></a> <br/>';

        echo '<div class="row1">请选择一块土地</div>';
		echo '<div class="row1">一共有的: ' .$userdata['user_points']. ' 红豆</div>';
        echo '<div class="pagination"><a href="naperstki.php?">返回</a></div>';
        break;
    case "go":

        $thimble = (int)$_GET['thimble'];
        if (!isset($_SESSION['naperstki'])) {
            $_SESSION['naperstki'] = 0;
        } 

        if ($userdata['user_points'] >= 50) {
            if ($_SESSION['naperstki'] < 9) {
                $_SESSION['naperstki']++;

                $rand_thimble = mt_rand(1, 9);

                if ($rand_thimble == 1) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> ';
                } 
				if ($rand_thimble == 2) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> ';
                }	
				if ($rand_thimble == 3) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> <br/>';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> <br/>';
                } 
                if ($rand_thimble == 4) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> ';
                } 
                if ($rand_thimble == 5) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" />';
                } 
				if ($rand_thimble == 6) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> <br/>';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> <br/>';
                } 
				if ($rand_thimble == 7) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> ';
                } 
                if ($rand_thimble == 8) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" />';
                } 
				if ($rand_thimble == 9) {
                    echo '<img src="images/naperstki/3.png" alt="image" /> ';
                } else {
                    echo '<img src="images/naperstki/2.png" alt="image" /> <br/>';
                } 
                if ($thimble == $rand_thimble)
				{
                   
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points+500 WHERE user_id=".$userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not update users table');
				}
		
                    echo '<div class="row1">恭喜你，你挖到了500红豆！</div>';
                }
                else {
                    $sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points-50 WHERE user_id=".$userdata['user_id'];
                    if ( !($result = $db->sql_query($sql)) )
                    {
                        message_die(GENERAL_ERROR, 'Could not update users table');
                    }
                    echo '<div class="row1">唉，什么也没挖到</div>';
                } 
            } 
            else {
				message_die(GENERAL_MESSEGE, '您必须选择一块土地');              
			} 

            echo '<div class="row1"><a href="naperstki.php?act=choice&amp;rand=' . $rand . '">继续玩</div>';
           
            echo '<div class="nav"><a href="naperstki.php?">返回</a></div>';
        }
		else 
		{
			message_die(GENERAL_MESSEGE, '你不可以挖，你没有足够的红豆');      
		} 
        break; 
    default:
       echo '- <a href="naperstki.php?">返回</a><br />';
        exit;
        endswitch;
    } 
else 
{
 echo '<div class="row1"><img src="images/naperstki/1.png" alt="image" /></div>';
 echo '<div class="nav">你还没有登录噢！<a href="login.php">登录</a><br />';
} 



?>