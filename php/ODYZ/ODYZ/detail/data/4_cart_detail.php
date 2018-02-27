<?php
/**接收客户端提交的用户名，向客户端输出该用户的购物车详情**/
header('Content-Type: application/json;charset=UTF-8');

@$uname = $_REQUEST['uname'];
@$pageNum=$_REQUEST['pageNum'];
$pager=[
'recordCount'=>0,
'pageSize'=>4,
'pageCount'=>0,
'pageNum'=>intval($pageNum),
'data'=>null
];



//连接数据库
include('0_config.php'); //包含指定文件的内容在当前位置
$conn = mysqli_connect($db_url, $db_user, $db_pwd, $db_name, $db_port);

//SQL1: 设置编码方式
$sql = "SET NAMES UTF8";
mysqli_query($conn, $sql);


//SQL2：根据用户名查询用户编号，再根据用户编号查询出购物车编号
$sql = "SELECT cid FROM olddeal_cart WHERE userId=( SELECT uid FROM olddeal_user WHERE uname='$uname' )";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);
$cid = $row['cid'];

//SQL2：获取总记录数，并计算总页数
$sql = "SELECT COUNT(*) FROM olddeal_cart_detail,pub WHERE cartId='$cid'  AND  productId=pid";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);
$pager['recordCount'] = intval($row['COUNT(*)']);//把字符串解析为整数
$pager['pageCount'] = ceil(($pager['recordCount'])/($pager['pageSize']));  //计算总页数

//SQL3：获取当前指定页中的记录
$start = ($pager['pageNum']-1)*$pager['pageSize']; //从哪一行开始读取记录
$count = $pager['pageSize']; //读取多少行
$sql = "SELECT did,cartId,productId,count,pname,uname,price,pic1 FROM olddeal_cart_detail,pub WHERE cartId='$cid'  AND  productId=pid LIMIT $start,$count";
$result = mysqli_query($conn, $sql);

//读取所有的产品记录
$pager['data'] = mysqli_fetch_all($result, MYSQLI_ASSOC);


//把分页对象编码为JSON字符串并输出
echo json_encode($pager);


