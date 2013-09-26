<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}</a></div>
<form action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">移动</div>
<div class="row1">
{L_MOVE_TO_CATEGORY}:<br />
{S_CATEGORY_SELECT}
</div>
<input class="subbutton" type="submit" name="move" value="移动" />
<!-- BEGIN pic_id_array -->
<input type="hidden" name="pic_id[]" value="{pic_id_array.VALUE}" />
<!-- END pic_id_array -->
</form>