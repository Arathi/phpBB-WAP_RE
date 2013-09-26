<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;登录</div>
<form action="{S_LOGIN_ACTION}" method="post" target="_top">
	<div class="catSides">登录</div>
	<div class="row1">
		请选择登录方式:<br/>
		<select name="method">
			<option value="username">使用昵称登录</option>
			<option value="uid">使用ID登录</option>
			<option value="email">使用E-mail登录</option>
		</select><br />
		<input type="text" name="username" value="{USERNAME}" />
	</div>
	<div class="row1">
		{L_PASSWORD}:<br/>
		<input type="password" name="password" />
	</div>
	<input type="hidden" name="autologin" value="0" />
	{S_HIDDEN_FIELDS}
	<input class="subbutton" type="submit" name="login" value="{L_LOGIN}" />
</form>
<br/>
- <a href="{U_AUTOLOGIN}">自动登录</a><br/>
- <a href="{U_SEND_PASSWORD}">{L_SEND_PASSWORD}</a>