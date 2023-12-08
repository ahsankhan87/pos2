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
                    <div class="col-md-6">

                        <?php foreach ($plaidItems as $values) {
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
                                        <a href="#" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i> Remove</a>
                                    </div>
                                </div>
                                <div class="portlet-body display-hide">

                                    <?php
                                    $palid_accounts = $this->M_institution->retrieveAccountsByItemID($values['plaid_item_id']);
                                    echo '<table class="table table-hover">';
                                    echo '<tbody>';

                                    foreach ($palid_accounts as $palid_accounts_values) {

                                        echo '<tr>';
                                        echo '<td><strong>';
                                        echo $palid_accounts_values['name'];
                                        echo '</br>';
                                        echo $palid_accounts_values['subtype'] . ' ' . $palid_accounts_values['iso_currency_code'] . ' ' . $palid_accounts_values['current_balance'];
                                        echo '<strong></td>';
                                        echo '<td class="text-right">';
                                        echo '<a href="" id="">View Transactions</a>';
                                        echo '</td>';
                                        echo '</tr>';

                                        $palid_transactions = $this->M_institution->retrieveTransactionsByAccountID($palid_accounts_values['plaid_account_id']);
                                        // var_dump($palid_transactions);
                                        if (count((array)$palid_transactions) > 0) {

                                            echo '<table class="table table-condenced">';
                                            echo '<thead>';
                                            echo '<tr>';
                                            echo '<th>Name</th>';
                                            echo '<th>Category</th>';
                                            echo '<th class="text-right">Amount</th>';
                                            echo '<th>Date</th>';
                                            echo '<th></th>';
                                            echo '</tr>';
                                            echo '</thead>';
                                            echo '<tbody>';
                                            $i = 1;
                                            foreach ($palid_transactions as $transactions_values) {
                                                echo '<tr>';
                                                echo '<td>';
                                                echo $transactions_values['name'];
                                                echo '</td>';
                                                echo '<td>';
                                                echo $transactions_values['category'];
                                                echo '</td>';
                                                echo '<td class="text-right">';
                                                echo $transactions_values['iso_currency_code'] . ' ' . -number_format($transactions_values['amount'], 2);
                                                echo '</td>';
                                                echo '<td>';
                                                echo date('m-d-Y', strtotime($transactions_values['date']));
                                                echo '</td>';
                                                echo '<td>';
                                                if ($transactions_values['posted'] == 1) {
                                                    echo '<a id="" class="btn btn-success btn-sm" href="#">Accepted</a>';
                                                } else {
                                                    echo '<a id="paymentEntry_' . $i . '" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a>';
                                                }
                                                echo '<input type="hidden" id="payee_' . $i . '" value="' . $transactions_values['name'] . '">' .
                                                    '<input type="hidden" id="amount_' . $i . '" value="' . -$transactions_values['amount'] . '">' .
                                                    '<input type="hidden" id="transid_' . $i . '" value="' . $transactions_values['plaid_transaction_id'] . '">';
                                                echo '</td>';
                                                echo '</tr>';
                                                $i++;
                                            }
                                            echo '</tbody>';
                                            echo '</table>';
                                        }
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                    ?>
                                </div>
                            </div>
                            <!-- END Portlet PORTLET-->
                        <?php } ?>
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


                        get_list_institutions();

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
        /////////////
        //Accept and do entry of the transaction
        $('.payment_entry').on('click', function(e) {
            var curId = this.id.split("_")[1];

            accountsDDL();
            // $('#account_id').select2();
            // $('#account_id_2').select2();
            $('#paymentEntryModal').modal('toggle');
            $('#payment_entry_title').html("Accept Transaction ");
            $('#payment_payee').val($("#payee_" + curId).val());
            $('#payment_amount').val($("#amount_" + curId).val());
            $('#plaid_trans_id').val($("#transid_" + curId).val());

        });
        ///////////////
        $("#payment_entry_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;

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
                                location.reload(); // load again 
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
    })(jQuery);
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