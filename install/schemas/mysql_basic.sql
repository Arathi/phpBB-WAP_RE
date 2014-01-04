
#-- 系统配置

INSERT INTO phpbb_config (config_name, config_value) VALUES ('config_id','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('board_disable','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('sitename','网站名称');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('site_desc','用于描述论坛的一段文字');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('cookie_name','phpbb2mysql');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('cookie_path','/');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('cookie_domain','');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('cookie_secure','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('session_length','3600');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_html','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_html_tags','b,i,u,pre');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_bbcode','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_smilies','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_sig','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_namechange','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_theme_create','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_avatar_local','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_avatar_remote','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_avatar_upload','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('enable_confirm', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_autologin','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_autologin_time','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('override_user_style','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('posts_per_page','10');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('topics_per_page','10');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('hot_threshold','10');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_poll_options','10');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_sig_chars','255');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_inbox_privmsgs','50');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_sentbox_privmsgs','25');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_savebox_privmsgs','50');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('board_email_sig','使用 phpBB-WAP 程序发送的邮件');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('board_email','youraddress@yourdomain.com');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smtp_delivery','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smtp_host','');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smtp_username','');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smtp_password','');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('sendmail_fix','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('require_activation','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('flood_interval','15');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('search_flood_interval','15');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('search_min_chars','3');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_login_attempts', '5');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('login_reset_time', '30');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('board_email_form','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('avatar_filesize','6144');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('avatar_max_width','80');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('avatar_max_height','80');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('avatar_path','images/avatars');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('avatar_gallery_path','images/avatars/gallery');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smilies_path','images/smiles');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('default_style','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('default_dateformat','M月d日 G:i');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('board_timezone','8');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('prune_enable','1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('privmsg_disable','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('gzip_compress','0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('coppa_fax', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('coppa_mail', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('record_online_users', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('record_online_date', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('server_name', 'localhost');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('server_port', '80');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('script_path', '/');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('version', 'REv2');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('rand_seed', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('warnings_enabled', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_enabled', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reports_enabled', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_least_respected', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_ban_warnings', '5');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_delete_expired', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_warning_expire', '1,100');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_ban_expire', '1,100');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_perms', '0,0,1,3,3,3,3,5,3,5,3,3,3');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_none', '0,0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_mod_norep', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_reviews_per_page', '25');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_reports_per_page', '25');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_display', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_most_respected', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_days_req', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_posts_req', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_points_req', '-100000');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_warnings_req', '100000');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_rotation_limit', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_time_limit', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_check_rate', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_last_check_time', '1152884749');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_empty_reviews', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_positive_only', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_check_reports', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_reports_color', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('reputation_admin_norep', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('online_time', '60');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_user_topics_per_page', '50');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_user_posts_per_page', '50');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('birthday_required', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('birthday_greeting', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_user_age', '100');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('min_user_age', '5');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('birthday_check_day', '7');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('index_announcement', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('index_spisok', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('send_user_icq', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('default_icq', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('default_icq_pass', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('flood_icq_interval', '60');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('last_check_icq_time', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('min_login_regdate', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('shop', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('pay_money', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('ref_url', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('kurs_payment', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('money_payment', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('money_earned', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smena_nika', '1000');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smena_cveta', '500');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('smena_zvaniya', '300');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('pokupka_uchetki', '300');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('pokupka_uchetki_posts', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('pokupka_uchetki_nedeli', '8');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('razblokirovka_druga', '100');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('time_click', '100');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('time_last_click', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('sites', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('verh_pay', '500');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('niz_pay', '300');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('verh', '3');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('niz', '3');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_medal_display', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('medal_display_row', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('medal_display_col', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('medal_display_width', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('medal_display_height', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('medal_display_order', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('posl_red', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('captcha_in_topic', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('max_smiles_in_message', '3');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('report_email', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('forums_index_top', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('forums_index_bottom', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('forums_other_top', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('forums_other_bottom', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('message_quote', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('newest_posts_first', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('allow_guests_gb', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('gb_posts', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('gb_quick', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('prune_shouts', '0');

#-- 奖
INSERT INTO phpbb_medal_cat VALUES ('1', '测试奖', '10');

#-- 论坛分类
INSERT INTO phpbb_categories (cat_id, cat_title, cat_order) VALUES (1, '测试分类', 10);

#-- 论坛
INSERT INTO phpbb_forums (forum_id, forum_name, forum_desc, cat_id, forum_order, forum_posts, forum_topics, forum_last_post_id, auth_view, auth_read, auth_post, auth_reply, auth_edit, auth_delete, auth_announce, auth_sticky, auth_pollcreate, auth_vote, auth_attachments) VALUES (1, '测试论坛', '', 1, 10, 1, 1, 1, 0, 0, 1, 1, 1, 1, 3, 3, 1, 1, 1);

#-- 会员信息
INSERT INTO phpbb_users (user_id, username, user_level, user_regdate, user_password, user_email, user_icq, user_website, user_occ, user_from, user_interests, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_posts, user_attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_pm, user_notify_pm, user_allow_viewonline, user_rank, user_avatar, user_lang, user_timezone, user_dateformat, user_actkey, user_newpasswd, user_active) VALUES ( -1, '匿名用户', 0, 0, '', '', '', '', '', '', '', '', 0, '', '', '', 0, 0, 1, 1, 1, 0, 1, 1, NULL, '', '', 0, '', '', '', 0);
INSERT INTO phpbb_users (user_id, username, user_level, user_regdate, user_password, user_email, user_icq, user_website, user_occ, user_from, user_interests, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_posts, user_attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_pm, user_notify_pm, user_popup_pm, user_allow_viewonline, user_rank, user_avatar, user_lang, user_timezone, user_dateformat, user_actkey, user_newpasswd, user_active) VALUES ( 1, 'admin', 1, 0, '202cb962ac59075b964b07152d234b70', 'admin@yourdomain.com', '', '', '', '', '', '', 1, '', '', '', 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, '', 'chinese', 0, 'M月d日 G:i', '', '', 1);

#-- 等级
INSERT INTO phpbb_ranks (rank_id, rank_title, rank_min, rank_special, rank_image) VALUES (1, '超级管理员', -1, 1, NULL);
INSERT INTO phpbb_ranks (rank_id, rank_title, rank_min, rank_special, rank_image) VALUES (2, '普通会员', 0, 0, NULL);

#-- 普通用户组
INSERT INTO phpbb_groups (group_id, group_name, group_description, group_single_user) VALUES (1, '匿名用户', 'Personal User', 1);
INSERT INTO phpbb_groups (group_id, group_name, group_description, group_single_user) VALUES (2, '超级管理员', 'Personal User', 1);

#-- 用户组
INSERT INTO phpbb_user_group (group_id, user_id, user_pending) VALUES (1, -1, 0);
INSERT INTO phpbb_user_group (group_id, user_id, user_pending) VALUES (2, 1, 0);

#-- 主题
INSERT INTO phpbb_topics (topic_id, topic_title, topic_poster, topic_time, topic_views, topic_replies, forum_id, topic_status, topic_type, topic_vote, topic_first_post_id, topic_last_post_id) VALUES (1, '欢迎使用phpBB-WAP！', 1, '0', 0, 0, 1, 0, 0, 0, 1, 1);

#-- 帖子
INSERT INTO phpbb_posts (post_id, topic_id, forum_id, poster_id, post_time, post_username, poster_ip) VALUES (1, 1, 1, 1, UNIX_TIMESTAMP(now()), NULL, '7F000001');
INSERT INTO phpbb_posts_text (post_id, post_subject, post_text) VALUES (1, NULL, '您好！这是一个测试的帖子，当你看到这个帖子时说明程序已经安装成功！假如您不喜欢这个帖子，您可以删除、移动或修改这个帖子。感谢您使用phpBB-WAP！');

#-- 表情
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 1, '[/微笑]', '1.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 2, '[/撇嘴]', '2.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 3, '[/色]', '3.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 4, '[/发呆]', '4.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 5, '[/得意]', '5.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 6, '[/流泪]', '6.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 7, '[/害羞]', '7.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 8, '[/闭嘴]', '8.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 9, '[/睡]', '9.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 10, '[/大哭]', '10.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 11, '[/尴尬]', '11.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 12, '[/发怒]', '12.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 13, '[/调皮]', '13.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 14, '[/龇牙]', '14.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 15, '[/惊讶]', '15.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 16, '[/难过]', '16.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 17, '[/酷]', '17.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 18, '[/冷汗]', '18.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 19, '[/抓狂]', '19.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 20, '[/吐]', '20.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 21, '[/偷笑]', '21.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 22, '[/可爱]', '22.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 23, '[/白眼]', '23.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 24, '[/傲慢]', '24.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 25, '[/饥饿]', '25.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 26, '[/困]', '26.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 27, '[/惊恐]', '27.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 28, '[/流汗]', '28.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 29, '[/憨笑]', '29.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 30, '[/大兵]', '30.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 31, '[/奋斗]', '31.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 32, '[/咒骂]', '32.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 33, '[/疑问]', '33.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 34, '[/嘘]', '34.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 35, '[/晕]', '35.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 36, '[/折磨]', '36.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 37, '[/衰]', '37.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 38, '[/骷髅]', '38.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 39, '[/敲打]', '39.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 40, '[/再见]', '40.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 41, '[/擦汗]', '41.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 42, '[/抠鼻]', '42.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 43, '[/鼓掌]', '43.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 44, '[/糗大了]', '44.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 45, '[/坏笑]', '45.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 46, '[/左哼哼]', '46.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 47, '[/右哼哼]', '47.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 48, '[/打哈欠]', '48.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 49, '[/鄙视]', '49.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 50, '[/委屈]', '50.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 51, '[/快哭了]', '51.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 52, '[/阴险]', '52.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 53, '[/亲亲]', '53.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 54, '[/吓]', '54.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 55, '[/可怜]', '55.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 56, '[/菜刀]', '56.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 57, '[/西瓜]', '57.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 58, '[/啤酒]', '58.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 59, '[/篮球]', '59.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 60, '[/乒乓球]', '60.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 61, '[/咖啡]', '61.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 62, '[/饭]', '62.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 63, '[/猪头]', '63.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 64, '[/玫瑰]', '64.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 65, '[/凋谢]', '65.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 66, '[/嘴唇]', '66.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 67, '[/爱心]', '67.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 68, '[/心碎]', '68.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 69, '[/蛋糕]', '69.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 70, '[/闪电]', '70.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 71, '[/炸弹]', '71.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 72, '[/匕首]', '72.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 73, '[/足球]', '73.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 74, '[/瓢虫]', '74.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 75, '[/便便]', '75.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 76, '[/月亮]', '76.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 77, '[/太阳]', '77.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 78, '[/礼物]', '78.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 79, '[/拥抱]', '79.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 80, '[/强]', '80.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 81, '[/弱]', '81.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 82, '[/握手]', '82.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 83, '[/胜利]', '83.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 84, '[/抱拳]', '84.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 85, '[/勾引]', '85.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 86, '[/拳头]', '86.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 87, '[/差劲]', '87.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 88, '[/爱你]', '88.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 89, '[/no]', '89.gif', ' ');
INSERT INTO phpbb_smilies (smilies_id, code, smile_url, emoticon) VALUES ( 90, '[/ok]', '90.gif', ' ');

#-- 搜索关键词
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 1, 'example', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 2, 'post', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 3, 'phpbb', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 4, 'installation', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 5, 'delete', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 6, 'topic', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 7, 'forum', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 8, 'since', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 9, 'everything', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 10, 'seems', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 11, 'working', 0 );
INSERT INTO phpbb_search_wordlist (word_id, word_text, word_common) VALUES ( 12, 'welcome', 0 );

#-- 搜索匹配
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 1, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 2, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 3, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 4, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 5, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 6, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 7, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 8, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 9, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 10, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 11, 1, 0 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 12, 1, 1 );
INSERT INTO phpbb_search_wordmatch (word_id, post_id, title_match) VALUES ( 3, 1, 1 );

#-- 附件
INSERT INTO phpbb_attachments_config VALUES ('upload_dir', 'download');
INSERT INTO phpbb_attachments_config VALUES ('upload_img', 'images/icons/icon_clip.gif');
INSERT INTO phpbb_attachments_config VALUES ('topic_icon', 'images/icons/icon_clip.gif');
INSERT INTO phpbb_attachments_config VALUES ('display_order', '0');
INSERT INTO phpbb_attachments_config VALUES ('max_filesize', '102400');
INSERT INTO phpbb_attachments_config VALUES ('attachment_quota', '0');
INSERT INTO phpbb_attachments_config VALUES ('max_filesize_pm', '102400');
INSERT INTO phpbb_attachments_config VALUES ('max_attachments', '5');
INSERT INTO phpbb_attachments_config VALUES ('max_attachments_pm', '3');
INSERT INTO phpbb_attachments_config VALUES ('disable_mod', '0');
INSERT INTO phpbb_attachments_config VALUES ('allow_pm_attach', '1');
INSERT INTO phpbb_attachments_config VALUES ('attachment_topic_review', '0');
INSERT INTO phpbb_attachments_config VALUES ('allow_ftp_upload', '0');
INSERT INTO phpbb_attachments_config VALUES ('show_apcp', '0');
INSERT INTO phpbb_attachments_config VALUES ('attach_version', '2.4.5');
INSERT INTO phpbb_attachments_config VALUES ('default_upload_quota', '0');
INSERT INTO phpbb_attachments_config VALUES ('default_pm_quota', '0');
INSERT INTO phpbb_attachments_config VALUES ('ftp_server', '');
INSERT INTO phpbb_attachments_config VALUES ('ftp_path', '');
INSERT INTO phpbb_attachments_config VALUES ('download_path', '');
INSERT INTO phpbb_attachments_config VALUES ('ftp_user', '');
INSERT INTO phpbb_attachments_config VALUES ('ftp_pass', '');
INSERT INTO phpbb_attachments_config VALUES ('ftp_pasv_mode', '1');
INSERT INTO phpbb_attachments_config VALUES ('img_display_inlined', '1');
INSERT INTO phpbb_attachments_config VALUES ('img_max_width', '0');
INSERT INTO phpbb_attachments_config VALUES ('img_max_height', '0');
INSERT INTO phpbb_attachments_config VALUES ('img_link_width', '0');
INSERT INTO phpbb_attachments_config VALUES ('img_link_height', '0');
INSERT INTO phpbb_attachments_config VALUES ('img_create_thumbnail', '0');
INSERT INTO phpbb_attachments_config VALUES ('img_min_thumb_filesize', '12000');
INSERT INTO phpbb_attachments_config VALUES ('img_imagick', '');
INSERT INTO phpbb_attachments_config VALUES ('use_gd2', '0');
INSERT INTO phpbb_attachments_config VALUES ('wma_autoplay', '0');
INSERT INTO phpbb_attachments_config VALUES ('flash_autoplay', '0');
INSERT INTO phpbb_attachments_config VALUES('download_cut_points', '10'); 
INSERT INTO phpbb_attachments_config VALUES('download_add_points', '5'); 
INSERT INTO phpbb_attachments_config VALUES('point_name', '积分'); 

#-- 禁止的扩展名
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (1,'php');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (2,'php3');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (3,'php4');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (4,'phtml');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (5,'pl');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (6,'asp');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (7,'cgi');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (8,'php5');
INSERT INTO phpbb_forbidden_extensions (ext_id, extension) VALUES (9,'php6');

#-- 扩展名小组
INSERT INTO phpbb_extension_groups VALUES (1, '其它', 0, 1, 1, '', 102400, '');
INSERT INTO phpbb_extension_groups VALUES (2, '视频', 0, 1, 1, '', 102400, '');
INSERT INTO phpbb_extension_groups VALUES (3, '图片', 1, 1, 1, '', 102400, '');
INSERT INTO phpbb_extension_groups VALUES (4, '文档', 0, 1, 1, '', 102400, '');
INSERT INTO phpbb_extension_groups VALUES (5, '音乐', 0, 1, 1, '', 102400, '');

#-- 扩展名
INSERT INTO phpbb_extensions VALUES (1, 3, 'gif', '');
INSERT INTO phpbb_extensions VALUES (2, 3, 'png', '');
INSERT INTO phpbb_extensions VALUES (3, 3, 'jpeg', '');
INSERT INTO phpbb_extensions VALUES (4, 3, 'jpg', '');
INSERT INTO phpbb_extensions VALUES (5, 4, 'tar', '');
INSERT INTO phpbb_extensions VALUES (6, 4, 'zip', '');
INSERT INTO phpbb_extensions VALUES (7, 4, 'rar', '');
INSERT INTO phpbb_extensions VALUES (8, 1, 'txt', '');
INSERT INTO phpbb_extensions VALUES (9, 1, 'sis', '');
INSERT INTO phpbb_extensions VALUES (10, 1, 'jad', '');
INSERT INTO phpbb_extensions VALUES (11, 4, 'jar', '');
INSERT INTO phpbb_extensions VALUES (12, 5, 'aac', '');
INSERT INTO phpbb_extensions VALUES (13, 2, 'avi', '');
INSERT INTO phpbb_extensions VALUES (14, 2, 'mp4', '');
INSERT INTO phpbb_extensions VALUES (15, 2, '3gp', '');
INSERT INTO phpbb_extensions VALUES (16, 5, 'amr', '');
INSERT INTO phpbb_extensions VALUES (17, 5, 'mmf', '');
INSERT INTO phpbb_extensions VALUES (18, 5, 'mp3', '');
INSERT INTO phpbb_extensions VALUES (19, 5, 'wav', '');
INSERT INTO phpbb_extensions VALUES (20, 5, 'midi', '');
INSERT INTO phpbb_extensions VALUES (21, 5, 'mid', '');
INSERT INTO phpbb_extensions VALUES (22, 3, 'bmp', '');
INSERT INTO phpbb_extensions VALUES (23, 4, 'gz', '');
INSERT INTO phpbb_extensions VALUES (24, 4, '7z', '');

#-- 限制设置
INSERT INTO phpbb_quota_limits (quota_limit_id, quota_desc, quota_limit) VALUES (1, '低', 262144);
INSERT INTO phpbb_quota_limits (quota_limit_id, quota_desc, quota_limit) VALUES (2, '中', 2097152);
INSERT INTO phpbb_quota_limits (quota_limit_id, quota_desc, quota_limit) VALUES (3, '高', 5242880);

#-- 相册配置
INSERT INTO phpbb_album_config VALUES ('max_pics', '1024');
INSERT INTO phpbb_album_config VALUES ('user_pics_limit', '50');
INSERT INTO phpbb_album_config VALUES ('mod_pics_limit', '250');
INSERT INTO phpbb_album_config VALUES ('max_file_size', '128000');
INSERT INTO phpbb_album_config VALUES ('max_width', '800');
INSERT INTO phpbb_album_config VALUES ('max_height', '600');
INSERT INTO phpbb_album_config VALUES ('rows_per_page', '3');
INSERT INTO phpbb_album_config VALUES ('cols_per_page', '1');
INSERT INTO phpbb_album_config VALUES ('fullpic_popup', '1');
INSERT INTO phpbb_album_config VALUES ('thumbnail_quality', '100');
INSERT INTO phpbb_album_config VALUES ('thumbnail_size', '125');
INSERT INTO phpbb_album_config VALUES ('thumbnail_cache', '0');
INSERT INTO phpbb_album_config VALUES ('sort_method', 'pic_time');
INSERT INTO phpbb_album_config VALUES ('sort_order', 'DESC');
INSERT INTO phpbb_album_config VALUES ('jpg_allowed', '1');
INSERT INTO phpbb_album_config VALUES ('png_allowed', '1');
INSERT INTO phpbb_album_config VALUES ('gif_allowed', '1');
INSERT INTO phpbb_album_config VALUES ('desc_length', '512');
INSERT INTO phpbb_album_config VALUES ('hotlink_prevent', '0');
INSERT INTO phpbb_album_config VALUES ('hotlink_allowed', 'smartor.is-root.com');
INSERT INTO phpbb_album_config VALUES ('personal_gallery', '0');
INSERT INTO phpbb_album_config VALUES ('personal_gallery_private', '0');
INSERT INTO phpbb_album_config VALUES ('personal_gallery_limit', '10');
INSERT INTO phpbb_album_config VALUES ('personal_gallery_view', '-1');
INSERT INTO phpbb_album_config VALUES ('rate', '1');
INSERT INTO phpbb_album_config VALUES ('rate_scale', '10');
INSERT INTO phpbb_album_config VALUES ('comment', '1');
INSERT INTO phpbb_album_config VALUES ('gd_version', '2');
INSERT INTO phpbb_album_config VALUES ('album_version', '.0.54');

#-- 规则分类
INSERT INTO phpbb_rules_cat (cat_r_id, cat_r_name) VALUES (1, '测试规则分类');

#-- 规则
INSERT INTO phpbb_rules (rule_id, rule_cat_id, rule_name, rule_subj, rule_moder) VALUES (1, 1, '1.1', '您好！这是一条测试的规则，如果你不喜欢您可以删除、编辑它。', 1);

#-- 留言板
INSERT INTO phpbb_profile_guestbook VALUES (1, 1, 1, 'dd7850a9d8', 1119444611, '欢迎使用留言板功能！', '这是一条留言板的测试消息，你可以删除或编辑它！','');

#-- 风格
INSERT INTO phpbb_styles (style_id, style_name, style_path) VALUES (1, '默认风格', 'prosilver');
INSERT INTO phpbb_styles (style_id, style_name, style_path) VALUES (2, '新春风格', 'NewYear');
INSERT INTO phpbb_styles (style_id, style_name, style_path) VALUES (3, '万圣节风格', 'Halloween');

#-- 自定义网页排版
INSERT INTO phpbb_pages (page_id, page_title, page_contents) VALUES (1, '网页标题', '网页内容');

#-- 自定义首页排版
INSERT INTO phpbb_index_page (index_content) VALUES ('网页内容');

#-- web
INSERT INTO phpbb_themes VALUES (1, 'prosilver', 'prosilver', 'prosilver.css', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'bg2', 'bg1', '', '', '', '', 0, 0, 0, '', '00AA00', 'AA0000', '', '', '', 0, 0);
INSERT INTO phpbb_config (config_name, config_value) VALUES ('default_web_style','1');

#-- 彩票
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_cost', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_ticktype', 'single');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_length', '500000');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_name', '虚拟彩票');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_base', '50');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_start', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_reset', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_status', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_items', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_win_items', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_show_entries', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_mb', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_mb_amount', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_history', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_currency', '');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_item_mcost', '1');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_item_xcost', '500');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('lottery_random_shop', '');

#-- 银行
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankinterest', '2');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankfees', '2');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankpayouttime', '86400');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankname', '虚拟银行');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankopened', 'off');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bankholdings', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('banktotaldeposits', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('banktotalwithdrew', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('banklastrestocked', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bank_minwithdraw', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bank_mindeposit', '0');
INSERT INTO phpbb_config (config_name, config_value) VALUES ('bank_interestcut', '0');

#-- 友情链接
INSERT INTO phpbb_linkexchange VALUES ('0', '爱疯的云', 'support@phpbb-wap.com', '中文phpBB-WAP', 'http://phpbb-wap.com/images/logo.png', '中文phpBB-WAP', 'phpbb-wap.com', '0', '0', '0');
