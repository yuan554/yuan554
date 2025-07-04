<?php
session_start();
// 连接数据库
$conn = new mysqli("localhost", "root", "y3462qwe", "luntan");
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 获取并验证用户输入
$uname = trim($_POST['uname'] ?? '');
$upwd = trim($_POST['upasswords'] ?? '');
$usex = trim($_POST['usex'] ?? '');
$uemail = trim($_POST['uemail'] ?? '');

// 检查用户名是否已存在
$check_sql = "SELECT uname FROM users WHERE uname = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $uname);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header("Location: 注册界面.php?error=exists");
    exit;
}

// 密码加密
$hashed_password = password_hash($upwd, PASSWORD_DEFAULT);

try {
    // 插入新用户
    $sql = "INSERT INTO users (uname, upasswords, usex, uemail, uqx) VALUES (?, ?, ?, ?, '0')";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('数据库准备失败: ' . $conn->error);
    }
    $stmt->bind_param("ssss", $uname, $hashed_password, $usex, $uemail);
    
    if ($stmt->execute()) {
        // 注册成功，设置会话
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $uname;
        $_SESSION['uqx'] = '0';
        
        // 获取新插入的用户ID
        $_SESSION['uid'] = $conn->insert_id;
        
        header("Location: 主页.php");
        exit; // 确保重定向后立即停止脚本执行
    } else {
        throw new Exception("注册失败: " . $stmt->error);
    }
} catch (Exception $e) {
    // 记录错误日志
    error_log("注册错误: " . $e->getMessage());
    header("Location: 注册界面.php?error=system");
    exit; // 确保重定向后立即停止脚本执行
}

// 关闭数据库连接
if (isset($stmt) && $stmt) {
    $stmt->close();
}
$check_stmt->close();
$conn->close();
?> 