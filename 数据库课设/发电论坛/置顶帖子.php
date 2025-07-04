<?php
session_start();

// 检查用户是否登录且是管理员
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['uqx']) || $_SESSION['uqx'] !== '1') {
    header('Location: 登录界面.php');
    exit();
}

// 检查是否有帖子ID
if (!isset($_GET['pid'])) {
    header('Location: 板块.php');
    exit();
}

// 连接数据库
$conn = new mysqli("localhost", "root", "y3462qwe", "luntan");
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

try {
    // 获取帖子所属板块和当前置顶状态
    $sql = "SELECT bid, is_sticky FROM post WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备查询语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("s", $_GET['pid']);
    if (!$stmt->execute()) {
        throw new Exception("获取帖子信息失败: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    if (!$post) {
        throw new Exception("帖子不存在");
    }

    // 切换置顶状态
    $new_sticky_status = $post['is_sticky'] ? 0 : 1;
    $sql = "UPDATE post SET is_sticky = ? WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备更新语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("is", $new_sticky_status, $_GET['pid']);
    if (!$stmt->execute()) {
        throw new Exception("更新帖子状态失败: " . $stmt->error);
    }
    
    // 重定向回板块页面
    header('Location: 板块.php?section=' . $post['bid']);
    exit();
    
} catch (Exception $e) {
    echo "操作失败: " . $e->getMessage();
}

// 关闭连接
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?> 