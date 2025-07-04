<?php
session_start();
// 检查是否登录
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: log.php");
    exit;
}

    // 连接数据库
    //$conn=mysqli_connect("localhost","root","y3462qwe","yuan");
    $conn = new mysqli("localhost", "root", "y3462qwe", "yuan");
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);//die() 函数输出一条消息，并退出当前脚本。
    }
    else
    {
        echo "连接成功";
    }
    echo "<br>";
    
    // 添加表格样式
    echo "<style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color:rgb(53, 198, 255);
            color: white;
        }
        tr:nth-child(even) {
            background-color:rgb(225, 220, 220);
        }
        tr:hover {
            background-color: #ddd;
        }
        .search-box {
            margin: 20px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .search-box input[type='text'] {
            padding: 5px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .search-box input[type='submit'] {
            padding: 5px 15px;
            background-color: rgb(53, 198, 255);
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .search-box input[type='submit']:hover {
            background-color: #4f83ba;
        }
        .change {
            margin: 20px 0;
        }
        .change a {
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            border: 1px solid #333;
            border-radius: 4px;
            margin-right: 10px;
        }
        .change a:hover {
            background-color: #333;
            color: white;
        }
        .welcome {
            margin: 20px 0;
            color: #333;
        }
    </style>";

    // 获取搜索关键词
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // 构建SQL查询
    $str = "select * from student";
    if (!empty($search)) {
        $str = "select * from student where sname like '%$search%'";
    }
    
    $res = $conn->query($str);

    // 显示操作按钮
    echo "<div class='welcome'>";
    echo "欢迎，" . $_SESSION['username'];
    echo "(" . ($_SESSION['user_qx'] == 1 ? "管理员" : "普通用户") . ")";
    echo "</div>";

    echo "<div class='change'>";
    // 根据权限显示不同按钮
    if($_SESSION['user_qx'] == 1) { // 管理员权限
        echo "<a href='ins.php'>新增学生</a>";
        echo "<a href='del.php'>删除学生</a>";
    }
    echo "</div>";

    // 添加搜索表单
    echo "<div class='search-box'>";
    echo "<form method='get' action=''>";
    echo "<input type='text' name='search' placeholder='请输入学生姓名' value='" . htmlspecialchars($search) . "'>";
    echo "<input type='submit' value='搜索'>";
    if (!empty($search)) {
        echo "&nbsp;&nbsp;<a href='test.php'>清除搜索</a>";
    }
    echo "</form>";
    echo "</div>";
    
    // 创建表格
    echo "<table>";
    echo "<tr>
            <th>学号</th>
            <th>姓名</th>
            <th>性别</th>
            <th>年龄</th>
            <th>专业</th>";
    if($_SESSION['user_qx'] == 1) { // 管理员权限
        echo "<th>操作</th>";
    }
    echo "</tr>";
     
    while($row = mysqli_fetch_row($res)) {//$row为获取数据库一行一行的数据
        echo "<tr>";
        echo "<td>" . $row[0] . "</td>";
        echo "<td>" . $row[1] . "</td>";
        echo "<td>" . $row[2] . "</td>";
        echo "<td>" . $row[3] . "</td>";
        echo "<td>" . $row[4] . "</td>";
        if($_SESSION['user_qx'] == 1) { // 管理员权限
            echo "<td><a href='del.php?sno=" . $row[0] . "' onclick='return confirm(\"确定要删除这个学生吗？\")'>删除</a></td>";
        }
        echo "</tr>";//传sno作删除条件
    }
    echo "</table>";
    
    echo "<br>";
    echo "<a href='logout.php'>退出登录</a>";
    
    $conn->close();
    //mysql_free_result($res);
    //mysql_close($conn);
?>