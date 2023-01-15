<?php
if (!isset($_REQUEST['plugin']) && isset($_SESSION['adminSessionKey']) && isset($_SESSION['adminLoggedIN'])) {
    header('Location: template.php?uid='.session_id());
    exit();
}
?>

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
</head>

<body class="fixed-sn" ng-app="cpi-app">

    <div class="row justify-content-center" ng-controller="AuthController">
        <div class="col-md-4 col-md-offset-4">
            <div class="card" style="margin-top: 150px">
                <div class="card-header" style="text-align: center; background: #131825;">
                    <img src="../lib/images/app_logo_resized.png" style="width: 150px">
                </div>
                <div class="card-body">
                    <h6>EMAIL</h6>
                    <input type="email" id="email" class="form-control" ng-model="auth.email">
                    <h6 style="margin-top: 25px">PASSWORD</h6>
                    <input type="password" id="password" class="form-control" ng-model="auth.password">

                    <div style="text-align: center;margin-top: 20px;">
                        <a href="#" class="btn btn-primary" ng-click="login()">Log in</a>
                    </div>

                    <p style="text-align: center; margin-top: 30px;"></p>

                    <div style="text-align: center;margin-top: 50px;">
                        <a href="reset-password.php" class="">Lost Password? Click Here...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php require_once "includes/footer.php"; ?>
<?php include_once("includes/scripts.php"); ?>




