<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_SHOP}">{L_SHOP}</a>&gt;投放广告</div>
<form action="{S_SHOP_ACTION}" method="POST">
<div class="catSides">投放广告</div>
<div class="row1">
URL地址(不要 http://):<br/>
<input type="text" name="url"/>
</div>
<div class="row1">
链接名称:<br/>
<input type="text" name="desc"/>
</div>
<div class="row1">
显示周期:<br />
<select name="time">
<option value="1">1 周</option>
<option value="2">2 周</option>
<option value="3">3 周</option>
<option value="4">4 周</option>
</select>
</div>
<div class="row1">
显示位置:<br/>
<input type="radio" name="order" value="0" checked="checked" /> 首页顶部<br />
<input type="radio" name="order" value="1" /> 首页底部
</div>
<input type="submit" name="submit" value=" 提交 " />
</form>