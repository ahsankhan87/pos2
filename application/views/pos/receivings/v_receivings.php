<form id="sale_form" action="">
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('select') . ' ' . lang('supplier') ?>:</label>
            <div class="col-sm-4">
                <select name="supplier_id" id="supplier_id" class="form-control select2me"></select>
                <!-- <?php echo form_dropdown('supplier_id', $supplierDDL, '', 'id="supplier_id" class="form-control select2me"'); ?> -->
                <?php echo anchor('#', lang('add_new') . ' <i class="fa fa-plus"></i>', ' data-toggle="modal" data-target="#supplierModal"'); ?>
            </div>

            <label class="control-label col-sm-2" for="sale_date"><?php echo lang('sale') . ' ' . lang('date') ?>:</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date("Y-m-d") ?>" />
            </div>

        </div>
        <!-- /.col-sm-12 -->

        <div class="col-sm-2 text-right">
            <div><?php echo lang('grand') . ' ' . lang('total'); ?></div>
            <div id="top_net_total"></div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('payment') . ' ' . lang('method') ?>:</label>
            <div class="col-sm-4">
                <select name="payment_acc_code" id="payment_acc_code" class="form-control select2me"></select>
            </div>

            <label class="control-label col-sm-2" for="due_date"><?php echo lang('due_date') ?>:</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo date("Y-m-d") ?>" />
            </div>

        </div>
        <!-- /.col-sm-12 -->

        <div class="col-sm-2 text-right">

        </div>

    </div>
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('business') . ' ' . lang('address') ?>:</label>
            <div class="col-sm-4">
                <input type="text" name="business_address" id="business_address" class="form-control" />
            </div>

        </div>
        <!-- /.col-sm-12 -->

        <div class="col-sm-2 text-right">

        </div>
    </div>
    <hr />
    <?php $i = 1; ?>
    <div class="row">
        <div class="col-sm-12">

            <table class="table table-striped table-bordered" id="sale_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo lang('product'); ?></th>
                        <th><?php echo lang('quantity'); ?></th>
                        <th><?php echo lang('cost') . ' ' . lang('price'); ?></th>
                        <th><?php echo lang('description'); ?></th>
                        <th><?php echo lang('tax'); ?></th>
                        <th><?php echo lang('sub_total'); ?></th>

                        <th></th>
                    </tr>
                </thead>
                <tbody class="create_table">


                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" rowspan="2">
                            <a href="#" class="btn btn-info btn-sm add_new" name="add_new"><?php echo lang('add_new'); ?></a>
                            <a href="#" class="btn btn-info btn-sm clear_all" name="clear_all"><?php echo lang('clear') . ' ' . lang('all'); ?></a>
                            <!-- <textarea name="description" id="description" class="form-control" placeholder="Description" cols="5" rows="6"></textarea> -->
                        </th>
                        <th class="text-right"><?php echo lang('sub_total'); ?></th>
                        <th class="text-right" id="sub_total">0.00</th>
                        <th><input type="hidden" name="sub_total" id="sub_total_txt" value=""></th>
                    </tr>
                    <!-- <tr>
                        <th class="text-right">Discount</th>
                        <th class="text-right" id="total_discount">0.00</th>
                        <th><input type="hidden" name="total_discount" id="total_discount_txt" value=""></th>
                    </tr> -->
                    <tr>
                        <th class="text-right"><select name="tax_rate" id="tax_rate" class="form-control"></select>
                            <input type="hidden" name="tax_acc_code" id="tax_acc_code_txt" value="">
                            <input type="hidden" name="tax_id" id="tax_id_txt" value="">
                        </th>
                        <th class="text-right" id="total_tax">0.00</th>
                        <th><input type="hidden" name="total_tax" id="total_tax_txt" value=""></th>
                    </tr>
                    <tr>
                        <th colspan="5"></th>
                        <th class="text-right"><?php echo lang('grand') . ' ' . lang('total'); ?></th>
                        <th class="text-right lead" id="net_total">0.00</th>
                        <th>
                            <input type="hidden" name="net_total" id="net_total_txt" value="">
                            <input type="hidden" name="purchase_type" id="purchase_type" value="<?php echo $purchaseType; ?>">
                        </th>
                    </tr>
                    <tr>
                        <td colspan="8">
                            Attachment: <input type="file" id="document" name="document" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <?php echo form_submit('', lang('save') . ' ' . lang('and') . ' ' . lang('new'), 'id="new" class="btn btn-success"'); ?>
                            <?php echo form_submit('', lang('save') . ' ' . lang('and') . ' ' . lang('close'), 'id="close" class="btn btn-success"'); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <p></p>

        </div>

    </div><!-- close main_div here -->
</form>
<!-- Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><?php echo lang('add_new') . ' ' . lang('supplier'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="supplierForm" action="">

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email"><?php echo lang('full') . ' ' . lang('name'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="name" id="first_name" placeholder="" required="">
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
                        <label class="control-label col-sm-3" for="website"><?php echo lang('website'); ?>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="website" id="website" placeholder="">
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo lang('save') . ' ' . lang('changes'); ?></button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        const module = '<?php echo $url1 = $this->uri->segment(3); ?>/';
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const curr_symbol = "<?php echo $_SESSION["home_currency_symbol"]; ?>";
        const curr_code = "<?php echo $_SESSION["home_currency_code"]; ?>";

        /////////////ADD NEW LINES
        let counter = 0; //counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
        $('.add_new').on('click', function(event) {
            event.preventDefault();
            counter++;
            // productDDL(counter);
            accountsDDL(counter);

            var div = '<tr><td>' + counter + '</td>' +
                // '<td width="25%"><select  class="form-control product_id" id="productid_' + counter + '" name="product_id[]"></select></td>' +
                '<td width="25%"><select  class="form-control account_id" id="accountid_' + counter + '" name="account_id[]"></select></td>' +
                '<td class="text-right" width="10%"><input type="number" min="1" class="form-control qty" id="qty_' + counter + '" name="qty[]" value="1" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control cost_price" id="costprice_' + counter + '" name="cost_price[]" step="0.0001" autocomplete="off">' +
                '<input type="hidden" unit_price" id="unitprice_' + counter + '" name="unit_price[]">' +
                '<input type="hidden" item_type" id="itemtype_' + counter + '" name="item_type[]"></td>' +
                '<input type="hidden" tax_id" id="taxid_' + counter + '" name="tax_id[]"></td>' +
                '<input type="hidden" tax_rate" id="taxrate_' + counter + '" name="tax_rate[]"></td>' +
                // '<td class="text-right"><input type="number" class="form-control discount" id="discount_' + counter + '" name="discount[]" value=""  ></td>' +
                '<td class="text-right"><textarea class="form-control description" id="description_' + counter + '" name="description[]" ></textarea></td>' +
                '<td class="text-right tax" id="tax_' + counter + '"></td>' +
                '<td class="text-right total" id="total_' + counter + '"></td>' +
                '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;"></i></td></tr>';
            $('.create_table').append(div);

            //SELECT 2 DROPDOWN LIST   
            // $('#productid_' + counter).select2();
            $('#accountid_' + counter).select2();
            ///

            //GET TOTAL WHEN QTY CHANGE
            $(".qty").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($(this).val());
                var price = parseFloat($('#costprice_' + curId).val());
                var discount = 0; // (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });
            //GET TOTAL WHEN UNIT PRICE CHANGE
            $(".cost_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($('#qty_' + curId).val());
                var discount = 0; // (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var price = parseFloat($(this).val());
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });
            //GET TOTAL WHEN DISCOUNT CHANGE
            $(".discount").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($('#qty_' + curId).val());
                var price = parseFloat($('#costprice_' + curId).val());
                var discount = 0; //(parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });

            ////// LOAD COST PRICE, UNIT PRICE, TAX WHEN PRODUCT DROPDOWN CHANGE
            $('.product_id').on('change', function(event) {
                // event.preventDefault();
                var curId = this.id.split("_")[1];
                var productid = $(this).val();
                var qty = parseFloat($('#qty_' + curId).val());
                var discount = 0; // (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var tax_rate = 0;
                var unit_price = 0;

                $.ajax({
                    url: site_url + "pos/Items/getSelected_items/" + productid,
                    type: 'GET',
                    dataType: 'json', // added data type
                    success: function(data) {

                        tax_rate = (parseFloat(data[0].tax_rate) ? parseFloat(data[0].tax_rate) : 0);
                        unit_price = parseFloat(data[0].unit_price);
                        tax = unit_price * tax_rate / 100;
                        $('#unitprice_' + curId).val(data[0].unit_price);
                        $('#costprice_' + curId).val(data[0].cost_price);
                        $('#itemtype_' + curId).val(data[0].item_type);
                        $('#taxid_' + curId).val(data[0].tax_id);
                        $('#taxrate_' + curId).val(data[0].tax_rate);
                        $('#tax_' + curId).text(tax.toFixed(2));

                        var total = (qty * unit_price ? qty * unit_price - discount : 0).toFixed(2);
                        $('#total_' + curId).text(total);

                        //console.log((tax ? tax : 0));
                        calc_gtotal();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    }
                });


            });

        });
        $(".add_new").trigger("click"); //ADD NEW LINE WHEN PAGE LOAD BY DEFAULT

        /////////////////////////////////
        $("#sale_table").on("click", "#removeItem", function() {
            $(this).closest("tr").remove();
            calc_gtotal();
        });

        ////////// CLEAR ALL TABLE
        $(".clear_all").on("click", function() {
            clearall();
        });

        function clearall() {
            counter = 0;
            calc_gtotal();
            $('#sub_total').html(parseFloat('0').toFixed(2));
            $('#total_discount').html(parseFloat('0').toFixed(2));
            $('#total_tax').html(parseFloat('0').toFixed(2));
            $('#net_total').html(parseFloat('0').toFixed(2));
            $("#sale_table > tbody").empty();
            $('#top_net_total').html('');
            $('#supplier_id').val('').trigger('change');
            $('#bank_id').val('').trigger('change');

            $(".add_new").trigger("click"); //add new line
        }

        ///////////////////
        // productDDL();
        ////////////////////////
        //GET product DROPDOWN LIST
        function productDDL(index = 0) {

            let product_ddl = '';
            $.ajax({
                url: site_url + "pos/Items/productDDL/" + false + '/service',
                type: 'GET',
                cache: true,
                dataType: 'json', // added data type
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    product_ddl += '<option value="0">Select Product</option>';

                    $.each(data, function(index, value) {

                        product_ddl += '<option value="' + value.id + '">' + value.name + '</option>';

                    });

                    $('#productid_' + index).html(product_ddl);

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
            var account_type = ['liability', 'equity', 'cos', 'revenue', 'expense', 'asset'];
            let accounts_ddl = '';
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type/",
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

                    $('#accountid_' + index).html(accounts_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////
        /////////////ADD NEW LINES END HERE

        function calc_gtotal() {
            var total = 0;
            var total_discount = 0;
            var total_tax = 0;
            var net_total = 0;

            $('.total').each(function() {
                total += parseFloat($(this).text());
            });

            var tax_rate = $('#tax_rate').val();
            total_tax = (tax_rate * total / 100);

            // $('.discount').each(function() {
            //     total_discount += (parseFloat($(this).val()) ? parseFloat($(this).val()) : 0);
            // });

            // net_total = (total - total_discount + total_tax ? total - total_discount + total_tax : 0);
            total_tax = (total_tax ? total_tax : 0);
            total = (total ? total : 0);
            net_total = (total + total_tax ? total + total_tax : 0);

            //ASSIGN VALUE TO TEXTBOXES
            $('#sub_total_txt').val(parseFloat(total));
            $('#total_discount_txt').val(parseFloat(total_discount));
            $('#total_tax_txt').val(parseFloat(total_tax));
            $('#net_total_txt').val(parseFloat(net_total));
            /////////////

            $('#top_net_total').html('<h2 style="margin:0">' + parseFloat(net_total).toLocaleString('en-US', 2) + '</h2>');
            $('#net_total').text(parseFloat(net_total).toLocaleString('en-US', 2));
            $('#sub_total').text(parseFloat(total).toLocaleString('en-US'));
            $('#total_discount').text(parseFloat(total_discount).toLocaleString('en-US'));
            $('#total_tax').text(parseFloat(total_tax).toLocaleString('en-US'));
            //console.log(total_discount);
        }

        $("#sale_form").on("submit", function(e) {
            //var formValues = $(this).serialize();
            //console.log(formValues);

            var formData = new FormData(this);
            var files = $('#document')[0].files;

            formData.append('document', files[0]);

            var submit_btn = document.activeElement.id;

            var confirmSale = confirm('Are you absolutely sure you want to sale?');

            if (confirmSale) {
                $.ajax({
                    type: "POST",
                    url: site_url + "trans/" + module + "/purchase_transaction",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data == '1') {
                            toastr.success("Bill saved successfully", 'Success');
                            if (submit_btn == 'close') {
                                window.location.href = site_url + "trans/" + module + "/all";
                            }
                        } else {
                            toastr.error("Bill not saved", 'Error');
                        }
                        clearall();
                        //console.log(data);
                    }
                });

            }
            e.preventDefault();
        });

        ////
        supplierDDL();
        ////////////////////////
        //GET supplier DROPDOWN LIST
        function supplierDDL() {

            let supplier_ddl = '';
            $.ajax({
                url: site_url + "trans/C_suppliers/get_activeSuppliers",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    console.log(data);
                    let i = 0;
                    supplier_ddl += '<option value="0">Select Supplier</option>';

                    $.each(data, function(index, value) {

                        supplier_ddl += '<option value="' + value.id + '">' + value.name + '</option>';

                    });

                    $('#supplier_id').html(supplier_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ////// LOAD address DROPDOWN CHANGE
        $('#supplier_id').on('change', function(event) {
            // event.preventDefault();

            var supplier_id = $(this).val();
            $.ajax({
                url: site_url + "trans/C_suppliers/get_activeSuppliers/" + supplier_id,
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    // console.log(data);
                    $('#business_address').val(data[0].address);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
            // console.log(account_code);
        });

        ///////////////////
        $("#supplierForm").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();

            /* Serialize the submitted form control values to be sent to the web server with the request */
            var formValues = $(this).serialize();

            //console.log($('#item_id').val());

            if ($('#first_name').val() == 0) {
                toastr.error("Please enter name", 'Error!');
            } else {
                // Send the form data using post
                $.post(site_url + "trans/C_suppliers/create/", formValues, function(data) {
                    // Display the returned data in browser
                    //$("#result").html(data);
                    toastr.success("Data saved successfully", 'Success');
                    console.log(data);
                    $('#supplierModal').modal('toggle');
                    supplierDDL();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 2000);

                });
            }
        });
        /////
        /////
        var purchaseType = '<?php echo $purchaseType; ?>';
        var deposit_to_acc_code = (purchaseType == 'cash' ? 1001 : 2000);
        ////////
        payment_acc_codeDDL(deposit_to_acc_code);
        ////////////////////////
        //GET payment_acc_code DROPDOWN LIST
        function payment_acc_codeDDL() {

            let payment_acc_code_ddl = '';
            var account_type = ['asset', 'liability'];
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: 'JSON', // added data type
                data: {
                    account_types: account_type
                },
                success: function(data) {
                    console.log(data);
                    let i = 0;
                    payment_acc_code_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index, value) {

                        payment_acc_code_ddl += '<option value="' + value.account_code + '"' + (value.account_code == deposit_to_acc_code ? "selected=''" : "") + '>' + value.title + '</option>';

                    });

                    $('#payment_acc_code').html(payment_acc_code_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////

        ////
        taxDDL();
        ////////////////////////
        //GET customer DROPDOWN LIST
        function taxDDL() {

            let taxDDL = '';
            $.ajax({
                url: site_url + "setting/C_taxes/tax_DDL",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    taxDDL += '<option value="0">No Tax</option>';

                    $.each(data, function(index, value) {

                        taxDDL += '<option value="' + value.rate + '" account_code="' + value.account_code + '" tax_id="' + value.id + '" >' + value.name + '</option>';

                    });

                    $('#tax_rate').html(taxDDL);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////

        ////// LOAD TAX DROPDOWN CHANGE
        $('#tax_rate').on('change', function(event) {
            // event.preventDefault();
            calc_gtotal();
            var account_code = $("#tax_rate option:selected").attr("account_code");
            var tax_id = $("#tax_rate option:selected").attr("tax_id");
            $("#tax_acc_code_txt").val(account_code);
            $("#tax_id_txt").val(tax_id);
            // console.log(account_code);
        });

    });
</script>