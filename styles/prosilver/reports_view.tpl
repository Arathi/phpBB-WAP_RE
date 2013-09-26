<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;收到的投诉</div>
<form name="status" action="{S_ACTION}" method="post">
<select name="status">
<option value="1"{S_OPEN}>{L_OPENED}</option>
<option value="2"{S_CLOSED}>{L_CLOSED}</option>
<option value="all"{S_ALL}>{L_ALL}</option>
</select>
<input type="submit" name="submit" value="{L_DISPLAY}" />
</form>
<form action="{S_ACTION}" method="post">
<div class="catSides">收到的投诉列表</div>
<!-- BEGIN postrow -->
<div class="{postrow.ROW_CLASS}">
<input type="checkbox" name="p[]" value="{postrow.REPORT_ID}" /> 选择<br/>
<b>所属帖子：</b> <a href="{postrow.U_VIEW_POST}">{postrow.TOPIC_TITLE}</a><br/>
<b>所属论坛：</b> {postrow.FORUM}<br/>
<b>举报者：</b> {postrow.REPORTER}[{postrow.DATE}]<br/>
<b>举报原因：</b>{postrow.COMMENTS}<br/>
<b>论坛版主：</b>{postrow.LAST_ACTION_COMMENTS}<br/>
<a href="{postrow.U_CLOSE_REPORT}">{postrow.L_CLOSE_REPORT}</a>
</div>
<!-- END postrow -->
<!-- BEGIN no_reports -->
<div class="row1">没有收到投诉！</div>
<!-- END no_reports -->
<select name="action">
<option value="">{L_SELECT_ONE}</option>
<option value="close">{L_CLOSE}</option>
<option value="open">{L_OPEN}</option>
<option value="delete">{L_DELETE}</option>
</select>
<input type="submit" value="{L_SUBMIT}" /> 
</form>
{PAGINATION}
<a href="{U_OPT_OUT}">{L_OPT_OUT}</a>
{DELETED_REPORTS}