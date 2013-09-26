<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_CONTROL_PANEL_TITLE}</div>
<span class="genmed">{L_CONTROL_PANEL_EXPLAIN}</span>
<form method="post" action="{S_MODE_ACTION}">
<div class="row1">
{L_VIEW}: {S_VIEW_SELECT}<br />
{L_SELECT_SORT_METHOD}：<br />
{S_MODE_SELECT} {L_ORDER} {S_ORDER_SELECT}
<input type="submit" name="submit" value="{L_SUBMIT}" />
</div>
</form>
<div class="catSides">附件列表</div>
<!-- BEGIN memberrow -->
<div class="{memberrow.ROW_CLASS}">
{L_USERNAME}: <b><a href="{memberrow.U_VIEW_MEMBER}">{memberrow.USERNAME}</a></b><br/>
{L_ATTACHMENTS}: <b>{memberrow.TOTAL_ATTACHMENTS}</b><br/>
{L_TOTAL_SIZE}: <b>{memberrow.TOTAL_SIZE}</b>
</div>
<!-- END memberrow -->
{PAGINATION}