<?php
session_start();
// 如果已经登录，直接跳转到test.php
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: 主页.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            background-color: #54b7e1;
            margin: 0;
            padding: 20px;
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
        input[type="password"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>用户登录</h1>
        <?php
        if(isset($_GET['error'])) {
            echo '<div class="error">用户名或密码错误</div>';
        }
        ?>
        <form method="post" action="login.php">
            <div class="form-group">
                <label>用户名：</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="password" required>
            </div>
            <div class="btn-group">
                <input type="submit" value="登录">
            </div>
        </form>
    </div>
</body>
</html>