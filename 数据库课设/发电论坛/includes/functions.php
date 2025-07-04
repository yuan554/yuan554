<?php
// 获取版主信息
function getModeratorInfo($section) {
    global $conn;
    try {
        $sql = "SELECT m.moname as username, m.moeimail as email 
                FROM moderator m 
                JOIN board b ON m.moid = b.moid 
                WHERE b.bid = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("准备语句失败: " . $conn->error);
        }
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result = $stmt->get_result();
        $moderator = $result->fetch_assoc();
        $stmt->close();
        return $moderator;
    } 
    catch (Exception $e) {
        error_log("获取版主信息错误: " . $e->getMessage());
        return null;
    }
}

// 获取帖子列表
function getPosts($section, $search = '') {
    global $conn;
    try {
        $sql = "SELECT p.*, COUNT(r.rid) as reply_count, u.uqx 
                FROM post p 
                LEFT JOIN reply r ON p.pid = r.pid 
                LEFT JOIN users u ON p.pname = u.uname 
                WHERE p.bid = ?";
        
        if (!empty($search)) {
            $sql .= " AND p.pcontent LIKE ?";
        }
        
        $sql .= " GROUP BY p.pid, p.bid, p.pname, p.pcontent, p.pnum, p.rid, p.updated_at, p.last_reply_time, p.is_sticky, u.uqx 
                 ORDER BY p.is_sticky DESC, p.updated_at DESC";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("准备语句失败: " . $conn->error);
        }
        
        if (!empty($search)) {
            $search_param = "%" . $search . "%";
            $stmt->bind_param("ss", $section, $search_param);
        } else {
            $stmt->bind_param("s", $section);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $posts;
    } catch (Exception $e) {
        error_log("获取帖子列表错误: " . $e->getMessage());
        return [];
    }
}

// 获取回复列表
function getReplies($pid) {
    global $conn;
    try {
        $sql = "SELECT * FROM reply WHERE pid = ? ORDER BY rid ASC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("准备语句失败: " . $conn->error);
        }
        $stmt->bind_param("s", $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        $replies = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $replies;
    } catch (Exception $e) {
        error_log("获取回复列表错误: " . $e->getMessage());
        return [];
    }
}

// 获取热门帖子
function getHotPosts($section) {
    global $conn;
    try {
        $sql = "SELECT p.*, COUNT(r.rid) as reply_count 
                FROM post p 
                LEFT JOIN reply r ON p.pid = r.pid 
                WHERE p.bid = ? 
                GROUP BY p.pid 
                ORDER BY reply_count DESC 
                LIMIT 10";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("准备语句失败: " . $conn->error);
        }
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $posts;
    } catch (Exception $e) {
        error_log("获取热门帖子错误: " . $e->getMessage());
        return [];
    }
}

// 安全检查函数
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// 检查用户权限
function checkUserPermission($requiredPermission = 'user') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    if ($requiredPermission === 'admin') {
        return isset($_SESSION['uqx']) && $_SESSION['uqx'] === '1';
    }
    
    return true;
}
?> 