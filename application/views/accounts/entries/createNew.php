<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i><?php echo $main; ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form class="form-inline">
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="Name">Date:</label>
                        <div class="col-sm-8">
                            <input type="date" value="<?php echo date('Y-m-d'); ?>" id="entry_date" name="entry_date" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group"><label class="control-label col-sm-4" for="type">Journal No:<span class="required text-danger">* </span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="journal_no" name="journal_no" placeholder="journal_no" readonly />
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Account</th>
                                    <th style="text-align: center;">Debits</th>
                                    <th style="text-align: center;">Credits</th>
                                    <th style="text-align: center;">Description</th>
                                </tr>
                            </thead>
                            <tbody class="create_table">
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_0" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit0" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit0" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" id="description0" name="description[]">
                                    </td>
                                    <!-- <td>  
                                        <a href="#" class="btn btn-primary bt-xs copyText">
                                        Copy
                                        </a>
                                    </td> -->
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_1" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit1" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit1" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_2" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit2" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit2" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_3" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit3" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit3" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_4" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit4" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit4" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>
                                        <select class="form-control account_id" id="account_id_5" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit5" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit5" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>
                                        <select class="form-control account_id " id="account_id_6" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit6" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit6" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>
                                        <select class="form-control account_id " id="account_id_7" name="account_id[]">
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control debit" id="debit7" name="debit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <input type="text" class="form-control credit" id="credit7" name="credit[]" autocomplete="off">
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- <textarea class="form-control"  ></textarea> -->
                                        <input type="text" class="form-control description" name="description[]">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table_foot">
                                <tr>
                                    <td></td>
                                    <th>Total</th>
                                    <th style="text-align: right;"><input type='text' class="form-control" id='debit_total' disabled /></th>
                                    <th style="text-align: right;"><input type='text' class="form-control" id='credit_total' disabled /></th>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button class="btn btn-info add_new" name="add_new" style="float: right;">Add lines</button>
                    </div>

                    <button class="btn btn-success" name="entry_submit" type="submit" style="float: right;">Save</button>

                </form>
                <!-- END FORM-->
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
<script>
    $(document).ready(function() {

        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const cur_symbol = '<?php echo $_SESSION['home_currency_symbol']; ?>';


        /////////////ADD NEW LINES IN JOURNAL ENTRY
        let counter = 8; //counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
        $('.add_new').on('click', function(event) {
            event.preventDefault();

            counter++;
            var div = '<tr><td>' + counter + '</td><td><select  class="form-control account_id" id="account_id_' + counter + '"  name="account_id[]"></select></td>' +
                '<td style="text-align: right;"><input type="text" class="form-control debit" id="debit' + counter + '" name="debit[]" autocomplete="off"></td>' +
                '<td style="text-align: right;"><input type="text" class="form-control credit" id="credit' + counter + '" name="credit[]" autocomplete="off"></td>' +
                '<td style="text-align: right;"><input type="text" class="form-control description" name="description[]"  ></td></tr>';
            $('.create_table').append(div);

            accountsDDL(counter);
            debit_keypress();
            credit_keypress();
            //SELECT 2 DROPDOWN LIST
            //$('.select2').select2();
            ///
        });
        //$( ".add_new" ).trigger( "click" );//ADD NEW LINE WHEN PAGE LOAD BY DEFAULT

        /////////////////////////////////

        debit_keypress();
        // we used jQuery 'keypress' to trigger the computation as the user type
        function debit_keypress() {
            $('.debit').keyup(function() {


                // initialize the sum (total price) to zero
                var sum = 0;

                // we use jQuery each() to loop through all the textbox with 'price' class
                // and compute the sum for each loop
                $('.debit').each(function() {
                    sum += Number($(this).val());
                });

                // set the computed value to 'totalPrice' textbox
                $('#debit_total').val(sum.toFixed(2));

                if ($(this).val().length > 0) {
                    //DISABLE THE OPPOSITE TEXTBOX
                    var debit_str = $(this).attr('id');
                    $("#credit" + debit_str.substring(5)).prop('readonly', true);
                    //////
                } else if ($(this).val().length <= 0) {
                    //DISABLE THE OPPOSITE TEXTBOX
                    var debit_str = $(this).attr('id');
                    $("#credit" + debit_str.substring(5)).prop('readonly', false);
                    //////
                }
            });

        }

        credit_keypress();
        // we used jQuery 'keypress' to trigger the computation as the user type
        function credit_keypress() {
            $('.credit').keyup(function() {

                // initialize the sum (total price) to zero
                var sum = 0;

                // we use jQuery each() to loop through all the textbox with 'price' class
                // and compute the sum for each loop
                $('.credit').each(function() {
                    sum += Number($(this).val());
                });

                // set the computed value to 'totalPrice' textbox
                $('#credit_total').val(sum.toFixed(2));

                console.log($(this).val().length);
                //DISABLE THE OPPOSITE TEXTBOX
                if ($(this).val().length > 0) {
                    var credit_str = $(this).attr('id');
                    $("#debit" + credit_str.substring(6)).prop('readonly', true);

                } else if ($(this).val().length <= 0) {
                    var credit_str = $(this).attr('id');
                    $("#debit" + credit_str.substring(6)).prop('readonly', false);

                }
                //////

            });
        }


        ////////////////////////
        //GET JOURNAL NO
        $.ajax({
            url: site_url + "accounts/C_entries/FetchMaxInvoice",
            type: 'GET',
            //dataType: 'json', // added data type
            success: function(data) {
                console.log(data);
                //var j_no = (data == '' ? 0 : parseInt(data)) + 1;

                // var d = new Date();
                let journal_no = data;
                $('#journal_no').val(journal_no);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
        ///////////////////
        accountsDDL(0);
        accountsDDL(1);
        accountsDDL(2);
        accountsDDL(3);
        accountsDDL(4);
        accountsDDL(5);
        accountsDDL(6);
        accountsDDL(7);
        ////////////////////////
        //GET Accounts DROPDOWN LIST
        function accountsDDL(index = 0) {
            const site_url = '<?php echo site_url($langs); ?>/';
            let accounts_ddl = '';
            var account_type = ['liability', 'equity', 'cos', 'revenue', 'expense', 'asset'];
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {
                    account_types: account_type
                },
                cache: true,
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    accounts_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index1, value) {

                        accounts_ddl += '<option value="' + value.account_code + '">' + value.title + '</option>';

                    });

                    $('#account_id_' + index).html(accounts_ddl);
                    //$('.account_id_2').html(accounts_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////

        /////////////////////////////////
        //SAVE JOURNAL ENTRY FORM
        $("form").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();

            /* Serialize the submitted form control values to be sent to the web server with the request */
            var formValues = $(this).serialize();

            if ($('#journal_no').val() == '') {
                toastr.error("Please Enter Journal No", 'Error!');
            } else if ($('.account_id').val() == 0) {
                toastr.error("Please Select Account", 'Error!');
            } else if ($('.description').val() == '') {
                toastr.error("Please Enter Description", 'Error!');
            } else if ($('#debit_total').val() !== $('#credit_total').val()) {
                toastr.error("Debit and Credit total are not equal", 'Error!');
            } else {
                // Send the form data using post
                $.post(site_url + "accounts/C_entries/addEntry", formValues, function(data) {
                    // Display the returned data in browser
                    //$("#result").html(data);
                    if (data == '1') {
                        toastr.success("Entry saved successfully", 'Success');

                        setTimeout(function() {
                            location.reload();
                        }, 2000);

                    } else {
                        toastr.error("Entry not saved", 'Error');

                    }



                });
            }
        });
        /////////////////////////////////
        $('.copyText').on('click', function(event) {
            event.preventDefault();
            var copyText = document.getElementById("description0");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            console.log('Copied');
            toastr.success("Copied", 'Success');

            ///
        });
        /////////////////////////////////

    });

    function outFunc() {
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "Copy to clipboard";
    }
</script>