<script type="text/javascript">

    angular.module('cpi-app', [])
    .constant('API', {
        ACTION_URL: "../action.php?uid=<?php echo isset($uid) ? $uid : '' ?>",
    })
    .factory("UserFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            GetUsers : function(data = []) {
                return APIService.MakeRequest("&entity=user&action=get_users", "POST", data);
            },
            CreateUser : function(data = []) {
                return APIService.MakeRequest("&entity=user&action=create_user", "POST", data);
            },
            UpdateUser: function(data = []) {
                return APIService.MakeRequest("&entity=user&action=update_user", "POST", data);
            },
        }
    }])
    .factory("SettingsFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            SaveSettings : function(data = []) {
                return APIService.MakeRequest("&entity=setting&action=save_settings", "POST", data);
            },
            GetSettings : function(data = []) {
                return APIService.MakeRequest("&entity=setting&action=get_settings", "GET", data);
            },
            GetMailSettings : function(data = []) {
                return APIService.MakeRequest("&entity=setting&action=get_mail_settings", "GET", data);
            },
            SaveMailSettings : function(data = []) {
                return APIService.MakeRequest("&entity=setting&action=save_mail_settings", "POST", data);
            },
            GetCostSettings : function() {
                return APIService.MakeRequest("&entity=setting&action=get_cost_settings", "GET");
            },
            SaveCostSettings : function(data = []) {
                return APIService.MakeRequest("&entity=setting&action=save_cost_settings", "POST", data);
            },
        }
    }])
    .factory("AuthFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            AuthLogin : function(data = []) {
                return APIService.MakeRequest("&entity=auth&action=login", "POST", data);
            },
            ResetPassword : function(data = []) {
                return APIService.MakeRequest("&entity=auth&action=reset_password", "POST", data);
            },
        }
    }])
    .factory("AdminFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            GetAdminDetails : function(data = []) {
                return APIService.MakeRequest("&entity=admin&action=get_admin", "POST", data);
            },
            UpdateAdmin : function(data = []) {
                return APIService.MakeRequest("&entity=admin&action=update_admin", "POST", data);
            },
        }
    }])
    .factory("MailLogFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            GetUserLogs : function(data = []) {
                return APIService.MakeRequest("&entity=mail_log&action=get_user_logs", "POST", data);
            },
        }
    }])
    .factory("CreditFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
        return {
            GetUserPurchaseHistory : async function(data = []) {
                return APIService.MakeRequest("&entity=credit&action=get_purchase_history", "POST", data);
            },
            GetUserCreditHistory : function(data = []) {
                return APIService.MakeRequest("&entity=credit&action=get_credits_history", "POST", data);
            },
            GetUserCurrentCredits : function(data = []) {
                return APIService.MakeRequest("&entity=credit&action=purchase_credits", "POST", data);
            },
            PurchaseCredits : function(data = []) {
                return APIService.MakeRequest("&entity=credit&action=purchase_credits", "POST", data);
            },
        }
    }])
    .service("APIService", ["$http", "$q", "API", function($http, $q, API) {
        return {
            MakeRequest : function(url, method, data = {}) {
                var d = $q.defer();

                $http({
                    method: method,
                    url: API.ACTION_URL + url,
                    data: data
                }).then(function (response){
                    d.resolve(response.data);
                },function (error){
                    d.reject(error);
                });

                return d.promise;
            }
        }
    }])
    .directive('loaderDirective', function() {
       return {
            template : 
            '<div class="fullscreen-loader" ng-init="is_processing = false;" ng-if="is_processing == true"><div class="loader-content"><img src="../lib/images/processing.gif"><br><span>Please wait...</span></div></div>'
        };
    })
    .directive('ckEditor', function() {
        return {
            require: '?ngModel',
            link: function(scope, elm, attr, ngModel) {
                var ck = CKEDITOR.replace(elm[0]);

                if (!ngModel) return;

                ck.on('pasteState', function() {
                    scope.$apply(function() {
                        ngModel.$setViewValue(ck.getData());
                    });
                });

                ngModel.$render = function(value) {
                    ck.setData(ngModel.$viewValue);
                };
            }
        };
    })
    .controller('AuthController', function AuthController($scope, $http, $location, AuthFactory) {

        $scope.auth = {};
        $scope.email = null;
        $scope.is_processing = false;

        $scope.login = function() {

            AuthFactory.AuthLogin($scope.auth).then(function(response){
                if (response.success) {
                    window.location = 'users.php?uid=' + response.data.uid;
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
            }) 
        }

        $scope.resetPassword = function(user_type) {

            $scope.is_processing = true;

            let data = {
                email : $scope.email,
                user_type : user_type
            }

            AuthFactory.ResetPassword(data).then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
            })
        }

    })
    .controller('SettingsController', function SettingsController($scope, $http, $location, SettingsFactory) {

        $scope.devkeys = {};
        $scope.mail = {
            failed_payment : {
                subject : '',
                body : '',
            },
            new_purchase : {
                subject : '',
                body : '',
            },
            rebill : {
                subject : '',
                body : '',
            },
            password_reset : {
                subject : '',
                body : '',
            }            
        };

        if (!localStorage.hasOwnProperty('yfs_settings_active_settings_tab')) {
            let tabs = {
                api : true,
                mail : false
            };

            localStorage['yfs_settings_active_settings_tab'] = JSON.stringify(tabs);
        }

        $scope.active_tab = JSON.parse(localStorage['yfs_settings_active_settings_tab']);

        _getSettings();
        _getMailSettings();
        _getCostSettings();

        function _getSettings() {
            $scope.is_processing = true;
            SettingsFactory.GetSettings().then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    $scope.devkeys = response.data;
                }
                
            })  
        }

        function _getMailSettings() {
            $scope.is_processing = true;
            SettingsFactory.GetMailSettings().then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    if (response.data) {
                        $scope.mail = response.data;
                    }                    
                }                
            })  
        }

        function _getCostSettings(){
            $scope.is_processing = true;
            SettingsFactory.GetCostSettings().then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    if (response.data) {
                        $scope.cost = response.data;
                    }                    
                }                
            })  
        }


        $scope.saveKeys = function() {

            $scope.is_processing = true;            
            SettingsFactory.SaveSettings($scope.devkeys).then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
                
            })
        }

        $scope.switchTab = function(tab) {

            let tabs = {
                api : false,
                mail : false
            };

            $scope.active_tab = tabs;

            tabs[ tab ] = true;
            $scope.active_tab[ tab ] = true;

            localStorage['yfs_settings_active_settings_tab'] = JSON.stringify(tabs);

        }

        $scope.saveMailSettings = function() {
            $scope.is_processing = true;

            let payload = {
                content : $scope.mail
            }

            SettingsFactory.SaveMailSettings(payload).then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
            })  
        }

        $scope.saveCostSettings = function() {
            $scope.is_processing = true;


            console.log($scope.cost);

            let payload = angular.copy($scope.cost);

            SettingsFactory.SaveCostSettings(payload).then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
            }) 
        }

    })
    .controller('UserController', function UserController($scope, $http, $location, API, UserFactory) {

        $scope.user = {
            showDisabled: false,
            users : [],
            new_user : {},
            edit_user : {},
        };

        _getUsers();

        function _getUsers() {
            $scope.is_processing = true;
            UserFactory.GetUsers().then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    $scope.user.users = response.data;                    
                }
            })
        }

        $scope.createUser = function(){
            $scope.is_processing = true;
            UserFactory.CreateUser($scope.user.new_user).then(function(response){
                $scope.is_processing = false;
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                    $('.modal').modal('hide');
                    $scope.user.new_user = {};
                    _getUsers();
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
                
            })
        }

        $scope.editUser = function(data) {

            let edit_data = angular.copy(data);

            edit_data.expireOn = new Date(angular.copy(data.expireOn));
            edit_data.new_password = "";

            $scope.user.edit_user = edit_data;
        }

        function formatDate(d, includeTime) {
            try {
                if (includeTime === undefined)
                    includeTime = false;

                d = new Date(d);
                var dd = d.getDate();
                var mm = d.getMonth() + 1;
                var yyyy = d.getFullYear();

                if (dd < 10)
                    dd = '0' + dd;

                if (mm < 10)
                    mm = '0' + mm;

                var date = mm + '/' + dd + '/' + yyyy;
                var time = d.toLocaleString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true});

                if (includeTime)
                    return date + " " + time;
                else
                    return date;
            } catch (e) {
                return "";
            }
        }

        $scope.updateUser = function() {
            $scope.is_processing = true;

            if ($scope.user.edit_user.new_password !== "") {
                if ($scope.user.edit_user.new_password !== $scope.user.edit_user.confirm_password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Passwords do not match',
                    });
                } else {
                    $scope.user.edit_user.changePasswordByAdmin = 1;

                    Swal.fire({
                        title: 'Are you sure you want to change user password?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.value)
                            proceedToUpdateUser();
                    });
                }
            } else
                proceedToUpdateUser();
        }

        $scope.updateCredits = function() {

            let payload = {
                id : $scope.user.edit_user.id,
                currentCredits : $scope.user.edit_user.currentCredits,
            }

            UserFactory.UpdateUser(payload).then(function (response) {
                $scope.is_processing = false;
                if (response.success) {

                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );

                    let index = $scope.user.users.findIndex(x => x.id === $scope.user.edit_user.id);
                    $scope.user.users[index] = $scope.user.edit_user;

                    $('.modal').modal('hide');

                    $scope.user.edit_user = {};                    

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }

            });
        }

        function proceedToUpdateUser() {
            UserFactory.UpdateUser($scope.user.edit_user).then(function (response) {
                $scope.is_processing = false;
                if (response.success) {

                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );

                    let index = $scope.user.users.findIndex(x => x.id === $scope.user.edit_user.id);
                    $scope.user.users[index] = $scope.user.edit_user;

                    $('.modal').modal('hide');

                    $scope.user.edit_user = {};                    

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }

            });
        }

        $scope.disableAccount = function(data) {

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, disable it!'
            }).then((result) => {
                if (result.value) {

                    let payload = {
                        expireOn : 'NOW()',
                        id : angular.copy(data.id)
                    };
                    $scope.is_processing = true;
                    UserFactory.UpdateUser(payload).then(function(response) {
                        $scope.is_processing = false;
                        if (response.success) {
                            Swal.fire(
                                'Success!',
                                'Account successfully disabled',
                                'success'
                            );
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.msg,
                            })
                        }
                        
                    })
                }
            })
        }
    })
    .controller('AdminController', function AdminController($scope, $http, $location, AdminFactory) {

        $scope.admin = {};
        $scope.admin.id = "<?= isset($userID) ? $userID : null ?>";

        _getAdminDetails();

        function _getAdminDetails() {
            $scope.is_processing = true;
            AdminFactory.GetAdminDetails($scope.admin).then(function(response) {
                $scope.is_processing = false;
                $scope.admin.name = response.data.name;
                $scope.admin.email = response.data.email;
            })
        }

        $scope.updateAdmin = function() {
            $scope.is_processing = true;
            AdminFactory.UpdateAdmin($scope.admin).then(function(response) {
                $scope.is_processing = false;
                $scope.admin.current_password = null;
                $scope.admin.new_password = null;
                $scope.admin.confirm_password = null;
                if (response.success) {                    
                    Swal.fire(
                        'Success!',
                        response.msg,
                        'success'
                    );
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg,
                    })
                }
                
            })
        }
    })
    .controller('MailLogController', function MailLogController($scope, $http, $location, MailLogFactory) {

        $scope.log = {
            logs : null,
            log_entries : [],
        };
        $scope.user_id = "<?= (isset($_GET['id'])) ? $_GET['id'] : '' ?>";
        $scope.user = {};

        _getUserLogs();

        function _getUserLogs() {
            $scope.is_processing = true;

            let payload = {
                user_id : $scope.user_id
            }

            MailLogFactory.GetUserLogs(payload).then(function(response) {
                $scope.is_processing = false;
                $scope.log.logs = response.data.logs;
                $scope.user = response.data.user;
            })
        }

        $scope.viewLogs = function(data) {
            $scope.log.log_entries = data;
        }

    })
    .controller('CreditController', function CreditController($scope, $http, $location, CreditFactory) {

        $scope.log = {
            logs : null,
            log_entries : [],
        };
        $scope.user_id = "<?= (isset($_GET['id'])) ? $_GET['id'] : '' ?>";
        $scope.user = {};
        $scope.purchase_data = [];
        $scope.credit_data = [];

        _getPurchaseHistory();
        _getCreditHistory();

        function _getPurchaseHistory() {

            let payload = {
                id: $scope.user_id
            }

            CreditFactory.GetUserPurchaseHistory(payload).then(function(response) {
                console.log(response);
                if (response.success){                                
                    $scope.purchase_data = response.data;
                    $scope.$digest()
                }else{
                    console.log(response.msg);
                }                
                    
            });

        }

        function _getCreditHistory() {

            let payload = {
                id: $scope.user_id
            }

            CreditFactory.GetUserCreditHistory(payload).then(function(response) {
                console.log(response);
                if (response.success){                                
                    $scope.credit_data = response.data;
                    $scope.$digest()
                }else{
                    console.log(response.msg);
                }                
                    
            });
   
        }

    });

</script>
