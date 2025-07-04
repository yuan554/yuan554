<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// 错误处理
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 添加调试信息
error_log("Session状态：" . print_r($_SESSION, true));
error_log("用户登录状态：" . (isset($_SESSION['logged_in']) ? '已登录' : '未登录'));
error_log("用户权限：" . (isset($_SESSION['uqx']) ? $_SESSION['uqx'] : '未设置'));

// 定义板块信息
$sections = [
    'food' => ['name' => '美食天地', 'icon' => 'fa-utensils'],
    'anime' => ['name' => '动漫世界', 'icon' => 'fa-tv'],
    'news' => ['name' => '新闻资讯', 'icon' => 'fa-newspaper'],
    'military' => ['name' => '军事天地', 'icon' => 'fa-shield-alt']
];

// 获取当前板块
$section = isset($_GET['section']) ? $_GET['section'] : 'food';
if (!array_key_exists($section, $sections)) {
    $section = 'food';
}
$section_name = $sections[$section]['name'];
$section_icon = $sections[$section]['icon'];

// 获取版主信息
$moderator = getModeratorInfo($section);

// 获取帖子列表
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$posts = getPosts($section, $search);

// 获取热门帖子
$hot_posts = getHotPosts($section);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $section_name; ?> - 发电论坛</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #54b7e1;
            --secondary-color: #4f83ba;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --background-color: #f5f5f5;
            --text-color: #333;
            --border-radius: 10px;
            --box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Microsoft YaHei', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .navbar {
            background-color: white;
            box-shadow: var(--box-shadow);
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .section-nav {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .section-nav .nav-link {
            color: var(--text-color);
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .section-nav .nav-link:hover,
        .section-nav .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .section-nav .nav-link i {
            margin-right: 10px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }

        .post-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }

        .post-item:hover {
            background-color: #f8f9fa;
        }

        .post-item.sticky {
            background-color: #f8f9fa;
            border-left: 4px solid var(--success-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
        }

        .search-box .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }

        .reply-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
        }

        .moderator-info {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .hot-posts .list-group-item {
            border: none;
            padding: 10px 0;
        }

        .hot-posts .list-group-item:not(:last-child) {
            border-bottom: 1px solid #eee;
        }

        .image-upload {
            margin-top: 10px;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
        .post-image {
            max-width: 120px;
            max-height: 120px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="主页.php">
                <i class="fas fa-bolt"></i> 发电论坛
            </a>
            <div class="d-flex align-items-center">
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <span class="me-3">欢迎，<?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="退出登录.php" class="btn btn-outline-danger">退出</a>
                <?php else: ?>
                    <a href="登录界面.php" class="btn btn-outline-primary">登录</a>
                <?php endif; ?> 
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- 左侧导航 -->
            <div class="col-md-3">
                <div class="section-nav">
                    <?php foreach ($sections as $key => $info): ?>
                        <a href="?section=<?php echo $key; ?>" 
                           class="nav-link d-flex align-items-center <?php echo $key === $section ? 'active' : ''; ?>">
                            <i class="fas <?php echo $info['icon']; ?>"></i>
                            <?php echo $info['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- 版主信息 -->
                <div class="moderator-info mt-3">
                    <h5><i class="fas fa-user-shield"></i> 版主信息</h5>
                    <?php if ($moderator): ?>
                        <p class="mb-1"><i class="fas fa-user"></i> <?php echo htmlspecialchars($moderator['username']); ?></p>
                        <p class="mb-0"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($moderator['email']); ?></p>
                    <?php else: ?>
                        <p class="text-muted">暂无版主信息</p>
                    <?php endif; ?>
                </div>

                <!-- 发帖表单 -->
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <div class="new-post-form">
                    <form action="发帖处理.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="<?php echo $section; ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" placeholder="写下你的想法..." required></textarea>
                        </div>
                        <div class="image-upload mb-2">
                            <label for="post-image" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-image"></i> 添加图片
                            </label>
                            <input type="file" id="post-image" name="post_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <span id="image-name" class="ms-2 small text-muted"></span>
                        </div>
                        <div id="image-preview-container" class="mb-2" style="display: none;">
                            <img id="image-preview" class="image-preview" src="" alt="预览图片">
                            <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removeImage()">
                                <i class="fas fa-times"></i> 移除图片
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">发布帖子</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- 右侧内容 -->
            <div class="col-md-9">
                <!-- 搜索框 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form action="" method="GET" class="search-box">
                            <input type="hidden" name="section" value="<?php echo $section; ?>">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="搜索帖子内容..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 热门帖子 -->
                <?php if (empty($search)): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-fire"></i> 热度排行榜</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($hot_posts)): ?>
                            <?php foreach ($hot_posts as $post): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="hot-post-content">
                                            <?php echo htmlspecialchars($post['pcontent']); ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-comments"></i> <?php echo $post['reply_count']; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                暂无热门帖子
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 帖子列表 -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas <?php echo $section_icon; ?>"></i> 
                            <?php echo $section_name; ?>
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <div class="post-item <?php echo $post['is_sticky'] ? 'sticky' : ''; ?>">
                                    <div class="post-content mb-2">
                                        <?php echo htmlspecialchars($post['pcontent']); ?>
                                    </div>
                                    <?php
                                    // 获取帖子图片
                                    $sql = "SELECT image_path FROM post_images WHERE pid = ?";
                                    $stmt = $conn->prepare($sql);
                                    if ($stmt) {
                                        $stmt->bind_param("s", $post['pid']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($image = $result->fetch_assoc()) {
                                            echo '<div class="text-center"><img src="' . htmlspecialchars($image['image_path']) . '" class="post-image mt-2 previewable-image" style="max-width:120px; max-height:120px; margin-bottom:10px;cursor:pointer;" alt="帖子图片"></div>';
                                        }
                                        $stmt->close();
                                    }
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="post-meta text-muted small">
                                            <span class="me-3">
                                                <i class="fas fa-user"></i> 
                                                <?php echo htmlspecialchars($post['pname']); ?>
                                            </span>
                                            <span class="me-3">
                                                <i class="fas fa-comments"></i> 
                                                <?php echo $post['reply_count']; ?>
                                            </span>
                                            <span class="me-3">
                                                <i class="fas fa-clock"></i> 发帖时间：
                                                <?php echo isset($post['created_at']) ? htmlspecialchars($post['created_at']) : (isset($post['updated_at']) ? htmlspecialchars($post['updated_at']) : '无'); ?>
                                            </span>
                                            <span class="me-3">
                                                <i class="fas fa-history"></i> 最后回复：
                                                <?php echo isset($post['last_reply_time']) ? htmlspecialchars($post['last_reply_time']) : '无'; ?>
                                            </span>
                                            <?php if ($post['is_sticky']): ?>
                                                <span>
                                                    <i class="fas fa-thumbtack"></i> 置顶
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-actions">
                                            <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="toggleReplyForm('<?php echo $post['pid']; ?>')">
                                                    回复
                                                </button>
                                                <?php if(isset($_SESSION['uqx']) && $_SESSION['uqx'] === '1'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="deletePost('<?php echo $post['pid']; ?>')">
                                                        删除
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="toggleSticky('<?php echo $post['pid']; ?>')">
                                                        <?php echo $post['is_sticky'] ? '取消置顶' : '置顶'; ?>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- 回复列表 -->
                                    <?php
                                    $replies = getReplies($post['pid']);
                                    if (!empty($replies)):
                                    ?>
                                        <div class="replies mt-2">
                                            <?php foreach ($replies as $reply): ?>
                                                <div class="reply-item p-2 bg-light rounded mb-1">
                                                    <div class="reply-content">
                                                        <?php echo htmlspecialchars($reply['rcontent']); ?>
                                                    </div>
                                                    <div class="reply-meta text-muted small">
                                                        <i class="fas fa-user"></i> 
                                                        <?php echo htmlspecialchars($reply['uid']); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- 回复表单 -->
                                    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                                        <div id="reply-form-<?php echo $post['pid']; ?>" class="reply-form">
                                            <form method="post" action="回复处理.php">
                                                <input type="hidden" name="pid" value="<?php echo $post['pid']; ?>">
                                                <input type="hidden" name="section" value="<?php echo $section; ?>">
                                                <input type="hidden" name="uid" value="<?php echo $_SESSION['username']; ?>">
                                                <div class="mb-2">
                                                    <textarea class="form-control" name="content" 
                                                              placeholder="写下你的回复..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">提交回复</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                暂无帖子
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="image-modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);justify-content:center;align-items:center;">
        <img id="modal-img" src="" style="max-width:90vw;max-height:90vh;border-radius:10px;box-shadow:0 2px 10px #000;">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleReplyForm(postId) {
            const form = document.getElementById('reply-form-' + postId);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        }

        function deletePost(postId) {
            if(confirm('确定要删除这个帖子吗？')) {
                window.location.href = '删除帖子.php?pid=' + postId;
            }
        }

        function toggleSticky(postId) {
            window.location.href = '置顶帖子.php?pid=' + postId;
        }

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const imageName = document.getElementById('image-name');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
                imageName.textContent = input.files[0].name;
            }
        }

        function removeImage() {
            const input = document.getElementById('post-image');
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const imageName = document.getElementById('image-name');
            
            input.value = '';
            preview.src = '';
            container.style.display = 'none';
            imageName.textContent = '';
        }

        // 页面加载完成后初始化
        document.addEventListener('DOMContentLoaded', function() {
            const replyForms = document.querySelectorAll('.reply-form');
            replyForms.forEach(form => {
                form.style.display = 'none';
            });
        });

        // 点击图片放大预览
        const modal = document.getElementById('image-modal');
        const modalImg = document.getElementById('modal-img');
        document.addEventListener('click', function(e) {
            if(e.target.classList.contains('previewable-image')) {
                modalImg.src = e.target.src;
                modal.style.display = 'flex';
            }
            // 点击遮罩关闭
            if(e.target === modal) {
                modal.style.display = 'none';
                modalImg.src = '';
            }
        });
    </script>
</body>
</html>
<?php
// 在页面最后关闭数据库连接
$conn->close();
?>