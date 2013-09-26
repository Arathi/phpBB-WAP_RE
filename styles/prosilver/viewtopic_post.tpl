<form action="{S_POST_ACTION}" method="post">
	<div class="catSides">文明上网，理性回帖</div>
	<!-- BEGIN switch_username_select -->
		<div class="row1">用户名: <input type="text" name="username" /></div>
	<!-- END switch_username_select -->
	<div class="row1">
		<!-- BEGIN bb_panel -->
			<a class="buttom" href="javascript:void(0);" onclick="bbcode('b');">B</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('url');">URL</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('code');">C</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('i');"><i>I</i></a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('u');"><u>U</u></a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('color');"><u>A</u></a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('size');"><u>T</u></a><br/>
		<!-- END bb_panel -->
		<textarea id="text" name="message" rows="4" style="width:99%" onkeydown="if(event.ctrlKey&&event.keyCode==13){document.getElementById('misubmit').click();return false};"></textarea>
	</div>
	<div class="row1"><input type="checkbox" name="translit" /> 自动翻译</div>
	<!-- BEGIN switch_notify_checkbox -->
		<div class="row1"><input type="checkbox" name="notify" {S_NOTIFY_CHECKED} /> {L_NOTIFY_ON_REPLY}<br/></div>
	<!-- END switch_notify_checkbox -->
	{S_HIDDEN_FORM_FIELDS}
	<div class="row1">
		{SMILES_SELECT}
		<input id="misubmit" class="subbutton" type="submit" name="post" value="{L_SUBMIT}" />
	</div>
</form>