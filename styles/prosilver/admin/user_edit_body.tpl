<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_USERS_ADMIN}">选择用户</a>&gt;{L_USER_TITLE}</div>
{ERROR_BOX}
<span class="genmed">{L_USER_EXPLAIN}</span><br/>
<form action="{S_PROFILE_ACTION}" {S_FORM_ENCTYPE} method="post">
	<div class="catSides">{L_REGISTRATION_INFO}</div>
	<div class="row_hard">
		<span class="genmed">{L_ITEMS_REQUIRED}</span>
	</div>
	<div class="row1">
		{L_USERNAME}: *<br/>
		<input type="text" name="username" maxlength="40" value="{USERNAME}" />
	</div>
	<div class="row1">
		{L_EMAIL_ADDRESS}: *<br/>
		<input type="text" name="email" maxlength="255" value="{EMAIL}" />
	</div>
	<div class="row1">
		{L_NEW_PASSWORD}: <br/>
		<span class="genmed">{L_PASSWORD_IF_CHANGED}</span><br/>
		<input type="password" name="password" maxlength="32" value="" />
	</div>
	<div class="row1">
		{L_CONFIRM_PASSWORD}: <br/>
		<span class="genmedl">{L_PASSWORD_CONFIRM_IF_CHANGED}</span><br/>
		<input type="password" name="password_confirm" maxlength="32" value="" />
	</div>
	<div class="catSides">{L_PROFILE_INFO}</div>
	<div class="row_hard">
		<span class="genmed">{L_PROFILE_INFO_NOTICE}</span>
	</div>
	<div class="row1">
		{L_ICQ_NUMBER}:<br/>
		<input type="text" name="icq" maxlength="15" value="{ICQ}" />
	</div>
	<div class="row1">
		{L_NUMBER}:<br/>
		<input type="text" name="number" maxlength="15" value="{NUMBER}" />
	</div>
	<div class="row1">
		{L_AIM}:<br/>
		<input type="text" name="aim" maxlength="255" value="{AIM}" />
	</div>
	<div class="row1">
		{L_MESSENGER}:<br/>
		<input type="text" name="msn" maxlength="255" value="{MSN}" />
	</div>
	<div class="row1">
		{L_YAHOO}:<br/>
		<input type="text" name="yim" maxlength="255" value="{YIM}" />
	</div>
	<div class="row1">
		{L_WEBSITE}:<br/>
		<input type="text" name="website" maxlength="255" value="{WEBSITE}" />
	</div>
	<div class="row1">
		{L_SIGNATURE}:<br />
		<span class="genmed">{L_SIGNATURE_EXPLAIN}</span><br />
		<input type="text" name="signature" maxlength="255" value="{SIGNATURE}" />
	</div>
	<div class="row1">
		{L_LOCATION}:<br/>
		<input type="text" name="location" maxlength="100" value="{LOCATION}" />
	</div>
	<div class="row1">
		{L_OCCUPATION}:<br/>
		<input type="text" name="occupation" maxlength="100" value="{OCCUPATION}" />
	</div>
	<div class="row1">
		{L_INTERESTS}:<br/>
		<input type="text" name="interests" maxlength="150" value="{INTERESTS}" />
	</div>
	<div class="row1">
		{L_BIRTHDAY}:<br/>
		{S_BIRTHDAY}
	</div>
	<div class="row1">
		{L_GENDER}:<br/>
		<input type="radio" name="gender" value="0" {GENDER_NO_SPECIFY_CHECKED}/> {L_GENDER_NOT_SPECIFY}<br/>
		<input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/> {L_GENDER_MALE}<br/>
		<input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/> {L_GENDER_FEMALE}
	</div> 
	<div class="catSides">{L_PREFERENCES}</div>
	<div class="row1">
		{L_PUBLIC_VIEW_EMAIL}:<br/>
		<input type="radio" name="viewemail" value="1" {VIEW_EMAIL_YES} /> {L_YES}
		<input type="radio" name="viewemail" value="0" {VIEW_EMAIL_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_HIDE_USER}:<br/>
		<input type="radio" name="hideonline" value="1" {HIDE_USER_YES} /> {L_YES}
		<input type="radio" name="hideonline" value="0" {HIDE_USER_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_NOTIFY_ON_REPLY}:<br/>
		<input type="checkbox" name="notifyreply_to_pm"{NOTIFY_REPLY_TO_PM} /> {L_NOTIFY_ON_REPLY_TO_PM}<br/>
		<input type="checkbox" name="notifyreply_to_email"{NOTIFY_REPLY_TO_EMAIL} /> {L_NOTIFY_ON_REPLY_TO_EMAIL}
	</div>
	<div class="row1">
		{L_NOTIFY_ON_PRIVMSG}:<br/>
		<input type="radio" name="notifypm" value="1" {NOTIFY_PM_YES} /> {L_YES}
		<input type="radio" name="notifypm" value="0" {NOTIFY_PM_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_POPUP_ON_PRIVMSG}:<br/>
		<input type="radio" name="popup_pm" value="1" {POPUP_PM_YES} /> {L_YES}
		<input type="radio" name="popup_pm" value="0" {POPUP_PM_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_GB_EMAIL}:<br />
		<input type="radio" name="gb_email" value="1" {GB_EMAIL_YES} /> {L_YES}
		<input type="radio" name="gb_email" value="0" {GB_EMAIL_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_GB_CAN}:<br />
		<input type="radio" name="gb_can" value="1" {GB_CAN_YES} /> {L_YES}
		<input type="radio" name="gb_can" value="0" {GB_CAN_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALWAYS_ADD_SIGNATURE}:<br />
		<input type="radio" name="attachsig" value="1" {ALWAYS_ADD_SIGNATURE_YES} /> {L_YES}
		<input type="radio" name="attachsig" value="0" {ALWAYS_ADD_SIGNATURE_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALWAYS_ALLOW_BBCODE}:<br />
		<input type="radio" name="allowbbcode" value="1" {ALWAYS_ALLOW_BBCODE_YES} /> {L_YES}
		<input type="radio" name="allowbbcode" value="0" {ALWAYS_ALLOW_BBCODE_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALWAYS_ALLOW_HTML}:<br />
		<input type="radio" name="allowhtml" value="1" {ALWAYS_ALLOW_HTML_YES} /> {L_YES}
		<input type="radio" name="allowhtml" value="0" {ALWAYS_ALLOW_HTML_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALWAYS_ALLOW_SMILIES}:<br />
		<input type="radio" name="allowsmilies" value="1" {ALWAYS_ALLOW_SMILIES_YES} /> {L_YES}
		<input type="radio" name="allowsmilies" value="0" {ALWAYS_ALLOW_SMILIES_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_NIC_COLOR}:<br />
		<input type="text" name="nic_color" maxlength="10" value="{NIC_COLOR}" />
	</div>
	<div class="row1">
		{L_TIMEZONE}:<br />
		{TIMEZONE_SELECT}
	</div>
	<div class="row1">
		{L_DATE_FORMAT}:<br />
		<input type="text" name="dateformat" value="{DATE_FORMAT}" maxlength="16" />
	</div>
	<div class="row1">
		{L_TOPICS_PER_PAGE}:<br />
		<input type="text" name="topics_per_page" value="{TOPICS_PER_PAGE}" size="5" maxlength="3" />
	</div>
	<div class="row1">
		{L_POSTS_PER_PAGE}:<br />
		<input type="text" name="posts_per_page" value="{POSTS_PER_PAGE}" size="5" maxlength="3" />
	</div>
	<div class="catSides">{L_AVATAR_PANEL}</div>
	<div class="row1">
		{L_CURRENT_IMAGE}:<br />
		{AVATAR}<br />
		<input type="checkbox" name="avatardel" /> {L_DELETE_AVATAR}
	</div>
	<!-- BEGIN avatar_local_upload -->
	<div class="row1">
		{L_UPLOAD_AVATAR_FILE}:<br />
		<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
		<input type="file" name="avatar" />
	</div>
	<!-- END avatar_local_upload -->
	<!-- BEGIN avatar_remote_upload -->
	<div class="row1">
		{L_UPLOAD_AVATAR_URL}:<br />
		<input type="text" name="avatarurl" />
	</div>
	<!-- END avatar_remote_upload -->
	<!-- BEGIN avatar_remote_link -->
	<div class="row1">
		{L_LINK_REMOTE_AVATAR}:<br />
		<input type="text" name="avatarremoteurl" />
	</div>
	<!-- END avatar_remote_link -->
	<!-- BEGIN avatar_local_gallery -->
	<div class="row1">
		{L_AVATAR_GALLERY}:<br />
		<input type="submit" name="avatargallery" value="{L_SHOW_GALLERY}" />
	</div>
	<!-- END avatar_local_gallery -->
	<div class="catSides">{L_SPECIAL}</div>
	<div class="row_hard">
		<span class="genmed">{L_SPECIAL_EXPLAIN}</span>
	</div>
	<div class="row1">
		{L_UPLOAD_QUOTA}:<br />
		{S_SELECT_UPLOAD_QUOTA}
	</div>
	<div class="row1">
		{L_PM_QUOTA}:<br />
		{S_SELECT_PM_QUOTA}
	</div>
	<div class="row1">
		{L_USER_ACTIVE}:<br />
		<input type="radio" name="user_status" value="1" {USER_ACTIVE_YES} /> {L_YES}
		<input type="radio" name="user_status" value="0" {USER_ACTIVE_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALLOW_PM}:<br />
		<input type="radio" name="user_allowpm" value="1" {ALLOW_PM_YES} /> {L_YES}
		<input type="radio" name="user_allowpm" value="0" {ALLOW_PM_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_ALLOW_AVATAR}:<br/>
		<input type="radio" name="user_allowavatar" value="1" {ALLOW_AVATAR_YES} /> {L_YES}
		<input type="radio" name="user_allowavatar" value="0" {ALLOW_AVATAR_NO} /> {L_NO}
	</div>
	<div class="row1">
		{L_SELECT_RANK}:<br/>
		<select name="user_rank">
			{RANK_SELECT_BOX}
		</select>
	</div>
	<div class="row1">
		{L_USER_ZVANIE}:<br/>
		<input type="text" name="user_zvanie" maxlength="50" value="{USER_ZVANIE}" />
	</div>
	<div class="row1">
		{L_DELETE_USER}:<br />
		<input type="checkbox" name="deleteuser" />
		{L_DELETE_USER_EXPLAIN}
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>