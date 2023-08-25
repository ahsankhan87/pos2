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
            <button class="btn btn-info" onclick="history.go(-1);">Back </button>
        </p>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i><span id="print_title"><?php echo $main; ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a> -->
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body flip-scroll">

                <table class="table table-striped table-condensed table-bordered flip-content" id="">
                    <thead class="flip-content">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Payment Channel</th>
                            <th>Category</th>
                            <th class="text-right">Amount</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody class="create_table">

                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th class="grand_total text-right"></th>
                        <th></th>
                    </tfoot>
                </table>
                <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif") ?>" alt="loader"></div>

            </div>
            <!-- /.portlet body -->
        </div>
        <!-- /.portlet -->
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
            <form class="form-horizontal" id="customerForm" action="<?php echo site_url($langs) . '/pos/Main/ponto_request_access_tokens'; ?>">

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

<script>
    $(document).ready(function() {

        const module = '<?php echo $url1 = $this->uri->segment(3); ?>/';
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const start_date = '<?php echo date("Y-m-d",strtotime("-3 month")) ?>';
        const end_date = '<?php echo date("Y-m-d") ?>';
        const account_id = '<?php echo $account_id ?>';
        
        $(".loader").show();
        ////
        get_transaction_list(account_id);
        ////////////////////////
        //GET get_ponto_list_accounts
        function get_transaction_list(account_id) {
            var div = '';
            $(".loader").show();
            $.ajax({
                url: site_url + "banking/C_connections/get_transactions/"+start_date+'/'+end_date,
                type: 'POST',
                dataType: 'json', // added data type
                success: function(json_response) {
                    //json_response = JSON.parse(response);
                    console.log(json_response);
                    var grand_total = 0;

                    if (json_response.error_code != undefined && Object.keys(json_response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(json_response.error_code);
                        $('#modal_message').html(json_response.error_message);

                    } else {
                        let i = 0;
                        $.each(json_response.transactions, function(index, value) {
                            
                            if (value.account_id == account_id) {
                                grand_total += -value.amount;
                                div += '<tr>' +
                                    '<td>' + value.date + '</td>' +
                                    '<td>' + value.name + '</td>' +
                                    '<td>' + value.payment_channel + '</td>' +
                                    '<td>' + value.category + '</td>' +
                                    '<td class="text-right">' + -value.amount + value.iso_currency_code + '</td>';

                                if (is_transaction_exist(value.transaction_id) == 1) {
                                    div += '<td><a id="" class="btn btn-success btn-sm" href="#">Accepted</a>';
                                } else {
                                    div += '<td><a id="paymentEntry_' + i + '" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a>';
                                }

                                div += '<input type="hidden" id="payee_' + i + '" value="' + value.name + '">' +
                                    '<input type="hidden" id="amount_' + i + '" value="' + -value.amount + '">' +
                                    '<input type="hidden" id="transid_' + i + '" value="' + value.transaction_id + '">' +
                                    '</td>' +
                                    '</tr>';;
                                
                                i++;
                            }


                        });
                        $(".loader").hide();
                        $('.create_table').html(div);
                        $(".grand_total").html(grand_total.toFixed(2));

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

                    }

                    function is_transaction_exist(trans_id) {

                        // const transaction_exist = JSON.parse(await $.post(site_url + 'banking/C_connections/is_transaction_exist/'+trans_id)).exist;
                        // console.log(transaction_exist);
                        // return transaction_exist;
                        
                        var result = '';
                        $.ajax({
                            url: site_url + "banking/C_connections/is_transaction_exist/",
                            type: 'POST',
                            // dataType: "JSON",
                            async: false,
                            data: {
                                trans_id: trans_id
                            },
                            //cache: true,
                            success: function(data) {
                                result = data;
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                console.log(xhr.status);
                                console.log(thrownError);
                            }
                        });
                        //console.log(result);
                                
                        return result;
                    }
                    ///////////////////
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
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
        //GET 
        // is_transaction_exist('jdljdkxpB1Cde3Bw6GGJhVjvVAp4zzuv9VJ9m');



        $("#payment_entry_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;

            var confirmSale = confirm('Are you absolutely sure you want to accept transaction?');

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
                                get_transaction_list(account_id); // load again 
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