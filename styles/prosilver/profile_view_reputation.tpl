<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_REPUTATION_BACK_PROFILE}">{L_PROFILE}</a>&gt;详细信息</div>
<!-- BEGIN warn -->
<div class="catSides">用户 {warn.U_VIEWPROFILE_USERNAME} 的禁止信息</div>
<!-- BEGIN rows -->
<div class="{warn.rows.ROW_CLASS}">
禁止类型: <b>{warn.rows.REVIEW}</b><br/>
开始时间：{warn.rows.DATE_TIME}<br/>
结束时间：{warn.rows.EXPIRE} {warn.rows.DELETE}<br/>
禁止原因：{warn.rows.COMMENTS} {warn.rows.EDITED_COMMENTS}<br/>
违反规则: {warn.rows.REVIEWER_NAME}<br />
禁止信息功能：{warn.rows.POST_REF}
{warn.rows.MESSAGE_TEXT}
</div>
<!-- END rows -->
<!-- END warn -->
<!-- BEGIN rep -->
<div class="catSides">{rep.BLOCK_TITLE}</div>
<!-- BEGIN rows -->
<div class="{rep.rows.ROW_CLASS}">
用户：<b>{rep.rows.REVIEWER_NAME}</b><br/>
修改：<b>{rep.rows.REVIEW}</b>{rep.rows.EDIT}{rep.rows.DELETE}<br/>
时间：{rep.rows.POST_DATE}<br/>
内容: {rep.rows.COMMENTS}
</div>
<!-- END rows -->
<!-- END rep -->
{PAGINATION}