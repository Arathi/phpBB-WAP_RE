{VERH}
{BAN_INFO}
{L_WHOSBIRTHDAY_TODAY}
{HOBOE}
<!-- BEGIN switch_user_logged_out -->
	<div class="navbar">您尚未(<a href="{U_LOGIN_LOGOUT}">登录</a>/<a href="{U_REGISTER}">注册</a>)</div>
<!-- END switch_user_logged_out -->
<!-- BEGIN switch_user_logged_in -->
	<div class="navbar">
		<a href="{U_PRIVATEMSGS}">{PRIVATE_MESSAGE_INFO}</a>|<a href="{U_PROFILE}">我的地盘</a>|<a href="{U_LOGIN_LOGOUT}">注销登录</a>
	</div>
<!-- END switch_user_logged_in -->
<!-- BEGIN announcement -->
	<div class="catSides">
		<span class="cattitle">网站公告</span>
	</div>
	<div class="row1">
		{announcement.ANNOUNCEMENT}
	</div>
<!-- END announcement -->
<div class="row1">
	<a href="{U_BANLIST}">黑名单</a>|<a href="{U_RULES}">FAQ</a>|<a href="{U_MEDALS}">奖项</a>|<a href="{U_STAFF}">管理员</a>
</div>
<div class="row1">
	<a href="{U_FORUM}">论坛</a>|<a href="{U_GROUP_CP}">团队</a>|<a href="{U_SEARCH}">搜索</a>
</div>
<div class="catSides" >
	<form action="search.php?mode=search_keywords" method="post">
		<input type="text" name="search_keywords" size="10"/>
		<input type="submit" value="搜本站"/>
	</form>
</div>
<div class="catSides" >论坛帖子[<a href="{U_NEW_TOPIC}">新</a>_<a href="{U_VERB_TOPIC}">动</a>_<a href="{U_HOT_TOPIC}">热</a>]</div>
<!-- BEGIN recent_topic_row -->
	<div class="row1">{recent_topic_row.L_THEME_NUMBER}.<a href="{recent_topic_row.U_TITLE}">{recent_topic_row.L_TITLE}</a></div>
<!-- END recent_topic_row -->
<!-- BEGIN empty_recent_topic_row -->
	<div class="row1">{EMPTY_SHOW}</div>
<!-- END empty_recent_topic_row -->
<!-- BEGIN catrow -->
	<div class="catSides" ><a href="{catrow.U_VIEWCAT}" class="cattitle">{catrow.CAT_DESC}</a></div>
	<!-- BEGIN forumrow -->
		<div class="forumline"><a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a>[{catrow.forumrow.TOPICS}/{catrow.forumrow.POSTS}]</div>
	<!-- END forumrow -->
<!-- END catrow -->
<div class="catSides" ><a href="./links/index.php">友情链接</a></div>
<div class="row1">
<a href="http://u.yicha.cn/union/search.jsp?site=2145956045&pageBegin=1&version=1">易查</a>=<a href="http://phpbb-wap.com">官方</a>=<a href="http://user.qzone.qq.com/53109774/infocenter">空间</a>=<a href="http://weibo.cn/phpbbwap">微博</a>=<a href="http://qiuqiu.90ng.com">球乐</a><br/>
<a href="http://kelink.com/link/gourl.aspx?id=2108_36048">柯林</a>=<a href="http://u.yicha.cn/union/search.jsp?site=2145956045&pageBegin=1&version=1">招链</a>=<a href="http://9acl.com">潮流</a>=<a href="http://wap.ai/link/gourl.aspx?id=1000_96363">娃派</a>=<a href="http://wapvy.cn/FLinkEnter.Asp?ID=2987">源码</a><br/>
<a href="http://phpbb-wap.ru">русский</a>|<a href="http://daivietpda.com/phpbbwap/">Việt Nam</a>
</div>
<div class="catSides">
	 <span class="cattitle"><a href="http://zisuw.com/chat.php"><font color=#ffffff>聊天室动态</font></a></span>
</div>
<!-- BEGIN shoutrow -->
	<div class="row1">
		<b>{shoutrow.SHOUT_USERNAME}{shoutrow.POSTER_ONLINE_STATUS}说：</b>{shoutrow.SHOUT}[{shoutrow.TIME}]
	</div>
<!-- END shoutrow --> 
<div class="navbar">简版|<a href="web/index.php">电脑版</a></div>
<!-- BEGIN switch_admin_link -->
	<div class="row1">[{ADMIN_LINK}]</div>
<!-- END switch_admin_link -->
<!-- BEGIN switch_modcp_link -->
	<div class="row1">[{MODCP_LINK}]</div>
<!-- END switch_modcp_link -->
{NIZ}