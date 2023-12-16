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
            <?php echo anchor('#', 'Import Transactions' . ' <i class="fa fa-plus"></i>', 'class="btn btn-success" data-toggle="modal" data-target="#importModal"'); ?>
            <button class="btn btn-info" onclick="history.go(-1);">Back </button>
            <!-- <button class="btn btn-success" id="load_transactions" disabled>Load Transactions</button> -->

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

                <table class="table table-striped table-condensed table-bordered flip-content" id="bank_trans_table">
                    <thead class="flip-content">
                        <tr>
                            <th>Sr.#</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                            <th>Action</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="create_table">

                    </tbody>

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
<script>
    $(document).ready(function() {

        const module = '<?php echo $url1 = $this->uri->segment(2); ?>/';
        const site_url = '<?php echo site_url($langs); ?>';
        const path = '<?php echo base_url(); ?>';
        const bank_acc_code = <?php echo str_replace("#", "", $bank_acc_code); ?>;

        $("#importForm").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();

            /* Serialize the submitted form control values to be sent to the web server with the request */
            var formData = new FormData(this);
            var files = $('.import_file')[0].files;

            if (files.length > 0) {
                formData.append('import_file', files[0]);
            }
            // console.log(formData);

            $.ajax({
                type: 'POST',
                url: site_url + "/banking/C_banking/import_bank_transactions",
                data: formData,
                dataType: "JSON",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {

                    clearall();
                    $("#removeItem").trigger("click"); // remove the first row

                    $.each(data, function(index, value) {
                        //counter++;
                        counter = index;
                        var d_date = new Date(value.date);;

                        var div = '<tr><td>' + counter + '</td>' +

                            '<td>' + d_date.toLocaleDateString("en-US") + '</td>' +
                            '<td>' + value.description + '</td>' +
                            '<td class="text-right">' + value.amount + '</td>' +
                            '<td class="text-center"><a id="paymentEntry_' + counter + '" class="payment_entry btn btn-primary btn-sm" href="#">Accept</a></td>' +
                            '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;cursor:pointer"></i>' +
                            '<input type="hidden" id="date_' + counter + '" value="' + value.date + '">' +
                            '<input type="hidden" id="amount_' + counter + '" value="' + value.amount + '">' +
                            '<input type="hidden" id="description_' + counter + '" value="' + value.description + '">' +
                            '</td>' +
                            '</tr>';

                        $('.create_table').append(div);


                    });
                    $(".loader").hide();
                    console.log("success");
                    //console.log(data);
                    $('#importModal').modal('toggle');

                    /////////////
                    //Accept and do entry of the transaction
                    $('.payment_entry').on('click', function(e) {
                        var curId = this.id.split("_")[1];
                        //console.log($("#date_" + curId).val());

                        accountsDDL(bank_acc_code);
                        accountsDDL_2();
                        // $('#account_id').select2();
                        // $('#account_id_2').select2();
                        $('#paymentEntryModal').modal('toggle');
                        $('#payment_entry_title').html("Accept Transaction ");
                        $('#date').val($("#date_" + curId).val());
                        $('#amount').val($("#amount_" + curId).val());
                        $('#description').val($("#description_" + curId).val());

                    });
                    ///////////////

                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                    $('#importModal').modal('toggle');
                }

            });


        });
        /////
        $("#bank_trans_table").on("click", "#removeItem", function() {
            $(this).closest("tr").remove();

        });

        function clearall() {
            counter = 0;
            $("#bank_trans_table > tbody").empty();

        }
        accountsDDL();
        ////////////////////////
        //GET Accounts DROPDOWN LIST
        function accountsDDL(account_code = 0) {

            let accounts_ddl = '';
            var account_type = ['liability', 'equity', 'cos', 'revenue', 'expense', 'asset'];
            $.ajax({
                url: site_url + "/accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {
                    account_types: account_type
                },
                cache: true,
                success: function(data) {
                    // console.log(data);
                    let i = 0;
                    accounts_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index, value) {

                        accounts_ddl += '<option value="' + value.account_code + '" ' + (value.account_code == account_code ? "selected" : "") + '>' + value.title + '</option>';

                    });

                    $('#account_id').html(accounts_ddl);

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
        function accountsDDL_2(account_code = 0) {

            let accounts_ddl = '';
            var account_type = ['liability', 'equity', 'cos', 'revenue', 'expense', 'asset'];
            $.ajax({
                url: site_url + "/accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {
                    account_types: account_type
                },
                cache: true,
                success: function(data) {
                    // console.log(data);
                    let i = 0;
                    accounts_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index, value) {

                        accounts_ddl += '<option value="' + value.account_code + '" ' + (value.account_code == account_code ? "selected" : "") + '>' + value.title + '</option>';

                    });

                    $('#account_id_2').html(accounts_ddl);

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
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;

            var confirmSale = confirm('Are you absolutely sure you want to accept transaction?');

            if (confirmSale) {

                if (formValues.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: site_url + "/banking/C_banking/save_bank_transaction",
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
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="form-horizontal" enctype="multipart/form-data" id="importForm" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo 'Import Bank Transactions'; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <p>

                                <label for="import_file"><?php echo 'Upload csv file'; ?>:</label>
                            <div>
                                <input type="file" class="form-control import_file" name="import_file" id="import_file" required="">
                            </div>
                            <p><a href="<?php echo base_url('images/bank_transactions/ledgersfi-sample-bank.csv') ?>">Download Sample file</a></p>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo 'Upload'; ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo 'close'; ?></button>
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
                                <th><?php echo lang('bank'); ?></th>
                                <th><?php echo lang('category'); ?></th>
                                <th><?php echo lang('amount'); ?></th>
                                <th><?php echo lang('description'); ?></th>

                            </tr>
                        </thead>
                        <tbody class="payment_entry_table">
                            <tr>
                                <td><input type="date" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" class="form-control"></td>
                                <td width="25%"><select class="form-control " id="account_id" name="account_id"></select></td>
                                <td width="25%"><select class="form-control " id="account_id_2" name="account_id_2"></select></td>
                                <td class=""><input type="text" readonly class="form-control" id="amount" name="amount" autocomplete="off"></td>
                                <td class=""><input type="text" readonly class="form-control" id="description" name="description" autocomplete="off">
                                    <input type="hidden" readonly class="form-control" id="bank_id" name="bank_id" value="<?php echo $bank_id; ?>" autocomplete="off">
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