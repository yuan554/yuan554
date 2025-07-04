<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>发电论坛</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #54b7e1;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .sections {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .section-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .section-card h2 {
            color: #333;
            margin-top: 0;
            border-bottom: 2px solid #54b7e1;
            padding-bottom: 10px;
        }
        .section-card p {
            color: #666;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 8px 20px;
            background-color: #54b7e1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #4f83ba;
        }
        .user-info {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .user-info a {
            color: #54b7e1;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }
        .user-info a:hover {
            background-color: #f0f0f0;
        }
        .user-info .divider {
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>发电论坛</h1>
    </div>

    <?php
    session_start();
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        echo '<div class="user-info">';
        echo '欢迎，' . htmlspecialchars($_SESSION['username']);
        echo '<span class="divider">|</span>';
        echo '<a href="退出登录.php">退出</a>';
        echo '</div>';
    } 
    else {
        echo '<div class="user-info">';
        echo '<a href="登录界面.php">登录</a>';
        echo '<span class="divider">|</span>';
        echo '<a href="注册界面.php">注册</a>';
        echo '</div>';
    }
    ?>

    <div class="container">
        <div class="sections">
            <div class="section-card">
                <h2>美食天地</h2>
                <p>分享美食心得，交流烹饪技巧</p>
                <a href="板块.php?section=food" class="btn">进入板块</a>
            </div>
            <div class="section-card">
                <h2>动漫世界</h2>
                <p>动漫资讯、讨论与分享</p>
                <a href="板块.php?section=anime" class="btn">进入板块</a>
            </div>
            <div class="section-card">
                <h2>新闻资讯</h2>
                <p>最新时事新闻与热点讨论</p>
                <a href="板块.php?section=news" class="btn">进入板块</a>
            </div>
            <div class="section-card">
                <h2>军事天地</h2>
                <p>军事新闻、装备与技术讨论</p>
                <a href="板块.php?section=military" class="btn">进入板块</a>
            </div>
        </div>
    </div>
</body>
</html>
