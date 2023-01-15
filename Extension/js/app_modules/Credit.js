angular.module('CreditModule', [])
.controller('CreditController', function CreditController($scope, $http, UserFactory, CreditFactory, AppExtensionService) {

    $scope.user = {};    
    $scope.creditHistory = [];

    _getCreditHistory();

    function _getUserData(callback) {

        $scope.user = {};

        LS.getItem('userID', function(userID) {
            let payload = {
                id: userID
            }

            UserFactory.GetUser(payload).then(function(response) {

                if (response.success){
                    $scope.user = response.data; 
                    callback();    
                }else{
                    alert(response.msg);
                }              
                    
            });
        });
    }

    function _getCreditHistory() {

        LS.getItem('userID', function(userID) {
            let payload = {
                id: userID
            }

            CreditFactory.GetUserCreditHistory(payload).then(function(response) {

                if (response.success){                                
                    $scope.creditHistory = response.data;
                }else{
                    alert(response.msg);
                }                
                    
            });
        });

    }

    $scope.logout = function() {
        AppExtensionService.LogoutUser();
    }

})
