<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;{L_BAN_TITLE}</div>
<span class="genmed">{L_BAN_EXPLAIN}</span><br />
<span class="genmed">{L_BAN_EXPLAIN_WARN}</span>
<form method="post" name="post" action="{S_BANLIST_ACTION}">
	<div class="catSides">
		<span class="cattitle">{L_BAN_USER}</span>
	</div>
	<div class="row1">
		{L_USERNAME}:<br/>
		<input type="text" name="username" maxlength="50"/>
		<input type="hidden" name="mode" value="edit" />
		{S_HIDDEN_FIELDS}
	</div>
	<div class="row1">
		{L_UNBAN_USER}:<br />
		{S_UNBAN_USERLIST_SELECT}
	</div>
	<div class="row1">
		{L_BAN_IP}:<br/>
		<span class="genmed">{L_BAN_IP_EXPLAIN}</span><br/>
		<input type="text" name="ban_ip"/>
	</div>
	<div class="row1">
		{L_UNBAN_IP}:<br />
		{S_UNBAN_IPLIST_SELECT}
	</div>
	<div class="row1">
		{L_BAN_EMAIL}:<br/>
		<span class="genmed">{L_BAN_EMAIL_EXPLAIN}</span><br/>
		<input type="text" name="ban_email"/>
	</div>
	<div class="row1">
		{L_UNBAN_EMAIL}:<br />
		{S_UNBAN_EMAILLIST_SELECT}
	</div>
	<input type="submit" name="submit" value="{L_SUBMIT}" class="liteoption" />
</form>