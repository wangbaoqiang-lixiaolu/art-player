<?php
// error_reporting(0);
header('Content-Type: text/html;charset=utf-8');//utf-8格式
header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');//禁止页面被缓存
include("tj.php");//统计在线人数代码
require $_SERVER["DOCUMENT_ROOT"] . "/admin/data.php";//获取播放器配置项
isset($_GET['url']) ? $url = htmlspecialchars(urldecode(trim($_GET['url']))) : exit('
<html>
   <head>
  <meta charset="UTF-8" />
  <title>' . htmlspecialchars($MIZHI['title']) . '</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta name="x5-orientation" content="portrait" />
  <meta name="renderer" content="webkit" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link href="./css/home/common.css" rel="stylesheet">
		<link href="./css/home/home.css" rel="stylesheet">
<style>body,html {
	font: 18px"Microsoft Jhenghei",Arial,Lucida Grande,Tahoma,sans-serif;
	width: 100%;
	height: 100%;
	padding: 0;
	margin: 0;
	overflow-x: hidden;
	overflow-y: hidden;
	background-color: black
}
#loading {
	width: 100%;
	height: 100%;
	padding: 0;
	margin: 0;
	text-align: center;
	display: table;
	position: absolute;
	z-index: 10000000001;
	background-size: 100%100%
}
h1 {
	color: #ffffff;
	font-size: 1.2rem;
	margin: 0;
	padding: 0;
	vertical-align: middle;
	display: table-cell
}</style>
  
		<script src="./js/home/runtime.js">
		</script>
		<script src="./js/home/vendor.js">
		</script>
		<script src="./js/home/home.js">
		</script>
 </head>
 <body>
  <div class="video-panel">
   <div style="background-image: url(/img/null_bg.png);" class="video-panel-bg-image"></div>
   <div class="video-panel-video-wrapper">
    <video id="js-panel-video" class="video-panel-bg-video" loop="" muted="" preload="none">
     <source src="' . htmlspecialchars($MIZHI['err_bg_vodurl']) . '" type="video/mp4"></source>
    </video>
   </div>
  </div>
  <div id="loading" class="center">
<h1 style="text-shadow: 2px 2px 5px #d5ebe1;">' . htmlspecialchars($MIZHI['title']) . '<br><br><br>' . htmlspecialchars($MIZHI['keywords']) . '<br><br>    <font size="10">解析失败 URL为空，请稍后再试~</font><br><br><font size="3"><p>本播放器可针对json解析进行播放，请在后台添加json解析接口</p><p><p><br>所有视频格式均可直接播放，如mp4,m3u8,flv，等等。。。</p><p><br></font><font size="2">2021-2022 All Rights Reserved 觅知json解析专用弹幕播放器<br>所有资源均来源第三方资源，并不提供影片资源存储，也不参与录制、上传相关视频，视频版权归属其合法持有人所有<br>本站不对使用者的行为负担任何法律责任。如果有因为本站而导致您的权益受到损害，请与我们联系，我们将理性对待，协助你解决相关问题。<a href="https://www.98dou.cn/" target="_Blank">觅知博客</a></font>
</h1></body></html>');
isset($_GET['live']) ? $live=htmlspecialchars(urldecode(trim($_GET['live']))) : "";
$title = isset($_GET['name']) ? htmlspecialchars(urldecode(trim($_GET['name']))) : (isset($MIZHI['title']) ? htmlspecialchars($MIZHI['title']) : null);
isset($_GET['ids']) ? $ids=htmlspecialchars(urldecode(trim($_GET['ids']))) : "";
isset($_GET['title']) ? $title=htmlspecialchars(urldecode(trim($_GET['title']))) : "";
isset($_GET['xl']) ? $xl=htmlspecialchars(urldecode(trim($_GET['xl']))) : "";
isset($_GET['next']) ? $next=htmlspecialchars(urldecode(trim($_GET['next']))) : "";

isset($_GET['pic']) ? $pic=htmlspecialchars(urldecode(trim($_GET['pic']))) : $pic=$MIZHI['err_bg_imgurl'];
isset($_GET['img']) ? $img=htmlspecialchars(urldecode(trim($_GET['img']))) : $img=$MIZHI['load_bg'];


/*isset($_GET['pic']) ? $pic=htmlspecialchars(urldecode(trim($_GET['pic']))) : $pic="";

$img = isset($_GET['img']) ? htmlspecialchars(urldecode(trim($_GET['img']))) : (isset($MIZHI['load_bg']) ? htmlspecialchars($MIZHI['load_bg']) : null);*/
isset($_GET['content']) ? $content=htmlspecialchars(urldecode(trim($_GET['content']))) : $content=$MIZHI['contenttext'];
function ser_url($url=''){
    if (substr($url, 0, 4) == "mac:") {
        $url = str_replace('mac:', 'http:', $url);
    } elseif (substr($url, 0, 2) == "//") {
        $url = str_replace('//', 'http://', $url);
    } elseif (substr($url, 0, 7) == "/upload" || substr($url, 0, 7) == "upload/") {
        $url = $MIZHI['cmsimg'].$url;
    } 
    return $url;
}
$url = !preg_match('/(http:\/\/)|(https:\/\/)/i', $url)&&preg_match('/(^\/\/)/i', $url)?$url = preg_replace('/(^\/\/)/i', 'https://', $url):$url;
if (strstr($url, 'm.v.qq.com')==true){
    parse_str(str_replace('?', '&', $_SERVER['QUERY_STRING']),$list);
    if ($list['vid'] && $list['cid']){
    $url='https://v.qq.com/x/cover/'.$list['cid'].'/'.$list['vid'].'.html';
    }elseif ($list['vid']){
    $url='https://v.qq.com/x/cover/'.$list['vid'].'/'.$list['vid'].'.html';
    }elseif ($list['cid']){
    $url='https://v.qq.com/x/cover/'.$list['cid'].'.html';
    }
}
elseif (strstr($url, 'm.fun.tv')==true){
    parse_str(str_replace('?', '&', $_SERVER['QUERY_STRING']),$list);
    if ($list['mid'] && $list['vid']){
    $url='https://www.fun.tv/vplay/g-'.$list['mid'].'.v-'.$list['vid'];
    }elseif($list['mid']){
    $url='https://www.fun.tv/vplay/g-'.$list['mid'].'/';
    }elseif($list['vid']){
    $url='https://www.fun.tv/vplay/v-'.$list['vid'].'/';
    }
}
$key = $MIZHI['key'];
$vkey = generateRandStr(16);

if(isset($live)&&$live=='1'){
    $live='true';
    $url = $MIZHI['live_api'].$url;
}else {
    $live='false';
}
function generateRandStr(int $length = 32): string
{
    $md5 = md5(uniqid(md5((string)time())) . mt_rand(10000, 9999999));
    return substr($md5, 0, $length);
}
$dm_url_md5 = isset($MIZHI['dm_url_md5']) ? $MIZHI['dm_url_md5'] : 'off'; // 默认值为'off为url地址'
?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">

<head>
    <title><?php echo ($MIZHI['title']); ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="referrer" content="no-referrer">
    <meta name="robots" content="noarchive">
    <meta http-equiv="Access-Control-Allow-Origin" content="*" />
    <meta http-equiv="Access-Control-Allow-Credentials" content="*" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="shortcut icon" href="./favicon.ico" />
    <link rel="stylesheet" href="./css/style.css">
    <?php
    $v_host = $_SERVER['HTTP_REFERER']?:$url;
    ?>
    <script src="./js/artplayer.js"></script>
    <script src="./js/artplayer-plugin-ads.js"></script>
    <script src="./js/artplayer-plugin-danmuku.js"></script>
    <script src="./js/artplayer-tool-thumbnail.js"></script>
    <script src="./js/hls.min.js"></script>
    <script src="./js/flv.min.js"></script>
    <script src="./js/shaka-player.compiled.js"></script>
    <script src="./js/aes.js"></script>
    <script src="./js/jquery.min.js"></script>
    <script src="./js/base64.min.js"></script>
    <script src="./js/1.0.8.hls.js"></script>
    <script src="./js/shaka-player.compiled.js"></script>
    <script src="./js/layui.js"></script>
    <script src="./js/MizhiPlayerART.js"></script>
    <link href="./css/layui.css" rel="stylesheet">
    <link href="./css/art_style.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/instagram.min.css">
    <style>
        .tsmsg {
            padding: 7px 12px;
            border-radius: 50px;
        }
        .tipmsg {
            padding: 7px 12px;
            border-radius: 50px;
        }
        .tsmsg .layui-layer-content {
            font-size: 16px!important;
        }
        .art-danmuku-emitter .art-danmuku-send {
            background-color:<?php echo($MIZHI['color'].'d5');?>!important;
        }
        .art-danmuku-emitter .art-danmuku-send:hover {
            background-color:<?php echo($MIZHI['color']);?>!important;
        }
        #loading{
            background:url(<?php echo($img);?>);
        }
    </style>
</head>

<body>
    <div id="loading" align="center">
    <div id="mizhi_zdyload" align="center"></div>
    </div>    <!--<div class="art-loading"><i class="art-icon art-icon-loading"><img src="./img/ploading.gif"></i></div>-->
    <script type="text/javascript">
        var up = {
            "usernum":"<?php echo($users_online);?>",//在线人数
            "mylink":"",//域名https://www.dlidl.com
            "diyid":[0,"dlidl",1],//自定义弹幕id
            "adtime":"<?php echo($MIZHI['ads']['set']['pic']['time']);?>",//广告图弹出时长,秒
            "adpic":"<?php echo($MIZHI['ads']['set']['pic']['img']);?>",//广告图
            "adlink":"<?php echo($MIZHI['ads']['set']['pic']['link']);?>",//广告图链接
            "advodurl":"<?php echo($MIZHI['ads']['set']['vod']['url']);?>",//视频广告地址
            "advodlink":"<?php echo($MIZHI['ads']['set']['vod']['link']);?>",//视频广告链接
            "adpic_pause_state":"<?php echo($MIZHI['ads']['pause']['state']);?>",//新增的属性，用于控制广告的开关
            "adpic_pause":"<?php echo($MIZHI['ads']['pause']['pic']);?>",//暂停广告图
            "adlink_pause":"<?php echo($MIZHI['ads']['pause']['link']);?>",//暂停广告链接
            "danmuku": "<?php echo($MIZHI['danmuku']);?>",//弹幕库地址 //dmku.byteamone.cn/dmku/ ./dmku/
            "dmrule": "<?php echo($MIZHI['dmrule']);?>",//弹幕礼仪地址 ./dmku/dm_rule.html
            "id": <?php echo $dm_url_md5 === 'on' ? '"'.md5($url).'"' : '"'.$url.'"'; ?>,//弹幕ID/视频url地址
            "v_host": "<?php echo($v_host);?>",//来路URL
            "title": "<?php echo($MIZHI['title']);?>",//站点名称
            "title_url": "<?php echo($MIZHI['title_url']);?>",//站点URL
            "mylink": "<?php $originalUrl = $MIZHI['title_url']; $cleanUrl = preg_replace('#^https?://#', '', $originalUrl); $cleanUrl = rtrim($cleanUrl, '/'); echo($cleanUrl); ?>"//去除前缀域名
        };
        var config = {
            "url": "<?php echo($url);?>",//URL地址
            "thumbnailsurl": "",//进度条上预览图地址https://artplayer.org/assets/sample/thumbnails.png
            "thumbnailsnumber": 100,//预览图数量
            "thumbnailscolumn": 10,//预览图列数
            "title": "<?php echo($title);?>",//标题
            "ids": "<?php echo($ids);?>",//标题
            "pic": "<?php echo($pic);?>",//播放器右上角视频封面图
            "time": "<?php echo(time());?>", //加载时间
            "key": "<?php echo($key);?>",//KEY
            "vkey": "<?php echo($vkey);?>",//VKEY
            "next":"<?php echo($next);?>",//下一集
            "xl": "<?php echo($xl);?>",//线路
            "live": <?php echo($live);?>,//直播
            "img": "<?php echo($img);?>",//加载背景图
            "logo": "<?php echo($MIZHI['logo']);?>",//播放器右上角LOGO
            "theme":"<?php echo($MIZHI['color']);?>",//主题颜色
            "tsmsg": "<?php echo($MIZHI['tsmsg']);?>",//提示信息
            "tstime": <?php echo($MIZHI['tstime']);?>, //提示时间，单位毫秒，1000等于1秒
           /* "tsbg": "<?php echo($MIZHI['tsbg']);?>", //提示背景颜色*/
            "show_time": "<?php echo($MIZHI['showtime']);?>",//右上角时间开关
            "content": "<?php echo($_GET['content']);?>",
            "right_text": "<?php echo($MIZHI['right_text']);?>",
            "right_link":"<?php echo($MIZHI['right_link']);?>",
            "api_zyurl_domain_all":"<?php echo($MIZHI['api_zyurl_domain_all']);?>",//去插播域名
            "beisu":"<?php echo($MIZHI['beisu']);?>",//倍速按钮
            /*"background":"https://photocdn.tv.sohu.com/img/20240604/pic_org_d757d96c-b75d-4db1-9e4c-1deb79e53f54.avif",*/
            "whitelist": ['*'],//如希望全部类型的移动设备都启用播放器，设置 whitelist 为通配符 ['*'] 即可
            //因为不同的移动设备存在多种差异和限制，所以本播放器默认在移动设备上使用原生方法挂载视频，假如你想在移动设备上使用本播放器，需要手动开启白名单
            //白名单是一个数组类型，分别与 window.navigator.userAgent 进行匹配，只要其中一项匹配成功则启用播放器
            //支持字符串匹配、函数匹配、正则匹配: [(ua) => /iPhone OS 11/gi.test(ua)] 、[/iPhone OS 11/gi] 、['iPhone OS 11'] 等
        };
        var subtitle = {
            "url":"<?php echo($MIZHI['title_url']);?>",//站点URL
            "type":"",
            "style":"",
            "encoding":"",
        };
        MIZHI.player(config,up);
    </script>
</body>

</html>