<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_ALBUM_PERSONAL_TITLE}</div>
<span class="genmed">{L_ALBUM_PERSONAL_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<div class="catSides">{L_GROUP_CONTROL}</div>
		<!-- BEGIN grouprow -->
			<div class="row1">
				<b>{grouprow.GROUP_NAME}</b>：<br />
				<input name="private[]" type="checkbox" {grouprow.PRIVATE_CHECKED} value="{grouprow.GROUP_ID}" /> 可以查看用户相册
			</div>
		<!-- END grouprow -->
	<input name="submit" type="submit" value="{L_SUBMIT}" />
</form>