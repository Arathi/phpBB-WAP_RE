<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;等级列表</div>
<span class="genmed">{L_RANKS_TEXT}</span>
<div class="catSides">等级列表</div>
<!-- BEGIN ranks -->
	<div class="{ranks.ROW_CLASS}">
	{ranks.L_NUMBER}、<b>{ranks.RANK}</b><br/>
	{L_RANK_MINIMUM}: <b>{ranks.RANK_MIN}</b><br/>
	{L_SPECIAL_RANK}: <b>{ranks.SPECIAL_RANK}</b><br/>
	<a href="{ranks.U_RANK_EDIT}">{L_EDIT}</a> | <a href="{ranks.U_RANK_DELETE}">{L_DELETE}</a>
	</div>
<!-- END ranks -->
<form method="post" action="{S_RANKS_ACTION}">
<input type="submit" name="add" value="{L_ADD_RANK}" />
</form>