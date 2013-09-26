<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ALBUM_LISTS}">分类列表</a>&gt;{L_CAT_DELETE}</div>
<span class="genmed">{L_CAT_DELETE_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<div class="catSides">{L_CAT_DELETE}</div>
	<div class="row1">
		{L_CAT_TITLE}:<br/>
		<b>{S_CAT_TITLE}</b>
	</div>
	<div class="row1">
		{L_MOVE_CONTENTS}<br/>
		{S_SELECT_TO}
	</div>
	<input type="hidden" name="mode" value="delete" />
	<input type="submit" name="submit" value="{L_MOVE_DELETE}" class="subbutton" />
</form>