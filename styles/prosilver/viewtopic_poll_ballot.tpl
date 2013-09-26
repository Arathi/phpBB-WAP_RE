<form method="POST" action="{S_POLL_ACTION}">
<div class="catSides">{POLL_QUESTION}</div>
<!-- BEGIN poll_option -->
<div class="row1">
<input type="radio" name="vote_id" value="{poll_option.POLL_OPTION_ID}"/>{poll_option.POLL_OPTION_CAPTION}
</div>
<!-- END poll_option -->
<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT_VOTE}"/><br/>
<a href="{U_VIEW_RESULTS}">{L_VIEW_RESULTS}</a>
{S_HIDDEN_FIELDS}
</form>