$(document).ready(function (){

    if (window.location.href.indexOf('todays_letter') >= 0) {
        //create button for printing
        chrome.storage.sync.get(['userID', 'loggedIn'], function(items) {

            dispute_aid.properties.userID = items.userID;

            if (items.loggedIn) {
                var btn = '<button style="float:right;margin-top:-30px;margin-right:0px;" align="right" class="green-btn-lined2 font-normal" id="printWithExtension" data-toggle="modal" data-target="#printModal">' +
                    'Print with 1ClickDispute APP</button>';
                $(btn).insertAfter($('#gridData'));

                dispute_aid.prependTryAgain();

                $(document).on('click', '.da-close-option-print-btn',function() {
                    location.reload();
                });

                $("#printWithExtension").on('click', function () {

                    async function processPayload() {

                        // Get the current Cost Settings
                        await dispute_aid.getCosts();

                        dispute_aid.prependStartPrint();

                        //#1 Verify if user is logged in. Get user credits.
                        $('.loader-message').text("Verifying user...");
                        let user = await dispute_aid.verifyUser(items.userID);
                        $('#userCurrentCredits').html(dispute_aid.properties.user.currentCredits);

                        //#2 Scrape CRC letters select. Collect the profile, id , etc.
                        $('.loader-message').text("Collecting letters...");
                        await dispute_aid.collectData();

                        //#3 Prepare payload to be sent in the back-end. Gets the address and letter content.
                        $('.loader-message').text("Getting recipients' address...");
                        await dispute_aid.createPayload();

                        //#4 Append the data to the table
                        $('.loader-message').text("Populating table...");
                        let rows = await dispute_aid.populateTable();
                        $('.dispute-aid-print-table').append(rows);

                        $('.print-loader').hide();
                        $('.loader-message').text("");
                       
                    }

                    processPayload();

                });

                $(document).on('click', '#handlePDFFiles', function(){

                    async function handlePDFFiles() {

                        $('.print-loader').show();
                        //#5 Back end processes the payload. Creates PDF files. Returns total pages of create PDF Files, etc.
                        $('.loader-message').text("Creating PDF Document(s)...");
                        await dispute_aid.handlePDFFiles();

                        //#6 Display the total cost of the letters processed.
                        $('.loader-message').text("Calculating cost...");
                        await dispute_aid.calculateTotal();

                        $('.da-start-print-btn').prop('disabled', false);
                        $('.print-loader').hide();
                        $('.loader-message').text("");

                        $('.start-print-container').show();
                        $('.handle-pdf-container').hide();
                    }

                    handlePDFFiles()

                })

                $(document).on('click', '.purchase-credits', function() {

                    chrome.runtime.sendMessage({tabRedirect: "purchase-credits.html"}, function(response) {
                    });

                })

                $(document).on('click', '.certified-all', function() {
                        
                    var val = $(this).is(":checked");

                    $('.is_certified_cb').prop('checked', val);

                    dispute_aid.calculateTotal();

                });

                $(document).on('click', '.is_certified_cb', function() {
                    dispute_aid.calculateTotal();
                })

                $(document).on('click', '.with_certified_return_receipt', function() {

                    var val = $(this).is(":checked");

                    dispute_aid.properties.options.with_certified_return_receipt = val;

                })

                $(document).on('click', '#restrict_identity_documents', function() {

                    var val = $(this).is(":checked");

                    dispute_aid.properties.options.restrict_identity_documents = val;

                })

                $(document).on('click', '.da-start-print-btn', function() {            

                    $('.print-loader').show();
                    
                    async function printLetter() {

                        //#1 Check if user's credits are enough to cover the over all cost.
                        $('.loader-message').text("Checking current credits...");
                        let response = await dispute_aid.verifyUserCredits();

                        if (response.success) {

                            //#2 Start printing.
                            $('.loader-message').text("Sending letters...");
                            await dispute_aid.printLetter();

                            //#3 Get user current credits.
                            let user = await dispute_aid.verifyUser(items.userID);
                            $('#userCurrentCredits').html(dispute_aid.properties.user.currentCredits);

                            $('.print-loader').hide();
                            $('.loader-message').text();
                            $(this).prop('disabled', true);
                            $('.successPrints').html(dispute_aid.properties.successPrints);
                            $('.failedPrints').html(dispute_aid.properties.failedPrints);
                            $('.cost-data-container').hide();
                            $('.cost-success-container').show();
                            

                        }else{
                            $('.print-error-msg').text(response.msg)
                            $('.print-error-msg').show().fadeOut(5000);
                            $('.print-loader').hide();
                            $('.loader-message').text();
                            $(this).prop('disabled', false);
                        }
        
                    }

                    printLetter();

                })

                $(document).on('click', '.tryAgainButton', function () {

                    var id = $(this).data('id');
                    var desc = $(this).data('message');

                    var senderAddress = dispute_aid.properties.tableData[ id ].address;
                    var recipientAddress = dispute_aid.properties.tableData[ id ].recipient;
                    var pdf = dispute_aid.properties.tableData[ id ].pdf;

                    dispute_aid.properties.failedPrintData = dispute_aid.properties.tableData[ id ];

                    $('.tryAgainWarningMessage').text(desc);
                    $('#tryAgainModal #letterPreview').html(pdf);

                    dispute_aid.autoFillTryAgainDetails(senderAddress, 'Sender');
                    dispute_aid.autoFillTryAgainDetails(recipientAddress, 'Recipient');

                    $('#tryAgainModal').show();
                });

                $("#tryAgainCloseBtn").on('click', function () {
                    $('#tryAgainModal #letterPreview').html("");
                    $('#tryAgainModal').hide();
                    $('.modal-backdrop.show').hide();
                });

                $(document).on('click', '#tryAgainPrintBtn', function() {

                    $('#tryAgainPrintBtn').prop('disabled', true);
                    $('#tryAgainPrintBtn').text("Sending letter...");
                    $('#tryAgainCloseBtn').prop('disabled', true);
                    console.log('Resending...');

                    async function resendLetter() {
                        
                        dispute_aid.properties.failedPrintData.address = {
                            firstName: $("#tryAgainSenderFirstName").val(),
                            lastName: $("#tryAgainSenderLastName").val(),
                            address: $("#tryAgainSenderAddress").val(),
                            city: $("#tryAgainSenderCity").val(),
                            country: $("#tryAgainSenderCountry").val(),
                            state: $("#tryAgainSenderState").val(),
                            zip: $("#tryAgainSenderZIP").val()
                        };

                        dispute_aid.properties.failedPrintData.recipient = {
                            firstName: $("#tryAgainRecipientFirstName").val(),
                            lastName: $("#tryAgainRecipientLastName").val(),
                            address: $("#tryAgainRecipientAddress").val(),
                            city: $("#tryAgainRecipientCity").val(),
                            country: $("#tryAgainRecipientCountry").val(),
                            state: $("#tryAgainRecipientState").val(),
                            zip: $("#tryAgainRecipientZIP").val()
                        }

                        let resend = await dispute_aid.resendLetter();

                        $('#tryAgainPrintBtn').prop('disabled', false);
                        $('#tryAgainPrintBtn').text("Print Letter");
                        $('#tryAgainCloseBtn').prop('disabled', false);  

                    }

                    resendLetter();     

                })
            }
        });
    }
});

