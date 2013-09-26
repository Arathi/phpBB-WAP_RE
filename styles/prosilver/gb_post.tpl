<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_PROFILE}">{L_PROFILE}</a>&gt;<a href="{U_GUESTBOOK}">{L_GB_POST}</a>&gt;发表、编辑</div>
{ERROR_BOX}
<form action="{S_POST_ACTION}" method="post" name="post">
<div class="catSides">{L_GB_POST}</div>
<div class="row1">{L_MESSAGE_BODY}:<br />
<textarea name="message" rows="5" style="width:99%">{MESSAGE}</textarea><br />
{S_HIDDEN_FORM_FIELDS}
<input type="submit" accesskey="s" tabindex="6" name="post" class="mainoption" value="{L_SUBMIT}" />
</form>