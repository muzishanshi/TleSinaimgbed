<?php
/**
 * 前台图床页面
 */
?>
<?php
!defined('EMLOG_ROOT') && exit('access deined!');
date_default_timezone_set('Asia/Shanghai');

$DB = Database::getInstance();

$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
$tle_sinaimgbed_set=unserialize($get_option["option_value"]);

$isMultiple="multiple";
if($tle_sinaimgbed_set["issavealbum"]=="y"){
	$isMultiple="";
}
try{
?>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $site_title; ?>图床</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="renderer" content="webkit">
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
	<meta name="author" content="同乐儿">
	<link rel="alternate icon" href="<?=$tle_sinaimgbed_set["weiboprefix"];?>ecabade5ly1fxpiemcap1j200s00s744.jpg" type="image/png" />
	<script src="https://www.tongleer.com/api/web/include/layui/layui.js"></script>
</head>
<body>
<div id="weibofile_webimg_container" onclick="weibofile_file.click()" style="margin:5px 0px;position: relative; border: 2px dashed #e2e2e2; background-image:url('<?=$tle_sinaimgbed_set["webimgbg"];?>'); text-align: center; cursor: pointer;">
	<p id="weibofile_webimg_upload" style="height: <?=$tle_sinaimgbed_set["webimgheight"];?>px;line-height:<?=$tle_sinaimgbed_set["webimgheight"];?>px;position: relative;font-size:20px; color:#d3d3d3;">点击选择图片上传至图床</p> 
	<input type="file" id="weibofile_file" style="display:none" accept="image/*" <?=$isMultiple;?> /> 
</div>
<script>
var weibofile_webimgdiv = document.getElementById('weibofile_webimg_upload');
var weibofile_file = document.getElementById('weibofile_file');
weibofile_file.addEventListener('change', function() {
	inputFileHandler();
}, false);
function inputFileHandler(){
	var file = weibofile_file.files;
	upLoad(file);
}
function upLoad(file){
	layui.use('layer', function(){
		var $ = layui.jquery, layer = layui.layer;
		var xhr = new XMLHttpRequest();
		var data;
		var upLoadFlag = true;
		if(upLoadFlag === false){
			layer.msg('正在上传中……请稍后……');
			return;
		}
		if(!file){
			layer.msg('不要上传图片了吗……');
			return;
		}
		data = new FormData();
		data.append('action', 'upload');
		for (var i=0;i<file.length;i++){
			if(file[i] && file[i].type.indexOf('image') === -1){
				layer.msg('这不是一张图片……请重新选择……');
				return;
			}
			data.append('file['+i+']', file[i]);
		}
		xhr.open("POST", "<?=BLOG_URL.'content/plugins/tle_sinaimgbed/ajax/ajax_webimg.php';?>");
		xhr.send(data);
		upLoadFlag = false;
		weibofile_webimgdiv.innerHTML = '上传中……';
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				upLoadFlag = true;
				var data=JSON.parse(xhr.responseText);
				if(data.status=="noset"){
					weibofile_webimgdiv.innerHTML = data.msg;
				}else if(data.status=="disable"){
					weibofile_webimgdiv.innerHTML = data.msg;
				}else if(data.status=="ok"){
					weibofile_webimgdiv.innerHTML = '点击选择图片上传至图床';
					layer.confirm('<small><font color="green">'+data.msg+'<br />'+data.hrefs+'</font></small><textarea style="width:100%;margin: 0 auto;" rows="2" onfocus="this.select();">'+data.codes+'</textarea>', {
						btn: ['关闭']
					},function(index){
						layer.close(index);
					});
					var urls=data.urls.split("<br />");
					document.getElementById('weibofile_webimg_container').style.backgroundImage = "url("+urls[0]+")";
				}else{
					weibofile_webimgdiv.innerHTML = data.msg;
				}
			}
		}
	});
}
</script>
</body>
</html>
<?php
}catch(Exception $e){}
?>