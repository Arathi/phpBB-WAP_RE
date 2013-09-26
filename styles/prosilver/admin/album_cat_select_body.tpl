<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;选择</div>
<span class="genmed">{L_ALBUM_AUTH_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<div class="catSides">{L_SELECT_CAT}</div>
	<div class="row1">
		<select name="cat_id">
			<!-- BEGIN catrow -->
				<option value="{catrow.CAT_ID}">{catrow.CAT_TITLE}</option>
			<!-- END catrow -->
		</select>
	</div>
	<input name="submit" type="submit" value="{L_LOOK_UP_CAT}" />
</form>