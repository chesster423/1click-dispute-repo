angular.module('PurchaseModule', [])
.controller('PurchaseController', function PurchaseController($scope, $http, UserFactory, CreditFactory, AppExtensionService) {

    $scope.user = {};  
    $scope.payload = {};  
    $scope.is_processing = false;
    $scope.purchaseHistory = [];

    _getUserData();
    
    if (window.location.href.indexOf("purchase-history.html") > -1) {
        _getPurchaseHistory();
    }

    $scope.logout = function() {
        AppExtensionService.LogoutUser();
    }

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


    function _getPurchaseHistory() {

        LS.getItem('userID', function(userID) {
            let payload = {
                id: userID
            }

            CreditFactory.GetUserPurchaseHistory(payload).then(function(response) {

                if (response.success){                                
                    $scope.purchaseHistory = response.data;
                    $scope.$digest()
                }else{
                    alert(response.msg);
                }                
                    
            });
        });

    }

    $scope.extractData = function(json) {

        var data = JSON.parse(json);

        return data;
    }


    $scope.purchaseCredits = function() {

        const amount = $scope.payload.creditsAmount;   

        Swal.fire({
          title: 'Are you sure?',
          text: `Are you sure you want to purchase `+amount+` credits? We will charge $`+$scope.payload.creditsAmount*2+` on your card.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, buy it!'
        }).then((result) => {

          if (result.isConfirmed) {

            $scope.payload.cardNumber = $('.card-number').val();
            $scope.payload.expiryMonth = $('.expiry-month').val();
            $scope.payload.expiryYear = $('.expiry-year').val();

            $scope.is_processing = true;

            LS.getItem('userID', function(userID) {

                $scope.payload.email = angular.copy($scope.user.email);

                CreditFactory.PurchaseCredits($scope.payload).then(function(response) {

                    $scope.is_processing = false;

                    if (response.success) {
                        Swal.fire({
                          title: 'Success!',
                          text: response.msg,
                          icon: 'success',
                        })
                        $scope.user.hasCoupon = false;
                        $scope.payload = {};

                    }else{
                        Swal.fire({
                          title: 'Warning',
                          text: response.msg,
                          icon: 'warning',
                        })
                    }
                    
                })

            });
          }
        })
    }
})
