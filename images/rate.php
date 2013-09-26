<?php
error_reporting(0);
$i = intval($_GET['i']);
$i = $i - 1;
header("Content-type: image/gif");
$im = imagecreate(100, 4);
$c0 = imagecolorallocate ($im, 0, 0, 0);
$c1 = imagecolorallocate($im, 255, 50, 0);
$c2 = imagecolorallocate($im, 0, 100, 225);
imagefill($im, 100, 0, $c2);
imagefilledrectangle($im, 0, 0, $i, 3, $c1);
imagerectangle($im, 0, 0, 99, 3, $c0);
imagegif($im);
?>