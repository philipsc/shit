<?php
/**
 * 如果使用url重写的话
 * 会生成的短链形式是http://域名/s/****** 的
 * 要在伪静态规则中添加相应的规则否则会报404错误
 */
error_reporting(E_ALL^E_NOTICE);
header('Content-type: text/html;charset=utf-8');
?>
<html>
<head>
	<title>KLOLI短网址服务</title>
</head>
<body>
<?php
/**
 * 载入urls.txt,如不存在则创建该文件
 */
$file = 'urls.txt';
if(!file_exists($file)){
	fopen($file, 'w+');
}
/**
 * 是否启用url重写规则 0 不重写 或 1 重写
 */
$use_rewrite = 1;
/**
 * language/style/output variables
 */
$l_url = 'URL';
$l_nourl = '<strong>没有输入URL地址</strong>';
$l_yoururl = '<strong>你的短网址:</strong>';
$l_invalidurl = '<strong>无效的URL.</strong>';
$l_createurl = '生成短网址';
function getLineNum($filePath, $target, $first = false){
	$fp = fopen($filePath, "r");
	$lineNumArr = array();
	$lineNum = 0;
	while(!feof($fp)){
		$lineNum++;
		$lineCont = fgets($fp);
		if(strstr($lineCont, $target)){
			if($first){
				return $lineNum;
			}else{
				$lineNumArr[] = $lineNum;
			}
		}
	}
	return $lineNumArr;
}
//////////////////// 不需要编辑的部分 ////////////////////
if(!is_writable($file) || !is_readable($file)){
	die('Cannot write or read from file. Please CHMOD the url file (urls.txt) by default to 777 and make sure it is uploaded.');
}
$action = (empty($_GET['id'])) ? 'create' : 'redirect';
// $valid = "~^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$~";
$valid = "/(http|https|ftp|file){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=]*)*\/?/";
$output = '';
if($action == 'create'){
	if(isset($_POST['create'])){
		$url = trim($_POST['url']);
		if($url == ''){
			$output = $l_nourl;
		}else{
			if(preg_match($valid, $url)){
				$count = count(getLineNum($file, $url));
				if($count !== 0){
					$id = getLineNum($file, $url, true);
				}else{
					$fp = fopen($file, 'a');
					fwrite($fp, "\r\n{$url}");
					fclose($fp);
					$id	= count(file($file));
				}
				$id = base_convert($id, '10', '32');
				$dir = dirname($_SERVER['PHP_SELF']);
				$filename = explode('/', $_SERVER['PHP_SELF']);
				$filename = '/'.$filename[(count($filename) - 1)];
				$shorturl = ($use_rewrite == 1) ? "http://{$_SERVER['HTTP_HOST']}{$dir}s/{$id}" : "http://{$_SERVER['HTTP_HOST']}{$dir}{$filename}?id={$id}";
				$output = "{$l_yoururl} <a href=\"{$shorturl}\" onclick=\"window.open(this.href);return false\">{$shorturl}</a>";
			}else{
				$output = $l_invalidurl;
			}
		}
	}
}
if($action == 'redirect'){
	$urls = file($file);
	$id   = trim($_GET['id']);
	$id   = base_convert($id, '32', '10') - 1;
	if(isset($urls[$id])){
		header("Location: {$urls[$id]}");
		exit();
	}else{
		die('Script error');
	}
}
//////////////////// 以下部分可以编辑 ////////////////////
?>
<p>短网址服务可以帮助你把一个长网址缩短，方便你在社交网络和微博上分享链接。</p>
<!-- start html output -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p class="response"><?php echo $output; ?></p>
<p>
	<label for="s-url">请输入URL地址:</label>
	<input name="url" type="text" id="s-url" size="60" placeholder="示例:http://blog.yukimax.com" />
</p>
<p>
	<input type="submit" class="button" name="create" value="<?php echo $l_createurl; ?>" />
</p>
</form>
<!-- end html output -->
<!-- update: 201310101030 已经隔绝重复地址生成短链问题,UI方面待完善,暂时只考虑吧功能做起来 -->
</body>
</html>
<?php
ob_end_flush();
$a = '0001001';
// echo (int)$a;
// echo intval($a);
?>
