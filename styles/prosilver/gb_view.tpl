<div class="catSides">{L_GUESTBOOK}</div>
<div class="row1">{L_DELETE}</div>
<!-- BEGIN error -->
{error.ERROR}
<!-- END error -->

<!-- BEGIN main -->
<!-- BEGIN postrow -->
<div class="row1">
<b>{main.postrow.NUMBER}</b>.(<b>{main.postrow.POSTER_STATUS}</b>)<b>{main.postrow.POSTER_NAME}</b><br/>
{main.postrow.MESSAGE}{main.postrow.SIGNATURE}<span class="gensmall">{main.postrow.EDITED_MESSAGE}</span><br/>
[{main.postrow.QUOTE_IMG}{main.postrow.EDIT_IMG}{main.postrow.DELETE_IMG}]
</div>
<!-- END postrow -->
<!-- END main -->

<div class="row1">{L_DELETE}</div>

<!-- BEGIN quick -->
<div class="catSides">文明用语<a href="#top" style="float:right;">{L_BACK_TO_TOP}</a></div>
<div class="row1">
<form action="{S_POST_ACTION}" method="post" name="post">
{L_TXT}<br />
<textarea name="message" style="width:99%">{MESSAGE}</textarea><br />
{S_HIDDEN_FORM_FIELDS}
<input type="submit" accesskey="s" tabindex="6" name="post" class="mainoption" value="给Ta留言" /></td>
</form>
</div>
<!-- END quick -->
{PAGE_NUMBER}
{PAGINATION}