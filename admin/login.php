<?php
// 设置浏览器缓存控制
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: max-age=0, must-revalidate");

// 引入配置文件
require_once './config.php';

// 禁用错误报告
error_reporting(0);

// 定义管理员密码
$adminPass = $yzm['adminpass'];

// 使用更安全的密码哈希
$hashedAdminPass = password_hash($adminPass, PASSWORD_DEFAULT);

// 如果没有设置 pass cookie，则设置并刷新页面
if (empty($_COOKIE["pass"])) {
    setcookie("pass", $hashedAdminPass, time() + 3600 * 24 * 7, "/");
    echo "<script>
        setTimeout(function(){ window.location.reload(); }, 100);
    </script>";
    exit; // 退出脚本以防止进一步执行
}

// 检查 zt cookie 和密码是否正确
$errorMessage = ''; // 初始化错误消息
if ($_COOKIE["zt"] !== "ok") {
    if (isset($_POST['pass'])) {
        if (password_verify($_POST['pass'], $hashedAdminPass)) {
            setcookie("zt", "ok", time() + 86400, "/");
            header("Location: index.php"); // 重定向到管理后台
            exit;
        } else {
            $errorMessage = '密码错误，请重新输入！';
        }
    }
    // 将错误消息传递给视图
    include 'login_form.html'; // 显示登录表单
    exit;
} else {
    // 用户已登录，重定向到管理后台
    header("Location: index.php");
    exit;
}
?>