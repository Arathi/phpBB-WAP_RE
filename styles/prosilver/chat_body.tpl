<div class="nav"><a href="{U_INDEX}">{L_INDEX}</a>&gt;聊天室</div>
{HOBOE}
{ERROR_BOX}
<div class="catSides">用户聊天室</div>
<div class="row1">点击<a href="{UPDATE}">这里</a>刷新聊天消息</div>

<!-- BEGIN switch_user_logged_out -->
	<div class="row1"><font color="red">您好，你还没有登录！请 <b><a href="{U_LOGIN}">登录</a></b> 后再发言！</font></div>
<!-- END switch_user_logged_in -->

<!-- BEGIN switch_user_logged_in -->
	<form action="{U_SHOUTBOX}" method="post" name="post">
		<div class="row1">
			<textarea id="text" name="message" tabindex="3" style="width:99%"></textarea><br />
			{S_HIDDEN_FORM_FIELDS}
			{SMILES_SELECT}
			<input type="submit" name="submit" value="发表" class="subbutton" />
		</div>
	</form>
<!-- END switch_user_logged_in -->

<!-- BEGIN shoutrow -->
	<div class="row1">
		<b>昵称:{shoutrow.SHOUT_USERNAME}</b>
		{shoutrow.POSTER_ONLINE_STATUS}<br />
		内容:{shoutrow.SHOUT}<br />
		时间:{shoutrow.TIME}<br />
		---------{shoutrow.DELETE}	
	</div>
<!-- END shoutrow -->
{PAGINATION}