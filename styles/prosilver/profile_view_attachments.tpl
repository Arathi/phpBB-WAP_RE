<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;附件</div>
<div class="catSides">该用户发表的附件</div>
<!-- BEGIN attachrow -->
<div class="{attachrow.ROW_CLASS}">
<b>{attachrow.ROW_NUMBER}. {attachrow.VIEW_ATTACHMENT}</b> ({attachrow.SIZE} {attachrow.SIZE_LANG})<br/>
帖子：{attachrow.POST_TITLE}<br />
时间：{attachrow.POST_TIME}<br />
附件被下载了 {attachrow.DOWNLOAD_COUNT} 次
</div>
<!-- END attachrow -->
{PAGINATION}
<form method="post" action="{S_MODE_ACTION}">
排列方式:{S_MODE_SELECT}{S_ORDER_SELECT}<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>