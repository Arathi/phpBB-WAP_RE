<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;<a href="{U_WORDS_ADMIN}">{L_WORDS_TITLE}</a>&gt;{L_WORD_CENSOR}</div>
<form method="post" action="{S_WORDS_ACTION}">
	<div class="catSides">
		<span class="cattitle">{L_WORD_CENSOR}</span>
	</div>
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