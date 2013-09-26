<script type="text/javascript">
function DrawImage(ImgOBJ,Width,Height){var OWidth=ImgOBJ.width;var OHeight=ImgOBJ.height;if((OWidth>0)&&(OHeight>0)){if(OWidth/OHeight>=Width/Height){if(OWidth>Width){ImgOBJ.width=Width;ImgOBJ.height=(OHeight*Width)/OWidth}else{ImgOBJ.width=OWidth;ImgOBJ.height=OHeight}}else{if(OHeight>Height){ImgOBJ.height=Height;ImgOBJ.width=(OWidth*Height)/OHeight}else{ImgOBJ.width=OWidth;ImgOBJ.height=OHeight}}}}
</script>
<!-- BEGIN attach --> 
	<hr size="1" color="white" noshade/>
	<!-- BEGIN denyrow --> 
		{postrow.attach.denyrow.L_DENIED}<br/> 
	<!-- END denyrow --> 
	<!-- BEGIN cat_images --> 
		{postrow.attach.cat_images.S_UPLOAD_IMAGE}
		<a href="{postrow.attach.cat_images.IMG_SRC}"><img src="{postrow.attach.cat_images.IMG_SRC}" onload="DrawImage(this,220,320)" alt="." border="0" /></a><br />
		文件大小：{postrow.attach.cat_images.FILESIZE}{postrow.attach.cat_images.SIZE_VAR}<br />
		{postrow.attach.cat_images.L_DOWNLOADED_VIEWED}:{postrow.attach.cat_images.L_DOWNLOAD_COUNT}<br />
	<!-- END cat_images --> 
	<!-- BEGIN cat_thumb_images -->
		{postrow.attach.cat_thumb_images.S_UPLOAD_IMAGE}<a href="{postrow.attach.cat_thumb_images.IMG_SRC}">{postrow.attach.cat_thumb_images.DOWNLOAD_NAME}</a> ({postrow.attach.cat_thumb_images.FILESIZE} {postrow.attach.cat_thumb_images.SIZE_VAR})<br/>
		{postrow.attach.cat_thumb_images.L_DOWNLOADED_VIEWED} {postrow.attach.cat_thumb_images.L_DOWNLOAD_COUNT}<br />
	<!-- END cat_thumb_images -->
	<!-- BEGIN attachrow -->
		<a href="{postrow.attach.attachrow.U_DOWNLOAD_LINK}">{postrow.attach.attachrow.S_UPLOAD_IMAGE}{postrow.attach.attachrow.DOWNLOAD_NAME}</a><br />
		(文件大小：{postrow.attach.attachrow.FILESIZE} {postrow.attach.attachrow.SIZE_VAR})<br/>
		{postrow.attach.attachrow.L_DOWNLOADED_VIEWED} {postrow.attach.attachrow.L_DOWNLOAD_COUNT}<br />
	<!-- END attachrow --> 
<!-- END attach --> 