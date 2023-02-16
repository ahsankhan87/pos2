<form id="sale_form" action="">
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('select') . ' ' . lang('customer') ?>:</label>
            <div class="col-sm-4">
                <?php echo form_dropdown('customer_id', $customersDDL, '', 'id="customer_id" class="form-control select2me"'); ?>
            </div>

            <label class="control-label col-sm-2" for="sale_date"><?php echo lang('sale') . ' ' . lang('date') ?>:</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date("Y-m-d") ?>" />
            </div>
            
        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            
        </div>

    </div>
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('deposit') . ' ' . lang('to') ?>:</label>
            <div class="col-sm-4">
                <select name="deposit_to_acc_code" id="deposit_to_acc_code" class="form-control select2me"></select>
            </div>

            
        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            <div id="top_net_total"></div>
            
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
                        <th><?php echo lang('sale').' '.lang('price'); ?></th>
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
                        <th colspan="5">
                            <a href="#" class="btn btn-info btn-sm add_new" name="add_new"><?php echo lang('add_new'); ?></a>
                            <a href="#" class="btn btn-info btn-sm clear_all" name="clear_all"><?php echo lang('clear').' '.lang('all'); ?></a>
                        </th>
                        <th class="text-right"><?php echo lang('sub_total'); ?></th>
                        <th class="text-right" id="sub_total">0.00</th>
                        <th><input type="hidden" name="sub_total" id="sub_total_txt" value=""></th>
                    </tr>
                    <!-- <tr>
                        <th class="text-right" colspan="6">Discount</th>
                        <th class="text-right" id="total_discount">0.00</th>
                        <th><input type="hidden" name="total_discount" id="total_discount_txt" value=""></th>
                    </tr> -->
                    <tr>
                        <th class="text-right" colspan="6"><?php echo lang('tax'); ?></th>
                        <th class="text-right" id="total_tax">0.00</th>
                        <th><input type="hidden" name="total_tax" id="total_tax_txt" value=""></th>
                    </tr>
                    <tr>
                        <th colspan="5"><?php echo form_submit('',lang('save'), 'class="btn btn-success"'); ?></th>
                        <th class="text-right" ><?php echo lang('grand').' '.lang('total'); ?></th>
                        <th class="text-right lead" id="net_total">0.00</th>
                        <th><input type="hidden" name="net_total" id="net_total_txt" value=""></th>
                    </tr>
                </tfoot>
            </table>

            <p></p>

        </div>
        
    </div><!-- close main_div here -->
</form>

<script>
    $(document).ready(function() {

        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const curr_symbol = "<?php echo $_SESSION["home_currency_symbol"]; ?>";
        const curr_code = "<?php echo $_SESSION["home_currency_code"]; ?>";

        /////////////ADD NEW LINES
        let counter = 0; //counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
        $('.add_new').on('click', function(event) {
            event.preventDefault();
            counter++;
            accountsDDL(counter);

            var div = '<tr><td>' + counter + '</td>' +
                '<td width="25%"><select  class="form-control account_id" id="accountid_' + counter + '" name="account_id[]"></select></td>' +
                '<td class="text-right" width="10%"><input type="number" min="1" class="form-control qty" id="qty_' + counter + '" name="qty[]" value="1" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control unit_price" id="unitprice_' + counter + '" name="unit_price[]" autocomplete="off">' +
                '<input type="hidden" cost_price" id="costprice_' + counter + '" name="cost_price[]">'+
                '<input type="hidden" item_type" id="itemtype_' + counter + '" name="item_type[]"></td>'+
                '<input type="hidden" tax_id" id="taxid_' + counter + '" name="tax_id[]"></td>'+
                '<input type="hidden" tax_rate" id="taxrate_' + counter + '" name="tax_rate[]"></td>'+
                // '<td class="text-right"><input type="number" class="form-control discount" id="discount_' + counter + '" name="discount[]" value=""  ></td>' +
                '<td class="text-right"><input type="text" class="form-control description" id="description_' + counter + '" name="description[]" value=""  ></td>' +
                '<td class="text-right tax" id="tax_' + counter + '"></td>' +
                '<td class="text-right total" id="total_' + counter + '"></td>' +
                '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;"></i></td></tr>';
            $('.create_table').append(div);

            //SELECT 2 DROPDOWN LIST   
            $('#accountid_' + counter).select2();
            ///

            //GET TOTAL WHEN QTY CHANGE
            $(".qty").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($(this).val());
                var price = parseFloat($('#unitprice_' + curId).val());
                var discount = (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });
            //GET TOTAL WHEN UNIT PRICE CHANGE
            $(".unit_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($('#qty_' + curId).val());
                var discount = (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var price = parseFloat($(this).val());
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });
            //GET TOTAL WHEN DISCOUNT CHANGE
            // $(".discount").on("keyup change", function(e) {
            //     var curId = this.id.split("_")[1];
            //     var qty = parseFloat($('#qty_' + curId).val());
            //     var price = parseFloat($('#unitprice_' + curId).val());
            //     var discount = (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
            //     var total = (qty * price ? qty * price - discount : 0).toFixed(2);
            //     $('#total_' + curId).text(total);

            //     calc_gtotal();
            // });

            ////// LOAD COST PRICE, UNIT PRICE, TAX WHEN PRODUCT DROPDOWN CHANGE
            $('.product_id').on('change', function(event) {
                // event.preventDefault();
                var curId = this.id.split("_")[1];
                var accountid = $(this).val();
                var qty = parseFloat($('#qty_' + curId).val());
                var discount = (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var tax_rate = 0;
                var unit_price = 0;

                $.ajax({
                    url: site_url + "pos/Items/getSelected_items/" + accountid,
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
        
        function clearall()
        {
            counter = 0;
            calc_gtotal();
            $('#sub_total').html(parseFloat('0').toFixed(2));
            $('#total_discount').html(parseFloat('0').toFixed(2));
            $('#total_tax').html(parseFloat('0').toFixed(2));
            $('#net_total').html(parseFloat('0').toFixed(2));
            $("#sale_table > tbody").empty();
            
            $('#customer_id').val('').trigger('change');
            $(".add_new").trigger("click");//add new line
        }

        ///////////////////
        // productDDL();
        ////////////////////////
        //GET product DROPDOWN LIST
        function productDDL(index = 0) {

            let product_ddl = '';
            $.ajax({
                url: site_url + "pos/Items/productDDL",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    product_ddl += '<option value="0">Select Product</option>';

                    $.each(data, function(index, value) {

                        product_ddl += '<option value="' + value.id + '">' + value.name + '</option>';

                    });

                    $('#accountid_' + index).html(product_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ////////////////////////
        //GET Accounts DROPDOWN LIST
        function accountsDDL(index = 0) {

            let accounts_ddl = '';
            var account_type = ['liability','equity','cos','revenue','expense','asset'];
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {account_types:account_type},
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

            $('.tax').each(function() {
                total_tax += parseFloat($(this).text());
            });
            // $('.discount').each(function() {
            //     total_discount += (parseFloat($(this).val()) ? parseFloat($(this).val()) : 0);
            // });

            total_tax = (total_tax ? total_tax : 0);
            net_total = (total ? total : 0);
            
            //ASSIGN VALUE TO TEXTBOXES
            $('#sub_total_txt').val(parseFloat(total));
            // $('#total_discount_txt').val(parseFloat(total_discount));
            $('#total_tax_txt').val(parseFloat(total_tax));
            $('#net_total_txt').val(parseFloat(net_total));
            /////////////

            $('#top_net_total').html('Grand Total:<h2 style="margin:0">'+parseFloat(net_total).toLocaleString('en-US', 2)+'</h2>');
            $('#net_total').text(parseFloat(net_total).toLocaleString('en-US', 2));
            $('#sub_total').text(parseFloat(total).toLocaleString('en-US'));
            // $('#total_discount').text(parseFloat(total_discount).toLocaleString('en-US'));
            $('#total_tax').text(parseFloat(total_tax).toLocaleString('en-US'));
            //console.log(total_discount);
        }

        $("form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            // return false;
           
            var confirmSale = confirm('Are you absolutely sure you want to sale?');
           
            if (confirmSale) {
                
                if(formValues.length > 0)
                {
                   $.ajax({
                        type: "POST",
                        url: site_url + "pos/C_estimate/sale_transaction",
                        data: formValues,
                        success: function(data) {
                            if(data == '1')
                            {
                                toastr.success("Estimate saved successfully",'Success');
                                window.location.href = site_url+"pos/C_estimate/allestimate";
                            }
                            clearall();
                            console.log(data);
                        }
                    });
                }
            }
            e.preventDefault();
        });
        ////
        deposit_to_acc_codeDDL();
            ////////////////////////
            //GET deposit_to_acc_code DROPDOWN LIST
            function deposit_to_acc_codeDDL() {

            let deposit_to_acc_code_ddl = '';
            var account_type = ['asset'];
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {account_types:account_type},
                //dataType: 'json', // added data type
                success: function(data) {
                    console.log(data);
                    let i = 0;
                    deposit_to_acc_code_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index, value) {

                        deposit_to_acc_code_ddl += '<option value="' + value.account_code + '">' + value.title+ '</option>';

                    });

                    $('#deposit_to_acc_code').html(deposit_to_acc_code_ddl);

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