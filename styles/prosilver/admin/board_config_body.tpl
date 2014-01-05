<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">超级管理面板</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_CONFIGURATION_TITLE}</div>
<span class="genmed">{L_CONFIGURATION_EXPLAIN}</span>
<form action="{S_CONFIG_ACTION}" method="post">
<div class="catSides">{L_GENERAL_SETTINGS}</div>
<div class="row1">{L_SERVER_NAME}:<br/>
<input type="text" maxlength="255" name="server_name" value="{SERVER_NAME}" />
</div>
<div class="row1">{L_SERVER_PORT}:<br/>
<span class="genmed">{L_SERVER_PORT_EXPLAIN}</span><br/>
<input type="text" maxlength="5" size="5" name="server_port" value="{SERVER_PORT}" />
</div>
<div class="row1">{L_SCRIPT_PATH}:<br/>
<span class="genmed">{L_SCRIPT_PATH_EXPLAIN}</span><br/>
<input type="text" maxlength="255" name="script_path" value="{SCRIPT_PATH}" />
</div>
<div class="row1">{L_SITE_NAME}:<br/>
<input type="text" size="25" maxlength="100" name="sitename" value="{SITENAME}" />
</div>
<div class="row1">{L_SITE_DESCRIPTION}:<br/>
<input type="text" maxlength="255" name="site_desc" value="{SITE_DESCRIPTION}" />
</div>
<div class="row1">{L_INDEX_ANNOUNCEMENT}:<br/>
<span class="genmed">{L_INDEX_ANNOUNCEMENT_EXPLAIN}</span><br/>
<textarea name="index_announcement" rows="5" cols="30" style="width: 300px">{INDEX_ANNOUNCEMENT}</textarea>
</div>
<div class="row1">{L_DISABLE_BOARD}:<br/>
<span class="genmed">{L_DISABLE_BOARD_EXPLAIN}</span><br/>
<input type="radio" name="board_disable" value="1" {S_DISABLE_BOARD_YES} /> {L_YES}<br/>
<input type="radio" name="board_disable" value="0" {S_DISABLE_BOARD_NO} /> {L_NO}
</div>
<div class="row1">{L_SPISOK}:<br/>
<input type="radio" name="index_spisok" value="1" {SPISOK_YES} /> {L_YES}<br/>
<input type="radio" name="index_spisok" value="0" {SPISOK_NO} /> {L_NO}
</div>
<div class="row1">{L_ACCT_ACTIVATION}:<br/>
<input type="radio" name="require_activation" value="{ACTIVATION_NONE}" {ACTIVATION_NONE_CHECKED} /> {L_NONE}<br/>
<input type="radio" name="require_activation" value="{ACTIVATION_USER}" {ACTIVATION_USER_CHECKED} /> {L_USER}<br/>
<input type="radio" name="require_activation" value="{ACTIVATION_ADMIN}" {ACTIVATION_ADMIN_CHECKED} /> {L_ADMIN}
</div>
<div class="row1">{L_VISUAL_CONFIRM}:<br/><span class="genmed">{L_VISUAL_CONFIRM_EXPLAIN}</span><br/>
<input type="radio" name="enable_confirm" value="1" {CONFIRM_ENABLE} /> {L_YES}<br/>
<input type="radio" name="enable_confirm" value="0" {CONFIRM_DISABLE} /> {L_NO}
</div> 
<div class="row1">{L_BOARD_EMAIL_FORM}:<br/>
<span class="genmed">{L_BOARD_EMAIL_FORM_EXPLAIN}</span><br/>
<input type="radio" name="board_email_form" value="1" {BOARD_EMAIL_FORM_ENABLE} /> {L_ENABLED}<br/>
<input type="radio" name="board_email_form" value="0" {BOARD_EMAIL_FORM_DISABLE} /> {L_DISABLED}
</div>
<div class="row1">{L_ICQ_SEND}:<br/>
<span class="genmed">{L_ICQ_SEND_EXPLAIN}</span><br/>
<input type="radio" name="send_user_icq" value="1" {ICQ_YES} /> {L_ENABLED}<br/>
<input type="radio" name="send_user_icq" value="0" {ICQ_NO} /> {L_DISABLED}
</div>
<div class="row1">{L_DEFAULT_ICQ}:<br/>
<span class="genmed">{L_DEFAULT_ICQ_EXPLAIN}</span><br/>
<input type="text" maxlength="25" name="default_icq" value="{DEFAULT_ICQ}" />
</div>
<div class="row1">{L_DEFAULT_ICQ_PASS}:<br/>
<input type="text" maxlength="25" name="default_icq_pass" value="{DEFAULT_ICQ_PASS}" />
</div>
<div class="row1">{L_FLOOD_ICQ_INTERVAL}:<br/>
<span class="genmed">{L_FLOOD_ICQ_INTERVAL_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="flood_icq_interval" value="{FLOOD_ICQ_INTERVAL}" />
</div>
<div class="row1">{L_FLOOD_INTERVAL}:<br/>
<span class="genmed">{L_FLOOD_INTERVAL_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="flood_interval" value="{FLOOD_INTERVAL}" />
</div>
<div class="row1">{L_CAPTCHA_IN_TOPIC}:<br/>
<input type="radio" name="captcha_in_topic" value="1" {CAPTCHA_IN_TOPIC_YES} /> {L_YES}<br/>
<input type="radio" name="captcha_in_topic" value="0" {CAPTCHA_IN_TOPIC_NO} /> {L_NO}
</div>
<div class="row1">{L_MESSAGE_QUOTE}:<br/>
<input type="radio" name="message_quote" value="1" {MESSAGE_QUOTE_YES} /> {L_YES}<br/>
<input type="radio" name="message_quote" value="0" {MESSAGE_QUOTE_NO} /> {L_NO}
</div>
<div class="row1">{L_SEARCH_FLOOD_INTERVAL}:<br/>
<span class="genmed">{L_SEARCH_FLOOD_INTERVAL_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="search_flood_interval" value="{SEARCH_FLOOD_INTERVAL}" />
</div>
<div class="row1">{L_MAX_LOGIN_ATTEMPTS}:<br/>
<span class="genmed">{L_MAX_LOGIN_ATTEMPTS_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="max_login_attempts" value="{MAX_LOGIN_ATTEMPTS}" />
</div>
<div class="row1">{L_MIN_LOGIN_REGDATE}:<br/>
<span class="genmed">{L_MIN_LOGIN_REGDATE_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="min_login_regdate" value="{MIN_LOGIN_REGDATE}" />
</div>
<div class="row1">{L_LOGIN_RESET_TIME}:<br/>
<span class="genmed">{L_LOGIN_RESET_TIME_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="login_reset_time" value="{LOGIN_RESET_TIME}" />
</div>
<div class="row1">{L_TOPICS_PER_PAGE}:<br/>
<input type="text" name="topics_per_page" size="3" maxlength="4" value="{TOPICS_PER_PAGE}" />
</div>

<div class="row1">{L_POSTS_PER_PAGE}:<br/>
<input type="text" name="posts_per_page" size="3" maxlength="4" value="{POSTS_PER_PAGE}" />
</div>
<div class="row1">{L_MAX_USER_TOPICS_PER_PAGE}:<br/>
<input type="text" name="max_user_topics_per_page" size="3" maxlength="4" value="{MAX_USER_TOPICS_PER_PAGE}" />
</div>
<div class="row1">{L_MAX_USER_POSTS_PER_PAGE}:<br/>
<input type="text" name="max_user_posts_per_page" size="3" maxlength="4" value="{MAX_USER_POSTS_PER_PAGE}" />
</div>
<div class="row1">{L_HOT_THRESHOLD}:<br/>
<input type="text" name="hot_threshold" size="3" maxlength="4" value="{HOT_TOPIC}" />
</div>
<div class="row1">{L_DATE_FORMAT}:<br/>
<input type="text" name="default_dateformat" value="{DEFAULT_DATEFORMAT}" />
</div>
<div class="row1">{L_SYSTEM_TIMEZONE}:<br/>
{TIMEZONE_SELECT}
</div>
<div class="row1">{L_DEFAULT_STYLE}:<br/>
{STYLE_SELECT}
</div>
<div class="row1">{L_ONLINE_TIME}:<br/><span class="genmed">{L_ONLINE_TIME_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="online_time" value="{ONLINE_TIME}" />
</div>
<div class="row1">{L_ENABLE_GZIP}:<br/>
<input type="radio" name="gzip_compress" value="1" {GZIP_YES} /> {L_YES}<br/>
<input type="radio" name="gzip_compress" value="0" {GZIP_NO} /> {L_NO}
</div>
<div class="row1">{L_ENABLE_PRUNE}:<br/>
<input type="radio" name="prune_enable" value="1" {PRUNE_YES} /> {L_YES}<br/>
<input type="radio" name="prune_enable" value="0" {PRUNE_NO} /> {L_NO}
</div>
<div class="row1">{L_NO_GUEST_GB}<br />
<input type="radio" name="allow_guests_gb" value="1" {NO_GUEST_YES} /> {L_YES}<br />
<input type="radio" name="allow_guests_gb" value="0" {NO_GUEST_NO} /> {L_NO}
</div>
<div class="row1">{L_GB_QUICK}<br />
<input type="radio" name="gb_quick" value="1" {GB_QUICK_YES} /> {L_YES}<br />
<input type="radio" name="gb_quick" value="0" {GB_QUICK_NO} /> {L_NO}
</div>
<div class="row1">{L_POST_GB}<br />
<input type="text" name="gb_posts" value="{GB_POST}" />
</div>
<div class="row1">{L_REPORT_EMAIL}:<br/>
<input type="radio" name="report_email" value="1" {REPORT_EMAIL_YES} /> {L_YES}<br/>
<input type="radio" name="report_email" value="0" {REPORT_EMAIL_NO} /> {L_NO}
</div>
<div class="row1">{L_POSLEDNEE_REDAKTIROVANIE}:<br/>
<span class="genmed">{L_POSLEDNEE_REDAKTIROVANIE_EXPLAIN}</span><br/>
<input type="radio" name="posl_red" value="1" {POSL_YES} /> {L_YES}<br/>
<input type="radio" name="posl_red" value="0" {POSL_NO} /> {L_NO}
</div>
<div class="row1">{L_BIRTHDAY_REQUIRED}:<br/>
<input type="radio" name="birthday_required" value="1" {BIRTHDAY_REQUIRED_YES} /> {L_YES}<br/>
<input type="radio" name="birthday_required" value="0" {BIRTHDAY_REQUIRED_NO} /> {L_NO}
</div>
<div class="row1">{L_MAX_USER_AGE}:<br/>
<input type="text" size="4" maxlength="4" name="max_user_age" value="{MAX_USER_AGE}" />
</div>
<div class="row1">{L_MIN_USER_AGE}:<br/>
<input type="text" size="4" maxlength="4" name="min_user_age" value="{MIN_USER_AGE}" />
</div>
<div class="row1">{L_BIRTHDAY_LOOKFORWARD}:<br/>
<input type="radio" name="birthday_check_day" value="1" {BIRTHDAY_LOOK_YES} /> {L_YES}<br/>
<input type="radio" name="birthday_check_day" value="0" {BIRTHDAY_LOOK_NO} /> {L_NO}
</div>
<div class="row1">下一个新用户的ID:<br/>
<input type="text" size="4" maxlength="4" name="next_new_user_id" value="0" />
</div>
<div class="catSides">
{L_COOKIE_SETTINGS}
</div>
<div class="row_hard">
<span class="genmed">{L_COOKIE_SETTINGS_EXPLAIN}</span>
</div>
<div class="row1">{L_COOKIE_DOMAIN}:<br/>
<input type="text" maxlength="255" name="cookie_domain" value="{COOKIE_DOMAIN}" />
</div>
<div class="row1">{L_COOKIE_NAME}:<br/>
<input type="text" maxlength="16" name="cookie_name" value="{COOKIE_NAME}" />
</div>
<div class="row1">{L_COOKIE_PATH}:<br/>
<input type="text" maxlength="255" name="cookie_path" value="{COOKIE_PATH}" />
</div>
<div class="row1">{L_COOKIE_SECURE}:<br/>
<span class="genmed">{L_COOKIE_SECURE_EXPLAIN}</span><br/>
{L_DISABLED} <input type="radio" name="cookie_secure" value="0" {S_COOKIE_SECURE_DISABLED} /><br/>
{L_ENABLED} <input type="radio" name="cookie_secure" value="1" {S_COOKIE_SECURE_ENABLED} />
</div>
<div class="row1">{L_SESSION_LENGTH}:<br/>
<input type="text" maxlength="5" size="5" name="session_length" value="{SESSION_LENGTH}" />
</div>
<div class="catSides">
{L_PRIVATE_MESSAGING}
</div>
<div class="row1">{L_DISABLE_PRIVATE_MESSAGING}:<br/>
<input type="radio" name="privmsg_disable" value="0" {S_PRIVMSG_ENABLED} /> {L_ENABLED}<br/>
<input type="radio" name="privmsg_disable" value="1" {S_PRIVMSG_DISABLED} /> {L_DISABLED}
</div>
<div class="row1">{L_INBOX_LIMIT}:<br/>
<input type="text" maxlength="4" size="4" name="max_inbox_privmsgs" value="{INBOX_LIMIT}" />
</div>
<div class="row1">{L_SENTBOX_LIMIT}:<br/>
<input type="text" maxlength="4" size="4" name="max_sentbox_privmsgs" value="{SENTBOX_LIMIT}" />
</div>
<div class="row1">{L_SAVEBOX_LIMIT}:<br/>
<input type="text" maxlength="4" size="4" name="max_savebox_privmsgs" value="{SAVEBOX_LIMIT}" />
</div>
<div class="catSides">
{L_ABILITIES_SETTINGS}
</div>
<div class="row1">{L_MAX_POLL_OPTIONS}:<br/>
<input type="text" name="max_poll_options" size="4" maxlength="4" value="{MAX_POLL_OPTIONS}" />
</div>
<div class="row1">{L_ALLOW_BBCODE}:<br/>
<input type="radio" name="allow_bbcode" value="1" {BBCODE_YES} /> {L_YES}<br/>
<input type="radio" name="allow_bbcode" value="0" {BBCODE_NO} /> {L_NO}
</div>
<div class="row1">{L_ALLOW_SMILIES}:<br/>
<input type="radio" name="allow_smilies" value="1" {SMILE_YES} /> {L_YES}<br/>
<input type="radio" name="allow_smilies" value="0" {SMILE_NO} /> {L_NO}
</div>
<div class="row1">{L_SMILIES_PATH}:<br/>
<span class="genmed">{L_SMILIES_PATH_EXPLAIN}</span><br/>
<input type="text" size="20" maxlength="255" name="smilies_path" value="{SMILIES_PATH}" />
</div>
<div class="row1">{L_MAX_SMILES}:<br/>
<input type="text" maxlength="4" size="4" name="max_smiles_in_message" value="{MAX_SMILES}" />
</div>
<div class="row1">{L_ALLOW_SIG}:<br/>
<input type="radio" name="allow_sig" value="1" {SIG_YES} /> {L_YES}<br/>
<input type="radio" name="allow_sig" value="0" {SIG_NO} /> {L_NO}
</div>
<div class="row1">{L_MAX_SIG_LENGTH}:<br/>
<span class="genmed">{L_MAX_SIG_LENGTH_EXPLAIN}</span><br/>
<input type="text" size="5" maxlength="4" name="max_sig_chars" value="{SIG_SIZE}" />
</div>
<div class="row1">{L_ALLOW_NAME_CHANGE}:<br/>
<input type="radio" name="allow_namechange" value="1" {NAMECHANGE_YES} /> {L_YES}<br/>
<input type="radio" name="allow_namechange" value="0" {NAMECHANGE_NO} /> {L_NO}
</div>
<div class="catSides">
{L_AVATAR_SETTINGS}
</div>
<div class="row1">{L_ALLOW_LOCAL}:<br/>
<input type="radio" name="allow_avatar_local" value="1" {AVATARS_LOCAL_YES} /> {L_YES}<br/>
<input type="radio" name="allow_avatar_local" value="0" {AVATARS_LOCAL_NO} /> {L_NO}
</div>
<div class="row1">{L_ALLOW_REMOTE}:<br/>
<span class="genmed">{L_ALLOW_REMOTE_EXPLAIN}</span><br/>
<input type="radio" name="allow_avatar_remote" value="1" {AVATARS_REMOTE_YES} /> {L_YES}<br/>
<input type="radio" name="allow_avatar_remote" value="0" {AVATARS_REMOTE_NO} /> {L_NO}
</div>
<div class="row1">{L_ALLOW_UPLOAD}:<br/>
<input type="radio" name="allow_avatar_upload" value="1" {AVATARS_UPLOAD_YES} /> {L_YES}<br/>
<input type="radio" name="allow_avatar_upload" value="0" {AVATARS_UPLOAD_NO} /> {L_NO}
</div>
<div class="row1">{L_MAX_FILESIZE}:<br/>
<span class="genmed">{L_MAX_FILESIZE_EXPLAIN}</span><br/>
<input type="text" size="4" maxlength="10" name="avatar_filesize" value="{AVATAR_FILESIZE}" /> Bytes
</div>
<div class="row1">{L_MAX_AVATAR_SIZE}:<br/>
<span class="genmed">{L_MAX_AVATAR_SIZE_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="4" name="avatar_max_height" value="{AVATAR_MAX_HEIGHT}" />
x 
<input type="text" size="3" maxlength="4" name="avatar_max_width" value="{AVATAR_MAX_WIDTH}" />
</div>
<div class="row1">{L_AVATAR_STORAGE_PATH}:<br/>
<span class="genmed">{L_AVATAR_STORAGE_PATH_EXPLAIN}</span><br/>
<input type="text" size="20" maxlength="255" name="avatar_path" value="{AVATAR_PATH}" />
</div>
<div class="row1">{L_AVATAR_GALLERY_PATH}:<br/>
<span class="genmed">{L_AVATAR_GALLERY_PATH_EXPLAIN}</span><br/>
<input type="text" size="20" maxlength="255" name="avatar_gallery_path" value="{AVATAR_GALLERY_PATH}" />
</div>
<div class="catSides">
{L_EMAIL_SETTINGS}
</div>
<div class="row1">{L_ADMIN_EMAIL}:<br/>
<input type="text" size="25" maxlength="100" name="board_email" value="{EMAIL_FROM}" />
</div>
<div class="row1">{L_EMAIL_SIG}:<br/>
<span class="genmed">{L_EMAIL_SIG_EXPLAIN}</span><br/>
<textarea name="board_email_sig" rows="5" cols="30" style="width: 300px">{EMAIL_SIG}</textarea>
</div>
<div class="row1">{L_USE_SMTP}:<br/>
<span class="genmed">{L_USE_SMTP_EXPLAIN}</span><br/>
<input type="radio" name="smtp_delivery" value="1" {SMTP_YES} /> {L_YES}<br/>
<input type="radio" name="smtp_delivery" value="0" {SMTP_NO} /> {L_NO}
</div>
<div class="row1">{L_SMTP_SERVER}:<br/>
<input type="text" name="smtp_host" value="{SMTP_HOST}" size="25" maxlength="50" />
</div>
<div class="row1">{L_SMTP_USERNAME}:<br/>
<span class="genmed">{L_SMTP_USERNAME_EXPLAIN}</span><br/>
<input type="text" name="smtp_username" value="{SMTP_USERNAME}" size="25" maxlength="255" />
</div>
<div class="row1">{L_SMTP_PASSWORD}:<br/>
<span class="genmed">{L_SMTP_PASSWORD_EXPLAIN}</span><br/>
<input type="password" name="smtp_password" value="{SMTP_PASSWORD}" size="25" maxlength="255" />
</div>
<br/>
{S_HIDDEN_FIELDS} 
<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>