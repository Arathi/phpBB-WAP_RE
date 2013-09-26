<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_PROFILE}">{L_PROFILE}</a>&gt;个人设置</div>
{ERROR_BOX}
<form action="{S_PROFILE_ACTION}" {S_FORM_ENCTYPE} method="post">
<div class="catSides">会员设置</div>
<div class="row1">
{L_PUBLIC_VIEW_EMAIL}:<br/>
<input type="radio" name="viewemail" value="1" {VIEW_EMAIL_YES} />{L_YES} <input type="radio" name="viewemail" value="0" {VIEW_EMAIL_NO} />{L_NO}
</div>
<div class="row1">
当有新留言时发送电子邮件通知我:<br/>
<input type="radio" name="user_email_new_gb" value="1" {GB_EMAIL_YES} /> {L_YES}
<input type="radio" name="user_email_new_gb" value="0" {GB_EMAIL_NO} /> {L_NO}
</div>
<div class="row1">
{L_HIDE_USER}:<br/>
<input type="radio" name="hideonline" value="1" {HIDE_USER_YES} />{L_YES} <input type="radio" name="hideonline" value="0" {HIDE_USER_NO} />{L_NO}
</div>
<div class="row1">
在论坛显示在线、离线、隐身:<br/>
<input type="radio" name="on_off" value="1" {ON_OFF_YES} />{L_YES} <input type="radio" name="on_off" value="0" {ON_OFF_NO} />{L_NO}
</div>
<div class="row1">
一个标准的Web浏览器禁用文件附件*：<br/>
<input type="radio" name="attach_on" value="0" {ATTACH_ON_NO} />{L_YES} <input type="radio" name="attach_on" value="1" {ATTACH_ON_YES} />{L_NO}
</div>
<div class="row1">
帖子页面显示快速回复主题：<br/>
<input type="radio" name="quick_answer" value="1" {QUICK_ANSWER_YES} />{L_YES} <input type="radio" name="quick_answer" value="0" {QUICK_ANSWER_NO} />{L_NO}
</div>
<div class="row1">
回复框前显示插入BB代码工具栏（JavaScript）：<br/>
<input type="radio" name="bb_panel" value="1" {BB_PANEL_YES} />{L_YES} <input type="radio" name="bb_panel" value="0" {BB_PANEL_NO} />{L_NO}
</div>
<div class="row1">
点击那个按钮进入最后一页：<br/>
<input type="radio" name="view_latest_post" value="1" {VIEW_LATEST_POST_YES} />&gt;&gt;&gt; <input type="radio" name="view_latest_post" value="0" {VIEW_LATEST_POST_NO} />»
</div>
<div class="row1">
快速键入“回复”（JavaScript）：<br/>
<input type="radio" name="java_otv" value="1" {JAVA_OTV_YES} />{L_YES} <input type="radio" name="java_otv" value="0" {JAVA_OTV_NO} />{L_NO}
</div>
<!-- BEGIN switch_message_quote -->
<div class="row1">
开启帖子“引用”按钮:<br/>
<input type="radio" name="message_quote" value="1" {MESSAGE_QUOTE_YES} />{L_YES} <input type="radio" name="message_quote" value="0" {MESSAGE_QUOTE_NO} />{L_NO}
</div>
<!-- END switch_message_quote -->
<div class="row1">
是否使用 ICQ 信息:<br/>
<input type="radio" name="icq_send" value="1" {ICQ_SEND_YES} />{L_YES} <input type="radio" name="icq_send" value="0" {ICQ_SEND_NO} />{L_NO}
</div>
<div class="row1">
展开的论坛列表:<br/>
<input type="radio" name="index_spisok" value="1" {INDEX_SPISOK_YES} />{L_YES} <input type="radio" name="index_spisok" value="0" {INDEX_SPISOK_NO} />{L_NO}
</div>
<div class="row1">
显示最新文章的主题编辑:<br/>
<input type="radio" name="posl_red" value="1" {POSL_RED_YES} />{L_YES} <input type="radio" name="posl_red" value="0" {POSL_RED_NO} />{L_NO}
</div>
<div class="row1">{L_NOTIFY_ON_REPLY}:<br/>
<input type="checkbox" name="notifyreply_to_pm"{NOTIFY_REPLY_TO_PM} /> {L_NOTIFY_ON_REPLY_TO_PM}<br/>
<input type="checkbox" name="notifyreply_to_email"{NOTIFY_REPLY_TO_EMAIL} /> {L_NOTIFY_ON_REPLY_TO_EMAIL}
</div>
<div class="row1">
{L_NOTIFY_ON_PRIVMSG}:<br/>
<input type="radio" name="notifypm" value="1" {NOTIFY_PM_YES} />{L_YES} <input type="radio" name="notifypm" value="0" {NOTIFY_PM_NO} />{L_NO}
</div>
<div class="row1">
{L_TIMEZONE}:<br/>
{TIMEZONE_SELECT}
</div>
<div class="row1">
{L_DATE_FORMAT}:<br/>
{DATE_FORMAT}
</div>
<div class="row1">
{L_TOPICS_PER_PAGE}:<br/>
<input type="text" name="topics_per_page" value="{TOPICS_PER_PAGE}" size="5" maxlength="3" />
</div>
<div class="row1">
{L_POSTS_PER_PAGE}:<br/>
<input type="text" name="posts_per_page" value="{POSTS_PER_PAGE}" size="5" maxlength="3" />
</div>
<div class="row1">
帖子显示最大长度**:<br/>
<input type="text" name="post_leng" value="{POST_LENG}" size="8" maxlength="8" />
</div>
<!-- BEGIN switch_avatar_block -->
<div class="row1">
{L_AVATAR_EXPLAIN}:<br/>
{AVATAR}
<!-- BEGIN switch_avatar_local_upload -->
{L_UPLOAD_AVATAR_FILE}:<br/>
<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" /><input type="file" name="avatar"/><br/>
<!-- END switch_avatar_local_upload -->
<!-- BEGIN switch_avatar_local_upload_om -->
{L_UPLOAD_AVATAR_FILE}:<br/>
<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
<input name="fileupload" value = ""><br/>
<a href="op:fileselect">Обзор...</a><br/>
<!-- END switch_avatar_local_upload_om -->
<!-- BEGIN switch_avatar_remote_upload -->
{L_UPLOAD_AVATAR_URL}:<br/>
<input type="text" name="avatarurl"/><br/>
<!-- END switch_avatar_remote_upload -->
<!-- BEGIN switch_avatar_remote_link -->
{L_LINK_REMOTE_AVATAR}:<br/>
<input type="text" name="avatarremoteurl"/><br/>
<!-- END switch_avatar_remote_link -->
<!-- BEGIN switch_avatar_local_gallery -->
{L_AVATAR_GALLERY}:<br/>
<input type="submit" name="avatargallery" value="{L_SHOW_GALLERY}" class="subbutton" />
<!-- END switch_avatar_local_gallery -->
</div>
<!-- END switch_avatar_block -->
{S_HIDDEN_FIELDS}<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT}" />
</form>
<br/>
* 使用它，如果你试图发送一个消息，一个标准的手机浏览器, （电话）关闭.<br/>
必须指出, 投资与Opera Mini和您的计算机的工作，即使你使用此功能<br/>
** 如果文章的字符串长度超过该限制，超出部分则显示 "--&gt;"，0表示不限制