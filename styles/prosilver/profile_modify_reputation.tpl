<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_REPUTATION_BACK_PROFILE}">{L_PROFILE}</a>&gt;评价</div>
<form action="{S_PROFILE_ACTION}" name="modify_reputation" method="post">
<div class="catSides">评价</div>
<div class="row1">
评价内容:<br />
<textarea name="message" rows="5" style="width: 99%;">{REVIEW}</textarea>
</div>
<div class="row1">
您确定要给予Ta <b>{REVIEW_IMG}</b>？
</div>
{S_HIDDEN_FORM_FIELDS}
<input class="subbutton" type="submit" name="submit" value="Да" />
</form>