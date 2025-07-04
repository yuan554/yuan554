<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>新增学生信息</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            background-color: #54b7e1;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
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
            display: inline-block;
            width: 80px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"] {
            width: 200px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .radio-group {
            display: inline-block;
        }
        .radio-group label {
            width: auto;
            margin-right: 15px;
            font-weight: normal;
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
        }
        input[type="submit"],
        input[type="reset"] {
            padding: 10px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input[type="submit"] {
            background-color: rgb(53, 198, 255);
            color: white;
        }
        input[type="reset"] {
            background-color: #f44336;
            color: white;
        }
        input[type="submit"]:hover {
            background-color: #4f83ba;
        }
        input[type="reset"]:hover {
            background-color: #bf5a53;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #333;
            border-radius: 4px;
        }
        .back-link a:hover {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>新增学生信息</h1>
        <form method="post" action="ins1.php">
            <div class="form-group">
                <label>学号：</label>
                <input type="text" name="sno" required>
            </div>
            <div class="form-group">
                <label>姓名：</label>
                <input type="text" name="sname" required>
            </div>
            <div class="form-group">
                <label>性别：</label>
                <div class="radio-group">
                    <label><input type="radio" name="ssex" value="男" required> 男</label>
                    <label><input type="radio" name="ssex" value="女"> 女</label>
                </div>
            </div>
            <div class="form-group">
                <label>年龄：</label>
                <input type="text" name="sage" required>
            </div>
            <div class="form-group">
                <label>专业：</label>
                <input type="text" name="sdept" required>
            </div>
            <div class="btn-group">
                <input type="submit" value="提交">
                <input type="reset" value="重置">
            </div>
        </form>
        <div class="back-link">
            <a href="test.php">返回学生列表</a>
        </div>
    </div>
</body>
</html>