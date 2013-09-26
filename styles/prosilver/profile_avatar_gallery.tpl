<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>|{L_AVATAR_GALLERY}</div>
<form action="{S_PROFILE_ACTION}" method="post">
<div class="catSides">
{L_CATEGORY}
</div>
<div class="row1">
{S_CATEGORY_SELECT}<br/>
<input class="subbutton" type="submit" value="GO" name="avatargallery" />
</div>
<!-- BEGIN avatar_row -->
<!-- BEGIN avatar_column -->
<div class="row1">
<img src="{avatar_row.avatar_column.AVATAR_IMAGE}" alt="" /><br/>
<input type="radio" name="avatarselect" value="{avatar_row.avatar_column.S_OPTIONS_AVATAR}" /> выбрать
</div>
<!-- END avatar_column -->
<!-- END avatar_row -->
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="submitavatar" value="{L_SELECT_AVATAR}"/>
</form>