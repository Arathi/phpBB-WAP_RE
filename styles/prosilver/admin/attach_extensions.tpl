<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_EXTENSIONS_TITLE}</div>
{ERROR_BOX}
<span class="genmed">{L_EXTENSIONS_EXPLAIN}</span>
<form method="post" action="{S_ATTACH_ACTION}">
<div class="catSides">添加新的扩展名</div>
<div class="row1">
{L_EXPLANATION}:<br />
<input type="text" maxlength="100" name="add_extension_explain" value="{ADD_EXTENSION_EXPLAIN}" />
</div>
<div class="row1">
{L_EXTENSION}:<br/>
<input type="text" maxlength="100" name="add_extension" value="{ADD_EXTENSION}" />
</div>
<div class="row1">
{L_EXTENSION_GROUP}:<br />
{S_ADD_GROUP_SELECT}
</div>
<div class="row1"><input type="checkbox" name="add_extension_check" /> 确认{L_ADD_NEW}
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" class="liteoption" value="{L_SUBMIT}" />
</div>
<div class="catSides">扩展名列表</div>
<!-- BEGIN extension_row -->
<div class="row1">
{L_EXTENSION}: <b>{extension_row.EXTENSION}</b><br/>
{L_EXPLANATION}:<br/>
<input type="hidden" name="extension_change_list[]" value="{extension_row.EXT_ID}" />
<input type="text" maxlength="100" name="extension_explain_list[]" value="{extension_row.EXTENSION_EXPLAIN}" /><br/>
{L_EXTENSION_GROUP}: {extension_row.S_GROUP_SELECT}<br/>
{L_DELETE} <input type="checkbox" name="extension_id_list[]" value="{extension_row.EXT_ID}" />
</div>
<!-- END extension_row -->
<input type="submit" name="submit" class="liteoption" value="{L_SUBMIT}" />
</form>