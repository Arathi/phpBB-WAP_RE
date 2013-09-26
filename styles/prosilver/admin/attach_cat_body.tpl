<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_MANAGE_CAT_TITLE}</div>
{ERROR_BOX}
<span class="genmed">{L_MANAGE_CAT_EXPLAIN}</span>
<form action="{S_ATTACH_ACTION}" method="post">
<div class="catSides">{L_MANAGE_CAT_TITLE}</div>
<div class="row1">
<span class="genmed">{L_SETTINGS_CAT_IMAGES}</span><br />
{L_ASSIGNED_GROUP}: {S_ASSIGNED_GROUP_IMAGES}
</div>
<div class="row1">
{L_DISPLAY_INLINED}<br/>
<input type="radio" name="img_display_inlined" value="1" {DISPLAY_INLINED_YES} /> {L_YES}<br/>
<input type="radio" name="img_display_inlined" value="0" {DISPLAY_INLINED_NO} /> {L_NO}
</div>
<!-- BEGIN switch_thumbnail_support -->
<div class="row1">
{L_CREATE_THUMBNAIL}<br/>
<span class="genmed">{L_CREATE_THUMBNAIL_EXPLAIN}</span><br/>
<input type="radio" name="img_create_thumbnail" value="1" {CREATE_THUMBNAIL_YES} /> {L_YES}<br/>
<input type="radio" name="img_create_thumbnail" value="0" {CREATE_THUMBNAIL_NO} /> {L_NO}
</div>
<div class="row1">
{L_MIN_THUMB_FILESIZE}<br/>
<input type="text" size="7" maxlength="15" name="img_min_thumb_filesize" value="{IMAGE_MIN_THUMB_FILESIZE}" /> {L_BYTES}
</div>
<div class="row1">
{L_USE_GD2}<br/>
<span class="genmed">{L_USE_GD2_EXPLAIN}</span><br/>
<input type="radio" name="use_gd2" value="1" {USE_GD2_YES} /> {L_YES}<br/>
<input type="radio" name="use_gd2" value="0" {USE_GD2_NO} /> {L_NO}
</div>
<!-- END switch_thumbnail_support -->
<div class="row1">
{L_IMAGICK_PATH}<br/>
<span class="genmed">{L_IMAGICK_PATH_EXPLAIN}</span><br/>
<input type="text" size="20" maxlength="200" name="img_imagick" value="{IMAGE_IMAGICK_PATH}" />
</div>
<div class="row1">
{L_MAX_IMAGE_SIZE}<br/>
<input type="text" size="3" maxlength="4" name="img_max_width" value="{IMAGE_MAX_WIDTH}" /> x <input type="text" size="3" maxlength="4" name="img_max_height" value="{IMAGE_MAX_HEIGHT}" />
</div>
<div class="row1">
{L_IMAGE_LINK_SIZE}<br/>
<input type="text" size="3" maxlength="4" name="img_link_width" value="{IMAGE_LINK_WIDTH}" /> x <input type="text" size="3" maxlength="4" name="img_link_height" value="{IMAGE_LINK_HEIGHT}" />
</div>
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{L_SUBMIT}" />
<input type="submit" name="search_imagick" value="{L_SEARCH_IMAGICK}" />
<input type="submit" name="cat_settings" value="{L_TEST_SETTINGS}" />
</form>