<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_SHADOW_TITLE}</div>
<span class="genmed">{L_SHADOW_EXPLAIN}</span><br/>
{ERROR_BOX}
<form method="post" name="shadow_list" action="{S_ATTACH_ACTION}">
<div class="catSides">附件列表1</div>
<div class="row_hard">
<span class="genmed">{L_EXPLAIN_FILE}</span>
</div>
<!-- BEGIN file_shadow_row -->
<div class="row1">
<input type="checkbox" name="attach_file_list[]" value="{file_shadow_row.ATTACH_ID}" /> {L_ATTACHMENT}：<a href="{file_shadow_row.U_ATTACHMENT}">{file_shadow_row.ATTACH_FILENAME}</a>
</div>
<!-- END file_shadow_row -->
<div class="catSides">附件列表2</div>
<div class="row_hard">
<span class="genmed">{L_EXPLAIN_ROW}</span>
</div>
<!-- BEGIN table_shadow_row -->
<div class="row1">
<input type="checkbox" name="attach_id_list[]" value="{table_shadow_row.ATTACH_ID}" /> {L_ATTACHMENT}：{table_shadow_row.ATTACH_FILENAME}
</div>
<!-- END table_shadow_row -->
<input type="submit" name="submit" value="{L_DELETE_MARKED}" />
{S_HIDDEN}
</form>