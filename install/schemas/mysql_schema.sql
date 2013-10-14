#
# 文件名：mysql_schema.sql
#

# 表：phpbb_auth_access
# 描述：权限相关
CREATE TABLE IF NOT EXISTS phpbb_auth_access (
   group_id mediumint(8) DEFAULT '0' NOT NULL,
   forum_id smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   auth_view tinyint(1) DEFAULT '0' NOT NULL,
   auth_read tinyint(1) DEFAULT '0' NOT NULL,
   auth_post tinyint(1) DEFAULT '0' NOT NULL,
   auth_reply tinyint(1) DEFAULT '0' NOT NULL,
   auth_edit tinyint(1) DEFAULT '0' NOT NULL,
   auth_delete tinyint(1) DEFAULT '0' NOT NULL,
   auth_sticky tinyint(1) DEFAULT '0' NOT NULL,
   auth_announce tinyint(1) DEFAULT '0' NOT NULL,
   auth_vote tinyint(1) DEFAULT '0' NOT NULL,
   auth_pollcreate tinyint(1) DEFAULT '0' NOT NULL,
   auth_attachments tinyint(1) DEFAULT '0' NOT NULL,
   auth_mod tinyint(1) DEFAULT '0' NOT NULL,
   auth_download TINYINT(1) DEFAULT '0' NOT NULL,
   KEY group_id (group_id),
   KEY forum_id (forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_user_group
# 描述：用户组
CREATE TABLE IF NOT EXISTS phpbb_user_group (
   group_id mediumint(8) DEFAULT '0' NOT NULL,
   user_id mediumint(8) DEFAULT '0' NOT NULL,
   user_pending tinyint(1),
   KEY group_id (group_id),
   KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_groups
# 描述：全局小组
CREATE TABLE IF NOT EXISTS phpbb_groups (
    group_id mediumint(8) NOT NULL auto_increment,
    group_type tinyint(4) DEFAULT '1' NOT NULL,
    group_name varchar(40) DEFAULT '' NOT NULL,
    group_description varchar(255) NOT NULL,
    group_moderator mediumint(8) DEFAULT '0' NOT NULL,
    group_single_user tinyint(1) DEFAULT '1' NOT NULL,
    guestbook_enable tinyint(1) DEFAULT '1' NOT NULL,
    group_logo varchar(100) DEFAULT '' NOT NULL,
    PRIMARY KEY (group_id),
    KEY group_single_user (group_single_user)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_groups_guestbook
# 描述：全局小组留言板
CREATE TABLE IF NOT EXISTS phpbb_groups_guestbook (
    gb_id int(10) NOT NULL auto_increment,
    group_id int(10) NOT NULL default '0',
    poster_id int(10) NOT NULL default '0',
    bbcode varchar(64) NOT NULL default '',
    gb_time int(10) NOT NULL default '0',
    message text NOT NULL,
    PRIMARY KEY  (gb_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_banlist
# 描述：黑名单列表
CREATE TABLE IF NOT EXISTS phpbb_banlist (
    ban_id mediumint(8) UNSIGNED NOT NULL auto_increment,
    ban_userid mediumint(8) NOT NULL,
    ban_ip char(8) NOT NULL,
    ban_email varchar(255),
    PRIMARY KEY (ban_id),
    KEY ban_ip_user_id (ban_ip, ban_userid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_bookmarks
# 描述：书签
CREATE TABLE IF NOT EXISTS phpbb_bookmarks (
    topic_id mediumint(8) unsigned NOT NULL default '0',
    user_id mediumint(8) NOT NULL default '0',
    start mediumint(8) NOT NULL DEFAULT '0',
    KEY topic_id (topic_id),
    KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_categories
# 描述：论坛分类
CREATE TABLE IF NOT EXISTS phpbb_categories (
    cat_id mediumint(8) UNSIGNED NOT NULL auto_increment,
    cat_title varchar(100),
    cat_icon varchar(100),
    cat_order mediumint(8) UNSIGNED NOT NULL,
    PRIMARY KEY (cat_id),
    KEY cat_order (cat_order)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_config
# 描述：网站全局配置
CREATE TABLE IF NOT EXISTS phpbb_config (
    config_name varchar(255) NOT NULL,
    config_value varchar(255) NOT NULL,
    PRIMARY KEY (config_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_confirm
# 描述：表单提交确认处理
CREATE TABLE IF NOT EXISTS phpbb_confirm (
    confirm_id char(32) DEFAULT '' NOT NULL,
    session_id char(32) DEFAULT '' NOT NULL,
    code char(6) DEFAULT '' NOT NULL, 
    PRIMARY KEY  (session_id,confirm_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_disallow
# 描述：封禁
CREATE TABLE IF NOT EXISTS phpbb_disallow (
    disallow_id mediumint(8) UNSIGNED NOT NULL auto_increment,
    disallow_username varchar(25) DEFAULT '' NOT NULL,
    PRIMARY KEY (disallow_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_forum_prune
# 描述：论坛的相关值
CREATE TABLE IF NOT EXISTS phpbb_forum_prune (
    prune_id mediumint(8) UNSIGNED NOT NULL auto_increment,
    forum_id smallint(5) UNSIGNED NOT NULL,
    prune_days smallint(5) UNSIGNED NOT NULL,
    prune_freq smallint(5) UNSIGNED NOT NULL,
    PRIMARY KEY(prune_id),
    KEY forum_id (forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_forums
# 描述：论坛
CREATE TABLE IF NOT EXISTS phpbb_forums (
   forum_id smallint(5) UNSIGNED NOT NULL,
   cat_id mediumint(8) UNSIGNED NOT NULL,
   forum_name varchar(150),
   forum_desc text,
   forum_icon varchar(100),
   forum_status tinyint(4) DEFAULT '0' NOT NULL,
   forum_order mediumint(8) UNSIGNED DEFAULT '1' NOT NULL,
   forum_posts mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   forum_postcount tinyint(1) DEFAULT '1' NOT NULL,
   forum_topics mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   forum_last_post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   prune_next int(11),
   prune_enable tinyint(1) DEFAULT '0' NOT NULL,
   auth_view tinyint(2) DEFAULT '0' NOT NULL,
   auth_read tinyint(2) DEFAULT '0' NOT NULL,
   auth_post tinyint(2) DEFAULT '0' NOT NULL,
   auth_reply tinyint(2) DEFAULT '0' NOT NULL,
   auth_edit tinyint(2) DEFAULT '0' NOT NULL,
   auth_delete tinyint(2) DEFAULT '0' NOT NULL,
   auth_sticky tinyint(2) DEFAULT '0' NOT NULL,
   auth_announce tinyint(2) DEFAULT '0' NOT NULL,
   auth_vote tinyint(2) DEFAULT '0' NOT NULL,
   auth_pollcreate tinyint(2) DEFAULT '0' NOT NULL,
   auth_attachments tinyint(2) DEFAULT '0' NOT NULL,
   auth_download tinyint(2) DEFAULT '0' NOT NULL,
   forum_money int(12) default '0',
   PRIMARY KEY (forum_id),
   KEY forums_order (forum_order),
   KEY cat_id (cat_id),
   KEY forum_last_post_id (forum_last_post_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_posts
# 描述：帖子的信息，不包含内容
CREATE TABLE IF NOT EXISTS phpbb_posts (
   post_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   topic_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   forum_id smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   poster_id mediumint(8) DEFAULT '0' NOT NULL,
   post_time int(11) DEFAULT '0' NOT NULL,
   poster_ip char(8) NOT NULL,
   post_username varchar(25),
   enable_bbcode tinyint(1) DEFAULT '1' NOT NULL,
   enable_html tinyint(1) DEFAULT '0' NOT NULL,
   enable_smilies tinyint(1) DEFAULT '1' NOT NULL,
   enable_sig tinyint(1) DEFAULT '1' NOT NULL,
   post_edit_time int(11),
   post_edit_count smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   post_reviews smallint(8) unsigned NOT NULL default 0,
   post_locked tinyint(1) unsigned NOT NULL default 0,
   post_attachment tinyint(1) DEFAULT '0' NOT NULL,
   PRIMARY KEY (post_id),
   KEY forum_id (forum_id),
   KEY topic_id (topic_id),
   KEY poster_id (poster_id),
   KEY post_time (post_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_post_reports
# 描述：帖子回复
CREATE TABLE IF NOT EXISTS phpbb_post_reports (
  report_id mediumint(8) NOT NULL auto_increment,
  post_id mediumint(8) NOT NULL default '0',
  reporter_id mediumint(8) NOT NULL default '0',
  report_status tinyint(1) NOT NULL default '0',
  report_time int(11) NOT NULL default '0',
  report_comments text,
  last_action_user_id mediumint(8) default '0',
  last_action_time int(11) NOT NULL default '0',
  last_action_comments text,
  PRIMARY KEY (report_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_posts_text
# 描述：帖子的内容和标题
CREATE TABLE IF NOT EXISTS phpbb_posts_text (
   post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   bbcode_uid char(10) DEFAULT '' NOT NULL,
   post_subject char(60),
   post_text text,
   PRIMARY KEY (post_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_privmsgs
# 描述：信息，不包含信息的内容
CREATE TABLE IF NOT EXISTS phpbb_privmsgs (
   privmsgs_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   privmsgs_type tinyint(4) DEFAULT '0' NOT NULL,
   privmsgs_subject varchar(255) DEFAULT '0' NOT NULL,
   privmsgs_from_userid mediumint(8) DEFAULT '0' NOT NULL,
   privmsgs_to_userid mediumint(8) DEFAULT '0' NOT NULL,
   privmsgs_date int(11) DEFAULT '0' NOT NULL,
   privmsgs_ip char(8) NOT NULL,
   privmsgs_enable_bbcode tinyint(1) DEFAULT '1' NOT NULL,
   privmsgs_enable_html tinyint(1) DEFAULT '0' NOT NULL,
   privmsgs_enable_smilies tinyint(1) DEFAULT '1' NOT NULL,
   privmsgs_attach_sig tinyint(1) DEFAULT '1' NOT NULL,
   privmsgs_attachment TINYINT(1) DEFAULT '0' NOT NULL,
   PRIMARY KEY (privmsgs_id),
   KEY privmsgs_from_userid (privmsgs_from_userid),
   KEY privmsgs_to_userid (privmsgs_to_userid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_privmsgs_text
# 描述：信息的内容
CREATE TABLE IF NOT EXISTS phpbb_privmsgs_text (
   privmsgs_text_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   privmsgs_bbcode_uid char(10) DEFAULT '0' NOT NULL,
   privmsgs_text text,
   PRIMARY KEY (privmsgs_text_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_ranks
# 描述：等级
CREATE TABLE IF NOT EXISTS phpbb_ranks (
   rank_id smallint(5) UNSIGNED NOT NULL auto_increment,
   rank_title varchar(50) NOT NULL,
   rank_min mediumint(8) DEFAULT '0' NOT NULL,
   rank_special tinyint(1) DEFAULT '0',
   rank_image varchar(255),
   PRIMARY KEY (rank_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_reputation
# 描述：评价
CREATE TABLE IF NOT EXISTS phpbb_reputation (
  id mediumint(8) unsigned NOT NULL auto_increment,
  modification tinyint(1) NOT NULL default '0',
  user_id mediumint(8) NOT NULL default '0',
  voter_id mediumint(8) NOT NULL default '0',
  post_id mediumint(8) NOT NULL default '-1',
  forum_id smallint(5) NOT NULL default '-1',
  poster_ip char(8) NOT NULL default '',
  date int(11) default NULL,
  expire int(11) default NULL,
  edit_time int(11) default NULL,
  edit_count smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY voter_id (voter_id),
  KEY post_id (post_id),
  KEY date (date),
  KEY expire (expire)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_reputation_text
# 描述：评价内容
CREATE TABLE IF NOT EXISTS phpbb_reputation_text (
   id mediumint(8) unsigned NOT NULL default '0',
   text text,
   text_message text,
   mess_bbcode_uid varchar(10) NOT NULL default '',
   bbcode_uid varchar(10) NOT NULL default '',
   PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_search_results
# 描述：搜索结果
CREATE TABLE IF NOT EXISTS phpbb_search_results (
  search_id int(11) UNSIGNED NOT NULL default '0',
  session_id char(32) NOT NULL default '',
  search_time int(11) DEFAULT '0' NOT NULL,
  search_array text NOT NULL,
  PRIMARY KEY  (search_id),
  KEY session_id (session_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_search_wordlist
# 描述：搜索关键词
CREATE TABLE IF NOT EXISTS phpbb_search_wordlist (
   word_text varchar(50) binary NOT NULL default '',
   word_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   word_common tinyint(1) unsigned NOT NULL default '0',
   PRIMARY KEY (word_text),
   KEY word_id (word_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_search_wordmatch
# 描述：搜索匹配
CREATE TABLE IF NOT EXISTS phpbb_search_wordmatch (
   post_id mediumint(8) UNSIGNED NOT NULL default '0',
   word_id mediumint(8) UNSIGNED NOT NULL default '0',
   title_match tinyint(1) NOT NULL default '0',
   KEY post_id (post_id),
   KEY word_id (word_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_sessions
# 描述：sessions
CREATE TABLE IF NOT EXISTS phpbb_sessions (
   session_id char(32) DEFAULT '' NOT NULL,
   session_user_id mediumint(8) DEFAULT '0' NOT NULL,
   session_start int(11) DEFAULT '0' NOT NULL,
   session_time int(11) DEFAULT '0' NOT NULL,
   session_ip char(8) DEFAULT '0' NOT NULL,
   session_page int(11) DEFAULT '0' NOT NULL,
   session_logged_in tinyint(1) DEFAULT '0' NOT NULL,
   session_admin tinyint(2) DEFAULT '0' NOT NULL,
   PRIMARY KEY (session_id),
   KEY session_user_id (session_user_id),
   KEY session_id_ip_user_id (session_id, session_ip, session_user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_sessions_keys
# 描述：sessions keys
CREATE TABLE IF NOT EXISTS phpbb_sessions_keys (
  key_id varchar(32) DEFAULT '0' NOT NULL,
  user_id mediumint(8) DEFAULT '0' NOT NULL,
  last_ip varchar(8) DEFAULT '0' NOT NULL,
  last_login int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (key_id, user_id),
  KEY last_login (last_login)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_smilies
# 描述：表情
CREATE TABLE IF NOT EXISTS phpbb_smilies (
   smilies_id smallint(5) UNSIGNED NOT NULL auto_increment,
   code varchar(50),
   smile_url varchar(100),
   emoticon varchar(75),
   PRIMARY KEY (smilies_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_topics
# 描述：帖子的主题信息
CREATE TABLE IF NOT EXISTS phpbb_topics (
   topic_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   forum_id smallint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_title char(60) NOT NULL,
   topic_poster mediumint(8) DEFAULT '0' NOT NULL,
   topic_time int(11) DEFAULT '0' NOT NULL,
   topic_views mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_replies mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_status tinyint(3) DEFAULT '0' NOT NULL,
   topic_vote tinyint(1) DEFAULT '0' NOT NULL,
   topic_type tinyint(3) DEFAULT '0' NOT NULL,
   topic_first_post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_last_post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_moved_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_attachment TINYINT(1) DEFAULT '0' NOT NULL,
   topic_closed mediumint(8) not null DEFAULT '0',
   topic_color varchar(6),
   topic_special tinyint(8) default '0',
   PRIMARY KEY (topic_id),
   KEY forum_id (forum_id),
   KEY topic_moved_id (topic_moved_id),
   KEY topic_status (topic_status),
   KEY topic_type (topic_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_topics_watch
# 描述：用于帖子回复通知
CREATE TABLE IF NOT EXISTS phpbb_topics_watch (
  topic_id mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  user_id mediumint(8) NOT NULL DEFAULT '0',
  notify_status tinyint(1) NOT NULL default '0',
  KEY topic_id (topic_id),
  KEY user_id (user_id),
  KEY notify_status (notify_status)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_users
# 描述：会员数据
CREATE TABLE IF NOT EXISTS phpbb_users (
   user_id mediumint(8) NOT NULL,
   user_active tinyint(1) DEFAULT '1',
   username varchar(25) NOT NULL,
   user_password varchar(32) NOT NULL,
   user_session_time int(11) DEFAULT '0' NOT NULL,
   user_session_page smallint(5) DEFAULT '0' NOT NULL,
   user_lastvisit int(11) DEFAULT '0' NOT NULL,
   user_regdate int(11) DEFAULT '0' NOT NULL,
   user_level tinyint(4) DEFAULT '0',
   user_posts mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   user_timezone decimal(5,2) DEFAULT '0' NOT NULL,
   user_lang varchar(255),
   user_dateformat varchar(14) DEFAULT 'd M Y H:i' NOT NULL,
   user_new_privmsg smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   user_unread_privmsg smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   user_last_privmsg int(11) DEFAULT '0' NOT NULL,
   user_login_tries smallint(5) UNSIGNED DEFAULT '0' NOT NULL,
   user_last_login_try int(11) DEFAULT '0' NOT NULL,
   user_emailtime int(11),
   user_viewemail tinyint(1),
   user_attachsig tinyint(1),
   user_allowhtml tinyint(1) DEFAULT '1',
   user_allowbbcode tinyint(1) DEFAULT '1',
   user_allowsmile tinyint(1) DEFAULT '1',
   user_allowavatar tinyint(1) DEFAULT '1' NOT NULL,
   user_allow_pm tinyint(1) DEFAULT '1' NOT NULL,
   user_allow_viewonline tinyint(1) DEFAULT '1' NOT NULL,
   user_notify_to_email tinyint(1) DEFAULT '1' NOT NULL,
   user_notify_to_pm tinyint(1) DEFAULT '1' NOT NULL,
   user_notify_pm tinyint(1) DEFAULT '0' NOT NULL,
   user_popup_pm tinyint(1) DEFAULT '0' NOT NULL,
   user_rank int(11) DEFAULT '0',
   user_avatar varchar(100),
   user_avatar_type tinyint(4) DEFAULT '0' NOT NULL,
   user_email varchar(255),
   user_icq varchar(15),
   user_number varchar(15),
   user_website varchar(100),
   user_from varchar(100),
   user_sig text,
   user_sig_bbcode_uid char(10),
   user_aim varchar(255),
   user_yim varchar(255),
   user_msnm varchar(255),
   user_occ varchar(100),
   user_interests varchar(255),
   user_actkey varchar(32),
   user_newpasswd varchar(32),
   user_reputation smallint(8) NOT NULL default 0,
   user_reputation_plus smallint(8) NOT NULL default 0,
   user_warnings tinyint(4) unsigned NOT NULL default 0,
   user_topics_per_page TINYINT(2) DEFAULT '15' NOT NULL,
   user_posts_per_page TINYINT(2) DEFAULT '15' NOT NULL,
   user_birthday INT DEFAULT '999999' not null,
   user_next_birthday_greeting INT DEFAULT '0' not null,
   user_gender TINYINT not null DEFAULT '0',
   user_on_off tinyint(4) NOT NULL default '1',
   user_attach_mod tinyint(4) NOT NULL default '1',
   user_nic_color varchar(100) NOT NULL default '',
   user_quick_answer TINYINT not null DEFAULT '0',
   user_icq_send TINYINT not null DEFAULT '1',
   user_index_spisok TINYINT not null DEFAULT '0',
   user_zvanie varchar(50) default '',
   user_purse varchar(50) default '',
   user_money_earned varchar(100) NOT NULL default '0',
   user_money_payment varchar(100) NOT NULL default '0',
   user_points int(100) NOT NULL default '0',
   user_post_leng SMALLINT NOT NULL default '0',
   time_last_click int(12) default '0',
   user_posl_red TINYINT not null DEFAULT '1',
   user_java_otv TINYINT not null DEFAULT '0',
   user_bb_panel TINYINT not null DEFAULT '0',
   user_report_optout TINYINT(1) DEFAULT '0' NOT NULL,
   user_message_quote TINYINT(1) DEFAULT '1' NOT NULL,
   user_view_latest_post TINYINT(1) DEFAULT '0' NOT NULL,
   user_post_order TINYINT(1) DEFAULT '0' NOT NULL,
   user_can_gb TINYINT(1) DEFAULT '1' NOT NULL,
   user_email_new_gb TINYINT(1) DEFAULT '0' NOT NULL,
   user_style mediumint(8) DEFAULT '1',
   user_web_style mediumint(8) DEFAULT '1',
   invite_num int(5) NOT NULL default '0',
   PRIMARY KEY (user_id),
   KEY user_session_time (user_session_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_vote_desc
# 描述：投票选项、问题
CREATE TABLE IF NOT EXISTS phpbb_vote_desc (
  vote_id mediumint(8) UNSIGNED NOT NULL auto_increment,
  topic_id mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  vote_text text NOT NULL,
  vote_start int(11) NOT NULL DEFAULT '0',
  vote_length int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (vote_id),
  KEY topic_id (topic_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_vote_results
# 描述：投票的结果
CREATE TABLE IF NOT EXISTS phpbb_vote_results (
  vote_id mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  vote_option_id tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  vote_option_text varchar(255) NOT NULL,
  vote_result int(11) NOT NULL DEFAULT '0',
  KEY vote_option_id (vote_option_id),
  KEY vote_id (vote_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_vote_voters
# 描述：参与投票的用户
CREATE TABLE IF NOT EXISTS phpbb_vote_voters (
  vote_id mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  vote_user_id mediumint(8) NOT NULL DEFAULT '0',
  vote_user_ip char(8) NOT NULL,
  KEY vote_id (vote_id),
  KEY vote_user_id (vote_user_id),
  KEY vote_user_ip (vote_user_ip)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_words
# 描述：敏感词
CREATE TABLE IF NOT EXISTS phpbb_words (
   word_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   word char(100) NOT NULL,
   replacement char(100) NOT NULL,
   PRIMARY KEY (word_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_attachments_config
# 描述：附件全局配置
CREATE TABLE IF NOT EXISTS phpbb_attachments_config (
  config_name varchar(255) NOT NULL,
  config_value varchar(255) NOT NULL,
  PRIMARY KEY (config_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_forbidden_extensions
# 描述：禁止的扩展名
CREATE TABLE IF NOT EXISTS phpbb_forbidden_extensions (
  ext_id mediumint(8) UNSIGNED NOT NULL auto_increment, 
  extension varchar(100) NOT NULL, 
  PRIMARY KEY (ext_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_extension_groups
# 描述：小组的设置
CREATE TABLE IF NOT EXISTS phpbb_extension_groups (
  group_id mediumint(8) NOT NULL auto_increment,
  group_name char(20) NOT NULL,
  cat_id tinyint(2) DEFAULT '0' NOT NULL, 
  allow_group tinyint(1) DEFAULT '0' NOT NULL,
  download_mode tinyint(1) UNSIGNED DEFAULT '1' NOT NULL,
  upload_icon varchar(100) DEFAULT '',
  max_filesize int(20) DEFAULT '0' NOT NULL,
  forum_permissions varchar(255) default '' NOT NULL,
  PRIMARY KEY group_id (group_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_extensions
CREATE TABLE IF NOT EXISTS phpbb_extensions (
  ext_id mediumint(8) UNSIGNED NOT NULL auto_increment,
  group_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
  extension varchar(100) NOT NULL,
  comment varchar(100),
  PRIMARY KEY ext_id (ext_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_attachments_desc
# 描述：附件的信息
CREATE TABLE IF NOT EXISTS phpbb_attachments_desc (
  attach_id mediumint(8) UNSIGNED NOT NULL auto_increment,
  physical_filename varchar(255) NOT NULL,
  real_filename varchar(255) NOT NULL,
  download_count mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
  comment varchar(255),
  extension varchar(100),
  mimetype varchar(100),
  filesize int(20) NOT NULL,
  filetime int(11) DEFAULT '0' NOT NULL,
  thumbnail tinyint(1) DEFAULT '0' NOT NULL,
  PRIMARY KEY (attach_id),
  KEY filetime (filetime),
  KEY physical_filename (physical_filename(10)),
  KEY filesize (filesize)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_attachments
CREATE TABLE IF NOT EXISTS phpbb_attachments (
  attach_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL, 
  post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL, 
  privmsgs_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
  user_id_1 mediumint(8) NOT NULL,
  user_id_2 mediumint(8) NOT NULL,
  KEY attach_id_post_id (attach_id, post_id),
  KEY attach_id_privmsgs_id (attach_id, privmsgs_id),
  KEY post_id (post_id),
  KEY privmsgs_id (privmsgs_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 

# 表：phpbb_quota_limits
# 描述：全局配额范围
CREATE TABLE IF NOT EXISTS phpbb_quota_limits (
  quota_limit_id mediumint(8) unsigned NOT NULL auto_increment,
  quota_desc varchar(20) NOT NULL default '',
  quota_limit bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (quota_limit_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_attach_quota
# 描述：附件的配额范围
CREATE TABLE IF NOT EXISTS phpbb_attach_quota (
  user_id mediumint(8) unsigned NOT NULL default '0',
  group_id mediumint(8) unsigned NOT NULL default '0',
  quota_type smallint(2) NOT NULL default '0',
  quota_limit_id mediumint(8) unsigned NOT NULL default '0',
  KEY quota_type (quota_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_album
# 描述：相册
CREATE TABLE IF NOT EXISTS phpbb_album (
   pic_id int(11) UNSIGNED NOT NULL auto_increment,
   pic_filename varchar(255) NOT NULL,
   pic_thumbnail varchar(255),
   pic_title varchar(255) NOT NULL,
   pic_desc text,
   pic_user_id mediumint(8) NOT NULL,
   pic_username varchar(32),
   pic_user_ip char(8) NOT NULL DEFAULT '0',
   pic_time int(11) UNSIGNED NOT NULL,
   pic_cat_id mediumint(8) UNSIGNED NOT NULL DEFAULT '1',
   pic_view_count int(11) UNSIGNED NOT NULL DEFAULT '0',
   pic_lock tinyint(3) NOT NULL DEFAULT '0',
   pic_approval tinyint(3) NOT NULL DEFAULT '1',
   PRIMARY KEY (pic_id),
   KEY pic_cat_id (pic_cat_id),
   KEY pic_user_id (pic_user_id),
   KEY pic_time (pic_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_album_rate
# 描述：相册的评价率
CREATE TABLE IF NOT EXISTS phpbb_album_rate (
   rate_pic_id int(11) UNSIGNED NOT NULL,
   rate_user_id mediumint(8) NOT NULL,
   rate_user_ip char(8) NOT NULL,
   rate_point tinyint(3) UNSIGNED NOT NULL,
   KEY rate_pic_id (rate_pic_id),
   KEY rate_user_id (rate_user_id),
   KEY rate_user_ip (rate_user_ip),
   KEY rate_point (rate_point)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_album_comment
# 描述：相册的评价信息
CREATE TABLE IF NOT EXISTS phpbb_album_comment (
   comment_id int(11) UNSIGNED NOT NULL auto_increment,
   comment_pic_id int(11) UNSIGNED NOT NULL,
   comment_user_id mediumint(8) NOT NULL,
   comment_username varchar(32),
   comment_user_ip char(8) NOT NULL,
   comment_time int(11) UNSIGNED NOT NULL,
   comment_text TEXT,
   comment_edit_time int(11) UNSIGNED,
   comment_edit_count smallint(5) UNSIGNED NOT NULL DEFAULT '0',
   comment_edit_user_id mediumint(8),
   PRIMARY KEY(comment_id),
   KEY comment_pic_id (comment_pic_id),
   KEY comment_user_id (comment_user_id),
   KEY comment_user_ip (comment_user_ip),
   KEY comment_time (comment_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_album_cat
# 描述：相册的分类
CREATE TABLE IF NOT EXISTS phpbb_album_cat (
   cat_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   cat_title varchar(255) NOT NULL,
   cat_desc text,
   cat_order mediumint(8) NOT NULL,
   cat_view_level tinyint(3) NOT NULL DEFAULT '-1',
   cat_upload_level tinyint(3) NOT NULL DEFAULT '0',
   cat_rate_level tinyint(3) NOT NULL DEFAULT '0',
   cat_comment_level tinyint(3) NOT NULL DEFAULT '0',
   cat_edit_level tinyint(3) NOT NULL DEFAULT '0',
   cat_delete_level tinyint(3) NOT NULL DEFAULT '2',
   cat_view_groups varchar(255),
   cat_upload_groups varchar(255),
   cat_rate_groups varchar(255),
   cat_comment_groups varchar(255),
   cat_edit_groups varchar(255),
   cat_delete_groups varchar(255),
   cat_moderator_groups varchar(255),
   cat_approval tinyint(3) NOT NULL DEFAULT '0',
   PRIMARY KEY (cat_id),
   KEY cat_order (cat_order)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_album_config
# 描述：相册的全局配置
CREATE TABLE IF NOT EXISTS phpbb_album_config (
   config_name varchar(255) NOT NULL,
   config_value varchar(255) NOT NULL,
   PRIMARY KEY (config_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_shop_icq
# 描述：出售的 ICQ 号码信息
CREATE TABLE IF NOT EXISTS phpbb_shop_icq (
   id mediumint(10) auto_increment,
   icq_number bigint(20) default '0',
   icq_password varchar(255) default NULL,
   icq_cost mediumint(20) default '0',
   PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_shop_url
# 描述：点击链接赚取积分
CREATE TABLE IF NOT EXISTS phpbb_shop_url (
   id mediumint(10) auto_increment,
   url varchar(255) default NULL,
   nazvanie varchar(255) default NULL,
   url_cost mediumint(20) default '0',
   PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_shop_sites
# 描述：用户购买的广告位置
CREATE TABLE IF NOT EXISTS phpbb_shop_sites (
   id mediumint(10) auto_increment,
   site_url varchar(255) default NULL,
   site_desc varchar(80) default NULL,
   site_time int(11) default '0',
   site_order mediumint(1) default '0',
   PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_medal
# 描述：奖
CREATE TABLE IF NOT EXISTS phpbb_medal (
   medal_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   cat_id mediumint(8) UNSIGNED NOT NULL default '1',
   medal_name varchar(40) NOT NULL,
   medal_description varchar(255) NOT NULL,
   medal_image varchar(40) NULL,
   PRIMARY KEY  (medal_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_medal_user
# 描述：获奖的用户
CREATE TABLE IF NOT EXISTS phpbb_medal_user (
   issue_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   medal_id mediumint(8) UNSIGNED NOT NULL,
   user_id mediumint(8) UNSIGNED NOT NULL,
   issue_reason varchar(255) NOT NULL,
   issue_time int(11) NOT NULL,
   PRIMARY KEY  (issue_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_medal_mod
# 描述：版主奖项
CREATE TABLE IF NOT EXISTS phpbb_medal_mod (
   mod_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   medal_id mediumint(8) UNSIGNED NOT NULL,
   user_id mediumint(8) UNSIGNED NOT NULL,
   PRIMARY KEY  (mod_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_medal_cat
# 描述：奖的分类
CREATE TABLE IF NOT EXISTS phpbb_medal_cat (
   cat_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   cat_title varchar(100) NOT NULL,
   cat_order mediumint(8) UNSIGNED NOT NULL default '0',
   PRIMARY KEY  (cat_id),
   KEY cat_order (cat_order)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_rules
# 描述：规则
CREATE TABLE IF NOT EXISTS phpbb_rules (
   rule_id int(11) NOT NULL,
   rule_cat_id int(11) NOT NULL,
   rule_name varchar(200) NOT NULL,
   rule_subj text NOT NULL,
   rule_moder tinyint(4) NOT NULL,
   PRIMARY KEY (rule_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_rules_cat
# 描述：规则的分类
CREATE TABLE IF NOT EXISTS phpbb_rules_cat (
   cat_r_id int(11) NOT NULL,
   cat_r_name varchar(200) NOT NULL,
   PRIMARY KEY (cat_r_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_topic_collect
# 描述：帖子收藏
CREATE TABLE IF NOT EXISTS phpbb_topic_collect (
   id mediumint(8) NOT NULL auto_increment,
   user_id mediumint(8) NOT NULL,
   post_id mediumint(8) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_profile_guestbook 
# 描述：留言板
CREATE TABLE IF NOT EXISTS phpbb_profile_guestbook (
  gb_id int(10) NOT NULL auto_increment,
  user_id int(10) NOT NULL default '0',
  poster_id int(10) NOT NULL default '0',
  bbcode varchar(64) NOT NULL default '',
  gb_time int(10) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  message text NOT NULL,
  user_guest_name varchar(64) NOT NULL default '',
  PRIMARY KEY  (gb_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_shout
# 描述：聊天室
CREATE TABLE IF NOT EXISTS phpbb_shout (
  shout_id mediumint(8) unsigned NOT NULL auto_increment,
  shout_username varchar(25) NOT NULL,
  shout_user_id mediumint(8) NOT NULL,
  shout_session_time int(11) NOT NULL,
  shout_ip char(8) NOT NULL,
  shout_text text NOT NULL,
  enable_bbcode tinyint(1) NOT NULL,
  enable_html tinyint(1) NOT NULL,
  enable_smilies tinyint(1) NOT NULL,
  shout_bbcode_uid varchar(10) NOT NULL,
  KEY shout_id (shout_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_styles
# 描述：风格
CREATE TABLE IF NOT EXISTS phpbb_styles (
	style_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	style_name varchar(64) DEFAULT '' NOT NULL,
	style_path varchar(64) DEFAULT '' NOT NULL,
	PRIMARY KEY (style_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_pages
# 描述：自定义页面
CREATE TABLE IF NOT EXISTS phpbb_pages (
	page_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	page_title varchar(255) DEFAULT '' NOT NULL,
	page_contents text,
	PRIMARY KEY (page_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_index_page
# 描述：首页排版
CREATE TABLE IF NOT EXISTS phpbb_index_page (
	index_content text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_themes
# 描述：WEB版的主题
CREATE TABLE IF NOT EXISTS phpbb_themes (
	themes_id mediumint(8) UNSIGNED NOT NULL auto_increment, 
	template_name varchar(30) NOT NULL default '', 
	style_name varchar(30) NOT NULL default '', 
	head_stylesheet varchar(100) default NULL, 
	body_background varchar(100) default NULL, 
	body_bgcolor varchar(6) default NULL, 
	body_text varchar(6) default NULL, 
	body_link varchar(6) default NULL, 
	body_vlink varchar(6) default NULL, 
	body_alink varchar(6) default NULL, 
	body_hlink varchar(6) default NULL, 
	tr_color1 varchar(6) default NULL, 
	tr_color2 varchar(6) default NULL, 
	tr_color3 varchar(6) default NULL, 
	tr_class1 varchar(25) default NULL, 
	tr_class2 varchar(25) default NULL, 
	tr_class3 varchar(25) default NULL, 
	th_color1 varchar(6) default NULL, 
	th_color2 varchar(6) default NULL, 
	th_color3 varchar(6) default NULL, 
	th_class1 varchar(25) default NULL, 
	th_class2 varchar(25) default NULL, 
	th_class3 varchar(25) default NULL, 
	td_color1 varchar(6) default NULL, 
	td_color2 varchar(6) default NULL, 
	td_color3 varchar(6) default NULL, 
	td_class1 varchar(25) default NULL, 
	td_class2 varchar(25) default NULL, 
	td_class3 varchar(25) default NULL, 
	fontface1 varchar(50) default NULL, 
	fontface2 varchar(50) default NULL, 
	fontface3 varchar(50) default NULL, 
	fontsize1 tinyint(4) default NULL, 
	fontsize2 tinyint(4) default NULL, 
	fontsize3 tinyint(4) default NULL, 
	fontcolor1 varchar(6) default NULL, 
	fontcolor2 varchar(6) default NULL, 
	fontcolor3 varchar(6) default NULL, 
	span_class1 varchar(25) default NULL, 
	span_class2 varchar(25) default NULL, 
	span_class3 varchar(25) default NULL, 
	img_size_poll smallint(5) UNSIGNED, 
	img_size_privmsg smallint(5) UNSIGNED, 
	PRIMARY KEY (themes_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_theme_name
# 描述：WEB版主题
CREATE TABLE IF NOT EXISTS phpbb_themes_name (
	themes_id smallint(5) UNSIGNED DEFAULT '0' NOT NULL, 
	tr_color1_name char(50), 
	tr_color2_name char(50), 
	tr_color3_name char(50), 
	tr_class1_name char(50), 
	tr_class2_name char(50), 
	tr_class3_name char(50), 
	th_color1_name char(50), 
	th_color2_name char(50), 
	th_color3_name char(50), 
	th_class1_name char(50), 
	th_class2_name char(50), 
	th_class3_name char(50), 
	td_color1_name char(50), 
	td_color2_name char(50), 
	td_color3_name char(50), 
	td_class1_name char(50), 
	td_class2_name char(50), 
	td_class3_name char(50), 
	fontface1_name char(50), 
	fontface2_name char(50), 
	fontface3_name char(50), 
	fontsize1_name char(50), 
	fontsize2_name char(50), 
	fontsize3_name char(50), 
	fontcolor1_name char(50), 
	fontcolor2_name char(50), 
	fontcolor3_name char(50), 
	span_class1_name char(50), 
	span_class2_name char(50), 
	span_class3_name char(50), 
	PRIMARY KEY (themes_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_linkexchange
# 描述：友情链接
CREATE TABLE IF NOT EXISTS phpbb_linkexchange (
  link_id smallint(8) unsigned NOT NULL default '0',
  link_name varchar(255) NOT NULL default '',
  link_email varchar(255) NOT NULL default '',  
  link_website varchar(255) NOT NULL default '',
  link_img varchar(255) NOT NULL default '',    
  link_desc text NOT NULL,
  link_url varchar(255) NOT NULL default '',
  link_active tinyint(3) unsigned NOT NULL default '1',
  link_out int(11) NOT NULL default '0',
  link_in int(11) NOT NULL default '0',
  PRIMARY KEY  (link_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_specials
# 描述：专题
CREATE TABLE IF NOT EXISTS phpbb_specials (
  special_id mediumint(8) unsigned NOT NULL auto_increment,
  special_name varchar(255) NOT NULL,
  special_forum mediumint(8) NOT NULL,
  PRIMARY KEY  (special_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_sign
# 描述：签到
CREATE TABLE IF NOT EXISTS phpbb_sign (
  sign_id mediumint(8) unsigned NOT NULL auto_increment,
  sign_user_id mediumint(8) NOT NULL default '-1',
  sign_time int(11) NOT NULL default '0',
  sign_talk text,
  PRIMARY KEY  (sign_id),
  KEY sign_user_id (sign_user_id),
  KEY sign_time (sign_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_lottery
# 描述：彩票
CREATE TABLE IF NOT EXISTS phpbb_lottery (
  id int(10) unsigned NOT NULL auto_increment,
  user_id int(10) NOT NULL,
  PRIMARY KEY  (id),
  KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_lottery_history
# 描述：彩票中奖记录
CREATE TABLE IF NOT EXISTS phpbb_lottery_history (
  id int(10) unsigned NOT NULL auto_increment,
  user_id int(10) NOT NULL,
  amount int(10) NOT NULL,
  currency char(32) NOT NULL,
  time int(10) NOT NULL,
  PRIMARY KEY  (id),
  KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;	

# 表：phpbb_bank
# 描述：银行
CREATE TABLE IF NOT EXISTS phpbb_bank (
  id int(10) unsigned NOT NULL auto_increment,
  user_id int(10) NOT NULL,
  holding int(10) unsigned default '0',
  totalwithdrew int(10) unsigned default '0',
  totaldeposit int(10) unsigned default '0',
  opentime int(10) unsigned NOT NULL,
  fees char(5) NOT NULL default 'on',
  PRIMARY KEY  (user_id),
  KEY id (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 表：phpbb_invite
# 描述：邀请
CREATE TABLE IF NOT EXISTS phpbb_invite (
  id int(6) NOT NULL auto_increment,
  user_id int(8) NOT NULL,
  ip text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS phpbb_deliver (
  deliver_id mediumint(8) unsigned NOT NULL auto_increment,
  deliver_poster mediumint(8) NOT NULL default '0',
  deliver_point int(11) NOT NULL default '0',
  deliver_cut_point int(11) NOT NULL default '0',
  deliver_title char(255) NOT NULL,
  deliver_reason text,
  PRIMARY KEY (deliver_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS phpbb_partake_deliver (
  partake_id mediumint(8) unsigned NOT NULL auto_increment,
  partake_user_id mediumint(8) NOT NULL default '0',
  deliver_id mediumint(8) NOT NULL default '0',
  PRIMARY KEY (partake_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;