chrome.runtime.onMessage.addListener( function(request, sender, sendResponse) {

    if(request.tabRedirect) {
        chrome.tabs.create({
            url: chrome.runtime.getURL('pages/'+request.tabRedirect)
        });
    }

});