<?php
session_start();
require_once 'config/database.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    error_log("用户未登录");
    header('Location: 登录界面.php');
    exit();
}

// 检查是否有POST数据
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['content']) || !isset($_POST['pid'])) {
    error_log("回复提交失败：缺少必要参数");
    error_log("POST数据：" . print_r($_POST, true));
    header('Location: 板块.php');
    exit();
}

// 开始事务
$conn->begin_transaction();

try {
    // 检查用户权限并获取用户ID
    $sql = "SELECT uid FROM users WHERE uname = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备用户查询语句失败: " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        throw new Exception("用户不存在");
    }

    // 获取帖子所属板块
    $sql = "SELECT bid FROM post WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备帖子查询语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("s", $_POST['pid']);
    if (!$stmt->execute()) {
        throw new Exception("获取帖子信息失败: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
    
    if (!$post) {
        throw new Exception("帖子不存在");
    }

    // 生成回复ID
    $sql = "SELECT MAX(CAST(SUBSTRING(rid, 2) AS UNSIGNED)) as max_id FROM reply WHERE rid LIKE 'R%'";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("获取最大回复ID失败: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $next_id = ($row['max_id'] ?? 0) + 1;
    $rid = 'R' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
    
    // 插入回复 - 使用用户ID而不是用户名
    $sql = "INSERT INTO reply (rid, pid, uid, rcontent) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备插入回复语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $rid, $_POST['pid'], $user['uid'], $_POST['content']);
    if (!$stmt->execute()) {
        throw new Exception("插入回复失败: " . $stmt->error);
    }
    $stmt->close();
    
    // 更新帖子回复数和最后回复时间
    $sql = "UPDATE post SET pnum = pnum + 1, last_reply_time = NOW() WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备更新帖子语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("s", $_POST['pid']);
    if (!$stmt->execute()) {
        throw new Exception("更新帖子失败: " . $stmt->error);
    }
    $stmt->close();
    
    // 提交事务
    $conn->commit();
    
    // 重定向回板块页面
    header('Location: 板块.php?section=' . $post['bid']);
    exit();
    
} catch (Exception $e) {
    // 回滚事务
    $conn->rollback();
    error_log("回复失败: " . $e->getMessage());
    die("回复失败: " . $e->getMessage());
} finally {
    // 确保关闭连接
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?> 