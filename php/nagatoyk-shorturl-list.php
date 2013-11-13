<?php
error_reporting(E_ALL^E_NOTICE);
header('Content-type: text/html;charset=utf-8');
$file = 'urls.txt';
$use_rewrite = 1;
if(!file_exists($file)){
	fopen($file, 'w+');
}
$urls = file($file);
for($i = 0; $i < count($urls); $i++){
	$id = $i + 1;
	$id = base_convert($id, '10', '32');
	$dir = dirname($_SERVER['PHP_SELF']);
	$filename = explode('/', $_SERVER['PHP_SELF']);
	$filename = '/'.$filename[(count($filename) - 1)];
	$shorturl = ($use_rewrite == 1) ? "http://{$_SERVER['HTTP_HOST']}{$dir}s/{$id}" : "http://{$_SERVER['HTTP_HOST']}{$dir}{$filename}?id={$id}";
	$url = urldecode($urls[$i]);
	echo $output = "<p>".sprintf("%04d", base_convert($id, '32', '10'))."{$l_yoururl} <a href=\"{$shorturl}\" onclick=\"window.open(this.href);return false\">{$shorturl}</a></p>";
}
