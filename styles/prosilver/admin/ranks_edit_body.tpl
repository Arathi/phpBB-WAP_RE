<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_RANKS}">等级列表</a>&gt;{L_RANKS_TITLE}</div>
<span class="genmed">{L_RANKS_TEXT}</span>
<div class="catSides">{L_RANKS_TITLE}</div>
<form action="{S_RANK_ACTION}" method="post">
	<div class="row1">
		{L_RANK_TITLE}：<br />
		<input type="text" name="title" size="35" maxlength="40" value="{RANK}" />
	</div>
	<div class="row1">
		{L_RANK_SPECIAL}：<br />
		<input type="radio" name="special_rank" value="1" {SPECIAL_RANK} />{L_YES}<br/>
		<input type="radio" name="special_rank" value="0" {NOT_SPECIAL_RANK} /> {L_NO}
	</div>
	<div class="row1">
		{L_RANK_MINIMUM}：<br/>
		<input type="text" name="min_posts" size="5" maxlength="10" value="{MINIMUM}" />
	</div>
	<div class="row1">
		{L_RANK_IMAGE}：<br />
		<span>{L_RANK_IMAGE_EXPLAIN}</span><br />
		<input type="text" name="rank_image" size="40" maxlength="255" value="{IMAGE}" /><br />{IMAGE_DISPLAY}
	</div>
	{S_HIDDEN_FIELDS}	
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>