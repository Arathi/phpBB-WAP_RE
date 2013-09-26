<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_STYLE_LISTS}">风格列表</a>&gt;编辑风格</div>
<form action="{S_PROFILE_ACTION}" method="post">
	<div class="catSides">编辑风格</div>
	<div class="row1">
		风格的名称:<br/>
		<input type="text" name="style_name" value="{STYLE_NAME}" />
	</div>
	<div class="row1">
		风格的路径:<br/>
		<input type="text" name="style_path" value="{STYLE_PATH}" />
	</div>
	{S_HIDDEN_FIELDS}
	<input class="subbutton" type="submit" value="{L_SUBMIT}" />
</form>