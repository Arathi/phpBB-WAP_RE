<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_SHOP}">{L_SHOP}</a>&gt;{L_SHOP_ACTION}</div>
<form method="post" action="{S_SHOP_ACTION}">
<div class="catSides">
{L_SHOP_ACTION}
</div>
<div class="row1">{SHOP_DAOJU_TYPE}<br/>
<select name="type">
<option value="stick">道具-置顶卡</option>
<option value="highlight">道具-高亮卡</option>
<option value="qianglou">道具-抢楼卡</option>
</select><br/>
<div class="row1">{SHOP_ACTION}<br/>
<input type="text" name="{SHOP_ACTION_DB}" maxlength="{SHOP_ACTION_MAX}" /><br/>
{PRICE}<br/>
您的账户还剩 {USER_POINTS} {SHOP_POINT_NAME}！
</div>
<input type="submit" name="submit" value=" 提交 " class="subbutton" />
</form>