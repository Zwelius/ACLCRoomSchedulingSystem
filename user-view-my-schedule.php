<?php
session_start();
include 'config.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.html");
    exit;
} else {
    $user_id = $_SESSION['teacher_id'];

    $sql = mysqli_query($con, "SELECT * FROM `teacher_tb` WHERE `teacher_id` = '$user_id'");
    while ($row = mysqli_fetch_array($sql)) {
        $user_name = $row['teacher_name'];

        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Manage Account</title>
            <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
            <link rel="stylesheet" href="css/SD/sidebar.css">
            <link rel="stylesheet" href="css/SD/manage-account.css">
        </head>

        <body>
            <div class="sidebar close">
                <div class="logo-details">
                    <img src="media/ACLC-logo.png">
                    <span class="logo-name">SchedSystem</span>
                </div>
                <ul class="nav-links">
                    <li>
                        <a href="#">
                            <i class='bx bx-grid-alt'></i>
                            <span class="link-name">Manage</span>
                        </a>
                        <ul class="sub-menu blank">
                            <li><a class="link-name" href="#">Manage</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="icon-link">
                            <a href="#">
                                <i class='bx bx-collection'></i>
                                <span class="link-name">Schedule</span>
                            </a>
                            <i class='bx bxs-chevron-down arrow'></i>
                        </div>
                        <ul class="sub-menu">
                            <li><a class="link-name">Schedule</a></li>
                            <li><a href="user-view-room-schedule.php">Room Schedule</a></li>
                            <li><a href="user-view-my-schedule.php">My Schedule</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="user-manage-account.php">
                            <i class='bx bxs-cog'></i>
                            <span class="link-name">Account</span>
                        </a>
                        <ul class="sub-menu blank">
                            <li><a class="link-name" href="user-manage-account.php">Account</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="profile-details">
                            <div class="profile-content">
                                <i class='bx bxs-user-circle' id="profile-img"></i>
                            </div>
                            <div class="name-job">
                                <div class="profile-name"><?php echo $user_name ?></div>
                            </div>
                            <a href="./logout.php"><i class='bx bx-log-out' id="logout"></i></a>
                        </div>
                    </li>
                </ul>
            </div>
            <section class="home-section">
                <div class="home-content">
                    <i class='bx bx-menu'></i> <!-- button -->
                    <span class="text">Manage Account</span>
                    <button id="edit_btn" type="button" onclick="print()">Print</button>
                </div>
                <?php include './PHP Backend/teacher pages/mySchedule.php' ?>
            </section>
        </body>
        <?php
    }
}
?>
<script>
    let arrow = document.querySelectorAll(".arrow");
    for (var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e) => {
            let arrowParent = e.target.parentElement.parentElement;
            arrowParent.classList.toggle("showMenu");
        });
    }
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".bx-menu");
    console.log(sidebarBtn);
    sidebarBtn.addEventListener("click", () => {
        sidebar.classList.toggle("close");
    });
</script>

</html>