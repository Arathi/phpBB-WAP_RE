<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;选择</div>
<form action="{S_ACTION}" method="post">
	<div class="catSides">{L_CHOOSE_TEMPLATE}</div>
	<div class="row1">
		{FILE_NAME}
		<select name="template" class="post">
			{S_TEMPLATES}
		</select>
	</div>
	{HIDDEN_THINGS}
	<input type="submit" name="{SUBMIT_NAME}" value="{L_SUBMIT}"/>
</form>
