<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_SMILEY_ADMIN}">表情</a>&gt;{L_SMILEY_TITLE}</div>
<span class="genmed">{L_SMILEY_EXPLAIN}</span>
<form method="post" action="{S_SMILEY_ACTION}">
<div class="catSides">{L_SMILEY_IMPORT}</div>
<div class="row1">{L_SELECT_LBL}:<br/>
{S_SMILE_SELECT}
</div>
<div class="row1">
<input type="checkbox" name="clear_current" value="1" /> {L_DEL_EXISTING}
</div>
<div class="row1">{L_CONFLICTS}:<br/>
<input type="radio" name="replace" value="1" checked="checked"/> {L_REPLACE_EXISTING}<br/>
<input type="radio" name="replace" value="0" /> {L_KEEP_EXISTING}
</div>
{S_HIDDEN_FIELDS}
<input name="import_pack" type="submit" value="{L_IMPORT}" />
</form>