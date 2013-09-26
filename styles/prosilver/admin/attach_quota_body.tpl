<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_MANAGE_QUOTAS_TITLE}</div>
<span class="genmed">{L_MANAGE_QUOTAS_EXPLAIN}</span>
<form method="post" action="{S_ATTACH_ACTION}">
<div class="catSides">添加一个新的限制</div>
<div class="row1">
{L_DESCRIPTION}:<br/>
<input type="text" maxlength="25" name="quota_description" value=""/>
</div>
<div class="row1">
{L_SIZE}:<br/>
<input type="text" maxlength="15" size="6" name="add_max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}
</div>
<div class="row1">
<input type="checkbox" name="add_quota_check" /> 确认{L_ADD_NEW}
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" class="liteoption" value="{L_SUBMIT}" />
</div>
<div class="catSides">{L_MANAGE_QUOTAS_TITLE}</div>
<!-- BEGIN limit_row -->
<div class="row1">
<input type="hidden" name="quota_change_list[]" value="{limit_row.QUOTA_ID}" />
{L_DESCRIPTION}:<br/>
<input type="text" maxlength="25" name="quota_desc_list[]" value="{limit_row.QUOTA_NAME}" /><br/>
{L_SIZE}:<br/>
<input type="text" maxlength="15" name="max_filesize_list[]" value="{limit_row.MAX_FILESIZE}" /> {limit_row.S_FILESIZE}<br/>
<input type="checkbox" name="quota_id_list[]" value="{limit_row.QUOTA_ID}" /> 确认{L_DELETE}
</div>
<!-- END limit_row -->
<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>