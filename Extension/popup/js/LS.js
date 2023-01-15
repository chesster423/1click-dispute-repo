const LS = {
    getAllItems: async function() {
        return await chrome.storage.local.get();
    },
    getItem: function(key, callback) {
        var response = "";

        chrome.storage.local.get(key, function(result) {
            response = (LS.isPromise(result) || LS.isEmptyObject(result)) ? undefined : result[key];
            callback(response);
        });
    },
    setItem: function(key, val) {
        var toSave = {};
        toSave[key] = val;

        chrome.storage.local.set(toSave, () => {
            if (chrome.runtime.lastError)
                console.log('Error setting');
        });
    },
    removeItems: async function(keys) {
        await chrome.storage.local.remove(keys);
    },
    isPromise: (p) => {
        return p && Object.prototype.toString.call(p) === "[object Promise]";
    },
    isEmptyObject: (obj) => {
        return JSON.stringify(obj) === JSON.stringify({});
    }
}

