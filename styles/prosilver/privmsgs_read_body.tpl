<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;查看信息</div>
<div class="catSides">{L_MESSAGE}</div>
<div class="row1">
<b>{L_FROM}:</b><br/>
<a href="{U_FROM_USER_PROFILE}">{MESSAGE_FROM}</a>
</div>
<div class="row1"><b>{L_TO}:</b>{MESSAGE_TO}</div>
<div class="row1"><b>{L_POSTED}:</b>{POST_DATE}</div>
<div class="row1"><b>{L_SUBJECT}:</b>{POST_SUBJECT}</div>
<div class="row1">
<b>内容:</b>{MESSAGE}
<!-- BEGIN postrow -->
{ATTACHMENTS}
<!-- END postrow -->
</div>
<br/>{REPLY_PM}<br/>
<a href="{S_HISTORY}" class="buttom">信息记录</a><br/>
<form method="post" action="{S_PRIVMSGS_ACTION}">
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="save" value="{L_SAVE_MSG}"/>
<input class="subbutton" type="submit" name="delete" value="{L_DELETE_MSG}"/>
<!-- BEGIN switch_attachments -->
<input class="subbutton" type="submit" name="pm_delete_attach" value="{L_DELETE_ATTACHMENTS}"/>
<!-- END switch_attachments -->
</form>