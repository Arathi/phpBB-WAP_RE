<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;<a href="{U_RANK_ADMIN}">{L_RANKS_TITLE}</a>&gt;编辑等级</div>
<form action="{S_RANK_ACTION}" method="post">
	<div class="catSides">
		<span class="cattitle">编辑等级信息</span>
	</div>
	<div class="row_hard">
		<span class="genmed">{L_RANKS_TEXT}</span>
	</div>
	<div class="row1">
		{L_RANK_TITLE}:<br/>
		<input class="post" type="text" name="title" size="35" maxlength="40" value="{RANK}" />
	</div>
	<div class="row1">
		{L_RANK_SPECIAL}:<br/>
		<input type="radio" name="special_rank" value="1" {SPECIAL_RANK} />{L_YES}<br/>
		<input type="radio" name="special_rank" value="0" {NOT_SPECIAL_RANK} /> {L_NO}
	</div>
	<div class="row1">
		{L_RANK_MINIMUM}:<br/>
		<input class="post" type="text" name="min_posts" size="5" maxlength="10" value="{MINIMUM}" />
	</div>
	<div class="row1">
		{L_RANK_IMAGE}:<br/>
		<span class="genmed">{L_RANK_IMAGE_EXPLAIN}</span><br/>
		<input class="post" type="text" name="rank_image" value="{IMAGE}" /><br/>{IMAGE_DISPLAY}
	</div>
	<input type="submit" name="submit" value="{L_SUBMIT}" class="mainoption" />
	{S_HIDDEN_FIELDS}
</form>
