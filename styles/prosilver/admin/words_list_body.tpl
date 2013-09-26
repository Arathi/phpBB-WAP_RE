<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_WORDS_TITLE}</div>
<span class="genmed">{L_WORDS_TEXT}</span>
<form method="post" action="{S_WORDS_ACTION}">
	<div class="catSides">{L_WORDS_TITLE}</div>
	<!-- BEGIN words -->
		<div class="{words.ROW_CLASS}">
			{L_WORD}: {words.WORD}<br/>
			{L_REPLACEMENT}: {words.REPLACEMENT}<br/>
			<a href="{words.U_WORD_EDIT}">{L_EDIT}</a> | <a href="{words.U_WORD_DELETE}">{L_DELETE}</a>
		</div>
	<!-- END words -->
	{S_HIDDEN_FIELDS}
	<input type="submit" name="add" value="{L_ADD_WORD}" />
</form>