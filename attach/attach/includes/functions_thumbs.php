<?php

if ( !defined('IN_PHPBB') )
{
	die("ERROR!!! THIS FILE PROTECTED. IF YOU SAW THIS REPORT, MEANS HACKERS HERE IS NOTHING TO DO ");
}


$imagick = '';

function get_img_size_format($width, $height)
{
	$max_width = 400;

	if ($width > $height)
	{
		return array(
			round($width * ($max_width / $width)),
			round($height * ($max_width / $width))
		);
	} 
	else 
	{
		return array(
			round($width * ($max_width / $height)),
			round($height * ($max_width / $height))
		);
	}
}

function is_imagick() 
{
	global $imagick, $attach_config;

	if ($attach_config['img_imagick'] != '')
	{
		$imagick = $attach_config['img_imagick'];
		return true;
	}
	else
	{
		return false;
	}
}

function get_supported_image_types($type)
{
	if (@extension_loaded('gd'))
	{
		$format = imagetypes();
		$new_type = 0;

		switch ($type)
		{
			case 1:
				$new_type = ($format & IMG_GIF) ? IMG_GIF : 0;
				break;
			case 2:
			case 9:
			case 10:
			case 11:
			case 12:
				$new_type = ($format & IMG_JPG) ? IMG_JPG : 0;
				break;
			case 3:
				$new_type = ($format & IMG_PNG) ? IMG_PNG : 0;
				break;
			case 6:
			case 15:
				$new_type = ($format & IMG_WBMP) ? IMG_WBMP : 0;
				break;
		}
		
		return array(
			'gd'		=> ($new_type) ? true : false,
			'format'	=> $new_type,
			'version'	=> (function_exists('imagecreatetruecolor')) ? 2 : 1
		);
	}

	return array('gd' => false);
}

function create_thumbnail($source, $new_file, $mimetype) 
{
	global $attach_config, $imagick;

	$source = amod_realpath($source);
	$min_filesize = (int) $attach_config['img_min_thumb_filesize'];
	$img_filesize = (@file_exists($source)) ? @filesize($source) : false;

	if (!$img_filesize || $img_filesize <= $min_filesize)
	{
		return false;
	}

	list($width, $height, $type, ) = getimagesize($source);

	if (!$width || !$height)
	{
		return false;
	}

	list($new_width, $new_height) = get_img_size_format($width, $height);

	$tmp_path = $old_file = '';

	if (intval($attach_config['allow_ftp_upload']))
	{
		$old_file = $new_file;

		$tmp_path = explode('/', $source);
		$tmp_path[count($tmp_path)-1] = '';
		$tmp_path = implode('/', $tmp_path);

		if ($tmp_path == '')
		{
			$tmp_path = '/tmp';
		}

		$value = trim($tmp_path);

		if ($value[strlen($value)-1] == '/')
		{
			$value[strlen($value)-1] = ' ';
		}

		$new_file = tempnam(trim($value), 't00000');

		@unlink($new_file);
	}

	$used_imagick = false;

	if (is_imagick()) 
	{
		passthru($imagick . ' -quality 85 -antialias -sample ' . $new_width . 'x' . $new_height . ' "' . str_replace('\\', '/', $source) . '" +profile "*" "' . str_replace('\\', '/', $new_file) . '"');
		if (@file_exists($new_file))
		{
			$used_imagick = true;
		}
	} 

	if (!$used_imagick) 
	{
		$type = get_supported_image_types($type);
		
		if ($type['gd'])
		{
			switch ($type['format']) 
			{
				case IMG_GIF:
					$image = imagecreatefromgif($source);
					break;
					