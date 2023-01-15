<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>1Click Dispute</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../lib/css/font-awesome-4.7.0/css/font-awesome.css">
    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="../css/mdb.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="../css/style.css" rel="stylesheet">
    <!-- AtJS styles -->
    <link href="../js/At.js-master/dist/css/jquery.atwho.min.css" rel="stylesheet">
</head>

<body class="fixed-sn" ng-app="cpi-app">

    <!--Main Navigation-->
    <header>

        <!--Navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark double-nav  fixed-top scrolling-navbar">

            <!-- SideNav slide-out button -->
            <div class="float-left">
                <a href="#" data-activates="slide-out" class="button-collapse">
                    <img src="https://app.1clickdispute.com/uploads/bars.svg" style="width: 30px" />
                </a>
            </div>

            <!-- Links -->
            <ul class="nav navbar-nav nav-flex-icons ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <img height="37px" src="../uploads/<?php echo $_SESSION['adminPicture']; ?>" style="border-radius: 100%">
                        <span class="clearfix d-none d-sm-inline-block"><?php echo $_SESSION['adminName']; ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="edit-profile.php?uid=<?php echo $uid ?>">Edit Profile</a>
                        <a class="dropdown-item" href="logout.php?uid=<?php echo $uid ?>">Log Out</a>
                    </div>
                </li>
            </ul>

        </nav>
        <!--/.Navbar-->

        <!-- Sidebar navigation -->
        <div id="slide-out" class="side-nav fixed">
            <ul class="custom-scrollbar list-unstyled">
                <!-- Logo -->
                <li class="logo-sn waves-effect" style="background-color: #131825; height: 170px; padding:10px;border-right: 1px solid rgba(0,0,0,.125);">
                    <div class=" text-center">
                        <a href="#" class="pl-0">
                            <img src="../lib/images/app_logo_resized.png">
                        </a>
                    </div>
                </li>
                <!--/. Logo -->


                <!-- Side navigation links -->
                <li style="margin-top:-15px;">
                    <ul class="collapsible collapsible-accordion">
                        <li>
                            <a class="waves-effect arrow-r" href="<?php echo "users.php?uid=$uid"; ?>">
                                <i class="fa fa-users"></i> Users
                            </a>
                        </li>

                        <li>
                            <a class="waves-effect arrow-r" href="<?php echo "settings.php?uid=$uid"; ?>">
                                <i class="fa fa-gear"></i> Settings
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/. Side navigation links -->
            </ul>

            <!-- Mask -->
            <div class="sidenav-bg mask-strong"></div>

        </div>
        <!--/. Sidebar navigation -->

    </header>
    <!--Main Navigation-->
