var translation = null;

var menuItems = [
    {
        title: 'Credits History',
        icon: 'mdi-coin',
        path: 'credits-history.html',
        explanation: 'Credits History'
    },
    {
        title: 'Purchase History',
        icon: 'mdi-receipt',
        path: 'purchase-history.html',
        explanation: 'Purchase History'
    },
    {
        title: 'Purchase Credits',
        icon: 'mdi-cart-plus',
        path: 'purchase-credits.html',
        explanation: 'Purchase Credits'
    },
];

/* Load menu */
$(document).ready(function () {
    $.get('menu.html', function (r) {
        $("#main-wrapper").prepend(r);

        $("#logOut").on('click', function () {
            logOut();
        });

        LS.getItem('loggedIn', function(is_logged_in) {
            if (is_logged_in === undefined) {
                logOut();
            } else {
                for (var i = 0; i < menuItems.length; i++) {
                    $("#sidebarnav").append('' +
                        '<li data-page="menu_items" class="sidebar-item translate-title" title="' + menuItems[i].explanation + '">' +
                        '   <a class="sidebar-link waves-effect waves-dark sidebar-link" href="' + menuItems[i].path + '" aria-expanded="false">' +
                        '       <i class="mdi me-2 ' + menuItems[i].icon + '"></i>' +
                        '       <span data-page="menu_items" class="hide-menu translate-text">' + menuItems[i].title + '</span>' +
                        '   </a>' +
                        '</li>'
                    );
                }

                LS.getItem('auth_user', function(data) {

                    if(data.couponCode) {

                        $("#sidebarnav").append('' +
                            '<li data-page="menu_items" class="sidebar-item translate-title coupon-code" title="Copy to clipboard">' +
                            '   <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#" aria-expanded="false">' +
                            '       <i class="mdi me-2 mdi-ticket"></i>' +
                            '       <span data-page="menu_items" class="hide-menu translate-text">'+data.couponCode+'</span>' +
                            '       <i class="mdi me-2 mdi-content-copy"></i>' +
                            '       <i class="copy-text">Copied!</i>' +
                            '   </a>' +
                            '</li>'
                        );
                    }
    
                });

               

                $('.sidebar-nav ul').css('margin-left', '-20px').css('margin-top', '-10px');
            }
        });
    });


    $(document).on('click', '.sidebar-item.coupon-code', function() {

        var code = $(this).children().children('span').html();

        copyToClipboard(code);

        $('.copy-text').show().fadeOut(1000);

    })

});

function logOut() {
    let toRemove = ['loggedIn', 'auth_user', 'userID', 'gp8YlEeTGqG166QY4IU8815MdeQOxSaHtF'];

    LS.removeItems(toRemove).then(() => {
        window.close();
    });
}

function copyToClipboard(text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
}
