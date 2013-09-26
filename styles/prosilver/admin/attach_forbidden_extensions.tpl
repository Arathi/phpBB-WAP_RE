<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_EXTENSIONS_TITLE}</div>
{ERROR_BOX}
<span class="genmed">{L_EXTENSIONS_EXPLAIN}</span>
<form method="post" action="{S_ATTACH_ACTION}">
<div class="catSides">添加一个新的扩展名禁止</div>
<div class="row1">
{L_EXTENSION}:<br/>
<input type="text" maxlength="15" name="add_extension" value=""/><br/>
<input type="checkbox" name="add_extension_check" /> 确认{L_ADD_NEW}
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" class="liteoption" value="{L_SUBMIT}" />
</div>
<div class="catSides">已禁止的扩展名列表</div>
<!-- BEGIN extensionrow -->
<div class="row1">
<input type="checkbox" name="extension_id_list[]" value="{extensionrow.EXTENSION_ID}" /> {L_EXTENSION}: <b>{extensionrow.EXTENSION_NAME}</b><br/>
</div>
<!-- END extensionrow -->
将选中项 <input type="submit" name="submit" value="{L_DELETE}" />
</form>