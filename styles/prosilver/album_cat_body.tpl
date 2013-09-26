<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}&gt;</a>{CAT_TITLE}</div>
<div class="catSides">{CAT_TITLE}</div>
<!-- BEGIN no_pics -->
<div class="row1">{L_NO_PICS}</div>
<!-- END no_pics -->
<!-- BEGIN picrow -->
<div class="{picrow.ROW_CLASS}">
{L_PIC_TITLE}: <a href="{picrow.U_PIC}" {TARGET_BLANK}>{picrow.TITLE}</a><br />
<!-- BEGIN piccol -->
<a href="{picrow.piccol.U_PIC}" {TARGET_BLANK}><img src="{picrow.piccol.THUMBNAIL}" border="0" alt="" /></a><br/>{picrow.piccol.APPROVAL}
<!-- END piccol -->
{L_POSTER}: {picrow.POSTER}<br />
{L_POSTED}: {picrow.TIME}<br />
{L_VIEW}: {picrow.VIEW}<br />
{picrow.RATING}
{picrow.COMMENTS}
{picrow.IP}
{picrow.EDIT}{picrow.DELETE}{picrow.LOCK}{picrow.MOVE}
</div>
<!-- END picrow -->
{PAGINATION}
<br/>{L_MODERATORS}: {MODERATORS}<br/>
{ALBUM_JUMPBOX}<br/>
<div class="row1">
<a href="{U_UPLOAD_PIC}">{L_UPLOAD_PIC}</a><br/>
{U_MODERKA}
</div>