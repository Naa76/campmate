<?php
require_once("../db_connect.php");

$sqlAll = "SELECT * FROM ticket WHERE valid = 1";
$resultAll = $conn->query($sqlAll);
$allTicketCount = $resultAll->num_rows;

if (isset($_GET["search"])) {
    $search = $_GET["search"];
    $sql = "SELECT id, title, description, user_id, reply, createtime, closetime, status FROM ticket WHERE title LIKE '%$search%' AND valid = 1";
    $pageTitle = "$search 的搜尋結果";
} else if (isset($_GET["page"]) && isset($_GET["order"])) {
    $page = $_GET["page"];
    $perPage = 5;
    $firstItem = ($page - 1) * $perPage;
    $pageCount = ceil($allTicketCount / $perPage);

    $order = $_GET["order"];

    switch ($order) {
        case 1:
            $orderClause = "ORDER BY id ASC";
            break;
        case 2:
            $orderClause = "ORDER BY id DESC";
            break;
        case 3:
            $orderClause = "ORDER BY status ASC";
            break;
        case 4:
            $orderClause = "ORDER BY status DESC";
            break;
    }
    $sql = "SELECT * FROM ticket WHERE valid=1
    $orderClause LIMIT $firstItem, $perPage";
    $pageTitle = "客服單列表 第 $page 頁";
} else {
    $sql = "SELECT id, title, description, user_id, reply, createtime, closetime, status FROM ticket WHERE valid = 1";
    $pageTitle = "客服單列表";
    header("location: tickets.php?page=1&order=1");
}

$result = $conn->query($sql);
$rows = $result->fetch_all(MYSQLI_ASSOC);
$ticketCount = $result->num_rows;
if (isset($_GET["page"])) {
    $ticketCount = $allTicketCount;
}

?>
<!doctype html>
<html lang="zh-Hant">

<head>
    <title><?= $pageTitle ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- css -->
    <?php include("../css.php") ?>
    <?php include("../css_neumorphic.php") ?>
    <style>
        :root {
            --aside-width: 250px;
            --header-height: 186px;
        }

        .main-header {
            width: var(--aside-width);
            background: var(--secondary-color);

            .logo,
            .text {
                margin-left: 30px;
                margin-right: 30px;
                margin-top: 20px;
                border-radius: 24px;
            }

            .logo {
                padding: 30px 20px;
                background: #9ba45c;
                box-shadow: 6px 6px 10px #798048,
                    -6px -6px 10px #bdc870;

                &:hover {
                    box-shadow: inset 6px 6px 10px #798048,
                        inset -6px -6px 10px #bdc870;
                }
            }

            .text {
                margin-bottom: 20px;
                text-align: center;
                padding: 9px;
                font-size: 14px;
                color: var(--primary-color);
                background: #9ba45c;
                box-shadow: inset 6px 6px 10px #798048,
                    inset -6px -6px 10px #bdc870;
            }
        }

        .aside-left {
            padding: var(--header-height) 20px 0 20px;
            width: var(--aside-width);
            top: 0;
            overflow: auto;
            background: var(--secondary-color);

            li {
                margin-bottom: 18px;

                a {
                    transition: 0.3s ease;
                    color: #fff;
                    letter-spacing: 1px;

                    &:hover {
                        transform: translate(-3px, -3px);

                        i {
                            color: #9ba45c;
                            background: linear-gradient(145deg, #ffefda, #d7c9b8);
                            box-shadow: 2px 2px 8px #baae9f,
                                -2px -2px 8px #fffff9;
                        }
                    }
                }

                i {
                    width: 48px;
                    height: 48px;
                    text-align: center;
                    transition: 0.3s ease;
                    padding: 15px;
                    margin-right: 10px;
                    border-radius: 16px;
                    background: linear-gradient(145deg, #a6af62, #8c9453);
                    box-shadow: 6px 6px 12px #848b4e,
                        -6px -6px 12px #b2bd6a;
                }

                span {
                    font-size: 12px;
                }

                .line {
                    margin: 0 16px;
                    border: none;
                    height: 1px;
                    background: var(--primary-color);
                }
            }
        }

        .main-content {
            margin-left: var(--aside-width);
            margin-top: 10px;
        }

        .aside-a-active {
            transform: translate(-3px, -3px);
        }

        .aside-i-active {
            color: #9ba45c;
            background: linear-gradient(145deg, #ffefda, #d7c9b8) !important;
            box-shadow: 2px 2px 8px #baae9f,
                -2px -2px 8px #fffff9 !important;
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0 15px;
        }

        .table-custom thead th {
            border: none;
            background-color: #343a40;
            color: white;
        }

        .table-custom tbody tr {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-custom tbody tr td {
            background-color: white;
            border: none;
        }

        .pagination-shadow {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination-shadow .page-item {
            margin: 0 5px;
        }

        .pagination-shadow .page-item .page-link {
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: #000;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s, color 0.3s;
        }

        .pagination-shadow .page-item .page-link:hover {
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #007bff;
        }

        .pagination-shadow .page-item.active .page-link {
            color: white;
            background-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <header class="main-header d-flex flex-column fixed-top justify-content-center">
        <a href="" class="text-decoration-none logo">
            <img src="/campmate/images/logo.svg" alt="">
        </a>
        <div class="text">
            Hi, Admin
        </div>
    </header>
    <aside class="aside-left position-fixed vh-100">
        <ul class="list-unstyled mt-3 text-truncate">
            <li>
                <a class="d-block px-3 text-decoration-none" href="user-list.php">
                    <i class="fa-solid fa-user"></i> <span>一般會員</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="/campmate/campground_owner/owners.php">
                    <i class="fa-solid fa-user-tie"></i> <span>營地主系統</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="campground-management.php">
                    <i class="fa-solid fa-campground"></i> <span>營地訂位管理</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="equipment-rental.php">
                    <i class="fa-solid fa-person-hiking"></i> <span>露營用品租用管理</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="group-system.php">
                    <i class="fa-solid fa-people-roof"></i> <span>揪團系統</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="coupons/coupons-list.php">
                    <i class="fa-solid fa-ticket"></i> <span>優惠券</span>
                </a>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="tickets.php">
                    <i class="fa-solid fa-headset"></i> <span>客服</span>
                </a>
            </li>
            <li>
                <div class="line"></div>
            </li>
            <li>
                <a class="d-block px-3 text-decoration-none" href="logout.php">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>登出</span>
                </a>
            </li>
        </ul>
    </aside>
    <main class="main-content">
        <!-- 這裡將顯示動態加載的內容 -->
        <div class="container">
            <h1><?= $pageTitle ?></h1>
            <div class="py-2 mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php if (isset($_GET["search"])) : ?>
                            <a class="btn btn-warning" href="tickets.php"><i class="fa-solid fa-arrow-left"></i></a>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-3">
                        <form action="">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." name="search">
                                <button class="btn btn-warning" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </form>
                        <a class="btn btn-warning" href="create_ticket.php"><i class="fa-solid fa-plus"></i></a>
                    </div>
                </div>
            </div>
            <div class="pb-2 d-flex justify-content-between">
                <div>
                    共 <?= $ticketCount ?> 單
                </div>
                <?php if (isset($_GET["page"])) : ?>
                    <div>
                        排序:
                        <div class="btn-group">
                            <a href="?page=<?= $page ?>&order=1" class="btn btn-warning <?php if ($order == 1) echo "active"; ?>">編號<i class="fa-solid fa-arrow-down-1-9"></i></a>

                            <a href="?page=<?= $page ?>&order=2" class="btn btn-warning <?php if ($order == 2) echo "active"; ?>">編號<i class="fa-solid fa-arrow-up-1-9"></i></a>

                            <a href="?page=<?= $page ?>&order=3" class="btn btn-warning <?php if ($order == 3) echo "active"; ?>">尚未回覆</a>

                            <a href="?page=<?= $page ?>&order=4" class="btn btn-warning <?php if ($order == 4) echo "active"; ?>">已回覆</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($result->num_rows > 0) : ?>
                <table class="table table-custom table-hover">
                    <thead>
                        <tr class="text-truncate">
                            <th>編號</th>
                            <th>標題</th>
                            <th>描述</th>
                            <th>使用者id</th>
                            <th>回覆</th>
                            <th>狀態</th>
                            <th>建立時間</th>
                            <th>檢視</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $user) : ?>
                            <tr>
                                <td scope="row" class="text-center"><?= $user["id"] ?></td>
                                <td class="text-truncate"><?= $user["title"] ?></td>
                                <td><?= $user["description"] ?></td>
                                <td class="text-center"><?= $user["user_id"] ?></td>
                                <td><?= $user["reply"] ?></td>
                                <td class="text-truncate"><?= $user["status"] ?></td>
                                <td><?= $user["createtime"] ?></td>
                                <td><a class="btn btn-warning" href="ticket.php?id=<?= $user["id"] ?>"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php if (isset($_GET["page"])) : ?>
                    <nav aria-label="Page ">
                        <ul class="pagination pagination-shadow">
                            <?php for ($i = 1; $i <= $pageCount; $i++) : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $i ?>&order=<?= $order ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else : ?>
                沒有客訴單
            <?php endif; ?>

        </div>
    </main>
    <!-- js -->
    <?php include("../js.php") ?>
</body>

</html>