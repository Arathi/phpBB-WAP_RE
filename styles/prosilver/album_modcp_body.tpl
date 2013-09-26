<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt<a href="{U_ALBUM}">{L_ALBUM}</a>&gt;<a href="{U_VIEW_CAT}">{CAT_TITLE}</a></div>
<form name="modcp" action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">
{L_MODCP}</div>
<!-- BEGIN no_pics -->
<div class="row1">
{L_NO_PICS}</div>
<!-- END no_pics -->
<!-- BEGIN picrow -->
<div class="{picrow.ROW_CLASS}">
{L_PIC_TITLE}: {picrow.PIC_TITLE}<br />
{L_POSTER}: {picrow.POSTER}<br />
{L_TIME}: {picrow.TIME}<br />
{L_RATING}: {picrow.RATING}<br />
{L_COMMENTS}: {picrow.COMMENTS}<br />
{L_STATUS}: {picrow.LOCK}<br />
{L_APPROVAL}: {picrow.APPROVAL}<br />
{L_SELECT}: <input type="checkbox" name="pic_id[]" value="{picrow.PIC_ID}" />
</div>
<!-- END picrow -->
<input type="hidden" name="mode" value="modcp" />
<input type="submit" name="move" value="{L_MOVE}" />
<input type="submit" name="lock" value="{L_LOCK}" />
<input type="submit" name="unlock" value="{L_UNLOCK}" />
{DELETE_BUTTON}
{APPROVAL_BUTTON}
{UNAPPROVAL_BUTTON}
</form>
{PAGINATION}