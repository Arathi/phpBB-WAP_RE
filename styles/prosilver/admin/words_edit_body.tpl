<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_WORDS_LISTS}">敏感词列表</a>&gt;{L_WORD_CENSOR}</div>
<span class="genmed">{L_WORDS_TEXT}</span>
<form method="post" action="{S_WORDS_ACTION}">
	<div class="catSides">{L_WORD_CENSOR}</div>
	<div class="row1">
		{L_WORD}:<br/>
		<input type="text" name="word" value="{WORD}" />
	</div>
	<div class="row1">
		{L_REPLACEMENT}:<br/>
		<input type="text" name="replacement" value="{REPLACEMENT}" />
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="save" value="{L_SUBMIT}" />
</form>