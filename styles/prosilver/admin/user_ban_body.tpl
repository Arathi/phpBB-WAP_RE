<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_BAN_TITLE}</div>
<span class="genmed">{L_BAN_EXPLAIN}</span>
<form method="post" name="post" action="{S_BANLIST_ACTION}">
	<div class="catSides">{L_BAN_USER}</div>
	<div class="row1">
		{L_USERNAME}：<br />
		{S_HIDDEN_FIELDS}
		<input type="text" name="username" maxlength="50"/>
		<input type="hidden" name="mode" value="edit" />
	</div>
	<div class="catSides">{L_UNBAN_USER}</div>
	<div class="row1">
		{S_UNBAN_USERLIST_SELECT}
	</div>
	<div class="catSides">{L_BAN_IP}</div>
	<div class="row1">
		<span class="genmed">{L_BAN_IP_EXPLAIN}</span><br />
		<input type="text" name="ban_ip"/>
	</div>
	<div class="catSides">{L_UNBAN_IP}</div>
	<div class="row1">
		{S_UNBAN_IPLIST_SELECT}
	</div>
	<div class="catSides">{L_BAN_EMAIL}</div>
	<div class="row1">
		<span class="genmed">{L_BAN_EMAIL_EXPLAIN}</span><br />
		<input type="text" name="ban_email"/>
	</div>
	<div class="catSides">{L_UNBAN_EMAIL}</div>
	<div class="row1">
		{S_UNBAN_EMAILLIST_SELECT}
	</div>
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>
<span class="genmed">{L_BAN_EXPLAIN_WARN}</span>