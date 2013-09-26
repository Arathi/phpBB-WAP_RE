<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>&gt;<a href="{U_MODCP}">版主面板</a>&gt;{L_SPLIT_TOPIC}</div>
{L_SPLIT_TOPIC_EXPLAIN}
<form method="post" action="{S_SPLIT_ACTION}">
<div class="catSides">主题</div>
<!-- BEGIN postrow -->
<div class="{postrow.ROW_CLASS}">
{postrow.S_SPLIT_CHECKBOX} {L_AUTHOR}：<b>{postrow.POSTER_NAME}{postrow.POSTER_POSTS}</b><br/>
{L_POSTED}：<b>{postrow.POST_DATE}</b><br/>
{postrow.MESSAGE}
</div>
<!-- END postrow -->
<br/>
<div class="catSides">选项</div>
<div class="row1">
{L_SPLIT_SUBJECT}:<br/>
<input type="text" maxlength="60" name="subject" />
</div>
<div class="row1">
{L_SPLIT_FORUM}:<br/>
{S_FORUM_SELECT}
</div>
<br/>
<input class="subbutton" type="submit" name="split_type_all" value="{L_SPLIT_POSTS}" />
<input class="subbutton" type="submit" name="split_type_beyond" value="{L_SPLIT_AFTER}" />
{S_HIDDEN_FIELDS}
</form>