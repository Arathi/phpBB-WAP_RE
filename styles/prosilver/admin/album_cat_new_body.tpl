<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ALBUM_LISTS}">分类列表</a>&gt;{L_PANEL_TITLE}</div>
<span class="genmed">{L_ALBUM_CAT_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<div class="catSides">{L_PANEL_TITLE}</div>
	<div class="row1">
		{L_CAT_TITLE}:<br/>
		<input name="cat_title" type="text" value="{S_CAT_TITLE}" />
	</div>
	<div class="row1">
		{L_CAT_DESC}:<br/>
		<textarea name="cat_desc" rows="5" style="width: 99%;">{S_CAT_DESC}</textarea>
	</div>
	<div class="catSides">{L_CAT_PERMISSIONS}</div>
	<div class="row1">
		{L_VIEW_LEVEL}:<br/>
		<select name="cat_view_level">
			<option {VIEW_GUEST} value="{S_GUEST}">{L_GUEST}</option>
			<option {VIEW_REG} value="{S_USER}">{L_REG}</option>
			<option {VIEW_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {VIEW_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {VIEW_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_UPLOAD_LEVEL}:<br/>
		<select name="cat_upload_level">
			<option {UPLOAD_GUEST} value="{S_GUEST}">{L_GUEST}</option>
			<option {UPLOAD_REG} value="{S_USER}">{L_REG}</option>
			<option {UPLOAD_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {UPLOAD_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {UPLOAD_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_RATE_LEVEL}:<br/>
		<select name="cat_rate_level">
			<option {RATE_GUEST} value="{S_GUEST}">{L_GUEST}</option>
			<option {RATE_REG} value="{S_USER}">{L_REG}</option>
			<option {RATE_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {RATE_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {RATE_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_COMMENT_LEVEL}:<br/>
		<select name="cat_comment_level">
			<option {COMMENT_GUEST} value="{S_GUEST}">{L_GUEST}</option>
			<option {COMMENT_REG} value="{S_USER}">{L_REG}</option>
			<option {COMMENT_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {COMMENT_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {COMMENT_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_EDIT_LEVEL}:<br/>
		<select name="cat_edit_level">
			<option {EDIT_REG} value="{S_USER}">{L_REG}</option>
			<option {EDIT_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {EDIT_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {EDIT_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_DELETE_LEVEL}:<br/>
		<select name="cat_delete_level">
			<option {DELETE_REG} value="{S_USER}">{L_REG}</option>
			<option {DELETE_PRIVATE} value="{S_PRIVATE}">{L_PRIVATE}</option>
			<option {DELETE_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {DELETE_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<div class="row1">
		{L_PICS_APPROVAL}:<br/>
		<select name="cat_approval">
			<option {APPROVAL_DISABLED} value="{S_USER}">{L_DISABLED}</option>
			<option {APPROVAL_MOD} value="{S_MOD}">{L_MOD}</option>
			<option {APPROVAL_ADMIN} value="{S_ADMIN}">{L_ADMIN}</option>
		</select>
	</div>
	<input type="hidden" value="{S_MODE}" name="mode" />
	<input name="submit" type="submit" value="{L_PANEL_TITLE}" class="subbutton" />
</form>