<!-- BEGIN bb_panel -->
<script>
var  id ='text';
function bbcode(Tag) {
	var Open='['+Tag+']';
	var Close='[/'+Tag+']';
	var object = document.getElementById(id);
	object.focus();
	if (Open=='[url]'){
		var Open='['+Tag+'=http://]';
	}
	if (window.attachEvent && navigator.userAgent.indexOf('Opera') === -1) {                                        
		var s = object.sel;
		if(s){                                  
			var l = s.text.length;
			s.text = Open + s.text + Close;
			s.moveEnd("character", -Close.length);
			s.moveStart("character", -l);                                           
			s.select();                
		}
	} else {                                              
		var ss = object.scrollTop;
		sel1 = object.value.substr(0, object.selectionStart);
		sel2 = object.value.substr(object.selectionEnd);
		sel = object.value.substr(object.selectionStart, object.selectionEnd - object.selectionStart);                                              
		object.value = sel1 + Open + sel + Close + sel2;
		object.selectionStart = sel1.length + Open.length;
		object.selectionEnd = object.selectionStart + sel.length;
		object.scrollTop = ss;                                             
	}
	return false;
}
</script>
<!-- END bb_panel -->
<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;
<!-- BEGIN switch_not_privmsg -->
<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a> &gt;<a href="{U_VIEW_TOPIC}">{TOPIC_TITLE}</a>&gt;发帖
<!-- END switch_not_privmsg -->
<!-- BEGIN switch_privmsg -->
信息
<!-- END switch_privmsg -->
</div>
{ERROR_BOX}
<form action="{S_POST_ACTION}" method="post" {S_FORM_ENCTYPE}>
<div class="catSides">{L_POST_A}</div>
<!-- BEGIN switch_username_select -->
<div class="row1">
{L_USERNAME}:<br/>
<input type="text" name="username" />
</div>
<!-- END switch_username_select -->
<!-- BEGIN switch_privmsg -->
<div class="row1">
{L_USERNAME}:<br/>
<input type="text" name="username" value="{USERNAME}" />
</div>
<!-- END switch_privmsg -->
<!-- BEGIN switch_allow_subject_on -->
<div class="row1">
{L_SUBJECT}:<br/>
<input type="text" name="subject" value="{SUBJECT}" style="width: 235px;"/>
</div>
<!-- END switch_allow_subject_on -->
<!-- BEGIN switch_privmsg -->
<div class="row1">
{L_SUBJECT}:<br/>
<input type="text" name="subject" value="{SUBJECT}" style="width: 235px;"/>
</div>
<!-- END switch_privmsg -->
<div class="row1">
内容:<br/>
<!-- BEGIN bb_panel -->
<a class="buttom" href="javascript:void(0);" onclick="bbcode('b');">B</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('url');">URL</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('code');">Code</a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('i');"><i>I</i></a> <a class="buttom" href="javascript:void(0);" onclick="bbcode('u');"><u>U</u></a><br/>
<!-- END bb_panel -->
<textarea id="text" name="message" rows="5" style="width: 99%;">{MESSAGE}</textarea>
</div>
<!-- BEGIN switch_confirm -->
<div class="row1">
验证码：<br/>
{CONFIRM_IMG}<br/>
<input type="text" name="confirm_code" value="" />
</div>
<!-- END switch_confirm -->
{POLLBOX}
{ATTACHBOX}
<!-- BEGIN switch_type_toggle -->
<div class="row1">{S_TYPE_TOGGLE}</div>
<!-- END switch_type_toggle -->
<div class="row1">
<input type="checkbox" name="translit" /> 自动翻译<br/>
<!-- BEGIN switch_notify_checkbox -->
<input type="checkbox" name="notify" {S_NOTIFY_CHECKED} /> {L_NOTIFY_ON_REPLY}<br/>
<!-- END switch_notify_checkbox -->
<!-- BEGIN switch_delete_checkbox -->
<input type="checkbox" name="delete" /> 删除<br/>
<!-- END switch_delete_checkbox -->
{S_HIDDEN_FORM_FIELDS}
<input class="subbutton" type="submit" name="post" value="{L_SUBMIT}" />
</div>
</form>
<br/>
- <a href="{TRANSLIT_TABLE}">自动翻译</a><br/>
- <a href="{SMILES_TABLE}">表情</a><br/>
- <a href="{BBCODE_TABLE}">BB-code</a>