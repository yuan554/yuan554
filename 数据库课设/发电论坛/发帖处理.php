<?php
session_start();
require_once 'config/database.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: 登录界面.php');
    exit();
}

// 检查是否有POST数据
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['content']) || !isset($_POST['section'])) {
    header('Location: 板块.php');
    exit();
}

// 开始事务
$conn->begin_transaction();

try {
    // 处理图片上传
    $image_path = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['post_image']['type'], $allowed_types)) {
            throw new Exception("只允许上传 JPG、PNG 或 GIF 格式的图片");
        }
        
        if ($_FILES['post_image']['size'] > $max_size) {
            throw new Exception("图片大小不能超过 5MB");
        }
        
        // 生成唯一的文件名
        $file_extension = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = 'images/' . $new_filename;
        
        // 移动上传的文件
        if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $upload_path)) {
            throw new Exception("图片上传失败");
        }
        
        $image_path = $upload_path;
    }

    // 生成帖子ID
    $sql = "SELECT MAX(CAST(SUBSTRING(pid, 2) AS UNSIGNED)) as max_id FROM post WHERE pid LIKE 'P%'";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("获取最大帖子ID失败: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $next_id = ($row['max_id'] ?? 0) + 1;
    $pid = 'P' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
    
    // 插入帖子
    $sql = "INSERT INTO post (pid, bid, pname, pcontent, pnum, rid) VALUES (?, ?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备插入帖子语句失败: " . $conn->error);
    }
    
    $stmt->bind_param("sssss", $pid, $_POST['section'], $_SESSION['username'], $_POST['content'], $pid);
    if (!$stmt->execute()) {
        throw new Exception("插入帖子失败: " . $stmt->error);
    }
    $stmt->close();
    
    // 如果有图片，创建图片记录
    if ($image_path) {
        $sql = "INSERT INTO post_images (pid, image_path) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("准备插入图片记录语句失败: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $pid, $image_path);
        if (!$stmt->execute()) {
            throw new Exception("插入图片记录失败: " . $stmt->error);
        }
        $stmt->close();
    }
    
    // 提交事务
    $conn->commit();
    
    // 重定向回板块页面
    header('Location: 板块.php?section=' . $_POST['section']);
    exit();
    
} catch (Exception $e) {
    // 回滚事务
    $conn->rollback();
    
    // 如果上传了图片但处理失败，删除已上传的图片
    if (isset($image_path) && file_exists($image_path)) {
        unlink($image_path);
    }
    
    error_log("发帖失败: " . $e->getMessage());
    die("发帖失败: " . $e->getMessage());
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