<div class="row">
    <div class="col-sm-12">
        <?php
        if ($this->session->flashdata('message')) {
            echo "<div class='alert alert-success fade in'>";
            echo $this->session->flashdata('message');
            echo '</div>';
        }
        if ($this->session->flashdata('error')) {
            echo "<div class='alert alert-danger fade in'>";
            echo $this->session->flashdata('error');
            echo '</div>';
        }

        ?>
        <p>
            <button class="btn btn-success" id="link-button">Add bank</button>
        </p><!-- BEGIN ACCORDION PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>Banks
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a> -->
                    <!-- <a href="javascript:;" class="reload"></a> -->
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                <h2><?php echo count($plaidItems); ?> Banks Linked</h2>
                Below is a list of all your connected banks. Click on a bank to view its associated accounts.
                </p>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (count($plaidItems) > 0) {
                            foreach ($plaidItems as $values) {
                        ?>
                                <!-- BEGIN Portlet PORTLET-->
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-bank"></i><?php echo $values['institution_name']; ?>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="expand"></a>
                                            <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a>
                                        <a href="javascript:;" class="reload"></a> -->
                                        </div>
                                        <div class="actions">
                                            <a href="#" class="refresh_transactions btn btn-success btn-sm" id="<?php echo '_' . $values['plaid_item_id'] ?>"><i class="fa fa-refresh"></i> Refresh</a>
                                            <a href="<?php echo site_url($langs) . '/banking/C_connections/remove_plaid_item/' . $values['plaid_item_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure, you want to delete?')"><i class="fa fa-trash-o"></i> Remove</a>
                                        </div>
                                    </div>
                                    <div class="portlet-body display-hide">

                                        <?php
                                        $palid_accounts = $this->M_institution->retrieveAccountsByItemID($values['plaid_item_id']);

                                        foreach ($palid_accounts as $palid_accounts_values) {
                                            echo '<table class="table">';
                                            echo '<tbody>';
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $palid_accounts_values['name'];
                                            echo '</br>';
                                            echo ucfirst($palid_accounts_values['subtype']) . ' • Balance ' . $palid_accounts_values['iso_currency_code'] . ' ' . number_format($palid_accounts_values['current_balance'], 2);
                                            echo '</td>';
                                            echo '<td class="text-right">';
                                            echo '<a href="#" class="view_transactions btn btn-default" id="viewTransactions_' . $palid_accounts_values['plaid_account_id'] . '">View Transactions</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                            echo '</tbody>';
                                            echo '</table>';
                                            echo '<div class="note note-success trans_note" id="note_' . $palid_accounts_values['plaid_account_id'] . '"></div>';
                                            echo '<div id="transactions_table_' . $palid_accounts_values['plaid_account_id'] . '"></div>';
                                        }

                                        ?>
                                        <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif") ?>" alt="loader"></div>
                                    </div>
                                </div>
                                <!-- END Portlet PORTLET-->
                        <?php }
                        } ?>
                        <!-- END Portlet PORTLET-->
                    </div>
                </div>
            </div>
        </div>
        <!-- END ACCORDION PORTLET-->
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
<!-- Modal -->
<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" id="customerForm" action="">
                <div class="modal-body">
                    <p id="modal_message"></p>
                </div>
                <div class="modal-footer">
                    <!-- <button type="submit" id="get_new_access_token" class="btn btn-primary">Get New Access Token</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- modal end -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script type="text/javascript">
    (async function($) {
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        let no_of_banks = 0;

        const get_link_token = JSON.parse(await $.post(site_url + 'banking/C_connections/create_link_token')).link_token;
        var handler = Plaid.create({
            // Create a new link_token to initialize Link
            token: get_link_token,
            //receivedRedirectUri: window.location.href,
            onLoad: function() {
                // Optional, called when Link loads
            },
            onSuccess: function(public_token, metadata) {
                // Send the public_token to your app server.
                // The metadata object contains info about the institution the
                // user selected and the account ID or IDs, if the
                // Account Select view is enabled.
                //console.log('public_token: ' + public_token);
                //
                $.post(site_url + 'banking/C_connections/exchange_public_token/', {
                        public_token: public_token,

                    })
                    .done(function(data) {
                        //console.log(data);
                        //console.log('access_token: ' + data.access_token);
                        var json_data = JSON.parse(data);
                        //Store access token into database
                        $.post(site_url + 'banking/C_connections/store_access_token/', {
                            access_token: json_data.access_token,
                            item_id: json_data.item_id
                        });
                        ////////////

                        location.reload();
                        // window.location.href = site_url + "banking/C_connections";

                    });

            },
            onExit: function(err, metadata) {
                // The user exited the Link flow.
                if (err != null) {
                    // The user encountered a Plaid API error prior to exiting.
                }
                // metadata contains information about the institution
                // that the user selected and the most recent API request IDs.
                // Storing this information can be helpful for support.
            },
            onEvent: function(eventName, metadata) {

            }
        });

        $('#link-button').on('click', function(e) {
            handler.open();
        });



    })(jQuery);

    $(document).ready(function() {

        const module = '<?php echo $url1 = $this->uri->segment(3); ?>/';
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const start_date = '<?php echo date("Y-m-d", strtotime("-3 month")) ?>';
        const end_date = '<?php echo date("Y-m-d") ?>';
        $(".loader").hide();
        $('.note').hide();

        $('.view_transactions').on('click', function(e) {
            var cur_plaidAccountId = this.id.split("_")[1];
            $(".loader").show();
            if ($(this).text() == 'Hide Transactions') {
                $(this).text('View Transactions');
                $('#transactions_table_' + cur_plaidAccountId).hide();
            } else {
                $(this).text('Hide Transactions');
                get_transaction_list(cur_plaidAccountId);
                $('#transactions_table_' + cur_plaidAccountId).show();
            }
            $(".loader").hide();
        });
        ///////////////

        ////////////////////////
        //GET get_ponto_list_accounts
        function get_transaction_list(account_id) {
            var div = '';
            $(".loader").show();
            $.ajax({
                url: site_url + "banking/C_connections/retrieveTransactionsByAccountID/" + account_id,
                type: 'POST',
                dataType: 'json', // added data type
                success: function(json_response) {
                    //json_response = JSON.parse(response);
                    //console.log(json_response);
                    var grand_total = 0;

                    if (json_response.error_code != undefined && Object.keys(json_response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(json_response.error_code);
                        $('#modal_message').html(json_response.error_message);

                    } else {
                        let i = 0;
                        div += '<table class="table table-condenced">' +
                            '<thead>' +
                            '<tr>' +
                            '<th></th>' +
                            '<th>Name</th>' +
                            '<th>Category</th>' +
                            '<th class="text-right">Amount</th>' +
                            '<th>Date</th>' +
                            '<th></th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>';

                        $.each(json_response, function(index, value) {

                            grand_total += -value.amount;
                            var d_date = new Date(value.date);

                            div += '<tr>' +
                                '<td>' +
                                '<input type="checkbox" class="checkboxes" name="chkbox_plaid_trans_id" value="' + -value.amount + '" id="transID_' + value.plaid_transaction_id + '" ' + (value.posted == 1 ? 'disabled' : '') + '/>' +
                                '</td>' +

                                '<td>' + value.name + '</td>' +
                                // '<td>' + value.payment_channel + '</td>' +
                                '<td>' + value.category + '</td>' +
                                '<td class="text-right">' + -value.amount + value.iso_currency_code + '</td>' +
                                '<td>' + d_date.toLocaleDateString("en-US") + '</td>';

                            if (value.posted == 1) {
                                div += '<td><a id="" class="btn btn-success btn-sm" href="#">Accepted</a>';
                            } else {
                                div += '<td><a id="paymentEntry_' + i + '" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a>';
                            }

                            div += '<input type="hidden" id="payee_' + i + '" value="' + value.name + '">' +
                                '<input type="hidden" id="amount_' + i + '" value="' + -value.amount + '">' +
                                '<input type="hidden" id="transid_' + i + '" value="' + value.plaid_transaction_id + '">' +
                                '<input type="hidden" id="plaidAccountId_' + i + '" value="' + value.account_id + '">' +
                                '<input type="hidden" id="date_' + i + '" value="' + value.date + '">' +
                                '</td>' +
                                '</tr>';

                            i++;

                        });
                        div += '</tbody></table>';

                        $(".loader").hide();
                        $('#transactions_table_' + account_id).html(div);

                        /////////////
                        //Accept and do entry of the transaction
                        $('.payment_entry').on('click', function(e) {
                            var curId = this.id.split("_")[1];

                            accountsDDL();
                            // $('#account_id').select2();
                            // $('#account_id_2').select2();
                            $('#paymentEntryModal').modal('toggle');
                            $('#payment_entry_title').html("Accept Transaction ");
                            $('#date').val($("#date_" + curId).val());
                            $('#payment_payee').val($("#payee_" + curId).val());
                            $('#payment_amount').val($("#amount_" + curId).val());
                            $('#plaid_trans_id').val($("#transid_" + curId).val());
                            $('#plaid_account_id').val($("#plaidAccountId_" + curId).val());

                        });

                        //GET transaction Amount from the selected checkboxes
                        $('.checkboxes').on('click', function(e) {
                            //var cur_plaidAccountId = $(this).id.split("_")[1];
                            checkbox_change();
                        });
                        ///////////////////
                        function checkbox_change() {
                            var arr = [];
                            var total = 0;
                            $.each($("input[name='chkbox_plaid_trans_id']:checked"), function() {
                                arr.push(this.id.split("_")[1]);
                                total += parseFloat($(this).val());
                                // $('input[name=""]').val($("#transid_" + curId).val());
                            });
                            // console.log(total);
                            $('#note_' + account_id).show();
                            $('#note_' + account_id).html('<strong>' + arr.length + ' money out transactions: ' + total.toFixed(3) + ' <a id="group_accept" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a></strong>');
                            //console.log("Your selected languages are: " + arr.join(", "));

                            $('.payment_entry').on('click', function(e) {
                                var curId = this.id.split("_")[1];

                                accountsDDL();
                                // $('#account_id').select2();
                                // $('#account_id_2').select2();
                                $('#paymentEntryModal').modal('toggle');
                                $('#payment_entry_title').html("Accept Transaction ");
                                $('#date').val(end_date);
                                // $('#payment_payee').val($("#payee_" + curId).val());
                                $('#payment_amount').val(total.toFixed(3));
                                $('#plaid_trans_id').val(arr.join(", "));
                                $('#plaid_account_id').val(account_id);


                            });
                        }
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }

        ///////////////////
        $("#payment_entry_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            var plaid_account_id = $('input[name=plaid_account_id]').val();
            //console.log(formValues);
            // var submit_btn = document.activeElement.id;
            //return false;

            var confirmSale = confirm('Are you sure you want to accept transaction?');

            if (confirmSale) {

                if (formValues.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: site_url + "/banking/C_connections/bank_entry_transaction",
                        data: formValues,
                        success: function(data) {
                            if (data == '1') {
                                toastr.success("transaction saved successfully", 'Success');
                                $('#paymentEntryModal').modal('toggle');
                                get_transaction_list(plaid_account_id); // load again 
                                $('#note_' + plaid_account_id).html();
                                $('#note_' + plaid_account_id).hide();
                                //console.log('account id ' + plaid_account_id);
                                //location.reload();
                            } else {
                                toastr.error("transaction not saved, please try again.", 'Error');
                            }

                            console.log(data);
                        }
                    });
                } else {
                    toastr.warning("Please select item", 'Warning');
                }
            }
            e.preventDefault();
        });

        //GET get_ponto_list_accounts
        $('.refresh_transactions').on('click', function(e) {
            var cur_plaidItemId = this.id.split("_")[1];

            $(".loader").show();
            $.ajax({
                url: site_url + "banking/C_connections/plaid_transaction_refresh/" + cur_plaidItemId,
                type: 'POST',
                dataType: 'json', // added data type
                success: function(json_response) {
                    //json_response = JSON.parse(response);
                    console.log(json_response);

                    if (json_response.error_code != undefined && Object.keys(json_response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(json_response.error_code);
                        $('#modal_message').html(json_response.error_message);

                    } else {
                        console.log('inside ' + json_response);

                        get_transaction_list(account_id);
                        $(".loader").hide();
                        toastr.success("Latest transactions are fetched.", 'Success');
                        location.reload();
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        });
        ///////////////////

        ////////////////////////
        //GET Accounts DROPDOWN LIST
        function accountsDDL(index = 0) {

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

                    $.each(data, function(index, value) {

                        accounts_ddl += '<option value="' + value.account_code + '">' + value.title + '</option>';

                    });

                    $('#account_id').html(accounts_ddl);
                    $('#account_id_2').html(accounts_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////
    });
</script>
<!-- Accept Payment Modal -->
<div class="modal fade" id="paymentEntryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payment_entry_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" id="payment_entry_form">

                <div class="modal-body">
                    <table class="table table-striped table-bordered" id="sale_table">
                        <thead>
                            <tr>
                                <th><?php echo lang('date') ?></th>
                                <th>Payee</th>
                                <th><?php echo lang('bank'); ?></th>
                                <th><?php echo lang('category'); ?></th>
                                <th><?php echo lang('amount'); ?></th>

                            </tr>
                        </thead>
                        <tbody class="payment_entry_table">
                            <tr>
                                <td><input type="date" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" class="form-control"></td>
                                <td><input type="text" name="payee" id="payment_payee" class="form-control" readonly></td>
                                <td width="25%"><select class="form-control " id="account_id" name="account_id"></select></td>
                                <td width="25%"><select class="form-control " id="account_id_2" name="account_id_2"></select></td>
                                <td class=""><input type="text" readonly class="form-control" id="payment_amount" name="payment_amount" autocomplete="off">
                                    <input type="hidden" name="plaid_trans_id" id="plaid_trans_id">
                                    <input type="hidden" name="plaid_account_id" id="plaid_account_id">
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </div>
                <div class="modal-footer">
                    <button type="submit" id="get_new_access_token" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!--Accept payment modal end -->