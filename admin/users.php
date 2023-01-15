<?php
require_once "logincheck.php";
require_once "includes/header.php"; 
?>

<!--Main layout-->
<main ng-controller="UserController">
    <loader-directive></loader-directive>
    <div class="container-fluid">
        <section class="card card-cascade narrower mb-5" style="box-shadow:none;background: #f8f9fa;">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <h6 class="card-header font-weight-bold text-uppercase py-4">Users</h6>
                        <div class="card-body">
                            <div id="table" class="table-editable">
                                <span class="table-add float-right mb-3 mr-2">
                                    <a href="#" class="text-success" data-toggle="modal" data-target="#createUserModal"><i class="fa fa-plus fa-2x" aria-hidden="true"></i></a>
                                </span>

                                <table class="table table-bordered" ng-if="user.users.length > 0">
                                    <thead >
                                        <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Email</th>
                                          <th scope="col">Credits</th>
                                          <th scope="col">Options</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                    <tr ng-repeat="(k, v) in user.users track by $index">
                                        <th scope="row">{{ k+1 }}</th>
                                        <td>{{ v.name }}&nbsp;<i ng-if="v.accepted_tos == 1" class="fa fa-check-circle fa-lg text-info" title="Accepted Terms of Service"></i></td>
                                        <td ng-bind="v.email"></td>
                                        <td ng-bind="v.currentCredits"></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle pull-left" 
                                                type="button" 
                                                id="dropdownMenuButton" 
                                                data-toggle="dropdown" 
                                                aria-haspopup="true" 
                                                aria-expanded="false">
                                                Select Options
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editUserModal" ng-click="editUser(v)">Edit</a>
                                                    <a class="dropdown-item" href="user_mail_logs.php?uid=<?= $uid ?>&id={{ v.id }}">View Mail Logs</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#" ng-click="disableAccount(v)">Disable</a>
                                                </div>
                                            </div>

                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle pull-left" 
                                                type="button" 
                                                id="dropdownMenuButton" 
                                                data-toggle="dropdown" 
                                                aria-haspopup="true" 
                                                aria-expanded="false">
                                                Credits
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editCreditsModal" ng-click="editUser(v)">Update Credits</a>
                                                    <a class="dropdown-item" href="purchase-history.php?uid=<?= $uid ?>&id={{ v.id }}">View Purchase History</a>
                                                    <a class="dropdown-item" href="credit-history.php?uid=<?= $uid ?>&id={{ v.id }}">View Credit History</a>
                                                </div>
                                            </div>
                               
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Default input -->
                        <div class="form-group">
                            <label for="formGroupExampleInputMD">Name</label>
                            <input type="text" class="form-control" placeholder="Enter name" ng-model="user.new_user.name">
                        </div>
                        <div class="form-group">
                            <label>User Type</label>
                            <select class="form-control" ng-model="user.new_user.user_type">
                                <option value="default">Default</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" placeholder="Enter email" ng-model="user.new_user.email">
                        </div>
                        <div class="form-group" ng-show="false">
                            <label>Account Type</label>
                            <select class="browser-default custom-select" ng-model="user.new_user.accType">
                                <option selected value="">- Select Plan -</option>
                                <option value="Basic">Basic</option>
                                <option value="Regular">Regular</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn-sm btn btn-primary" ng-click="createUser()">Create user</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END CREATE MODAL -->

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Default input -->
                        <div class="form-group">
                            <label for="formGroupExampleInputMD">Name</label>
                            <input type="text" class="form-control" placeholder="Enter name" ng-model="user.edit_user.name">
                        </div>
                        <div class="form-group">
                            <label>User Type</label>
                            <select class="form-control" ng-model="user.edit_user.user_type">
                                <option value="default">Default</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" placeholder="Enter email" ng-model="user.edit_user.email">
                        </div>
                        <div class="form-group" ng-show="false">
                            <label>Account Type</label>
                            <select class="browser-default custom-select" ng-model="user.edit_user.accType">
                                <option selected value="">- Select Plan -</option>
                                <option value="Basic">Basic</option>
                                <option value="Regular">Regular</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" placeholder="New Password" ng-model="user.edit_user.new_password">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" placeholder="New Password" ng-model="user.edit_user.confirm_password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn-sm btn btn-primary" ng-click="updateUser()">Update user</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT MODAL -->

    <!-- CREDITS MODAL -->
    <div class="modal fade" id="editCreditsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update user's credits</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Default input -->
                        <div class="form-group">
                            <label for="formGroupExampleInputMD">Enter new amount</label>
                            <input type="text" class="form-control" placeholder="Enter name" ng-model="user.edit_user.currentCredits">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn-sm btn btn-primary" ng-click="updateCredits()">Update credits</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END CREDITS MODAL -->


</main>
<!--Main layout-->

<?php require_once "includes/footer.php"; ?>
<?php include_once("includes/scripts.php"); ?>




