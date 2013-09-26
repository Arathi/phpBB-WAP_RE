<?php

if ( !defined('IN_PHPBB') )
{
	die("ERROR!!! THIS FILE PROTECTED. IF YOU SAW THIS REPORT, MEANS HACKERS HERE IS NOTHING TO DO ");
}


define('ATTACH_DEBUG', 0);
define('AUTH_DOWNLOAD', 20);
define('INLINE_LINK', 1);
define('PHYSICAL_LINK', 2);
define('NONE_CAT', 0);
define('IMAGE_CAT', 1);
define('STREAM_CAT', 2);
define('SWF_CAT', 3);
define('ATTACH_CONFIG_TABLE', $table_prefix . 'attachments_config');
define('EXTENSION_GROUPS_TABLE', $table_prefix . 'extension_groups');
define('EXTENSIONS_TABLE', $table_prefix . 'extensions');
define('FORBIDDEN_EXTENSIONS_TABLE', $table_prefix . 'forbidden_extensions');
define('ATTACHMENTS_DESC_TABLE', $table_prefix . 'attachments_desc');
define('ATTACHMENTS_TABLE', $table_prefix . 'attachments');
define('QUOTA_TABLE', $table_prefix . 'attach_quota');
define('QUOTA_LIMITS_TABLE', $table_prefix . 'quota_limits');
define('PAGE_UACP', -1210);
define('PAGE_RULES', -1214);
define('MEGABYTE', 1024);
define('ADMIN_MAX_ATTACHMENTS', 50); 
define('THUMB_DIR', 'thumbs');
define('MODE_THUMBNAIL', 1);
define('GPERM_ALL', 0); 
define('QUOTA_UPLOAD_LIMIT', 1);
define('QUOTA_PM_LIMIT', 2);
define('ATTACH_VERSION', '2.4.5');

?>