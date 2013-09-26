<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MEDALS}">{L_MEDALS}</a>&gt;<a href="{U_MEDALS_CP}">管理奖项</a>&gt;修改</div>
修改用户 {USERNAME} 的获奖信息
<form action="{S_MEDAL_ACTION}" method="post">
<div class="catSides">获奖的信息</div>
<div class="row1">名称：{MEDAL_NAME}</div>
<div class="row1">所属分类：{MEDAL_DESCRIPTION}
</div>
<div class="catSides">修改获奖原因</div>
<!-- BEGIN medaledit -->
<div class="row_hard">{medaledit.L_MEDAL_TIME}: {medaledit.ISSUE_TIME}</div>
<div class="row1">{medaledit.L_MEDAL_REASON}:<br/>
<textarea name="{medaledit.L_ISSUE_REASON}" rows="5" cols="15" style="width: 235px;">{medaledit.ISSUE_REASON}</textarea>
</div>
<!-- END medaledit -->
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>