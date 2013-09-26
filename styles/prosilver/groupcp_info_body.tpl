<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_GROUP_CP}">{L_USERGROUPS}</a>&gt;{GROUP_NAME}</div>
{ERROR_BOX}
<form action="{S_GROUPCP_ACTION}" {S_FORM_ENCTYPE} method="post">
<div class="catSides">
{L_GROUP_INFORMATION}</div>
<div class="row1">
{L_GROUP_NAME}:<br/>
<b>{GROUP_NAME}</b>
{CURRENT_LOGO}
</div>
<div class="row1">
{L_GROUP_DESC}:<br/>
{GROUP_DESC}
</div>
<div class="row1">
{L_GROUP_MEMBERSHIP}:<br/>
{GROUP_DETAILS}<br/>
<!-- BEGIN switch_subscribe_group_input -->
<input type="submit" name="joingroup" value="{L_JOIN_GROUP}" />
<!-- END switch_subscribe_group_input -->
<!-- BEGIN switch_unsubscribe_group_input -->
<input type="submit" name="unsub" value="{L_UNSUBSCRIBE_GROUP}" />
<!-- END switch_unsubscribe_group_input -->
</div>
<!-- BEGIN switch_mod_option -->
<div class="row1">
{L_GROUP_TYPE}:<br/>
<input type="radio" name="group_type" value="{S_GROUP_OPEN_TYPE}" {S_GROUP_OPEN_CHECKED} /> {L_GROUP_OPEN}<br/>
<input type="radio" name="group_type" value="{S_GROUP_CLOSED_TYPE}" {S_GROUP_CLOSED_CHECKED} /> {L_GROUP_CLOSED}<br/>
<input type="radio" name="group_type" value="{S_GROUP_HIDDEN_TYPE}" {S_GROUP_HIDDEN_CHECKED} /> {L_GROUP_HIDDEN}<br/>
<input type="submit" name="groupstatus" value="{L_UPDATE}" />
</div>
<!-- END switch_mod_option -->
<div class="row1">
{L_GROUP_MODERATOR}:<br/>
<a href="{U_MOD_VIEWPROFILE}">{MOD_USERNAME}</a>[{MOD_POSTS}]{MOD_PM}{MOD_EMAIL}
</div>
<!-- BEGIN yeah -->
<div class="catSides">小组的 Logo </div>
<div class="row1">
{L_AVATAR_EXPLAIN}:<br/>
{AVATAR}
<!-- BEGIN switch_avatar_local_upload -->
{L_UPLOAD_AVATAR_FILE}:<br/>
<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" /><input type="file" name="avatar"/><br/>
<!-- END switch_avatar_local_upload -->
<!-- BEGIN switch_avatar_local_upload_om -->
{L_UPLOAD_AVATAR_FILE}:<br/>
<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
<input name="fileupload" value = ""><br/>
<a href="op:fileselect">浏览...</a><br/>
<!-- END switch_avatar_local_upload_om -->
{L_UPLOAD_AVATAR_URL}:<br/>
<input type="text" name="avatarurl"/><br/>
<input type="submit" name="groupicon" value=" 上传 " />
</div>
<!-- END yeah -->
{S_HIDDEN_FIELDS}
</form>
{GUESTBOOK}
<form action="{S_GROUPCP_ACTION}" method="post" name="post">
<div class="catSides">
{L_GROUP_MEMBERS}</div>
<!-- BEGIN member_row -->
<div class="row1">
<a href="{member_row.U_VIEWPROFILE}">{member_row.USERNAME}</a>[{member_row.POSTS}]{member_row.PM}{member_row.EMAIL}<br/>
<!-- BEGIN switch_mod_option -->
{L_SELECT} <input type="checkbox" name="members[]" value="{member_row.USER_ID}" />
<!-- END switch_mod_option -->
</div>
<!-- END member_row -->
<!-- BEGIN switch_no_members -->
<div class="row1">
{L_NO_MEMBERS}</div>
<!-- END switch_no_members -->
<!-- BEGIN switch_hidden_group -->
<div class="row1">
{L_HIDDEN_MEMBERS}</div>
<!-- END switch_hidden_group -->
<!-- BEGIN switch_mod_option -->
<input type="submit" name="remove" value="{L_REMOVE_SELECTED}" />
<br/>
请输入用户名:<br/>
<input type="text" name="username" maxlength="50" /><input type="submit" name="add" value="{L_ADD_MEMBER}" /><br/>
<!-- END switch_mod_option -->
{S_HIDDEN_FIELDS}
</form>
{PAGINATION}
<form action="{S_GROUPCP_ACTION}" method="post" name="post">
{PENDING_USER_BOX}
{S_HIDDEN_FIELDS}
</form>
