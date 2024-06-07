<?php
include("session_check_login.php");
require_once("../db_connect.php");

$sqlAll="SELECT * FROM campground_info WHERE campground_owner_id=$owner_id";
$resultAll = $conn->query($sqlAll);
$allCampgroundCount = $resultAll->num_rows;

if(isset($_GET["search"])){
    $search=$_GET["search"];
    $sql="SELECT id, campground_name , phone, email, position FROM campground_info WHERE campground_owner_id=$owner_id AND campground_name LIKE '%$search%'";
    $pageTitle="$search 的搜尋結果";
}else if(isset($_GET["page"]) && isset($_GET["order"])){
    $page=$_GET["page"];
    $perPage=5;
    $firstItem=($page-1)*$perPage;
    $pageCount = ceil($allCampgroundCount/$perPage);

    $order=$_GET["order"];
    switch($order){
        case 1:// id ASC
            $orderClause= "ORDER BY id ASC";
            break;
        case 2:// id DESC
            $orderClause= "ORDER BY id DESC";
            break;
        case 3:// 名字 ASC
            $orderClause= "ORDER BY campground_name ASC";
            break;
         case 4:// 名字 DESC
            $orderClause= "ORDER BY campground_name DESC";
            break;
    }
    $sql="SELECT * FROM campground_info WHERE campground_owner_id=$owner_id $orderClause  LIMIT $firstItem, $perPage";

    $pageTitle="營區列表，第 $page 頁";
}else{
    $sql="SELECT id, campground_name , phone, email, position FROM campground_info WHERE campground_owner_id=$owner_id";
    $pageTitle="營區列表";
    header("location: camp_area_search.php?page=1&order=1");
}

$result = $conn->query($sql);
$rows=$result->fetch_all(MYSQLI_ASSOC);
$campCount = $result->num_rows;

// if(isset($_GET["page"])){
//     $campCount= $allCampCount;
// }


?>


<!doctype html>
<html lang="en">
    <head>
        <title><?=$pageTitle ?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <?php include("../css.php"); ?>
        <link rel="stylesheet" href="./style/sidebars.css">
        <script src="./style/sidebars.js"></script>

        <style>
            table td,
            table th {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            }

            .card {
            border-radius: .5rem;
            }
        </style>
    </head>

    <body>
        <?php include("title.php") ?>
        <div class="d-flex">
            <?php include("sidebar.php") ?>
            <!-- 列表 -->
            
            <div class="container">
            <div class="row">
                <div class="d-flex justify-content-center">
                <div class="col-lg-8 ">
                <div class="card">
                <div class="card-body">
                <h4 class="mb-3"><?=$pageTitle?></h4>
                <hr>
                <?php if($result->num_rows > 0): ?>
                <?php if(isset($_GET["search"])): ?>
                    <a href="camp_area_search.php" class="btn btn-primary">返回列表</a>
                <?php endif; ?>
                <div class="py-2 mb-3">
                    <div class="d-flex justify-content-center gap-3 mb-2">
                        <form action="">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="輸入營地名稱..." name="search">
                                <button class="btn btn-primary" type="submit">搜尋</button>
                            </div>
                            <div>
                        </div>
                        </form>
                    </div>
                </div>
                <table class="table table-hover mb-3" >
                    <thead>
                        <tr>
                            <th class="text-center">營地編號 <a href="?page=<?=$page?>&order=<?php if($order==1){echo "2";}else{echo "1";}?>"><i class="fa-solid fa-sort"></i></a></th>
                            <th >營地名稱 <a href="?page=<?=$page?>&order=<?php if($order==3){echo "4";}else{echo "3";}?>"><i class="fa-solid fa-sort"></i></a></th>
                            <th class="text-center">我的營區</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows as $camp): ?>
                        <tr>
                            <td class="text-center"><?=$camp["id"]?></td>
                            <td><?=$camp["campground_name"]?></td>
                            <td class="text-center"><a href="camp_area_list.php?camp_id=<?=$camp["id"]?>" class="btn btn-primary"><i class="fa-solid fa-campground"></i> 進入</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: echo "沒有結果";  ?>
                <?php endif; ?>

                <?php if(isset($_GET["page"])):?>
                    <div class="d-flex justify-content-center">
                    <nav aria-label="...">
                        <ul class="pagination">
                            <?php for($i=1; $i<=$pageCount; $i++):?>
                            <li class="page-item"><a class="page-link 
                            <?php if($i==$page)echo 'active'?>" href="?page=<?=$i?>&order=<?=$order?>"><?=$i?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    </div>
                <?php endif; ?>
            </div>
            </div> 
        </div>
        </div>
        </div>
        </div>  
        </div>

        <?php include("../js.php"); ?>
        
    </body>
</html>
