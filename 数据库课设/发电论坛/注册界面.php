<?php
session_start();
// 如果已经登录，直接跳转到主页
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: 主页.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            background-color: #54b7e1;
            margin: 0;
            padding: 0;
        }
        .top-nav {
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background: rgb(241, 241, 241);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            border-bottom: 2px solid rgb(53, 198, 255);
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
        }
        input[type="submit"] {
            padding: 10px 30px;
            background-color: rgb(53, 198, 255);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #4f83ba;
        }
        .error {
            color: #f44336;
            text-align: center;
            margin-bottom: 20px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: rgb(53, 198, 255);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- 添加顶部导航栏 -->
    <div class="top-nav">
        <div class="logo">发电论坛</div>
    </div>

    <div class="container">
        <h1>用户注册</h1>
        <?php
        if(isset($_GET['error'])) {
            if($_GET['error'] === 'exists') {
                echo '<div class="error">用户名已存在</div>';
            } else if($_GET['error'] === 'system') {
                echo '<div class="error">系统错误，请稍后重试</div>';
            }
        }
        ?>
        <form method="post" action="注册动作.php">
            <div class="form-group">
                <label>用户名：</label>
                <input type="text" name="uname" required>
            </div>
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="upasswords" required>
            </div>
            <div class="form-group">
                <label>性别：</label>
                <select name="usex" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">请选择性别</option>
                    <option value="男">男</option>
                    <option value="女">女</option>
                </select>
            </div>
            <div class="form-group">
                <label>邮箱：</label>
                <input type="email" name="uemail" required>
            </div>
            <div class="btn-group">
                <input type="submit" value="注册">
            </div>
        </form>
        <div class="login-link">
            <p style="color: #666;">已有账号？ <a href="登录界面.php">立即登录</a></p>
        </div>
    </div>
</body>
</html>