<?php
// 引入配置文件
require_once 'adminpass.php';

// 获取POST参数
$oldPassword = $_POST['old_password'];
$newPassword = $_POST['new_password'];

// 检查旧密码是否正确
if ($oldPassword === $adminpass) {
    // 检查新密码长度
    if (strlen($newPassword) < 5) {
        echo json_encode(['success' => false, 'message' => '密码必须至少5位字符']);
        exit;
    }

    // 更新密码
    file_put_contents('adminpass.php', "<?php\n\$adminpass = '" . addslashes($newPassword) . "';\n?>");
    echo json_encode(['success' => true, 'message' => '密码修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '旧密码错误']);
}
?>