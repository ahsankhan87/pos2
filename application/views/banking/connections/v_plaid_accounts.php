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
        </p>
        <!-- BEGIN ACCORDION PORTLET-->
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
                            $i = 0;
                            foreach ($plaidItems as $values) {
                                $palid_accounts = $this->M_institution->retrieveAccountsByItemID($values['plaid_item_id']);

                        ?>
                                <!-- BEGIN Portlet PORTLET-->
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-bank"></i><strong><?php echo $values['institution_name']; ?></strong>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="expand"></a>
                                            <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a>
                                        <a href="javascript:;" class="reload"></a> -->
                                        </div>
                                        <div class="actions">
                                            <a href="#" class="refresh_transactions btn btn-success btn-sm" id="<?php echo '_' . $values['plaid_item_id'] ?>" data-plaid_account_id="<?php echo $palid_accounts[$i]['plaid_account_id']; ?>"><i class="fa fa-refresh"></i> Refresh</a>
                                            <a href="<?php echo site_url($langs) . '/banking/C_connections/remove_plaid_item/' . $values['plaid_item_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure, you want to delete?')"><i class="fa fa-trash-o"></i> Remove</a>
                                        </div>
                                    </div>
                                    <div class="portlet-body display-hide">
                                        <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif") ?>" alt="loader"></div>
                                        <?php
                                        $i = 0;
                                        foreach ($palid_accounts as $palid_accounts_values) {
                                            echo '<table class="table" >';
                                            echo '<tbody>';
                                            echo '<tr>';
                                            echo '<td><strong>';
                                            echo $palid_accounts_values['name'];
                                            echo '</br>';
                                            echo ucfirst($palid_accounts_values['subtype']) . ' • Balance ' . $palid_accounts_values['iso_currency_code'] . ' ' . number_format($palid_accounts_values['current_balance'], 2);
                                            echo '</strong></td>';
                                            echo '<td class="text-right">';
                                            echo '<a href="#" class="view_transactions btn btn-default" id="viewTransactions_' . $palid_accounts_values['plaid_account_id'] . '">View Transactions</a>';
                                            echo '</td>';
                                            echo '</tr>';

                                            echo '</tbody>';
                                            echo '</table>';
                                            echo '<div class="note note-success trans_note" id="note_' . $palid_accounts_values['plaid_account_id'] . '"></div>';
                                            echo '<div class="text-center loader_2" id="loader_2_' . $palid_accounts_values['plaid_account_id'] . '><img src="' . base_url("assets/img/loading-spinner-grey.gif") . '" alt="loader"></div>';
                                            //echo '<table class="table table-condenced transactions_table" id="transactions_table_' . $palid_accounts_values['plaid_account_id'] . '">';
                                            echo '<table id="transactions_table_' . $palid_accounts_values['plaid_account_id'] . '" class="table table-condenced transactions_table display" cellspacing="0" width="100%">';
                                            echo '<thead>';
                                            echo '<tr>';
                                            //echo '<th></th>';
                                            echo '<th>Name</th>';
                                            echo '<th>Category</th>';
                                            echo '<th class="text-right">Amount</th>';
                                            echo '<th>Date</th>';
                                            echo '<th></th>';
                                            echo '</tr>';
                                            echo '</thead>';
                                            echo '<tbody id="transactions_table_data_' . $palid_accounts_values['plaid_account_id'] . '">';
                                            echo '</tbody></table>';
                                        }

                                        ?>

                                    </div>
                                </div>
                                <!-- END Portlet PORTLET-->
                        <?php }
                            $i++;
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
        $(".loader_2").hide();
        $('.note').hide();
        $('.transactions_table').hide(); //hide table when page load

        $('.view_transactions').on('click', function(e) {
            var cur_plaidAccountId = this.id.split("_")[1];
            $(".loader_2_" + cur_plaidAccountId).show();
            if ($(this).text() == 'Hide Transactions') {
                $(this).text('View Transactions');
                $('#transactions_table_data_' + cur_plaidAccountId).hide();
                $('#transactions_table_' + cur_plaidAccountId).hide();
                $('#transactions_table_' + cur_plaidAccountId + "_wrapper").hide();
            } else {
                $(this).text('Hide Transactions');
                get_transaction_list(cur_plaidAccountId);
                $('#transactions_table_' + cur_plaidAccountId).show();
                $('#transactions_table_data_' + cur_plaidAccountId).show();
                $('#transactions_table_' + cur_plaidAccountId + "_wrapper").show();

            }
            $(".loader_2_" + cur_plaidAccountId).hide();
        });

        ///////////////
        ////////////////////////
        //GET get_ponto_list_accounts
        function get_transaction_list(account_id) {
            var div = '';

            // $.ajax({
            //     url: site_url + "banking/C_connections/retrieveTransactionsByAccountID/" + account_id,
            //     type: 'POST',
            //     dataType: 'json', // added data type
            //     success: function(json_response) {
            //         //json_response = JSON.parse(response);
            //         //console.log('result ' + json_response);
            //         var grand_total = 0;

            //         if (json_response.error_code != undefined && Object.keys(json_response.error_code).length > 0) {

            //             $('#popupModal').modal('toggle');
            //             $('#modal_title').html(json_response.error_code);
            //             $('#modal_message').html(json_response.error_message);

            //         } else {
            //             let i = 0;

            //             // $.each(json_response, function(index, value) {

            //             //     grand_total += -value.amount;
            //             //     var d_date = new Date(value.date);


            //             //     div += '<tr>' +
            //             //         '<td>' +
            //             //         '<input type="checkbox" class="checkboxes" name="chkbox_plaid_trans_id" value="' + -value.amount + '" id="chk_transID_' + value.plaid_transaction_id + '" ' + (value.posted == 1 ? 'disabled' : '') + '/>' +

            //             //         '<input type="hidden" id="payee_' + i + '" value="' + value.name + '">' +
            //             //         '<input type="hidden" id="amount_' + i + '" value="' + -value.amount + '">' +
            //             //         '<input type="hidden" id="transid_' + i + '" value="' + value.plaid_transaction_id + '">' +
            //             //         '<input type="hidden" id="invoiceNo_' + i + '" value="' + value.invoice_no + '">' +
            //             //         '<input type="hidden" id="plaidAccountId_' + i + '" value="' + value.account_id + '">' +
            //             //         '<input type="hidden" id="date_' + i + '" value="' + value.date + '">' +
            //             //         '</td>' +

            //             //         '<td>' + value.name + '</td>' +
            //             //         // '<td>' + value.payment_channel + '</td>' +
            //             //         '<td>' + value.category + '</td>' +
            //             //         '<td class="text-right">' + -value.amount + value.iso_currency_code + '</td>' +
            //             //         '<td>' + d_date.toLocaleDateString("en-US") + '</td>' +
            //             //         '<td id="acceptButton_' + value.plaid_transaction_id + '">';

            //             //     if (value.posted == 1) {
            //             //         div += '<a id="" class="btn btn-success btn-sm" href="#">Accepted</a>';
            //             //         div += '<a onClick="return DeleteEntry(' + i + ')" class="btn btn-danger btn-sm" id="undoButton_' + value.plaid_transaction_id + '" href="#">Undo</a>';

            //             //     } else {
            //             //         //div += '<a id="paymentEntry_' + i + '" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a>';
            //             //         div += '<a class="btn btn-primary btn-sm collapseExample" data-toggle="collapse" href="#collapseExample_' + value.plaid_transaction_id + '" role="button" aria-expanded="false" aria-controls="collapseExample_' + value.plaid_transaction_id + '">Add</a>';
            //             //     }
            //             //     div += '</td></tr>';

            //             //     div += '<tr role="row" class="collapse" id="collapseExample_' + value.plaid_transaction_id + '">' +

            //             //         '<td colspan="6">' +
            //             //         '<form class="form-horizontal">' +
            //             //         '<div class="row" style="background-color:#f4f5f8; padding-bottom:20px">' +
            //             //         '<div class="col-xs-12 col-sm-12">' +

            //             //         //'<div class="form-body">' +
            //             //         //'<div class="form-group">' +
            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="date Name">Date:</label>' +
            //             //         '<input type="date" name="date_' + value.plaid_transaction_id + '" id="date_' + value.plaid_transaction_id + '" value="' + value.date + '" class="form-control">' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="customer">Customer/Vendor:</label>' +
            //             //         '<select class="form-control customer_or_supplier_id" id="customer_or_supplier_id_' + value.plaid_transaction_id + '" name="customer_or_supplier_id_' + value.plaid_transaction_id + '"></select>' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="payment_payee">Payee:</label>' +
            //             //         '<input type="text" name="payee' + value.plaid_transaction_id + '" id="payment_payee_' + value.plaid_transaction_id + '" value="' + value.name + '" class="form-control" readonly>' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="account_id">Bank:</label>' +
            //             //         '<select class="form-control account_id" id="account_id_' + value.plaid_transaction_id + '" name="account_id_' + value.plaid_transaction_id + '" required></select>' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="account_id_2">Category:</label>' +
            //             //         '<select class="form-control account_id_2" id="account_id_2_' + value.plaid_transaction_id + '" name="account_id_2_' + value.plaid_transaction_id + '"></select>' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="payment_amount">Amount:</label>' +
            //             //         '<input type="text" readonly class="form-control" value="' + -value.amount + '" id="payment_amount_' + value.plaid_transaction_id + '" name="payment_amount_' + value.plaid_transaction_id + '" autocomplete="off">' +
            //             //         '<input type="hidden" name="plaid_trans_id_' + value.plaid_transaction_id + '" id="plaid_trans_id_' + value.plaid_transaction_id + '" value="' + value.plaid_transaction_id + '">' +
            //             //         '<input type="hidden" name="plaid_account_id_' + value.plaid_transaction_id + '" id="plaid_account_id_' + value.plaid_transaction_id + '" value="' + value.account_id + '">' +
            //             //         '</div>' +

            //             //         '<div class="col-xs-12 col-sm-4">' +
            //             //         '<label class="control-label" for="submit"></label>' +
            //             //         '<input type="submit" onClick="return AddEntry(' + i + ')" class="btn btn-primary btn-sm submit" value="Add"/>' +
            //             //         '  <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#collapseExample_' + value.plaid_transaction_id + '" role="button" aria-expanded="false" aria-controls="collapseExample_' + value.plaid_transaction_id + '">Close</a>' +
            //             //         '</div>' +
            //             //         //'</div>' + //form group
            //             //         // '</div>' + //form body

            //             //         '</div>' +
            //             //         '</div>' +
            //             //         '</form>';

            //             //     '</td>' +
            //             //     '<td style="display: none">dfasdf</td>' +
            //             //     '<td style="display: none">adsfad</td>' +
            //             //     '<td style="display: none">asdfas</td>' +
            //             //     '<td style="display: none">asdfas</td>' +
            //             //     '<td style="display: none">asdf</td>' +
            //             //     '</tr>';

            //             //     i++;

            //             //     $('#customer_or_supplier_id_' + value.plaid_transaction_id).select2();
            //             //     $('.account_id').select2();
            //             //     $('#account_id_2_' + value.plaid_transaction_id).select2();



            //             // });

            //             ///////////////////

            //             //$('#transactions_table_data_' + account_id).html(div);
            //             //$('#transactions_table_' + account_id).dataTable();
            //             $(".loader").hide();

            //             /////////////
            //             //Accept and do entry of the transaction
            //             $('.payment_entry').on('click', function(e) {
            //                 var curId = this.id.split("_")[1];

            //                 accountsDDL();
            //                 // $('#account_id').select2();
            //                 // $('#account_id_2').select2();
            //                 $('#paymentEntryModal').modal('toggle');
            //                 $('#payment_entry_title').html("Accept Transaction ");
            //                 $('#date').val($("#date_" + curId).val());
            //                 $('#payment_payee').val($("#payee_" + curId).val());
            //                 $('#payment_amount').val($("#amount_" + curId).val());
            //                 $('#plaid_trans_id').val($("#transid_" + curId).val());
            //                 $('#plaid_account_id').val($("#plaidAccountId_" + curId).val());

            //             });

            //             //GET transaction Amount from the selected checkboxes
            //             $('.checkboxes').on('click', function(e) {
            //                 //var cur_plaidAccountId = $(this).id.split("_")[1];
            //                 checkbox_change();
            //             });
            //             ///////////////////
            //             function checkbox_change() {
            //                 var arr = [];
            //                 var total = 0;
            //                 $.each($("input[name='chkbox_plaid_trans_id']:checked"), function() {
            //                     arr.push(this.id.split("_")[1]);
            //                     total += parseFloat($(this).val());
            //                     // $('input[name=""]').val($("#transid_" + curId).val());
            //                 });
            //                 // console.log(total);
            //                 $('#note_' + account_id).show();
            //                 $('#note_' + account_id).html('<strong>' + arr.length + ' money out transactions: ' + total.toFixed(3) + ' <a id="group_accept" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a></strong>');
            //                 //console.log("Your selected languages are: " + arr.join(", "));

            //                 $('.payment_entry').on('click', function(e) {
            //                     var curId = this.id.split("_")[1];

            //                     accountsDDL();
            //                     // $('#account_id').select2();
            //                     // $('#account_id_2').select2();
            //                     $('#paymentEntryModal').modal('toggle');
            //                     $('#payment_entry_title').html("Accept Transaction ");
            //                     $('#date').val(end_date);
            //                     // $('#payment_payee').val($("#payee_" + curId).val());
            //                     $('#payment_amount').val(total.toFixed(3));
            //                     $('#plaid_trans_id').val(arr.join(", "));
            //                     $('#plaid_account_id').val(account_id);


            //                 });
            //             }

            //         }

            //     },
            //     error: function(xhr, ajaxOptions, thrownError) {
            //         console.log(xhr.status);
            //         console.log(thrownError);
            //     }
            // });

            if (!$.fn.dataTable.isDataTable('#transactions_table_' + account_id)) {

                var table = $('#transactions_table_' + account_id).DataTable({
                    "ajax": {
                        "url": site_url + "banking/C_connections/retrieveTransactionsByAccountID/" + account_id,
                        "dataSrc": ""
                    },
                    'columns': [{
                            'data': 'name'
                        },
                        {
                            'data': 'category'
                        },
                        {
                            'data': 'amount'
                        },
                        {
                            'data': 'date'
                        },
                        {
                            'className': 'details-control',
                            'orderable': false,
                            'data': null,
                            'defaultContent': ''
                        }
                    ],
                    "createdRow": function(nRow, aData, iDisplayIndex) {
                        // 
                        date = new Date(aData['date']).toLocaleDateString();
                        $('td:eq(2)', nRow).addClass('text-right');
                        $('td:eq(2)', nRow).html(-parseFloat(aData['amount']).toFixed(2));
                        $('td:eq(3)', nRow).html(date);

                        if (aData['posted'] == parseInt("1")) {

                            //div += '<a id="" class="btn btn-success btn-sm" href="#">Accepted</a>';
                            //div += '<a onClick="return DeleteEntry(' + i + ')" class="btn btn-danger btn-sm" id="undoButton_' + value.plaid_transaction_id + '" href="#">Undo</a>';

                            $('td:eq(4)', nRow).html('<button id="" class="btn btn-success btn-sm" >Accepted</button>' +
                                // '<button onClick="return DeleteEntry(' + aData['id'] + ')" class="btn btn-danger btn-sm" id="undoButton_' + aData['id'] + '" type="button" ' +
                                // '" onclick="return confirm(\'Are you sure you want to delete?\')"; title=\'Undo\'>Undo</button>' +
                                '<input type="hidden" id="transid_' + aData['id'] + '" value="' + aData['plaid_transaction_id'] + '">' +
                                '<input type="hidden" id="invoiceNo_' + aData['id'] + '" value="' + aData['invoice_no'] + '">');
                        } else {
                            $('td:eq(4)', nRow).html('<button class="btn btn-primary btn-sm " id="add_button_' + aData['plaid_transaction_id'] + '"  type="button" >Add</button>');
                        }
                        return nRow;
                    },
                    'order': [
                        [3, 'desc']
                    ],

                });
            }

            // Add event listener for opening and closing details
            $('#transactions_table_' + account_id + ' tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                if (row.data().posted == parseInt("1")) {
                    return;
                }

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    //AddEntry(row.data());
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        }

        //GET get_ponto_list_accounts
        $('.refresh_transactions').on('click', function(e) {
            var cur_plaidItemId = this.id.split("_")[1];
            var plaid_account_id = $(this).data('plaid_account_id');

            $(".loader").show();
            $.ajax({
                url: site_url + "banking/C_connections/plaid_transaction_refresh/" + cur_plaidItemId,
                type: 'POST',
                dataType: 'json', // added data type
                success: function(json_response) {
                    //json_response = JSON.parse(response);
                    //console.log(json_response);

                    if (json_response.error_code != undefined && Object.keys(json_response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(json_response.error_code);
                        $('#modal_message').html(json_response.error_message);

                    } else {
                        //console.log('inside ' + json_response);

                        get_transaction_list(plaid_account_id);
                        $(".loader").hide();
                        toastr.success("Latest transactions are fetched.", 'Success');
                        //location.reload();
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        });
        ///////////////////

    }); // document.ready

    function format(d) {
        // `d` is the original data object for the row
        // console.log(d.posted);

        customerDDL();
        accountsDDL();
        return '<table class="table no-border" style="background-color:#f4f5f8; >' +
            '<form class="form-horizontal">' +
            '<tr>' +
            '<td>' +
            //'<input type="checkbox" class="checkboxes" name="chkbox_plaid_trans_id" value="' + -d.amount + '" id="chk_transID_' + d.plaid_transaction_id + '" ' + (d.posted == 1 ? 'disabled' : '') + '/>' +

            '<input type="hidden" id="payee_' + d.id + '" value="' + d.name + '">' +
            '<input type="hidden" id="amount_' + d.id + '" value="' + -d.amount + '">' +
            '<input type="hidden" id="transid_' + d.id + '" value="' + d.plaid_transaction_id + '">' +
            '<input type="hidden" id="invoiceNo_' + d.id + '" value="' + d.invoice_no + '">' +
            '<input type="hidden" id="plaidAccountId_' + d.id + '" value="' + d.account_id + '">' +
            '<input type="hidden" id="date_' + d.id + '" value="' + d.date + '">' +

            '<label class="control-label" for="date Name">Date:</label>' +
            '<td>' +
            '<input type="date" name="date_' + d.plaid_transaction_id + '" id="date_' + d.plaid_transaction_id + '" value="' + d.date + '" class="form-control">' +
            '</td>' +

            '<td>' +
            '<label class="control-label" for="customer">Customer/Vendor:</label>' +
            '</td>' +
            '<td>' +
            '<select class="form-control customer_or_supplier_id" id="customer_or_supplier_id_' + d.plaid_transaction_id + '" name="customer_or_supplier_id_' + d.plaid_transaction_id + '"></select>' +
            '</td>' +

            '<td>' +
            '<label class="control-label" for="payment_payee">Payee:</label>' +
            '</td>' +
            '<td>' +
            '<input type="text" name="payee' + d.plaid_transaction_id + '" id="payment_payee_' + d.plaid_transaction_id + '" value="' + d.name + '" class="form-control" readonly>' +
            '</td>' +
            '</tr>' +

            '<tr>' +
            '<td>' +
            '<label class="control-label" for="account_id">Bank:</label>' +
            '</td>' +
            '<td>' +
            '<select class="form-control account_id" id="account_id_' + d.plaid_transaction_id + '" name="account_id_' + d.plaid_transaction_id + '" required></select>' +
            '</td>' +

            '<td>' +
            '<label class="control-label" for="account_id_2">Category:</label>' +
            '</td>' +
            '<td>' +
            '<select class="form-control account_id_2" id="account_id_2_' + d.plaid_transaction_id + '" name="account_id_2_' + d.plaid_transaction_id + '"></select>' +
            '</td>' +

            '<td>' +
            '<label class="control-label" for="payment_amount">Amount:</label>' +
            '</td>' +
            '<td>' +
            '<input type="text" readonly class="form-control" value="' + parseFloat(d.amount).toFixed(2) + '" id="payment_amount_' + d.plaid_transaction_id + '" name="payment_amount_' + d.plaid_transaction_id + '" autocomplete="off">' +
            '<input type="hidden" name="plaid_trans_id_' + d.plaid_transaction_id + '" id="plaid_trans_id_' + d.plaid_transaction_id + '" value="' + d.plaid_transaction_id + '">' +
            '<input type="hidden" name="plaid_account_id_' + d.plaid_transaction_id + '" id="plaid_account_id_' + d.plaid_transaction_id + '" value="' + d.account_id + '">' +
            '</td>' +
            '</tr>' +

            '<tr>' +
            '<td>' +
            '<label class="control-label" for="submit"></label>' +
            '<input type="submit" onClick="return AddEntry(' + d.id + ')" class="btn btn-primary btn-sm submit" value="Add"/>' +
            //'  <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#collapseExample_' + d.plaid_transaction_id + '" role="button" aria-expanded="false" aria-controls="collapseExample_' + d.plaid_transaction_id + '">Close</a>' +
            // '</div>' +
            //'</div>' + //form group
            // '</div>' + //form body

            '</td>' +
            '</tr>' +
            '</form>';
        '</table>';

    }

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

                $.each(data, function(index, value) {

                    accounts_ddl += '<option value="' + value.account_code + '">' + value.title + '</option>';

                });

                $('.account_id').html(accounts_ddl);
                $('.account_id_2').html(accounts_ddl);

            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    }
    ///////////////////

    //GET customer DROPDOWN LIST
    function customerDDL(customer_id = '') {
        const site_url = '<?php echo site_url($langs); ?>/';
        let customer_ddl = '';
        $.ajax({
            url: site_url + "pos/C_customers/get_act_customers_supplier_JSON",
            type: 'GET',
            dataType: 'json', // added data type
            success: function(data) {
                //console.log(data);
                let i = 0;
                customer_ddl += '<option value="0">Select Customer</option>';
                customer_ddl += '<option value="ADD_NEW" style="color:blue"><i class="fa fa-plus"></i>ADD NEW</option>';

                $.each(data, function(index, value) {

                    customer_ddl += '<option  value="' + value.customer + '_' + value.id + '" ' + (value.id == customer_id ? "selected=''" : "") + ' >' + value.first_name + ' <div style="color:blue">(' + value.customer + ')</div></option>';

                });

                $('.customer_or_supplier_id').html(customer_ddl);

            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    }

    //Load customers DDL and Account DDL when click on add button 
    $(document).on('click', '.collapseExample', function() {

        if (!$(this).hasClass('collapsed')) {
            //customerDDL();
            //accountsDDL();
        }

    });

    ////// LOAD add new customer DROPDOWN CHANGE
    $(document).on('change', '.customer_or_supplier_id', function() {
        var add_new = $(this).val();

        //console.log(add_new);
        if (add_new == 'ADD_NEW') {
            $('#customerModal').modal('toggle');
        }
    });

    ///////////////////
    $(document).on('submit', '#customerForm', function(event) {
        // $("").submit(function(event) {
        // Stop form from submitting normally
        event.preventDefault();
        const site_url = '<?php echo site_url($langs); ?>/';
        /* Serialize the submitted form control values to be sent to the web server with the request */
        var formValues = $(this).serialize();
        var isCustomer = $('input[name=isCustomer]:checked').val();
        var createPath = (isCustomer == 'supplier' ? "trans/C_suppliers/JSCreate/" : "pos/C_customers/create/")
        // console.log($('input[name=isCustomer]:checked').val());
        console.log(site_url + createPath);
        if ($('#first_name').val() == 0) {
            toastr.error("Please enter name", 'Error!');
        } else {
            // Send the form data using post
            $.post(site_url + createPath, formValues, function(data) {
                // Display the returned data in browser
                //$("#result").html(data);
                toastr.success("Data saved successfully", 'Success');
                //console.log(data);
                $('#customerModal').modal('toggle');
                customerDDL();
                // setTimeout(function() {
                //     location.reload();
                // }, 2000);

            });
        }
    });
    /////
    // Add the counter id as an argument, which we passed in the code above
    function AddEntry(id) {

        var confirmSale = confirm('Are you sure you want to accept transaction?');
        var plaidTransID = document.getElementById('transid_' + id).value
        //console.log('accept ' + plaidTransID);
        // Append the counter id to the ID to get the correct input
        var date = document.getElementById('date_' + plaidTransID).value;
        var customer_or_supplier_id = document.getElementById('customer_or_supplier_id_' + plaidTransID).value.split("_")[1];
        var is_customer_or_supplier = document.getElementById('customer_or_supplier_id_' + plaidTransID).value.split("_")[0];
        var payment_payee = document.getElementById('payment_payee_' + plaidTransID).value;
        var account_id = document.getElementById('account_id_' + plaidTransID).value;
        var account_id_2 = document.getElementById('account_id_2_' + plaidTransID).value;
        var payment_amount = document.getElementById('payment_amount_' + plaidTransID).value;
        var plaid_trans_id = document.getElementById('plaid_trans_id_' + plaidTransID).value;
        var plaid_account_id = document.getElementById('plaid_account_id_' + plaidTransID).value;
        var invoice_no = document.getElementById('invoiceNo_' + id).value;

        // var data = customer_or_supplier_id.split("_")[1];
        // var data1 = customer_or_supplier_id.split("_")[0];
        // console.log(data);
        console.log(customer_or_supplier_id);
        // return false;

        if (customer_or_supplier_id == undefined) {
            toastr.error("Please select customer/vendor", 'Error');
            document.getElementById('account_id_' + plaidTransID).focus();
            return false;
        } else if (account_id == 0) {
            toastr.error("Please select account/bank", 'Error');
            document.getElementById('account_id_' + plaidTransID).focus();
            return false;
        } else if (account_id_2 == 0) {
            toastr.error("Please select category", 'Error');
            document.getElementById('account_id_' + plaidTransID).focus();
            return false;
        } else if (confirmSale) {

            var sendInfo = {
                date: date,
                account_id: account_id,
                account_id_2: account_id_2,
                payment_amount: payment_amount,
                payee: payment_payee,
                plaid_trans_id: plaid_trans_id,
                plaid_account_id: plaid_account_id,
                customer_or_supplier_id: customer_or_supplier_id,
                is_customer_or_supplier: is_customer_or_supplier
            };
            $.ajax({
                type: "post",
                url: site_url + "/banking/C_connections/bank_entry_transaction",
                data: JSON.stringify(sendInfo),
                cache: false,
                contentType: 'application/json',
                success: function(data) {
                    if (data.length > 0) {
                        // Instead of using the class to set the message, use the ID,
                        // otherwise all elements will get the text. Again, append the counter id.
                        toastr.success(data + " transaction saved successfully", 'Success');
                        $('#collapseExample_' + plaidTransID).collapse('hide');
                        $('#acceptButton_' + plaidTransID).html('<a id="" class="btn btn-success btn-sm" href="#">Accepted</a><a onClick="return DeleteEntry(' + id + ')" class="btn btn-danger btn-sm" href="#">Undo</a>');
                        $('#invoiceNo_' + id).val(data); // return and change invoice no in textbox
                        $("#chk_transID_" + plaidTransID).attr("disabled", true);

                    } else {
                        toastr.error("transaction not saved, please try again.", 'Error');
                    }
                    //console.log(data);
                }

            });
        }
        return false;
    }

    function DeleteEntry(id) {
        var confirmSale = confirm('Are you sure you want to undo/delete transaction?');
        var plaidTransactionId = document.getElementById('transid_' + id).value;
        var invoice_no = document.getElementById('invoiceNo_' + id).value;
        //console.log('accept ' + plaidTransID);
        // Append the counter id to the ID to get the correct input

        if (confirmSale) {
            $.ajax({
                type: "post",
                url: site_url + "/banking/C_connections/undoPlaidTransAndDeleteSales/" + invoice_no + '/' + plaidTransactionId,
                //data: JSON.stringify(sendInfo),
                cache: false,
                contentType: 'application/json',
                success: function(data) {
                    if (data == '1') {
                        // Instead of using the class to set the message, use the ID,
                        // otherwise all elements will get the text. Again, append the counter id.
                        toastr.success("Transaction undone successfully", 'Success');
                        //$('#collapseExample_' + plaidTransID).collapse('hide');
                        $('#acceptButton_' + plaidTransactionId).html('<a class="btn btn-primary btn-sm collapseExample" data-toggle="collapse" href="#collapseExample_' + plaidTransactionId + '" role="button" aria-expanded="false" aria-controls="collapseExample_' + plaidTransactionId + '">Add</a>');
                        $('#invoiceNo_' + id).val('');
                        $("#chk_transID_" + plaidTransactionId).removeAttr("disabled");
                    } else {
                        toastr.error("transaction not undone, please try again.", 'Error');
                    }
                    console.log(data);
                }

            });
        }
        return false;
    }

    // $(document).on('click', '.submit', function() {
    //     var id = this.id.split("_")[1];

    //     var date = document.getElementById('date_' + id).value;
    //     var customer_or_supplier_id = document.getElementById('customer_or_supplier_id_' + id).value;
    //     var payment_payee = document.getElementById('payment_payee_' + id).value;
    //     var account_id = document.getElementById('account_id_' + id).value;
    //     var account_id_2 = document.getElementById('account_id_2_' + id).value;
    //     var payment_amount = document.getElementById('payment_amount_' + id).value;
    //     var plaid_trans_id = document.getElementById('plaid_trans_id_' + id).value;
    //     var plaid_account_id = document.getElementById('plaid_account_id_' + id).value;

    //     //get_transaction_list(plaid_account_id);
    //     alert('<strong>' + id + '</strong>');
    //     $('#collapseExample_' + id).collapse('hide');
    // });
</script>
<!-- Add Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><?php echo lang('add_new') . ' ' . lang('customer'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="customerForm" action="">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email">Choose:</label>
                        <div class="col-sm-9">
                            <input type="radio" class="" name="isCustomer" id="isCustomer" value="customer" checked><label for="isCustomer">&nbsp;Customer</label></br>
                            <input type="radio" class="" name="isCustomer" id="isSupplier" value="supplier"><label for="isSupplier">&nbsp;Vendor</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email"><?php echo lang('name'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email"><?php echo lang('store') . ' ' . lang('name'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="store_name" id="store_name" placeholder="" required="">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email"><?php echo lang('email'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" id="email" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="phone_no"><?php echo lang('phone'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="address"><?php echo lang('address'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="address" id="address" placeholder="">
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo lang('save'); ?></button>
            </div>
        </div>
        </form>
    </div>
</div>

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
                                <td class="">
                                    <input type="text" readonly class="form-control" id="payment_amount" name="payment_amount" autocomplete="off">
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
<style>
    .no-border>thead>tr>th,
    .no-border>tbody>tr>th,
    .no-border>tfoot>tr>th,
    .no-border>thead>tr>td,
    .no-border>tbody>tr>td,
    .no-border>tfoot>tr>td {
        border-top: none;
    }

    td.details-control {
        /* background: url(<?php echo base_url('assets/img/addButton.png'); ?>) no-repeat center center; */
        cursor: pointer;
    }

    tr.shown td.details-control {
        /* background: url('https://cdn.rawgit.com/DataTables/DataTables/6c7ada53ebc228ea9bc28b1b216e793b1825d188/examples/resources/details_close.png') no-repeat center center; */
    }
</style>