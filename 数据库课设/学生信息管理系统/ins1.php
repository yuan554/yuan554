<?php
    $sno=$_post["sno"];
    $sname=$_post["sname"];
    $ssex=$_post["ssex"];
    $sage=$_REQUEST["sage"];
    $sdept=$_REQUEST["sdept"];
    
    $conn=mysqli_connect("localhost","root","y3462qwe","yuan"); 
    if($conn->connect_error) {
        die("连接失败: ".$conn->connect_error);
    }
    
    $str="insert into student values('$sno','$sname','$ssex',$sage,'$sdept')";
    $res=$conn->query($str);   
    
    if($res) {
        echo "新增成功！";
        header("refresh:1;url=test.php");
    } else {
        echo "新增失败：" . $conn->error;
        header("refresh:1;url=ins.php");
    }
    
    $conn->close();
?>