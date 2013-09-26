<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_ALBUM_CAT_TITLE}</div>
<span class="genmed">{L_ALBUM_CAT_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<div class="catSides">{L_ALBUM_CAT_TITLE}</div>
	<!-- BEGIN catrow -->
		<div class="{catrow.COLOR}">
			<b>名称</b>: {catrow.TITLE}<br/>
			<b>描述</b>: {catrow.DESC}<br/>
			<a href="{catrow.S_MOVE_UP}">{L_MOVE_UP}</a>, <a href="{catrow.S_MOVE_DOWN}">{L_MOVE_DOWN}</a>, <a href="{catrow.S_EDIT_ACTION}">{L_EDIT}</a>, <a href="{catrow.S_DELETE_ACTION}">{L_DELETE}</a>
		</div>
	<!-- END catrow -->
	<input type="hidden" value="new" name="mode" />
	<input name="submit" type="submit" value="{L_CREATE_CATEGORY}" class="subbutton">
</form>