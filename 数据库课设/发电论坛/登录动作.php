<?php
session_start();

// 获取表单数据
$uname = $_POST['uname'];
$upwd = $_POST['upasswords'];

// 连接数据库
$conn = new mysqli("localhost", "root", "y3462qwe", "luntan");
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 查询用户
$sql = "SELECT * FROM users WHERE uname = ? AND upasswords = ?";
$stmt = $conn->prepare($sql);//预处理
$stmt->bind_param("ss", $uname, $upwd);//绑定，ss表示变量都是字符型
$stmt->execute();//执行
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 获取用户信息
    $user = $result->fetch_assoc();
    // 登录成功
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $user['uname'];
    $_SESSION['uid'] = $user['uid'];
    $_SESSION['uqx'] = $user['uqx'];
    header("Location: 主页.php");
} else {
    // 登录失败
    header("Location: 登录界面.php?error=1");
}

$stmt->close();
$conn->close();
?> 