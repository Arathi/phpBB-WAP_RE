<script language="javascript" type="text/javascript">
<!--
function update_smiley(newimage)
{
	document.smiley_image.src = "{S_SMILEY_BASEDIR}/" + newimage;
}
//-->
</script>
<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_SMILEY_ADMIN}">表情</a>&gt;{L_SMILEY_CONFIG}</div>
<form method="post" action="{S_SMILEY_ACTION}">
<span class="genmed">{L_SMILEY_EXPLAIN}</span>
<div class="catSides">{L_SMILEY_CONFIG}</div>
<div class="row1">{L_SMILEY_CODE}:<br/>
<input type="text" name="smile_code" value="{SMILEY_CODE}" />
</div>
<div class="row1">{L_SMILEY_URL}:<br/>
<select name="smile_url" onchange="update_smiley(this.options[selectedIndex].value);">{S_FILENAME_OPTIONS}</select> <img name="smiley_image" src="{SMILEY_IMG}" border="0" alt="" />
</div>
<div class="row1">{L_SMILEY_EMOTION}:<br/>
<input type="text" name="smile_emotion" value="{SMILEY_EMOTICON}" />
</div>
{S_HIDDEN_FIELDS}
<input type="submit" value="{L_SUBMIT}" />
</form>