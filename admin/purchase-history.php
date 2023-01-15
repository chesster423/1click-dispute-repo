<?php
require_once "logincheck.php";
require_once "includes/header.php"; 
?>

<!--Main layout-->
<main ng-controller="CreditController">
    <loader-directive></loader-directive>
    <div class="container-fluid">
        <section class="card card-cascade narrower mb-5" style="box-shadow:none;background: #f8f9fa;">
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-primary m-0 mb-3" href="users.php?uid=<?= $uid ?>">Go back</a>
                    <div class="card">
                        <h6 class="card-header font-weight-bold text-uppercase py-4">Purchase History</h6>
                        <div class="card-body">
                            <div id="table" class="table-editable">
                                <table class="table table-bordered" ng-if="purchase_data.length > 0">
                                    <thead >
                                        <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Credits Amount</th>
                                          <th scope="col">Receipt URL</th>
                                          <th scope="col">Date Purchased</th>

                                      </tr>
                                  </thead>
                                  <tbody>
                                        <tr ng-repeat="(k, v) in purchase_data track by $index">
                                        <th scope="row">{{ k+1 }}</th>
                                        <td ng-bind="v.creditsAmount"></td>
                                        <td><a class="text-primary" href="{{ v.receiptUrl }}"  target="_blank">Receipt URL</a></td>
                                        <td ng-bind="v.purchaseDate"></td>
                                        </tr>

                                    </tbody>
                                    <p class="text-center" ng-if="purchase_data.length == 0">No data found</p>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


</main>
<!--Main layout-->

<?php require_once "includes/footer.php"; ?>
<?php include_once("includes/scripts.php"); ?>




