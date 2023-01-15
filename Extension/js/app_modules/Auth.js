angular.module('AuthModule', [])
.controller('AuthController', function AuthController($scope, $http, $sce, AuthFactory, UserFactory) {

    $scope.auth = {
        login : {},
        reset : {},
        user : {},
    };

    $scope.is_loading = false;

    _getUserdata();

    function _getUserdata() {

        LS.getItem('auth_user', function(auth_user) {
            if (auth_user !== undefined) {
                $scope.auth.user = auth_user;
                $scope.is_loading = false;
            }

            let payload = {
                id: $scope.auth.user.id,
            }
            
            UserFactory.GetUser(payload).then(function (response) {

                $scope.is_loading = false;

                if (response.success) {
                    $scope.is_logged_in = true;
                    chrome.tabs.create({
                        url: chrome.runtime.getURL('pages/purchase-history.html')
                    });

                } else {
                    console.log(response.msg);
                }
            });
        });
    }


    LS.getItem('loggedIn', function(is_logged_in) {
        $scope.is_logged_in = (is_logged_in !== undefined);
    });

    $scope.callback = {
        success : false,
        msg : null,
    };

    $scope.login = function() {

        $scope.is_loading = true;
        $scope.auth.login.login_type = 'extension';

        AuthFactory.AuthLogin($scope.auth.login).then(function(response) {
            $scope.is_loading = false;

            if (response.success) {
                $scope.is_logged_in = true;

                let user_data = {
                    name : response.data.name,
                    email : $scope.auth.login.email,
                    id : response.data.id,
                    couponCode : response.data.couponCode
                };

                $scope.auth.user = user_data;

                let storage_data = {
                    userID : response.data.id,
                    gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF : response.data.token,
                    auth_user : user_data,
                    loggedIn : 'yes',
                    active_mail : 'lob'
                };

                chrome.storage.sync.set(storage_data, function() {
                  console.log('Data saved');
                });

                LS.setItem('loggedIn', 'yes');
                LS.setItem('auth_user', user_data);
                LS.setItem('userID', response.data.id);
                LS.setItem('gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF', response.data.token);

                chrome.tabs.create({
                    url: chrome.runtime.getURL('pages/credits-history.html')
                });

            } else {
                $scope.callback = response;
                $scope.callback.msg = $sce.trustAsHtml($scope.callback.msg);
            }
        });
    }

    $scope.resetPassword = function() {

        $scope.is_loading = true;

        let payload = {
            email : $scope.auth.reset.email,
            user_type : 'users',
        }

        AuthFactory.ResetPassword(payload).then(function(response) {
            $scope.callback = response;
            $scope.callback.msg = $sce.trustAsHtml($scope.callback.msg);
            $scope.is_loading = false;
        });
    }

    $scope.register = function() {

        $scope.is_loading = true;

        let payload = $scope.auth.register;

        UserFactory.CreateUser(payload).then(function(response) {

            $scope.is_loading = false;

            if (response.success) {  

                $scope.is_logged_in = true;
                let user_data = response.data.userData;

                $scope.auth.user = user_data;

                LS.setItem('loggedIn', 'yes');
                LS.setItem('auth_user', user_data);
                LS.setItem('userID', response.data.userData.id);
                LS.setItem('gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF', response.data.token); 

                chrome.tabs.create({
                    url: chrome.runtime.getURL('pages/credits-history.html')
                });

            }

            $scope.callback = response;
            $scope.callback.msg = $sce.trustAsHtml($scope.callback.msg);                
        })

    }

    $scope.logout = function() {

        $scope.is_logged_in = false;
        $scope.is_loading = false;
        $scope.auth.user = {}

        let toRemove = ['loggedIn', 'auth_user', 'userID', 'gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF'];
        LS.removeItems(toRemove).then(() => {
            window.location.reload();
        });
    }

})