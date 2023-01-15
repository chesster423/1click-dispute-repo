var dispute_aid = {
    urls : {
        profile: 'https://app.creditrepaircloud.com/userdesk/profile/',
        server: (false) ? 'http://localhost/1clickdispute/action.php' :'https://app.1clickdispute.com/action.php',
        previewLetter: 'https://app.creditrepaircloud.com/everything/preview_letter',
        local : 'http://localhost/1clickdispute/action.php',
        deleteLetter : 'https://app.creditrepaircloud.com/everything/print_letter_ids',
        member_login : 'https://app.1clickdispute.com/member/login.php?redirectToRC=1'
    },
    properties : {
        appTitle: '1ClickDispute',
        usersWithLettersToPrint : [],
        printProgress : 0,
        usersWithLettersToPrintTotal : 0,
        userLetterID : null,
        payloadData : [],
        tableData : [],
        options : {
            letter_type : null,
            mail_type: null,
            with_certified_return_receipt : false,
            restrict_identity_documents : false,
            upload_additional_documentation : false,
        },
        userID : null,
        successPrints : 0,
        failedPrints: 0,
        failedPrintData: {},
        failedPrintsTriggersInitiated: false,
        mostUsedAddresses: {
            default: {
                firstName: '',
                lastName: '',
                address: '',
                city: '',
                country: '',
                state: '',
                zip: ''
            },
            equifax: {
                firstName: 'Equifax',
                lastName: 'Information Services LLC',
                address: 'P.O. Box 740256',
                city: 'Atlanta',
                country: 'United States',
                state: 'GA',
                zip: '30374'
            },
            experian: {
                firstName: 'Experian',
                lastName: ' ',
                address: 'P.O. Box 4500',
                city: 'Allen',
                country: 'United States',
                state: 'TX',
                zip: '75013'
            },
            transunion: {
                firstName: 'TransUnion',
                lastName: 'Consumer Solutions',
                address: 'P.O. Box 2000',
                city: 'Chester',
                country: 'United States',
                state: 'PA',
                zip: '19016'
            },
        },
        uploadedFiles : [],
        active_mail : 'lob',
        user : {},
        costs : {},
        appDescription : {
            desc1 : {
                main: function(data) {
                    return 'First Class: from '+ data +' credit /each {black & white}';
                },
                sub: '4-6 days typical delivery time. Depending on the destination.',
            },
            desc2 : {
                main: function(data) {
                    return 'Certified: from '+ data +' credit /each (black & white)'
                },
                sub: '4-6 days typical delivery time. Certified Letters provide timestamped proof of delivery.',
            },          
            desc3 : {
                main: function(data) {
                    return 'Letters over 12 pages will have an additional costs of <strong>'+data+'</strong> credits'
                },
            }        
        },
        standardCost : 0.80,
        certifiedCost : 5.97,
        costPerPage : 0.12,
        additionalCost : 2.35,
    },
    autoFillTryAgainDetails: function (data, type) {
        $("#tryAgain" + type + "FirstName").val(data.firstName);
        $("#tryAgain" + type + "LastName").val(data.lastName);
        $("#tryAgain" + type + "Address").val(data.address);
        $("#tryAgain" + type + "City").val(data.city);
        $("#tryAgain" + type + "Country").val(data.country);
        $("#tryAgain" + type + "State").val(data.state);
        $("#tryAgain" + type + "ZIP").val(data.zip);
    },
    prependTryAgain : function() {

        $('body').prepend(
            '<div id="tryAgainModal" class="modal" tabindex="-1" style="margin: 0 auto;width: 75%;height: auto;background: #fff;top: 20px;position: absolute; left: 12%; padding: 12px; border-radius: 8px;padding-bottom: 12px; display: none;">' +
            '    <div class="modal-dialog" style="margin: unset; max-width: 100% !important; width: 100%;">' +
            '        <div class="modal-content">' +
            '            <div class="modal-header justify-content-center" style="padding: 12px !important;">' +
            '                <h3 style="font-weight: 600; padding: 5px 10px;font-size: 1.75rem;background: #f2f2f2;width: 100%;">Print with '+dispute_aid.properties.appTitle+'</h3>' +            
            '            </div>' +
            '            <div class="modal-body" style="padding-bottom: 0px !important;">' +
            '               <div class="row">' +
            '                   <div class="col-md-12">' +
            '                       <label style="color: #ed4040;" class="tryAgainWarningMessage"></label>'+
            '                   </div>' +
            '                   <div class="col-md-4">' +
            '                       <h4 class="text-center">Sender Address</h4>' +
            '                       First Name<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderFirstName">' +
            '                        Last Name<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderLastName">' +
            '                       Address<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderAddress">' +
            '                       Country<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderCountry">' +
            '                       City<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderCity">' +
            '                       State<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderState">' +
            '                       ZIP<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainSenderZIP">' +
            '                   </div>' +
            '                   <div class="col-md-4">' +
            '                       <h4 class="text-center">Recipient Address</h4>' +
            '                       Select Recipient<br>' +
            '                       <select id="recipientAddress" style="height: 37px; width: 100%">' +
            '                           <option value="">Please Select...</option>' +
            '                           <option value="experian">Experian</option>' +
            '                           <option value="transunion">TransUnion</option>' +
            '                           <option value="equifax">Equifax</option>' +
            '                           <option value="other">Other</option>' +
            '                       </select>' +
            '                       First Name<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientFirstName">' +
            '                       Last Name<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientLastName">' +
            '                       Address<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientAddress">' +
            '                       Country<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientCountry">' +
            '                       City<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientCity">' +
            '                       State<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientState">' +
            '                       ZIP<br>' +
            '                       <input style="height: 37px;" class="form-control" id="tryAgainRecipientZIP">' +
            '                       <p id="tryAgainStatus" style="font-size: 16px"></p>' +
            '                   </div>' +
            '                   <div class="col-md-4">' +
            '                       <h4 class="text-center">Letter Preview</h4>' +
            '                       <div style="background: #f2f2f2;overflow: scroll;height: 450px;padding: 20px;margin-top: 30px;" id="letterPreview"></div>' +
            '                   </div>' +
            '               </div>' +
            '            </div>' +
            '            <div class="modal-footer" style="float: right; padding-right: 24px">' +
            '                <label style="color: #ed4040; text-align: left; position: absolute; margin-top: -60px; display: none;" class="resendPrintErrorMessage"></label>'+
            '                <button id="tryAgainPrintBtn" style="background: #00a650; color: white; padding: 8px; border-radius: 5px; margin-right: 10px; border: 0;">Print Letter</button>' +
            '                <button id="tryAgainCloseBtn" style="background: #f2f2f2; color: black; border-radius: 5px; padding: 8px; border: 0;">Close</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</div>'
        );

    },
    prependStartPrint : function() {

        $('body').prepend(
            '<style>.dispute-aid-print-table tr th, td{padding: 10px 0px 10px 10px;}.dispute-aid-print-table tr td a{color:#2855b2;}</style>'+
            '<div class="print-options-da-backdrop" style="width:100%; height: 100%; background: rgba(0, 0, 0, .6); position: absolute; z-index: 999;" ng-app="dispute-aid-print">'+
                '<div class="dispute-aid-modal" ng-controller="UserController" style="margin: 0 auto;width: 60%;min-width:500px;height: auto;background: #fff;top: 3%;position: relative; padding: 12px; border-radius: 8px;">'+            
                    '<div class="da-container">'+
                        '<div class="da-content">'+
                            '<div class="da-header" style="padding: 5px 10px; background: #f2f2f2; padding-bottom: 20px;">'+
                                '<a class="da-close-option-print-btn" style="background: #f2f2f2; color: black; border-radius: 5px; padding: 8px; float: right; cursor: pointer;">Close</a>'+
                                '<h3>Print with '+dispute_aid.properties.appTitle+'</h3>'+                              
                                'Credits: <span id="userCurrentCredits" style="background: #3dd549; padding: 2px 10px; border-radius: 99px; color: #fff;">0<span>'+
                            '</div>'+
                            '<div class="da-body" style="padding: 20px 10px; display: block;">'+
                                '<div class="print-loader" style="position: absolute; width: 100%; height: 100%; background: #fff; z-index: 999; opacity: .5; top: 0; left: 0;">'+
                                   '<center><img class="print-loader" src="../img/ajax-loader.gif"><br><label class="loader-message"></label></center>'+
                                '</div>'+
                                '<h5 style="float: left;">Confirm Letter Sending Options</h5>'+
                                '<span style="float:right; margin-top: 15px;">'+
                                    '<input type="checkbox" id="restrict_identity_documents" style="position: absolute;margin-top: 5px; margin-left: -15px;">Do not send identity documents'+
                                '</span>' +
                                '<table class="dispute-aid-print-table" style="width: 100%;border: 1px solid #f2f2f2; margin-top: 10px;">'+
                                    '<thead>'+
                                        '<tr style="background: #ebf6f8;">'+
                                            '<th>Name</th>'+
                                            '<th>Letter to</th>'+
                                            '<th>File</th>'+
                                            '<th>Pages</th>'+
                                            '<th>Certified <input type="checkbox" class="certified-all" style="position: absolute;margin-top: 5px; margin-left: 7px;"></th>'+
                                            '<th>Total cost</th>'+
                                            '<th>Status</th>'+
                                        '</tr>'+
                                    '</thead>'+
                                    '<tbody>'+
                                    '</tbody>'+
                                '</table>'+                             
                            '</div>'+
                            '<div style="padding-left: 10px; margin-bottom: 15px;">'+
                                '<h5>'+dispute_aid.properties.appTitle+' App Options</h5>'+
                            '</div>'+
                            '<div style="display: flex;">'+
                                '<div class="da-body cost-container-instructions" style="padding: 5px 10px 45px 10px; justify-content: left; display: flex;">'+
                                    '<div style="padding-right: 10px;">'+  
                                        '<strong>'+dispute_aid.properties.appDescription.desc1.main(dispute_aid.properties.standardCost)+'</strong>'+
                                        '<p>'+dispute_aid.properties.appDescription.desc1.sub+'</p>'+
                                        '<p style="margin-top: 40px;">'+dispute_aid.properties.appDescription.desc3.main(dispute_aid.properties.additionalCost)+'</p>'+
                                    '</div>'+ 
                                    '<div>'+
                                        '<strong>'+dispute_aid.properties.appDescription.desc2.main(dispute_aid.properties.certifiedCost)+'</strong>'+
                                        '<p>'+dispute_aid.properties.appDescription.desc2.sub+'</p'+
                                    '</div>'+                             
                                '</div>'+
                                '<div class="da-body cost-container" style="padding: 0px 10px 45px 10px; justify-content: right; width: 45%;">'+
                                    '<div class="cost-success-container" style="border: 1px solid #ccc; padding: 20px; width: 100%;display: none;">'+
                                        '<div style="text-align: center;">'+
                                            '<h2>Printing finished!</h2>'+
                                            '<p style="color: #6bef63; margin-bottom: 0px;"><span class="successPrints"></span> Successful print(s).</p>'+
                                            '<p style="color: #ed4040;"><span class="failedPrints"></span> Failed print(s).</p>'+
                                            '<p>You can now close the window.</p>'+
                                        '</div>'+                                        
                                    '</div>'+  
                                    '<div class="cost-data-container handle-pdf-container" style="border: 1px solid #ccc; padding: 20px; width: 100%;">'+
                                        '<button id="handlePDFFiles" style="border: 0; background: #3256b5; color: white; padding: 8px; border-radius: 5px; display: block; text-align: center; width: 100%;">Process Letters</button>'+
                                    '</div>'+   
                                    '<div class="cost-data-container start-print-container" style="border: 1px solid #ccc; padding: 20px; width: 100%; display: none;">'+
                                        '<div style="justify-content: space-between; display: flex; font-size: 15px;">'+
                                            '<div><strong>Standard Total Cost</strong></div>'+
                                            '<div><span class="standard-cost">0</span> Credits</div>'+
                                        '</div>'+
                                        '<div style="justify-content: space-between; display: flex; font-size: 15px;">'+
                                            '<div><strong>Certified Total Cost</strong></div>'+
                                            '<div><span class="certified-cost">0</span> Credits</div>'+
                                        '</div>'+
                                        '<hr>'+
                                        '<div style="justify-content: space-between; display: flex; font-size: 16px;">'+
                                            '<div><strong>Grand Total</strong></div>'+
                                            '<div><strong class="grand-total">0</strong> Credits</div>'+
                                        '</div>'+
                                        '<div>'+
                                            '<div class="printing-warning" style=" display: none; text-align: center;">'+
                                                '<p style="color: #f12c2c; margin-bottom: 0px;">Insufficient credits.</p>'+
                                                '<p class="purchase-credits" style="color: #2855b2; cursor: pointer;">Click here to purchase credits.</p>'+
                                            '</div>'+
                                        '</div>'+
                                        '<button class="da-start-print-btn" style="border: 0; background: #00a650; color: white; padding: 8px; border-radius: 5px; display: block; text-align: center; width: 100%; margin-top: 10px;" disabled="true">Start printing</button>'+
                                        '<p style="color: #f12c2c; margin-bottom: 0px; display: none; margin-top: 10px; text-align: center;" class="print-error-msg"></p>'+
                                    '</div>'+                           
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>');

        if (dispute_aid.properties.usersWithLettersToPrintTotal > 1)
            $("#upload_additional_documentation_parent").hide();

        $("#fileupload").fileupload({
            url: dispute_aid.urls.server+"?entity=lob&action=upload_additional_files",
            dropZone: '#dropZone',
            dataType: 'json',
            autoUpload: false
        }).on('fileuploadadd', function (e, data) {
            var fileTypeAllowed = /.\.(jpg|png|jpeg)$/i;
            var fileName = data.originalFiles[0]['name'];
            var fileSize = data.originalFiles[0]['size'];

            if (!fileTypeAllowed.test(fileName))
                $("#error").html('Only images are allowed!');
            else if (fileSize > 1000000)
                $("#error").html('Your file is too big! Max allowed size is: 1MB');
            else {
                $("#error").html("");
                data.submit();
            }
        }).on('fileuploaddone', function(e, data) {
            var status = data.jqXHR.responseJSON.status;
            var msg = data.jqXHR.responseJSON.msg;

            if (data.jqXHR.responseJSON.success) {
                dispute_aid.properties.uploadedFiles.push(data.jqXHR.responseJSON.filename);
            }else{
                $('#error').html(data.jqXHR.responseJSON.msg);
                $("#progress").html("");
            }

        }).on('fileuploadprogressall', function(e,data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);

            $("#progress").html("Completed: " + progress + "%");

        });

    },
    getLetter : function(letterID, callback) {
        $.post(dispute_aid.urls.previewLetter, {
            lid: letterID,
            doc: 1,
            round: 1
        }, function (letter) {
            callback(letter);
        });
    },
    getAddress : function(profileID, callback) {
        $.get(dispute_aid.urls.profile + profileID, function (r) {

            var selectedCountry = dispute_aid.getBetween(r, 'id="country" value="', '"');

            var address = {
                firstName: dispute_aid.getBetween(r, 'id="fname"  value="', '"'),
                lastName: dispute_aid.getBetween(r, 'id="lname" value="', '"'),
                city: dispute_aid.getBetween(r, 'id="city" value="', '"'),
                state: dispute_aid.getBetween(r, 'id="state"  class="form-control" value="', '"'),
                country: dispute_aid.getBetween(r, '<option value=\''+selectedCountry+'\' selected>', '</option>'),
                zip: dispute_aid.getBetween(r, 'id="pcode" class="form-control" onpaste="return false;" maxlength="6" value="', '"'),
                fax: '', //dispute_aid.getBetween(r, 'id="fax" class="input" value="', '"'),
                address: dispute_aid.getBetween(r, 'onFocus="callGooglePlacesAPI()" value="', '"')
            };

            if (address.firstName === '')
                address.firstName = dispute_aid.getBetween(r, 'id="fname" value="', '"');

            if (address.state === '')
                address.state = dispute_aid.getBetween(r, 'id="state" class="form-control" value="', '"');

            callback(address);
        });
    },
    getBetween : function(str, start, end) {
        try {
            return str.split(start)[1].split(end)[0];
        } catch (e) {}

        return "";
    },
    deleteLetter : function(letterID) {
        if (localStorage.getItem('devTest') !== null)
            return;

        console.log('Deleting letter: ' + letterID);

        $.post(dispute_aid.urls.deleteLetter, {
            letter_id: letterID,
        }, function (letter) {
            console.log(letter);
            // callback(letter);
        });
    },
    sendLetter : function(data, callback) {

        $.post(dispute_aid.urls.server+"?entity=lob&action=send_letter", data, function (r) {
            callback(r);
        }).fail( function(r){
            dispute_aid.sendLetter(data, callback);
        });

    },
    createPDFDocument : function(data, callback) {

        $.post(dispute_aid.urls.server+"?entity=lob&action=create_pdf_files", data, function (r) {
            callback(r);
        }).fail( function(r){
            dispute_aid.createPDFDocument(data, callback);
        });

    },
    getUserData : function(data, callback) {

        $.post(dispute_aid.urls.server+"?entity=user&action=get_user", data, function (r) {
            callback(r);
        }).fail( function(r){
            dispute_aid.getUserData(data, callback);
        });

    },
    getCostSettings : function(callback) {
        $.get(dispute_aid.urls.server+"?entity=setting&action=get_cost_settings", function (letter) {
            callback(letter);
        });
    },    
    createTransactionCode : function(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    },
    verifyUser : function(id) {

        return new Promise(resolve => {

            let payload = {
                id : id
            }

            dispute_aid.getUserData(payload, function(response) {

                let r = JSON.parse(response);
                
                dispute_aid.properties.user = r.data;                

                resolve(dispute_aid.properties.user);

            });
            
        })

    },
    getCosts : function(id) {

        return new Promise(resolve => {

            dispute_aid.getCostSettings(function(response) {

                let r = JSON.parse(response);
                
                dispute_aid.properties.standardCost = parseFloat(r.data.initialCost);
                dispute_aid.properties.costPerPage = parseFloat(r.data.costPerPage);                
                dispute_aid.properties.certifiedCost = parseFloat(r.data.certifiedCost);
                dispute_aid.properties.additionalCost = parseFloat(r.data.additionalCost);

                resolve();

            });
            
        })

    },
    collectData : function() {

        return new Promise(resolve => {

            dispute_aid.properties.usersWithLettersToPrint = [];
            dispute_aid.properties.usersWithLettersToPrintTotal = 0;

            $('#gridData input[type="checkbox"]').each(function() {
                if ($(this).prop('checked') && $(this).attr('id') !== 'check_all_letter') {
                    var mainRow = $(this).closest('tr');
                    var letterID = $(this).attr('value');//mainRow.find('a[onclick^="return preview_letter_selected"]').attr('onclick').match(/[0-9]+/g)[0];
                    var editLetterBtn = mainRow.find('a[onclick^="edit_letter("]:eq(0)');
                    var profileID = editLetterBtn.attr('onclick').split(",'")[1].split("'")[0];
                    var toAddress = editLetterBtn.text().toLowerCase();
                    var recipient = null;

                    if (toAddress.indexOf('transunion') >= 0) {
                        recipient = dispute_aid.properties.mostUsedAddresses.transunion;
                    } else if (toAddress.indexOf('equifax') >= 0) {
                        recipient = dispute_aid.properties.mostUsedAddresses.equifax;
                    } else if (toAddress.indexOf('experian') >= 0) {
                        recipient = dispute_aid.properties.mostUsedAddresses.experian;
                    } else {
                        recipient = dispute_aid.properties.mostUsedAddresses.default;
                    }

                    mainRow.attr('id', 'pdf_'+letterID);
                    dispute_aid.properties.usersWithLettersToPrint.push({ letterID: letterID, profileID: profileID, recipient: recipient });
                    dispute_aid.properties.usersWithLettersToPrintTotal++;
                }
            });

            resolve(dispute_aid.properties.usersWithLettersToPrint);

        })

    },
    createPayload : function() {

        return new Promise(resolve => {

            function populateData() {
                var v = dispute_aid.properties.usersWithLettersToPrint.pop();

                if(v) {

                    dispute_aid.getAddress(v.profileID, function (address) {

                        dispute_aid.getLetter(v.letterID, function (letter) {

                            let transaction_code = dispute_aid.createTransactionCode(20)+"-"+dispute_aid.properties.userID;

                            let payload = {
                                letterID: v.letterID,
                                pdf: letter,
                                address: address,
                                options : dispute_aid.properties.options,
                                user_id : dispute_aid.properties.userID,
                                transaction_code : transaction_code,
                                uploaded_files : dispute_aid.properties.uploadedFiles,
                                recipient: v.recipient
                            };

                            dispute_aid.properties.payloadData.push(payload);
                            dispute_aid.properties.tableData[ v.letterID ] = payload;

                            populateData();
                
                        })
                    });
                }else{
                    resolve(dispute_aid.properties.payloadData);
                }
            }

            populateData();

        })
        
    },
    populateTable : function() {

        return new Promise(resolve => {

            console.log('Payload received.');
            $('.dispute-aid-print-table tbody').html('');

            var rows = '';

            dispute_aid.properties.tableData.forEach((v, k) => {

                const name = v.address.firstName + ' ' + v.address.lastName;
                
                rows += '<tr id="da-row-'+k+'" data-id="'+k+'">'+
                    '<td style="border: 1px solid #f2f2f2;" class="name">'+name+'</td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="toName">'+v.recipient.firstName+'</td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="file"><a href="" target="_blank"></a></td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="pages"></td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="is_certified"><input type="checkbox" class="is_certified_cb" id="certified-'+k+'"></td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="cost">'+
                        '<span class="letter-cost">0</span> credit(s) <span class="add-cost" style="color: #3dd549; opacity: 0; position: absolute;">+'+dispute_aid.properties.certifiedCost+'</span>'+
                    '</td>'+
                    '<td style="border: 1px solid #f2f2f2;" class="status"></td>'+
                '</tr>';
            })

            resolve(rows)

        })

    },
    handlePDFFiles : function() {

        return new Promise(resolve => {

            function processPDFDocument() {

                var v = dispute_aid.properties.payloadData.pop();

                if(v) {
                        
                    dispute_aid.createPDFDocument(v, function (response) {

                        var r = JSON.parse(response);

                        let letterID = r.letterID;

                        if (r.success) {
                
                            $('#da-row-'+letterID+' td.file a').attr('href', r.file_url);
                            $('#da-row-'+letterID+' td.file a').text('View file');
                            $('#da-row-'+letterID+' td.pages').html(r.document.total_pages);
                            $('#da-row-'+letterID+' td.cost span.letter-cost').html(r.document.cost);
                            $('#da-row-'+letterID+' td.status').html('Pending');

                            dispute_aid.properties.tableData[ letterID ].totalPages = r.document.total_pages;
                            dispute_aid.properties.tableData[ letterID ].fileUrl = r.file_url;
                            dispute_aid.properties.tableData[ letterID ].cost = r.document.cost;

                        }else{
                            $('#da-row-'+letterID+' td.status').html(r.msg);
                        }
         
                        processPDFDocument();

                    });

                }else{
                    resolve();
                }
            }

            processPDFDocument();

        })

    },
    calculateTotal : function() {

        return new Promise(resolve => {

            let totalCosts = {
                standard : 0,
                certified : 0,
                grandTotal : 0,
            }

            $( "input.is_certified_cb").each(function() {

                var letterID = $(this).parent().parent().data('id');
                let totalPages = parseInt( dispute_aid.properties.tableData[ letterID ].totalPages );

                if ($(this).is(":checked")) {

                    var newCost = dispute_aid.calculateDocumentCostTotal(totalPages, true);
                    $('#da-row-'+letterID+' td.cost span.add-cost').animate({opacity: 1}, 100);
                    $('#da-row-'+letterID+' td.cost span.add-cost').animate({opacity: 0}, 2000);
                    $('#da-row-'+letterID+' td.cost span.letter-cost').html(newCost);

                    dispute_aid.properties.tableData[ letterID ].isCertified = 'yes'; 

                    totalCosts.certified = (parseFloat(totalCosts.certified) + parseFloat(newCost)).toFixed(2);
                    
                }else{

                    dispute_aid.properties.tableData[ letterID ].isCertified = 'no';
  
                    var origCost = dispute_aid.calculateDocumentCostTotal(totalPages, false);

                    totalCosts.standard = (parseFloat(totalCosts.standard) + parseFloat(origCost)).toFixed(2);             

                    $('#da-row-'+letterID+' td.cost span.letter-cost').html(origCost); 
                }

            });

            totalCosts.grandTotal = (parseFloat(totalCosts.standard)+parseFloat(totalCosts.certified)).toFixed(2);
            dispute_aid.costs = totalCosts;

            $('.cost-container .standard-cost').html(totalCosts.standard);
            $('.cost-container .certified-cost').html(totalCosts.certified);
            $('.cost-container .grand-total').html(totalCosts.grandTotal);

            if (parseFloat(totalCosts.grandTotal) > parseFloat(dispute_aid.properties.user.currentCredits)) {
                $('.printing-warning').show();
            }

            resolve(totalCosts);
        })

    },
    calculateDocumentCostTotal : function(totalPages, isCertified) {

        let cost = 0;

        let initialCost = dispute_aid.properties.standardCost;

        if (isCertified) {
            initialCost = dispute_aid.properties.certifiedCost;
        }

        cost = initialCost + ( (totalPages - 1)  * dispute_aid.properties.costPerPage );

        if (totalPages >= 12) {
            cost += dispute_aid.properties.additionalCost;
        }

        return parseFloat(cost.toFixed(2));
    },
    verifyUserCredits : function() {

        return new Promise(resolve => {

            function verifyUserCredits() {

                let payload = {
                    user_id : dispute_aid.properties.userID,
                    totalCreditCost : dispute_aid.costs.grandTotal,
                    verifyUserCredits : true
                }

                dispute_aid.sendLetter(payload, function (response) {

                    var r = JSON.parse(response);

                    resolve(r);

                });
                
            }

            verifyUserCredits();

        })

    },
    printLetter : function() {

        var letters = [];
        for (var key in dispute_aid.properties.tableData) {
            letters.push(dispute_aid.properties.tableData[key]);
        }

        return new Promise(resolve => {

            function sendLetter() {

                var v = letters.pop();

                if(v) {

                    v.with_certified_return_receipt = dispute_aid.properties.options.with_certified_return_receipt;
                        
                    dispute_aid.sendLetter(v, function (response) {

                        var r = JSON.parse(response);

                        if(!r.success && r.error_type == 'payment_declined') {                        
                            $('.print-error-msg').text(r.msg)
                            $('.print-error-msg').show().fadeOut(5000);
                            resolve();
                        }

                        let letterID = r.letterID;

                        if (r.success) {
                    
                            dispute_aid.deleteLetter(letterID);
                            dispute_aid.properties.successPrints += 1;
                            $('#da-row-'+letterID+' td.status').html('<a style="color: #6bef63;">'+r.msg+'</a>');
                                                        
                        }else{

                            dispute_aid.properties.failedPrints += 1;

                            let tryAgain = '<a style="color: #ed4040; cursor: pointer;" class="tryAgainButton" data-toggle="modal" data-target="#tryAgainModal" data-id="'+letterID+'" data-message="'+r.msg+'">Try Again</a>'
                            $('#da-row-'+letterID+' td.status').html(tryAgain);                            
                        }

                        sendLetter();

                    });

                }else{                    
                    resolve();
                }
            }

            sendLetter();

        })

    },
    resendLetter : function(data) {

        return new Promise(resolve => {

            let data = dispute_aid.properties.failedPrintData;
                
            dispute_aid.sendLetter(data, function (response) {

                var r = JSON.parse(response);

                if(!r.success && r.error_type == 'payment_declined') {
                    $('.resendPrintErrorMessage').text(r.msg);
                    $('.resendPrintErrorMessage').show().fadeOut();
                    resolve();
                }

                let letterID = r.letterID;

                if (r.success) {

                    dispute_aid.deleteLetter(letterID);
                    $('#da-row-'+letterID+' td.status').html('<a style="color: #6bef63;">'+r.msg+'</a>');
                    $('#tryAgainModal').hide();
                    $('.modal-backdrop.show').hide();                
                                
                }else{
                    $('.resendPrintErrorMessage').text(r.msg);
                    $('.resendPrintErrorMessage').show().fadeOut(5000);                          
                }

                resolve(r.success);

            });

        })

    }
}
