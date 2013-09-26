<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}</a>&gt;<a href="{U_VIEW_CAT}">{CAT_TITLE}</a>&gt;{L_UPLOAD_PIC}</div>
<form name="upload" action="{S_ALBUM_ACTION}" method="post" enctype="multipart/form-data">
<div class="catSides">
{L_UPLOAD_PIC}</div>
<!-- BEGIN switch_user_logged_out -->
<div class="row1">{L_USERNAME}:<br/>
<input type="text" name="pic_username" maxlength="32" />
</div>
<!-- END switch_user_logged_out -->
<div class="row1">{L_PIC_TITLE}:<br/>
<input type="text" name="pic_title" />
</div>
<div class="row1">{L_PIC_DESC}:<br />
<span class="genmed">{L_PLAIN_TEXT_ONLY}<br />{L_MAX_LENGTH}: <b>{S_PIC_DESC_MAX_LENGTH}</b></span><br/>
<textarea name="pic_desc" rows="5" cols="15" style="width: 235px;"></textarea>
</div>
<div class="row1">{L_UPLOAD_PIC_FROM_MACHINE_OM}:<br/>
<input name="picupload" value = ""><br/>
<a href="op:fileselect">Обзор...</a>
</div>
<!-- BEGIN switch_manual_thumbnail -->
<div class="row1">{L_UPLOAD_THUMBNAIL}:<br />
<span class="genmed">{L_UPLOAD_THUMBNAIL_EXPLAIN}</span><br/>
<input type="file" name="pic_thumbnail" /><br/>
{L_THUMBNAIL_SIZE}:<br/>
<b>{S_THUMBNAIL_SIZE}</b>
</div>
<!-- END switch_manual_thumbnail -->
<div class="row1">{L_UPLOAD_TO_CATEGORY}:<br/>
{SELECT_CAT}
</div>
<div class="catSides">Информация
</div>
<div class="row1">
{L_MAX_FILESIZE}: <b>{S_MAX_FILESIZE}</b><br/>
{L_MAX_WIDTH}: <b>{S_MAX_WIDTH}</b><br/>
{L_MAX_HEIGHT}: <b>{S_MAX_HEIGHT}</b><br/>
{L_ALLOWED_JPG}: <b>{S_JPG}</b><br/>
{L_ALLOWED_PNG}: <b>{S_PNG}</b><br/>
{L_ALLOWED_GIF}: <b>{S_GIF}</b>
</div>
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>