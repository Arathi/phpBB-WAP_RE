<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_ALBUM_CONFIG}</div>
<span class="genmed">{L_ALBUM_CONFIG_EXPLAIN}</span>
<form action="{S_ALBUM_CONFIG_ACTION}" method="post">
	<div class="catSides">{L_ALBUM_CONFIG}</div>
	<div class="row1">
		{L_MAX_PICS}:<br/>
		<input type="text" maxlength="9" name="max_pics" value="{MAX_PICS}" />
	</div>
	<div class="row1">
		{L_USER_PICS_LIMIT}:<br/>
		<input type="text" maxlength="12" name="user_pics_limit" value="{USER_PICS_LIMIT}" />
	</div>
	<div class="row1">
		{L_MOD_PICS_LIMIT}:<br/>
		<input type="text" maxlength="12" name="mod_pics_limit" value="{MOD_PICS_LIMIT}" />
	</div>
	<div class="row1">
		{L_MAX_FILE_SIZE}:<br/>
		<input type="text" maxlength="12" name="max_file_size" value="{MAX_FILE_SIZE}" />
	</div>
	<div class="row1">
		{L_MAX_WIDTH}:<br/>
		<input type="text" maxlength="9" name="max_width" value="{MAX_WIDTH}" />
	</div>
	<div class="row1">
		{L_MAX_HEIGHT}:<br/>
		<input type="text" maxlength="9" name="max_height" value="{MAX_HEIGHT}" />
	</div>
	<div class="row1">
		{L_PIC_DESC_MAX_LENGTH}:<br/>
		<input type="text" name="desc_length" value="{PIC_DESC_MAX_LENGTH}" />
	</div>
	<div class="row1">
		{L_GD_VERSION}:<br/>
		<input type="radio" {NO_GD} name="gd_version" value="0" />{L_MANUAL_THUMBNAIL}&nbsp;&nbsp;<input type="radio" {GD_V1} name="gd_version" value="1" />GD1&nbsp;&nbsp;<input type="radio" {GD_V2} name="gd_version" value="2" />GD2
	</div>
	<div class="row1">
		{L_JPG_ALLOWED}:<br/>
		<input type="radio" {JPG_ENABLED} name="jpg_allowed" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {JPG_DISABLED} name="jpg_allowed" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_PNG_ALLOWED}:<br/>
		<input type="radio" {PNG_ENABLED} name="png_allowed" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {PNG_DISABLED} name="png_allowed" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_GIF_ALLOWED}:<br/>
		<input type="radio" {GIF_ENABLED} name="gif_allowed" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {GIF_DISABLED} name="gif_allowed" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_HOTLINK_PREVENT}:<br/>
		<input type="radio" {HOTLINK_PREVENT_ENABLED} name="hotlink_prevent" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {HOTLINK_PREVENT_DISABLED} name="hotlink_prevent" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_HOTLINK_ALLOWED}:<br/>
		<input type="text" name="hotlink_allowed" value="{HOTLINK_ALLOWED}" />
	</div>
	<div class="catSides">{L_THUMBNAIL_SETTINGS}</div>
	<div class="row1">
		{L_THUMBNAIL_CACHE}:<br/>
		<input type="radio" {THUMBNAIL_CACHE_ENABLED} name="thumbnail_cache" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {THUMBNAIL_CACHE_DISABLED} name="thumbnail_cache" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_THUMBNAIL_SIZE}:<br/>
		<input type="text" maxlength="4" name="thumbnail_size" value="{THUMBNAIL_SIZE}" />
	</div>
	<div class="row1">
		{L_THUMBNAIL_QUALITY}:<br/>
		<input type="text" maxlength="3" name="thumbnail_quality" value="{THUMBNAIL_QUALITY}" />
	</div>
	<div class="row1">
		{L_ROWS_PER_PAGE}:<br/>
		<input type="text" maxlength="2" name="rows_per_page" value="{ROWS_PER_PAGE}" />
	</div>
	<div class="row1">
		{L_DEFAULT_SORT_METHOD}:<br/>
		<select name="sort_method">
			<option {SORT_TIME} value='pic_time'>{L_TIME}</option>
			<option {SORT_PIC_TITLE} value='pic_title'>{L_PIC_TITLE}</option>
			<option {SORT_USERNAME} value='username'>{L_USERNAME}</option>
			<option {SORT_VIEW} value='pic_view_count'>{L_VIEW}</option>
			<option {SORT_RATING} value='rating'>{L_RATING}</option>
			<option {SORT_COMMENTS} value='comments'>{L_COMMENTS}</option>
			<option {SORT_NEW_COMMENT} value='new_comment'>{L_NEW_COMMENT}</option>
		</select>
	</div>
	<div class="row1">
		{L_DEFAULT_SORT_ORDER}:<br/>
		<select name="sort_order">
			<option {SORT_ASC} value='ASC'>{L_ASC}</option>
			<option {SORT_DESC} value='DESC'>{L_DESC}</option>
		</select>
	</div>
	<div class="row1">
		{L_FULLPIC_POPUP}:<br/>
		<input type="radio" {FULLPIC_POPUP_ENABLED} name="fullpic_popup" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {FULLPIC_POPUP_DISABLED} name="fullpic_popup" value="0" />{L_NO}
	</div>
	<div class="catSides">{L_EXTRA_SETTINGS}</div>
	<div class="row1">
		{L_PERSONAL_GALLERY}:<br/>
		<input type="radio" {PERSONAL_GALLERY_USER} name="personal_gallery" value="{S_USER}" />{L_REG}&nbsp;&nbsp;<input type="radio" {PERSONAL_GALLERY_PRIVATE} name="personal_gallery" value="{S_PRIVATE}" />{L_PRIVATE}&nbsp;&nbsp;<input type="radio" {PERSONAL_GALLERY_ADMIN} name="personal_gallery" value="{S_ADMIN}" />{L_ADMIN}
	</div>
	<div class="row1">
		{L_PERSONAL_GALLERY_LIMIT}:<br/>
		<input type="text" maxlength="5" name="personal_gallery_limit" value="{PERSONAL_GALLERY_LIMIT}" />
	</div>
	<div class="row1">
		{L_PERSONAL_GALLERY_VIEW}:<br/>
		<input type="radio" {PERSONAL_GALLERY_VIEW_ALL} name="personal_gallery_view" value="{S_GUEST}" />{L_GUEST}&nbsp;&nbsp;<input type="radio" {PERSONAL_GALLERY_VIEW_REG} name="personal_gallery_view" value="{S_USER}" />{L_REG}&nbsp;&nbsp;<input type="radio" {PERSONAL_GALLERY_VIEW_PRIVATE} name="personal_gallery_view" value="{S_PRIVATE}" />{L_PRIVATE}
	</div>
	<div class="row1">
		{L_RATE_SYSTEM}:<br/>
		<input type="radio" {RATE_ENABLED} name="rate" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {RATE_DISABLED} name="rate" value="0" />{L_NO}
	</div>
	<div class="row1">
		{L_RATE_SCALE}:<br/>
		<input type="text" name="rate_scale" value="{RATE_SCALE}" />
	</div>
	<div class="row1">
		{L_COMMENT_SYSTEM}:<br/>
		<input type="radio" {COMMENT_ENABLED} name="comment" value="1" />{L_YES}&nbsp;&nbsp;<input type="radio" {COMMENT_DISABLED} name="comment" value="0" />{L_NO}
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>