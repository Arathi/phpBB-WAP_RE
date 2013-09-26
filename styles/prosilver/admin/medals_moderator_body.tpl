<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_MEDAL_LISTS}">奖列表</a>&gt;{L_MEDAL_MOD_TITLE}</div>
<span class="genmed">{L_MEDAL_MOD_EXPLAIN}</span>
<form method="post" name="post" action="{S_MEDAL_ACTION}">
	<div class="catSides">{MEDAL_NAME}</div>
	<div class="row1">{L_MEDAL_DESCRIPTION}: {MEDAL_DESCRIPTION}</div>
	<div class="row1">{L_MEDAL_MOD}: {MEDAL_MODERATORS}</div>
	<div class="catSides">{L_MOD_USER}</div>
	<div class="row1">
		{L_USERNAME}:<br/>
		<input type="text" name="username" maxlength="50" /><input type="hidden" name="mode" value="edit" />{S_HIDDEN_FIELDS}
	</div>
	<div class="catSides">{L_UNMOD_USER}</div>
	<div class="row1">
		{L_USERNAME}:<br/>
		<span class="genmed">{L_UNMOD_USER_EXPLAIN}</span><br/>
		{S_UNMOD_USERLIST_SELECT}
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>