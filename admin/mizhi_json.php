<?php
error_reporting(0);
require $_SERVER["DOCUMENT_ROOT"] . "/admin/data.php"; // 获取播放器配置项
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate'); // 禁止页面被缓存

// 定义允许的域名列表，使用管道符号分隔
$allowed_domains_str = $MIZHI['fdhost'];
$allowed_domains = explode('|', $allowed_domains_str);

// 检查是否为AJAX请求
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    // 检查Referer是否来自允许的域名
    if (empty($_SERVER['HTTP_REFERER']) || !check_referer($_SERVER['HTTP_REFERER'], $allowed_domains)) {
        header("Location: " . $MIZHI['title_url']);
        exit;
    }
}

// 定义API接口列表
$api_urls = [
    $MIZHI['api_ur_1'],
    $MIZHI['api_xl_url'],
    $MIZHI['api_ur_3'],
    $MIZHI['api_ur_4'],
    $MIZHI['api_ur_5'],
];

// 定义特定域名与专用API接口的映射关系，使用管道符分隔多个域名
$special_domain_api_map = [
    $MIZHI['api_zyurl_domain_1'] => $MIZHI['api_zyurl_1'],
    $MIZHI['api_zyurl_domain_2'] => $MIZHI['api_zyurl_2'],
    $MIZHI['api_zyurl_domain_3'] => $MIZHI['api_zyurl_3'],
    $MIZHI['api_zyurl_domain_4'] => $MIZHI['api_zyurl_4'],
    $MIZHI['api_zyurl_domain_5'] => $MIZHI['api_zyurl_5'],
];

$gt_live = $MIZHI['live_api']; // 直播的转换接口
isset($_POST['url']) ? $url = htmlspecialchars(urldecode(trim($_POST['url']))):die('URL不能为空！');

// 定义支持的视频扩展名
$videoExtensions = [".m3u8", ".mp4", ".php", ".flv", ".avi", ".mov", ".wmv", ".webm", ".mkv", ".ts", ".mpg", ".mpeg", ".ogv", ".3gp", ".3g2"];

// 检查URL是否为直链
$isValidDirectLink = false;
$forceApiParse = false; // 新增变量用于标记是否强制使用API解析
$specialApiUrl = null; // 存储特定域名对应的API接口

// 检查是否是特定域名下的.m3u8文件
$allowed_special_domains_str = $MIZHI['api_zyurl_domain_all']; // 特定域名列表，使用管道符号分隔
$allowed_special_domains = explode('|', $allowed_special_domains_str);

foreach ($allowed_special_domains as $domain) {
    if (stripos($url, $domain) !== false && stristr($url, '.m3u8')) {
        $forceApiParse = true; // 标记需要强制使用API解析
        foreach ($special_domain_api_map as $domains => $api_url) {
            $domains_list = explode('|', $domains);
            foreach ($domains_list as $d) {
                if (stripos($url, $d) !== false) {
                    $specialApiUrl = $api_url; // 获取对应域名的专用API接口
                    break 2; // 跳出最外层循环
                }
            }
        }
        break;
    }
}

foreach ($videoExtensions as $ext) {
    if (stristr($url, $ext) && !$forceApiParse) { // 添加!$forceApiParse条件
        $isValidDirectLink = true;
        break;
    }
}

if ($isValidDirectLink || (stristr($url, '.jpg') && !$forceApiParse)) { // 包括图片文件，同时添加!$forceApiParse条件
    // 直链的格式是指直接指向媒体文件的URL，例如：
    // - 视频文件: http://example.com/video.mp4, http://example.com/video.avi, http://example.com/video.mov, ...
    // - HLS流媒体文件: http://example.com/video.m3u8
    // - 图片文件: http://example.com/image.jpg
    // - PHP脚本生成的媒体文件: http://example.com/get_video.php
    // - Flash视频文件: http://example.com/video.flv
    // 支持的其他视频格式包括: .wmv, .webm, .mkv, .ts, .mpg, .mpeg, .ogv, .3gp, .3g2

    $handled_url = handle_redirects($url);
    $data = array(
        'code' => 200,
        'msg' => $MIZHI['title'],
        'player' => 'MZPlayer',
        'qq' => 2319281411,
        'url' => $handled_url['url'], // 处理重定向
        'json_url' => $url, // 原始的未重定向的URL
        'is_hls' => strpos($url, '.m3u8') !== false, // 新增字段，标识是否为HLS链接
        'cdx' => $handled_url['redirected'] ? 'yes' : 'no' // 新增字段，标识是否重定向
    );
    die(json_encode($data));
} else {
    if ($forceApiParse && $specialApiUrl) {
        // 如果是特定域名下的.m3u8文件，使用对应的专用API接口
        $result = parse_url_with_api($specialApiUrl . $url, 5); // 设置每个接口的最大解析时间为5秒
        if ($result !== false) {
            die($result);
        }
        die("专用接口解析失败或无效地址，请检查输入的URL是否正确！");
    } else {
        foreach ($api_urls as $api_url) {
            if (empty($api_url)) {
                continue; // 如果接口地址为空，跳过此接口
            }
            $result = parse_url_with_api($api_url . $url, 5); // 设置每个接口的最大解析时间为5秒
            if ($result !== false) {
                die($result);
            }
        }
        die("所有接口均解析失败或无效地址，请检查输入的URL是否正确！");
    }
}

function parse_url_with_api($url, $timeout = 5) {
    $response = get_url($url, $timeout);
    $data = json_decode($response, true);

    // 检查返回的数据中是否有 url 或 URL 字段，并且不为空
    if (isset($data['url']) && !empty($data['url'])) {
        $handled_url = handle_redirects($data['url']);
        return json($handled_url['url'], $handled_url['redirected']);
    } elseif (isset($data['URL']) && !empty($data['URL'])) {
        $handled_url = handle_redirects($data['URL']);
        return json($handled_url['url'], $handled_url['redirected']);
    }

    // 检查返回的数据中是否有明显的失败状态码
    if (isset($data['code']) && in_array($data['code'], [400, 100]) && isset($data['success']) && $data['success'] === false) {
        return false; // 明显失败状态码，解析失败
    }

    // 其他情况也视为解析失败
    return false;
}

function json($url, $redirected) {
    global $MIZHI;

    // 检查 url 是否为空
    if (empty($url) || trim($url) === "" || trim($url) === "://") {
        return json_encode([
            'code' => 400,
            'msg' => '【无效地址解析失败】或【URL=为空】',
            'success' => '0'
        ]);
    }

    $arr = [
        "code" => "200", // 默认成功状态码
        "success" => "1", // 默认成功标志
        "parser" => $MIZHI['title'],
        'video_url' => $_POST['url'],
        'md5url' => md5($url),
        "url" => $url, // 处理重定向
        "json_url" => $url, // 原始的未重定向的URL
        "player" => "MZPlayer",
        "is_hls" => strpos($url, '.m3u8') !== false, // 新增字段，标识是否为HLS链接
        "cdx" => $redirected ? 'yes' : 'no' // 新增字段，标识是否重定向
    ];

    // 根据URL类型添加"type"字段
    if (strpos($url, ".flv")) {
        $arr["type"] = 'flv';
    } elseif (strpos($url, ".mp4")) {
        $arr["type"] = 'mp4';
    } elseif (strpos($url, ".m3u8")) {
        $arr["type"] = 'hls';
    }

    return json_encode($arr);
}

function get_url($url, $timeout = 10) {
    $headerArray = ["Content-type: application/json;"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 设置请求超时时间
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// 检查Referer是否来自允许的域名
function check_referer($referer, $allowed_domains) {
    foreach ($allowed_domains as $domain) {
        if (stripos($referer, $domain) !== false) {
            return true;
        }
    }
    return false;
}

// 处理重定向，超时10秒后返回原始地址
function handle_redirects($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // 不自动跟随重定向
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // 最多重定向次数（虽然这里设置了不自动跟随）
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置最大请求时间为10秒
    curl_setopt($ch, CURLOPT_NOBODY, true); // 只获取头部信息，提高效率

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

    if ($response !== false) {
        // 获取响应头
        $headers = [];
        $header_text = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        foreach (explode("\r\n", $header_text) as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
        }

        // 检查是否存在Location头
        if ($httpCode == 302 && isset($headers['location'])) {
            $redirect_url = $headers['location'];
            if ($redirect_url != $url) { // 确保重定向地址不是原始地址
                $final_url = $redirect_url;
            }
        }
    }

    if (curl_errno($ch) === CURLE_OPERATION_TIMEDOUT || $final_url === $url) {
        $final_url = $url; // 发生超时或没有实际重定向，返回原始URL
    }

    curl_close($ch);
    return ['url' => $final_url, 'redirected' => $final_url !== $url]; // 返回是否重定向的信息
}