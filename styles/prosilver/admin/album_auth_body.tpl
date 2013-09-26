<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ALBUM_AUTH_SELECT}">选择</a>&gt;{L_ALBUM_AUTH_TITLE}</div>
<span class="genmed">{L_ALBUM_AUTH_EXPLAIN}</span>
<form action="{S_ALBUM_ACTION}" method="post">
	<!-- BEGIN grouprow -->
		<div class="catSides">{grouprow.GROUP_NAME}</div>
		<div class="row1"><input name="view[]" type="checkbox" {grouprow.VIEW_CHECKED} value="{grouprow.GROUP_ID}" /> {L_VIEW}</div>
		<div class="row1"><input name="upload[]" type="checkbox" {grouprow.UPLOAD_CHECKED} value="{grouprow.GROUP_ID}" /> {L_UPLOAD}</div>
		<div class="row1"><input name="rate[]" type="checkbox" {grouprow.RATE_CHECKED} value="{grouprow.GROUP_ID}" /> {L_RATE}</div>
		<div class="row1"><input name="comment[]" type="checkbox" {grouprow.COMMENT_CHECKED} value="{grouprow.GROUP_ID}" /> {L_COMMENT}</div>
		<div class="row1"><input name="edit[]" type="checkbox" {grouprow.EDIT_CHECKED} value="{grouprow.GROUP_ID}" /> {L_EDIT}</div>
		<div class="row1"><input name="delete[]" type="checkbox" {grouprow.DELETE_CHECKED} value="{grouprow.GROUP_ID}" /> {L_DELETE}</div>
		<div class="row1"><input name="moderator[]" type="checkbox" {grouprow.MODERATOR_CHECKED} value="{grouprow.GROUP_ID}" /> {L_IS_MODERATOR}</div>
	<!-- END grouprow -->
	<input name="submit" type="submit" value="{L_SUBMIT}" />
</form>