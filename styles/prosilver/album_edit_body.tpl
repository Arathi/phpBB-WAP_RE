<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}</a>&gt;{CAT_TITLE}</div>
<form name="editform" action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">
{L_EDIT_PIC_INFO}</div>
<div class="row1">{L_PIC_TITLE}:<br/>
<input type="text" name="pic_title" value="{PIC_TITLE}" />
</div>
<div class="row1">{L_PIC_DESC}:
<span class="genmed">£¨{L_MAX_LENGTH}: <b>{S_PIC_DESC_MAX_LENGTH}</b>£©</span><br/>
<textarea name="pic_desc" rows="5" cols="15" style="width: 235px;">{PIC_DESC}</textarea>
</div>
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>