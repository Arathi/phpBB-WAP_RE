<script>
function selectCode(a)
{
	// Get ID of code block
	var e = a.parentNode.parentNode.getElementsByTagName('CODE')[0];

	// Not IE
	if (window.getSelection)
	{
		var s = window.getSelection();
		// Safari
		if (s.setBaseAndExtent)
		{
			s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
		}
		// Firefox and Opera
		else
		{
			// workaround for bug # 42885
			if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == '<BR>')
			{
				e.innerHTML = e.innerHTML + '&nbsp;';
			}

			var r = document.createRange();
			r.selectNodeContents(e);
			s.removeAllRanges();
			s.addRange(r);
		}
	}
	// Some older browsers
	else if (document.getSelection)
	{
		var s = document.getSelection();
		var r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	}
	// IE
	else if (document.selection)
	{
		var r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
	}
}
<!-- BEGIN bb_panel -->
var  id ='text';
function bbcode(Tag) {
	var Open='['+Tag+']';
	var Close='[/'+Tag+']';
	var object = document.getElementById(id);
	object.focus();
	if (Open=='[url]'){
		var Open='['+Tag+'=http://]';
	}
	else if (Open=='[color]')
	{
		var Open='['+Tag+'=颜色代码]';
	}
	else if (Open=='[size]')
	{
		var Open='['+Tag+'=5]';
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
<!-- END bb_panel -->
<!-- BEGIN user_otv -->
function otv(u) {
	var t = document.getElementById('text');
		t.value  += '@' + u + ', ';
	var end = t.value.length; 
	t.setSelectionRange(end,end); 
	t.focus(); 
}
<!-- END user_otv -->
</script>
<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>&gt;{TOPIC_TITLE}</div>
{HOBOE}
{POLL_DISPLAY}
<div class="catSides">标题:{TOPIC_TITLE}<font color="#ffffff">[{TOPIC_VIEWS}阅/{TOPIC_REPLIES}回]</font></div>
<!-- BEGIN postrow -->
	<div class="{postrow.ROW_CLASS}" id="{postrow.U_POST_ID}">
		<div class="nav">
			{postrow.AVATAR_IMG}
			【<b>{postrow.NOMER_POSTA}</b>】
			{postrow.ICON}
			{postrow.USER_LEVEL}
			<b>{postrow.POSTER_NAME}{postrow.POSTER_GENDER}</b>
			{postrow.POSTER_ONLINE_STATUS}
			[{postrow.POSTER_ID}]
			{postrow.RANK_IMAGE}{postrow.POSTER_RANK}
		</div>
		{postrow.POST_DATE}
		{postrow.REPORT_MESSAGE}
		{postrow.MESSAGE}
		<hr /><font color="red">签名：{postrow.SIGNATURE}</font>
		{postrow.ATTACHMENTS}
		{postrow.EDITED_MESSAGE}{postrow.BR}	
		{postrow.SPECIAL}
		{postrow.SPECIAL_SELECT}
		（{postrow.REPORT}{postrow.YELLOW}{postrow.RED}{postrow.QUOTE2}{postrow.QUOTE}{postrow.EDIT}{postrow.DELETE}{postrow.IP}）
	</div>
<!-- END postrow -->
{PAGINATION}
{POSTTOPIC}
<div class="catSides">快捷面板</div>
<div class="nav">
<a href="{U_POST_REPLY_TOPIC}" class="buttom">{L_POST_REPLY_TOPIC}</a><br/>
<a href="{DOWNLOAD_TOPIC}" class="buttom">{L_DOWNLOAD_TOPIC}</a><br/>
<!-- BEGIN bookmark_state -->
<a href="{U_BOOKMARK_ACTION}" class="buttom">{L_BOOKMARK_ACTION}</a><br/>
<!-- END bookmark_state -->
{S_WATCH_TOPIC}
{TOPIC_CLOSED}
</div>
{S_TOPIC_ADMIN}
