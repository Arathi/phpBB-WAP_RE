<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_MANAGE_TITLE}</div>
<span class="genmed">{L_MANAGE_EXPLAIN}</span><br/>
{ERROR_BOX}
<form action="{S_ATTACH_ACTION}" method="post">
<div class="catSides">
{L_ATTACHMENT_SETTINGS}
</div>
<div class="row1">
{L_UPLOAD_DIR}:<br/>
<input type="text" maxlength="100" name="upload_dir" value="{UPLOAD_DIR}" />
</div>
<div class="row1">
{L_ATTACHMENT_IMG_PATH}:<br/>
<span class="genmed">{L_IMG_PATH_EXPLAIN}</span><br/>
<input type="text" maxlength="100" name="upload_img" value="{ATTACHMENT_IMG_PATH}" />
</div>
<div class="row1">
{L_ATTACHMENT_TOPIC_ICON}:<br/>
<span class="genmed">{L_TOPIC_ICON_EXPLAIN}</span><br/>
<input type="text" maxlength="100" name="topic_icon" value="{TOPIC_ICON}" />
</div>
<div class="row1">
{L_DISPLAY_ORDER}:<br/>
<input type="radio" name="display_order" value="0" {DISPLAY_ORDER_DESC} /> {L_DESC}<br/>
<input type="radio" name="display_order" value="1" {DISPLAY_ORDER_ASC} /> {L_ASC}
</div>
<div class="row1">
下载扣取多少积分:<br/>
<input type="text" size="3" maxlength="3" name="download_cut_points" value="{DOWNLOAD_CUT_POINTS}" />
</div>
<div class="row1">
上传获得多少积分:<br/>
<input type="text" size="3" maxlength="3" name="download_add_points" value="{DOWNLOAD_ADD_POINTS}" />
</div>
<div class="catSides">
{L_ATTACHMENT_FILESIZE_SETTINGS}
</div>
<div class="row1">
{L_MAX_FILESIZE}:<br/>
<span class="genmed">{L_MAX_FILESIZE_EXPLAIN}</span><br/>
<input type="text" size="8" maxlength="15" name="max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}
</div>
<div class="row1">
{L_ATTACH_QUOTA}:<br/>
<span class="genmed">{L_ATTACH_QUOTA_EXPLAIN}</span><br/>
<input type="text" size="8" maxlength="15" name="attachment_quota" value="{ATTACHMENT_QUOTA}" /> {S_FILESIZE_QUOTA}
</div>
<div class="row1">
{L_MAX_FILESIZE_PM}:<br/>
<span class="genmed">{L_MAX_FILESIZE_PM_EXPLAIN}</span><br/>
<input type="text" size="8" maxlength="15" name="max_filesize_pm" value="{MAX_FILESIZE_PM}" /> {S_FILESIZE_PM}
</div>
<div class="row1">
{L_DEFAULT_QUOTA_LIMIT}:<br/>
<span class="genmed">{L_DEFAULT_QUOTA_LIMIT_EXPLAIN}</span><br/>
{L_UPLOAD_QUOTA} {S_DEFAULT_UPLOAD_LIMIT}<br/>
{L_PM_QUOTA} {S_DEFAULT_PM_LIMIT}
</div>
<div class="catSides">
{L_ATTACHMENT_NUMBER_SETTINGS}
</div>
<div class="row1">
{L_MAX_ATTACHMENTS}:<br/>
<span class="genmed">{L_MAX_ATTACHMENTS_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="3" name="max_attachments" value="{MAX_ATTACHMENTS}" />
</div>
<div class="row1">
{L_MAX_ATTACHMENTS_PM}:<br/>
<span class="genmed">{L_MAX_ATTACHMENTS_PM_EXPLAIN}</span><br/>
<input type="text" size="3" maxlength="3" name="max_attachments_pm" value="{MAX_ATTACHMENTS_PM}" />
</div>
<div class="catSides">{L_ATTACHMENT_OPTIONS_SETTINGS}</div>
<div class="row1">
{L_DISABLE_MOD}:<br/>
<span class="genmed">{L_DISABLE_MOD_EXPLAIN}</span><br/>
<input type="radio" name="disable_mod" value="1" {DISABLE_MOD_YES} /> {L_YES}<br/>
<input type="radio" name="disable_mod" value="0" {DISABLE_MOD_NO} /> {L_NO}
</div>
<div class="row1">
{L_PM_ATTACH}:<br/>
<input type="radio" name="allow_pm_attach" value="1" {PM_ATTACH_YES} /> {L_YES}<br/>
<input type="radio" name="allow_pm_attach" value="0" {PM_ATTACH_NO} /> {L_NO}
</div>
<div class="row1">
{L_ATTACHMENT_TOPIC_REVIEW}:<br/>
<span class="genmed">{L_ATTACHMENT_TOPIC_REVIEW_EXPLAIN}</span><br/>
<input type="radio" name="attachment_topic_review" value="1" {TOPIC_REVIEW_YES} /> {L_YES}<br/>
<input type="radio" name="attachment_topic_review" value="0" {TOPIC_REVIEW_NO} /> {L_NO}
</div>
<div class="row1">
{L_SHOW_APCP}:<br/>
<span class="genmed">{L_SHOW_APCP_EXPLAIN}</span><br/>
<input type="radio" name="show_apcp" value="1" {SHOW_APCP_YES} /> {L_YES}<br/>
<input type="radio" name="show_apcp" value="0" {SHOW_APCP_NO} /> {L_NO}
</div>
<!-- BEGIN switch_ftp -->
<div class="row1">
{L_FTP_UPLOAD}:<br/>
<input type="radio" name="allow_ftp_upload" value="1" {FTP_UPLOAD_YES} /> {L_YES}<br/>
<input type="radio" name="allow_ftp_upload" value="0" {FTP_UPLOAD_NO} /> {L_NO}
</div>
<div class="catSides">
{L_ATTACHMENT_FTP_SETTINGS}
</div>
<div class="row1">
{L_ATTACHMENT_FTP_SERVER}:<br/>
<span class="genmed">{L_ATTACHMENT_FTP_SERVER_EXPLAIN}</span><br/>
<input type="text" maxlength="100" name="ftp_server" value="{FTP_SERVER}" />
</div>
<div class="row1">
{L_ATTACHMENT_FTP_PATH}:<br/>
<span class="genmed">{L_ATTACHMENT_FTP_PATH_EXPLAIN}</span><br/>
<input type="text" maxlength="100" name="ftp_path" value="{FTP_PATH}" />
</div>
<div class="row1">
{L_DOWNLOAD_PATH}:<br/>
<span class="genmed">{L_DOWNLOAD_PATH_EXPLAIN}</span><br/>
<input type="text" maxlength="100" name="download_path" value="{DOWNLOAD_PATH}" />
</div>
<div class="row1">
{L_FTP_PASSIVE_MODE}:<br/>
<input type="radio" name="ftp_pasv_mode" value="1" {FTP_PASV_MODE_YES} /> {L_YES}<br/>
<input type="radio" name="ftp_pasv_mode" value="0" {FTP_PASV_MODE_NO} /> {L_NO}
</div>
<div class="row1">
{L_ATTACHMENT_FTP_USER}:<br/>
<input type="text" maxlength="100" name="ftp_user" value="{FTP_USER}" />
</div>
<div class="row1">
{L_ATTACHMENT_FTP_PASS}:<br/>
<input type="password" maxlength="20" name="ftp_pass" value="{FTP_PASS}" />
</div>
<!-- END switch_ftp -->
<!-- BEGIN switch_no_ftp -->
<input type="hidden" name="allow_ftp_upload" value="0" />
<div class="catSides">
{L_ATTACHMENT_FTP_SETTINGS}</span>
</div>
<div class="row1">
{L_NO_FTP_EXTENSIONS}
</div>
<!-- END switch_no_ftp -->
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{L_SUBMIT}" /><br/>
<input type="submit" name="settings" value="{L_TEST_SETTINGS}" />
</form>
