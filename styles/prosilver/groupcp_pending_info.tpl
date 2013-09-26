<div class="catSides">
{L_PENDING_MEMBERS}</div>
<!-- BEGIN pending_members_row -->
<div class="row1">
<a href="{pending_members_row.U_VIEWPROFILE}">{pending_members_row.USERNAME}</a>[{pending_members_row.POSTS}]{pending_members_row.PM}{pending_members_row.EMAIL}<br/>
{L_SELECT} <input type="checkbox" name="pending_members[]" value="{pending_members_row.USER_ID}" checked="checked" />
</div>
<!-- END pending_members_row -->
<input type="submit" name="approve" value="{L_APPROVE_SELECTED}" /><br/>
<input type="submit" name="deny" value="{L_DENY_SELECTED}" />