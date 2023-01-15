angular.module('dispute-aid-app', ['AuthModule','UserModule','PurchaseModule', 'CreditModule'])
.constant('API', {
    ACTION_URL: '',
})
.service("APIService", ["$http", "$q", "API", function($http, $q, API) {
    return {
        MakeRequest : function(url, method, data = {}) {
            var d = $q.defer();

            LS.getItem('1devPpbHELnRan5nawe66amQSsHs98bMmtestG0', function (devUser) {
                API.ACTION_URL = (devUser !== undefined) ? "http://localhost/1clickdispute/action.php" : "https://app.1clickdispute.com/action.php";                

                LS.getItem('gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF', function(userToken) {
                    data._token = userToken;

                    LS.getItem('userID', function(userID) {
                        data.user_id = userID;

                        if (data._token === undefined || data.user_id === undefined) {
                            data._token = "";
                            data.user_id = "";
                        }

                        $http({
                            method: method,
                            url: API.ACTION_URL + url,
                            data: data,
                            headers: {
                                'Content-Type' : 'application/x-www-form-urlencoded',
                            },
                            transformRequest: function(obj) {
                                var str = [];
                                for(var p in obj)
                                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                                return str.join("&");
                            }
                        }).then(function (response){
                            d.resolve(response.data);
                        },function (error){
                            d.reject(error);
                        });
                    });
                });
            });

            return d.promise;
        }
    }
}])
.factory("AuthFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
    return {
        AuthLogin : function(data = []) {
            return APIService.MakeRequest("?entity=auth&action=login_member", "POST", data);
        },
        ResetPassword : function(data = []) {
            return APIService.MakeRequest("?entity=auth&action=reset_password", "POST", data);
        }
    }
}])
.factory("UserFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
    return {
        GetUser : async function(data = []) {
            return APIService.MakeRequest("?entity=user&action=get_user", "POST", data);
        },
        CreateUser : function(data = []) {
            return APIService.MakeRequest("?entity=user&action=create_user", "POST", data);
        },
        UpdateUser : function(data = []) {
            return APIService.MakeRequest("?entity=user&action=update_user", "POST", data);
        },
    }
}])
.factory("CreditFactory",  ['$http', '$q', 'APIService', function ($http, $q, APIService) {
    return {
        GetUserPurchaseHistory : async function(data = []) {
            return APIService.MakeRequest("?entity=credit&action=get_purchase_history", "POST", data);
        },
        GetUserCreditHistory : function(data = []) {
            return APIService.MakeRequest("?entity=credit&action=get_credits_history", "POST", data);
        },
        GetUserCurrentCredits : function(data = []) {
            return APIService.MakeRequest("?entity=credit&action=purchase_credits", "POST", data);
        },
        PurchaseCredits : function(data = []) {
            return APIService.MakeRequest("?entity=credit&action=purchase_credits", "POST", data);
        },
    }
}])
.factory("AppExtensionService",  ['$http', '$q', function ($http, $q) {
    return {
        LogoutUser : async function() {
            let toRemove = ['loggedIn', 'auth_user', 'userID', 'gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF'];
            LS.removeItems(toRemove).then(() => {
                window.top.close();
            });
        },
    }
}])
.filter('range', function() {
    return function(input, total) {
        total = parseInt(total);

        for (var i=0; i<total; i++) {
            input.push(i);
        }

        return input;
    };
})
.filter('cut', function () {
    return function (value, wordwise, max, tail) {
        if (!value) return '';

        max = parseInt(max, 10);
        if (!max) return value;
        if (value.length <= max) return value;

        value = value.substr(0, max);
        if (wordwise) {
            var lastspace = value.lastIndexOf(' ');
            if (lastspace !== -1) {
                //Also remove . and , so its gives a cleaner result.
                if (value.charAt(lastspace-1) === '.' || value.charAt(lastspace-1) === ',') {
                    lastspace = lastspace - 1;
                }
                value = value.substr(0, lastspace);
            }
        }

        return value + (tail || ' …');
    };
})
.filter('rename', function() {
    return function(value) {

        var name = '';

        var strings = value.split(/(?=[A-Z])/);

        for (var i = 0; i < strings.length; i++) {
            name += strings[i]+" ";
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        return capitalizeFirstLetter(name);

    }
})

