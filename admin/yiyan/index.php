<?php
// 读取 JSON 文件
$file =  __DIR__ . '/sentences/a.json';
$quotes = file_get_contents($file);
$quotes = json_decode($quotes, true);

// 获取 type 参数
$type = isset($_GET['type']) ? $_GET['type'] : 'json'; // 默认为 'json'

// 随机选择一条记录
$randomIndex = array_rand($quotes);
$randomQuote = $quotes[$randomIndex];

// 构建一个只包含所需字段的新数组
$selectedFields = [
    'code' => 200,
    'msg' => '随机hitokoto一言',
    'full' => $randomQuote["hitokoto"].'—'.$randomQuote["creator"].'《'.$randomQuote["from"].'》', 
    'data' => [
    "author" => $randomQuote["creator"],  
    "works" => '《'.$randomQuote["from"].'》',
    "content" => $randomQuote["hitokoto"],
    'by' => '觅知博客www.98dou.cn提供技术支持',
    '文本调用' => 'https://api.98dou.cn/api/yiyan/hitokoto?type=text',
    
]
];

// 根据 type 参数决定输出格式
if ($type === 'text') {
    // 输出纯文本格式
    header('Content-Type: text/plain; charset=utf-8'); // 设置 Content-Type 为纯文本
    //echo $selectedFields['content'];
    /*echo $randomQuote["hitokoto"].'—'.$randomQuote["creator"].'《'.$randomQuote["from"].'》';*/
    echo $randomQuote["hitokoto"];
} else {
    // 设置 HTTP 响应头
    header('Content-Type: application/json; charset=utf-8');

    // 使用 JSON_UNESCAPED_UNICODE 选项确保中文字符被正确编码
    echo json_encode($selectedFields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
?>