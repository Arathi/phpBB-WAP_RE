<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;{L_RANKS_TITLE}</div>
<div class="catSides">
	<span class="cattitle">{L_RANKS_TITLE}</span>
</div>
<div class="row_hard">
	<span class="genmed">{L_RANKS_TEXT}</span>
</div>
<!-- BEGIN ranks -->
	<div class="{ranks.ROW_CLASS}">
		{L_RANK}: <b>{ranks.RANK}</b><br/>
		{L_RANK_MINIMUM}: <b>{ranks.RANK_MIN}</b><br/>
		{L_SPECIAL_RANK}: <b>{ranks.SPECIAL_RANK}</b><br/>
		<a href="{ranks.U_RANK_EDIT}">{L_EDIT}</a> | <a href="{ranks.U_RANK_DELETE}">{L_DELETE}</a>
	</div>
<!-- END ranks -->
<form method="post" action="{S_RANKS_ACTION}">
	<input type="submit" class="mainoption" name="add" value="{L_ADD_RANK}" />
</form>
