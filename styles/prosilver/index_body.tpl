{VERH}
{BAN_INFO}
{L_WHOSBIRTHDAY_TODAY}
{HOBOE}
<!-- BEGIN switch_user_logged_out -->
<div class="row1">您尚未(<a href="{U_LOGIN_LOGOUT}">登录</a>/<a href="{U_REGISTER}">注册</a>)</div>
<!-- END switch_user_logged_out -->
<!-- BEGIN switch_user_logged_in -->
	<div class="row1">
		<a href="{U_PRIVATEMSGS}">{PRIVATE_MESSAGE_INFO}</a>|<a href="{U_PROFILE}">我的地盘</a>|<a href="{U_LOGIN_LOGOUT}">注销登录</a>
	</div>
<!-- END switch_user_logged_in -->
<!-- BEGIN announcement -->
	<div class="catSides">
		 <span class="cattitle"> 网站公告</span>
	</div>
	<div class="row1">
		{announcement.ANNOUNCEMENT}
	</div>
<!-- END announcement -->
<div class="catSides">
  网站论坛</div>
<div class="row1">	
<a href="./ol/">工具</a>|<a href="{U_STAFF}">管理</a>|<a href="./naperstki.php">挖豆</a>|<a href="mods/lottery/index.php">彩票</a>|<a href="{U_SHOP}">商店</a>|<a href="mods/sign/index.php">签到</a>
</div>
<div class="row1">
<a href="{U_CHAT}">聊天</a>|<a href="{U_BANK}">银行</a>|<a href="./medals.php">勋章</a>|<a href="./mods/invite/index.php">邀请</a>|<a href="./memberlist.php">排行</a>|<a href="{U_SEARCH}">搜索</a>
</div>
<div class="row1">
<a href="{U_FORUM}">论坛</a>|<a href="{U_BANLIST}">监狱</a>|<a href="{U_BOOKMARKS}">书签</a>|<a href="{U_RULES}">规则</a>|<a href="{U_ALBUM}">相册</a>|<a href="{U_GROUP_CP}">小组</a>
</div>



<div class="catSides">
	 <span class="cattitle"> 论坛帖子[<a href="{U_NEW_TOPIC}"><font color="#ffffff">新</font></a>_<a href="{U_VERB_TOPIC}"><font color="#ffffff">动</font></a>_<a href="{U_HOT_TOPIC}"><font color="#ffffff">热</font></a>]</span>
</div>
<!-- BEGIN recent_topic_row -->
<div class="row1">
	{recent_topic_row.L_THEME_NUMBER}.<a href="{recent_topic_row.U_TITLE}">{recent_topic_row.L_TITLE}</a><br />
</div>
<!-- END recent_topic_row -->
<!-- BEGIN catrow -->


	<div class="catSides">
		{catrow.CAT_ICON}<a href="{catrow.U_VIEWCAT}" class="cattitle">{catrow.CAT_DESC}</a>
	</div>
	<!-- BEGIN forumrow -->
		<div class="forumline">
			<span class="genmed">
				{catrow.forumrow.FORUM_ICON}<a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a>[{catrow.forumrow.TOPICS}/{catrow.forumrow.POSTS}]
			</span>
		</div>
	<!-- END forumrow -->
<!-- END catrow -->
{INDEX_PAGE_BODY}
{NIZ}
<div class="catSides">
	 <span class="cattitle"><a href="http://zisuw.com/chat.php"><font color="#ffffff">聊天室动态</font></a></span>
</div>
<!-- BEGIN shoutrow -->
	<div class="row1">
		<b>{shoutrow.SHOUT_USERNAME}{shoutrow.POSTER_ONLINE_STATUS}说：</b>{shoutrow.SHOUT}[{shoutrow.TIME}]
	</div>
<!-- END shoutrow --> 
<form action="chat.php" method="post" name="post"> 
<div class="row1"> 
<textarea id="text" name="message" tabindex="3" style="width:99%"></textarea><br/> 
<input type="submit" name="submit" value="发表聊天内容" class="subbutton"/> 
</div> 
</form>
<div class="nav">简版|<a href="web/index.php">电脑版</a></div>
<!-- BEGIN switch_admin_link -->
	<div class="row1">[{ADMIN_LINK} == {EDIT_INDEX}]</div>
<!-- END switch_admin_link -->
<!-- BEGIN switch_modcp_link -->
	<div class="row1">[{MODCP_LINK}]</div>
<!-- END switch_modcp_link -->