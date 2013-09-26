<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;选择用户</div>
<span class="genmed">{L_USER_EXPLAIN}</span>
<form method="post" name="post" action="{S_USER_ACTION}">
	<div class="catSides">
		<span class="cattitle">{L_USER_SELECT}</span>
	</div> 
	<div class="row1">
		请输入要管理的用户名:<br/>
		<input type="text" name="username" maxlength="50"/>
		<input type="hidden" name="mode" value="edit" />
		{S_HIDDEN_FIELDS}
	</div>
	<input type="submit" name="submituser" value="{L_LOOK_UP}" />
</form>