<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}</a>&gt;<a href="{U_VIEW_CAT}">{CAT_TITLE}</a>&gt;ÆÀ·Ö</div>
<form name="rateform" action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">
{L_RATING}</div>
<div class="row1">
{L_PIC_TITLE}: <a href="{U_PIC}" {TARGET_BLANK}>{PIC_TITLE}</a><br/>
<a href="{U_PIC}" {TARGET_BLANK}><img src="{U_THUMBNAIL}" alt="" /></a><br/>
{L_PIC_DESC}: {PIC_DESC}<br/>
{L_POSTER}: {POSTER}<br/>
{L_POSTED}: {PIC_TIME}<br/>
{L_VIEW}: {PIC_VIEW}
</div>
<div class="row1">
{L_CURRENT_RATING}: {PIC_RATING}<br/>
{L_PLEASE_RATE_IT}:<br/>
<select name="rate">
<option value="-1">{S_RATE_MSG}</option>
<!-- BEGIN rate_row -->
<option value="{rate_row.POINT}">{rate_row.POINT}</option>
<!-- END rate_row -->
</select>
</div>
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>