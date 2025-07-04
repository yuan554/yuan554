<?php
// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'y3462qwe');
define('DB_NAME', 'luntan');

// 创建数据库连接
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // 检查连接
    if ($conn->connect_error) {
        die("数据库连接失败: " . $conn->connect_error);
    }
    
    // 设置字符集
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// 获取数据库连接
$conn = getDBConnection();
?> 