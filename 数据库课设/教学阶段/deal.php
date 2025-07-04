<?php
header('Content-Type: text/html; charset=utf-8');

// 检查请求方法
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $uname = isset($_POST["uname"]) ? $_POST["uname"] : '';
    $upwd = isset($_POST["upasswd"]) ? $_POST["upasswd"] : '';
    $usex = isset($_POST["usex"]) ? $_POST["usex"] : '';
    $ucity = isset($_POST["ucity"]) ? $_POST["ucity"] : '';
    $aihao = isset($_POST["aihao"]) ? $_POST["aihao"] : array();

    // 输出结果
    echo "<html><head><meta charset='utf-8'></head><body>";
    echo "<h2>提交的信息如下：</h2>";
    echo "用户名: " . htmlspecialchars($uname) . "<br/>";
    echo "密码: " . str_repeat("*", strlen($upwd)) . "<br/>";
    echo "性别: " . htmlspecialchars($usex) . "<br/>";
    echo "城市: " . htmlspecialchars($ucity) . "<br/>";
    if (is_array($aihao)) {
        echo "爱好: " . htmlspecialchars(implode(", ", $aihao)) . "<br/>";
    }
    $a='你岁的分别是u对话不i的书法古斯';
    $b=250;
    echo "你是 $b ";
    echo '你是 $ b';
    
    echo "</body></html>";
} else {
    // 如果不是POST请求，返回错误信息
    header("HTTP/1.1 405 Method Not Allowed");
    echo "只允许POST方法访问此页面";
}
?>