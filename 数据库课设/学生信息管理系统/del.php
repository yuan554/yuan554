<?php
    $sno = $_GET["sno"];
    
    $conn = mysqli_connect("localhost", "root", "y3462qwe", "yuan");
    if($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    $str = "delete from student where sno='$sno'";
    $res = $conn->query($str);
    
    if($res) {
        echo "<script>alert('删除成功！'); window.location.href='test.php';</script>";
    } else {
        echo "<script>alert('删除失败：" . $conn->error . "'); window.location.href='test.php';</script>";
    }
    
    $conn->close();
?>
