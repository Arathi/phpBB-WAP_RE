<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_RULES}">{L_RULES}</a>&gt;{CAT_URL}</div>
<form action="{S_RULES_ACTION}" method="post">
<div class="catSides">编辑规则内容</div>
<div class="row1">标题:<br/>
<input type="text" name="name" value="{NAME}" />
</div>
<div class="row1">内容:<br/>
<textarea name="subject" rows="5" cols="15" style="width: 235px;">{TEXT}</textarea>
</div>
<div class="row1">
<input type="checkbox" name="moder" value="1"{MODER} /> 恢复<br/>
<span class="genmed">如果您标记该选项，表单数据是不会提交到数据库。</span>
</div>
<input class="subbutton" type="submit" value="提交" />
</form>