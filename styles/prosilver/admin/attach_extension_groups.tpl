<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_EXTENSION_GROUPS_TITLE}</div>
{GROUP_PERMISSIONS_BOX}
{PERM_ERROR_BOX}
{ERROR_BOX}
<span class="genmed">{L_EXTENSION_GROUPS_EXPLAIN}</span>
<form method="post" action="{S_ATTACH_ACTION}">
<div class="catSides">添加一个新的扩展名组</div>
<div class="row1">{L_EXTENSION_GROUP}:<br/>
<input type="text" size="20" name="add_extension_group" value="{ADD_GROUP_NAME}" />
</div>
<div class="row1">{L_SPECIAL_CATEGORY}:<br/>{S_SELECT_CAT}</div>
<div class="row1">{L_ALLOWED} <input type="checkbox" name="add_allowed" /></div>
<div class="row1">{L_DOWNLOAD_MODE}:<br/>{S_ADD_DOWNLOAD_MODE}</div>
<div class="row1">{L_UPLOAD_ICON}:<br/><input type="text" name="add_upload_icon" value="{UPLOAD_ICON}" /></div>
<div class="row1">{L_MAX_FILESIZE}:<br/><input type="text" size="3" name="add_max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}</div>
<div class="row1">
<input type="checkbox" name="add_extension_group_check" /> 确认{L_ADD_NEW}
<input type="submit" name="submit" value="{L_SUBMIT}" />
</div>
<!-- BEGIN grouprow -->
<br/>
<div class="catSides">
{L_EXTENSION_GROUP}
</div>
<div class="row1">
<input type="hidden" name="group_change_list[]" value="{grouprow.GROUP_ID}" />
<input type="text" size="20" name="extension_group_list[]" value="{grouprow.EXTENSION_GROUP}" /><b><a href="{grouprow.U_VIEWGROUP}">{grouprow.CAT_BOX}</a></b><br/>
<!-- BEGIN extensionrow -->
<b>{grouprow.extensionrow.EXTENSION} {grouprow.extensionrow.EXPLANATION}</b> 
<!-- END extensionrow -->
</div>
<div class="row1">{L_SPECIAL_CATEGORY} {grouprow.S_SELECT_CAT}</div>
<div class="row1">{L_ALLOWED} <input type="checkbox" name="allowed_list[]" value="{grouprow.GROUP_ID}" {grouprow.S_ALLOW_SELECTED} /></div>
<div class="row1">{L_DOWNLOAD_MODE} {grouprow.S_DOWNLOAD_MODE}</div>
<div class="row1">
{L_UPLOAD_ICON}<br />
<input type="text" size="15" name="upload_icon_list[]" value="{grouprow.UPLOAD_ICON}" />
</div>
<div class="row1">{L_MAX_FILESIZE}<input type="text" size="3" name="max_filesize_list[]" value="{grouprow.MAX_FILESIZE}" /> {grouprow.S_FILESIZE}</div>
<div class="row1">{L_ALLOWED_FORUMS}:<br/><a href="{grouprow.U_FORUM_PERMISSIONS}">{L_FORUM_PERMISSIONS}</a></div>
<div class="row1">{L_DELETE}<input type="checkbox" name="group_id_list[]" value="{grouprow.GROUP_ID}" /></div>
<!-- END grouprow -->
<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>