angular.module('UserModule', [])
.controller('UserController', function UserController($scope, $http, UserFactory, CreditFactory, AppExtensionService) {

    $scope.user = {}; 
    $scope.edit = {
        current_password : null,
        new_password : null,
        confirm_password : null,
    };  

    _getUserData();

    function _getUserData() {

        $scope.user = {};

        LS.getItem('userID', function(userID) {
            let payload = {
                id: userID
            }

            UserFactory.GetUser(payload).then(function(response) {

                if (response.success){
                    $scope.user = response.data;   
                    $scope.$digest();                                    
                }else{
                    alert(response.msg);
                }              
                    
            });
        });
    }

    $scope.updateUser = function(changePassword = false) {

        if (changePassword && (!$scope.edit.current_password || !$scope.edit.new_password || !$scope.edit.confirm_password)) {
            Swal.fire({
              title: 'Warning!',
              text: 'Please fill up the missing fields',
              icon: 'warning',
            });

            return false;
        }

        LS.getItem('userID', function(userID) { 

            let payload = {
                id: userID,
                name : angular.copy($scope.user.name),
            }

            if(changePassword) {
                Object.assign(payload, $scope.edit);
            }

            UserFactory.UpdateUser(payload).then(function(response) {

                if (response.success){  
                    Swal.fire({
                      title: 'Success!',
                      text: response.msg,
                      icon: 'success',
                    })
                                     
                }else{
                    Swal.fire({
                      title: 'Warning!',
                      text: response.msg,
                      icon: 'warning',
                    })
                }              
                    
            });
        });

    }

    $scope.logout = function() {
        AppExtensionService.LogoutUser();
    }

})