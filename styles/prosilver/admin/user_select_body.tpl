<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_USER_TITLE}</div>
<span class="genmed">{L_USER_EXPLAIN}</span>
<form method="post" name="post" action="{S_USER_ACTION}">
	<div class="catSides">{L_USER_SELECT}</div>
	<div class="row1">
		请输入用户名:<br/>
		<input type="text" name="username" maxlength="50"/>
		<input type="hidden" name="mode" value="edit" />
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submituser" value="{L_LOOK_UP}" class="subbutton" />
</form>