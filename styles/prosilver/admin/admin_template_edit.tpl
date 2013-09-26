<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;选择&gt;网页编辑</div>
<div class="catSides">{L_EDIT_TEMPLATE}</div>
<form action="{S_ACTION}" method="post">
	<div class="row1">{FILE_NAME}</div>
	<div class="row1">
		<textarea name="file_data" rows="30" style="width:99%" class="post">{FILE_DATA}</textarea>
	</div>
	{HIDDEN_THINGS}
	<input type="submit" name="{SUBMIT_NAME}" value="{L_SUBMIT}" class="mainoption" />
	<input type="reset" value="{L_RESET}" class="liteoption" />
</form>
