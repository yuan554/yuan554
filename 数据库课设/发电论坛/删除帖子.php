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

// 开始事务
$conn->begin_transaction();

try {
    // 获取帖子所属板块
    $sql = "SELECT bid FROM post WHERE pid = ?";
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

    // 删除帖子的所有回复
    $sql = "DELETE FROM reply WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备删除回复语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("s", $_GET['pid']);
    if (!$stmt->execute()) {
        throw new Exception("删除回复失败: " . $stmt->error);
    }

    // 删除帖子
    $sql = "DELETE FROM post WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备删除帖子语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("s", $_GET['pid']);
    if (!$stmt->execute()) {
        throw new Exception("删除帖子失败: " . $stmt->error);
    }
    
    // 提交事务
    $conn->commit();
    
    // 重定向回板块页面
    header('Location: 板块.php?section=' . $post['bid']);
    exit();
    
} catch (Exception $e) {
    // 回滚事务
    $conn->rollback();
    echo "删除失败: " . $e->getMessage();
}

// 关闭连接
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?> 