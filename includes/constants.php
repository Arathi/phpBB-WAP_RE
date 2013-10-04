<?php
/***************************************************************************
 *                               constants.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

include($phpbb_root_path . 'album/album_constants.' . $phpEx);

define('DEBUG', 1); 

define('DELETED', -1);
define('ANONYMOUS', -1);

define('USER', 0);
define('ADMIN', 1);
define('MOD', 2);
define('MODCP', 3);

define('POST_MEDAL_URL', 'm');
define('MEDAL_CAT_URL', 'mc');
define('PAGE_URL', 'page');

define('USER_ACTIVATION_NONE', 0);
define('USER_ACTIVATION_SELF', 1);
define('USER_ACTIVATION_ADMIN', 2);

define('USER_AVATAR_NONE', 0);
define('USER_AVATAR_UPLOAD', 1);
define('USER_AVATAR_REMOTE', 2);
define('USER_AVATAR_GALLERY', 3);

define('GROUP_OPEN', 0);
define('GROUP_CLOSED', 1);
define('GROUP_HIDDEN', 2);

define('FORUM_UNLOCKED', 0);
define('FORUM_LOCKED', 1);

define('TOPIC_UNLOCKED', 0);
define('TOPIC_LOCKED', 1);
define('TOPIC_MOVED', 2);
define('TOPIC_WATCH_NOTIFIED', 1);
define('TOPIC_WATCH_UN_NOTIFIED', 0);

define('POST_NORMAL', 0);
define('POST_STICKY', 1);
define('POST_ANNOUNCE', 2);
define('POST_GLOBAL_ANNOUNCE', 3);

define('BEGIN_TRANSACTION', 1);
define('END_TRANSACTION', 2);

define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204);

define('PRIVMSGS_READ_MAIL', 0);
define('PRIVMSGS_NEW_MAIL', 1);
define('PRIVMSGS_SENT_MAIL', 2);
define('PRIVMSGS_SAVED_IN_MAIL', 3);
define('PRIVMSGS_SAVED_OUT_MAIL', 4);
define('PRIVMSGS_UNREAD_MAIL', 5);

define('POST_TOPIC_URL', 't');
define('POST_CAT_URL', 'c');
define('POST_FORUM_URL', 'f');
define('POST_USERNAME_URL', 'user');
define('POST_USERS_URL', 'u');
define('POST_POST_URL', 'p');
define('POST_GROUPS_URL', 'g');

define('SESSION_METHOD_COOKIE', 100);
define('SESSION_METHOD_GET', 101);

define('PAGE_INDEX', 0);
define('PAGE_LOGIN', -1);
define('PAGE_SEARCH', -2);
define('PAGE_REGISTER', -3);
define('PAGE_PROFILE', -4);
define('PAGE_VIEWONLINE', -6);
define('PAGE_VIEWMEMBERS', -7);
define('PAGE_FAQ', -8);
define('PAGE_POSTING', -9);
define('PAGE_PRIVMSGS', -10);
define('PAGE_GROUPCP', -11);
define('PAGE_SHOUTBOX_MAX',-1035);
define('PAGE_SHOUTBOX',-1035);
define('PAGE_TOPIC_OFFSET', 5000);
define('PAGE_PRAVILA', -13);
define('PAGE_MEDALS', -2115);

define('AUTH_LIST_ALL', 0);
define('AUTH_ALL', 0);

define('AUTH_REG', 1);
define('AUTH_ACL', 2);
define('AUTH_MOD', 3);
define('AUTH_ADMIN', 5);

define('AUTH_VIEW', 1);
define('AUTH_READ', 2);
define('AUTH_POST', 3);
define('AUTH_REPLY', 4);
define('AUTH_EDIT', 5);
define('AUTH_DELETE', 6);
define('AUTH_ANNOUNCE', 7);
define('AUTH_STICKY', 8);
define('AUTH_POLLCREATE', 9);
define('AUTH_VOTE', 10);
define('AUTH_ATTACH', 11);

define('REPORT_POST_NEW', 1);
define('REPORT_POST_CLOSED', 2);

define('CONFIRM_TABLE', $table_prefix.'confirm');
define('AUTH_ACCESS_TABLE', $table_prefix.'auth_access');
define('BANLIST_TABLE', $table_prefix.'banlist');
define('BOOKMARK_TABLE', $table_prefix.'bookmarks');
define('CATEGORIES_TABLE', $table_prefix.'categories');
define('CONFIG_TABLE', $table_prefix.'config');
define('DISALLOW_TABLE', $table_prefix.'disallow');
define('FORUMS_TABLE', $table_prefix.'forums');
define('GROUPS_TABLE', $table_prefix.'groups');
define('GROUPS_GUESTBOOK_TABLE', $table_prefix.'groups_guestbook');
define('POSTS_TABLE', $table_prefix.'posts');
define('POSTS_TEXT_TABLE', $table_prefix.'posts_text');
define('PRIVMSGS_TABLE', $table_prefix.'privmsgs');
define('PRIVMSGS_TEXT_TABLE', $table_prefix.'privmsgs_text');
define('PRIVMSGS_IGNORE_TABLE', $table_prefix.'privmsgs_ignore');
define('PRUNE_TABLE', $table_prefix.'forum_prune');
define('RANKS_TABLE', $table_prefix.'ranks');
define('RULES_TABLE', $table_prefix.'rules');
define('RULES_CAT_TABLE', $table_prefix.'rules_cat');
define('SEARCH_TABLE', $table_prefix.'search_results');
define('SEARCH_WORD_TABLE', $table_prefix.'search_wordlist');
define('SEARCH_MATCH_TABLE', $table_prefix.'search_wordmatch');
define('SESSIONS_TABLE', $table_prefix.'sessions');
define('SESSIONS_KEYS_TABLE', $table_prefix.'sessions_keys');
define('SMILIES_TABLE', $table_prefix.'smilies');
define('THEMES_TABLE', $table_prefix.'themes');
define('THEMES_NAME_TABLE', $table_prefix.'themes_name');
define('TOPICS_TABLE', $table_prefix.'topics');
define('TOPICS_WATCH_TABLE', $table_prefix.'topics_watch');
define('USER_GROUP_TABLE', $table_prefix.'user_group');
define('USERS_TABLE', $table_prefix.'users');
define('WORDS_TABLE', $table_prefix.'words');
define('VOTE_DESC_TABLE', $table_prefix.'vote_desc');
define('VOTE_RESULTS_TABLE', $table_prefix.'vote_results');
define('VOTE_USERS_TABLE', $table_prefix.'vote_voters');
define('POST_REPORTS_TABLE', $table_prefix.'post_reports');
define('REPUTATION_TABLE', $table_prefix.'reputation');
define('REPUTATION_TEXT_TABLE', $table_prefix.'reputation_text');
define('STYLES_CSS', $table_prefix.'styles_css');
define('STYLES_TABLE', $table_prefix.'styles');
define('MEDAL_TABLE', $table_prefix.'medal');
define('MEDAL_MOD_TABLE', $table_prefix.'medal_mod');
define('MEDAL_USER_TABLE', $table_prefix.'medal_user');
define('MEDAL_CAT_TABLE', $table_prefix.'medal_cat');
define("PROFILE_GUESTBOOK_TABLE", $table_prefix.'profile_guestbook');
define('SHOUTBOX_TABLE', $table_prefix.'shout');
define('INDEX_PAGE_TABLE', $table_prefix.'index_page');
define('PAGES_TABLE', $table_prefix.'pages');
define('INVITE_TABLE', $table_prefix.'invite');

define('REPUTATION_INC', 1);
define('REPUTATION_DEC', 2);
define('REPUTATION_WARNING', 3);
define('REPUTATION_BAN', 4);
define('REPUTATION_WARNING_EXPIRED', 7);
define('REPUTATION_BAN_EXPIRED', 8);

define('REPUTATION_SUM', 0);
define('REPUTATION_PLUSMINUS', 1);

define('POST_REVIEWS_URL', 'r');
define('RESPECTED_CACHE', 'respected.dat');
define('NO_ID', -1);

// 星座
$zodiacdates = array (
	'0101', '0120', '0121', '0219', '0220', '0320', '0321', '0420',
	'0421', '0520', '0521', '0621', '0622', '0722', '0723', '0823',
	'0824', '0922', '0923', '1022', '1023', '1122', '1123', '1221',
	'1222', '1231'
);
$zodiacs = array (
	'Capricorn','Aquarius', 'Pisces', 'Aries', 'Taurus',
	'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 
	'Sagittarius','Capricorn'
);
define('LINK_EXCHANGE_TABLE', $table_prefix.'linkexchange');
?>