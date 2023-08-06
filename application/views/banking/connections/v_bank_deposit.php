<form id="sale_form" action="">
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('account'); ?>:</label>
            <div class="col-sm-4">
                <select name="deposit_to_acc_code" id="deposit_to_acc_code" class="form-control select2me"></select>
            </div>

            <label class="control-label col-sm-2" for="sale_date"><?php echo lang('date'); ?>:</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date("Y-m-d") ?>" />
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
                        <th><?php echo lang('received').' '.lang('from') ?></th>
                        <th><?php echo lang('account');?></th>
                        <th><?php echo lang('description');?></th>
                        <th><?php echo lang('ref');?>No.</th>
                        <th><?php echo lang('amount');?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="create_table">

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" rowspan="1">
                            <a href="#" class="btn btn-info btn-sm add_new" name="add_new"><?php echo lang('add_new');?></a>
                            <a href="#" class="btn btn-info btn-sm clear_all" name="clear_all"><?php echo lang('clear').' '.lang('all');?></a>
                            <!-- <textarea name="description" id="description" class="form-control" placeholder="Description" cols="5" rows="6"></textarea> -->
                        </th>
                        <th class="text-right" ><?php echo lang('total');?></th>
                        <th class="text-right lead" id="net_total">0.00</th>
                        <th><input type="hidden" name="net_total" id="net_total_txt" value=""></th>
                        
                    </tr>
                    
                    
                    <tr>
                        <th colspan="7"><?php echo form_submit('', lang('save').' '.lang('and').' '.lang('new'), 'id="new" class="btn btn-success"'); ?>
                        <?php echo form_submit('', lang('save').' '.lang('and').' '.lang('close'), 'id="close" class="btn btn-success"'); ?></th>
                        
                    </tr>
                </tfoot>
            </table>

        </div>
        
    </div><!-- close main_div here -->
</form>

<script>
    $(document).ready(function() {

        const module = '<?php echo $url1 = $this->uri->segment(3); ?>/';
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const date = '<?php echo date("Y-m-d") ?>';
        const curr_symbol = "<?php echo $_SESSION["home_currency_symbol"]; ?>";
        const curr_code = "<?php echo $_SESSION["home_currency_code"]; ?>";
        // console.log(date);
        /////////////ADD NEW LINES
        let counter = 0; //counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
        $('.add_new').on('click', function(event) {
            event.preventDefault();
            counter++;
            // productDDL(counter);
            accountsDDL(counter);
            customerDDL(counter);

            var div = '<tr><td>' + counter + '</td>' +
                // '<td width="25%"><select  class="form-control product_id" id="productid_' + counter + '" name="product_id[]"></select></td>' +
                '<td width="25%"><select  class="form-control customer_id" id="customerid_' + counter + '" name="customer_id[]"></select></td>' +
                '<td width="25%"><select  class="form-control account_id" id="accountid_' + counter + '" name="account_id[]"></select></td>' +
                '<td class="text-right"><input type="text" class="form-control description" id="description_' + counter + '" name="description[]" value=""  ></td>' +
                '<td class="text-right" ></td>' +
                '<td class="text-right"><input type="number" class="form-control unit_price" id="unitprice_' + counter + '" name="unit_price[]" autocomplete="off">' +
                '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;"></i></td></tr>';
            $('.create_table').append(div);

            //SELECT 2 DROPDOWN LIST   
            // $('#productid_' + counter).select2();
            $('#accountid_' + counter).select2();
            $('#customerid_' + counter).select2();
            ///

            //GET TOTAL WHEN UNIT PRICE CHANGE
            $(".unit_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = 1; //parseFloat($('#qty_' + curId).val());
                var discount = 0; //(parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
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
                var productid = $(this).val();
                var qty = parseFloat($('#qty_' + curId).val());
                var discount = 0;// (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
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
        
        function clearall()
        {
            counter = 0;
            const  date = new Date();
            calc_gtotal();
            $('#sub_total').html(parseFloat('0').toFixed(2));
            $('#total_discount').html(parseFloat('0').toFixed(2));
            $('#total_tax').html(parseFloat('0').toFixed(2));
            $('#net_total').html(parseFloat('0').toFixed(2));
            $("#sale_table > tbody").empty();
            $('#top_net_total').html('');
            $('#customer_id').val('').trigger('change');
            $('#deposit_to_acc_code').val('').trigger('change');

            $('#business_address').val('');
            $('#description').val('');
            $('#due_date').val();

            $(".add_new").trigger("click");//add new line
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
        ////////////////////////
        //GET customer DROPDOWN LIST
        function customerDDL(index = 0) {

            let customer_ddl = '';
            $.ajax({
                url: site_url + "pos/C_customers/get_act_customers_JSON",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    customer_ddl += '<option value="0">Select Customer</option>';

                    $.each(data, function(index, value) {

                        customer_ddl += '<option value="' + value.id + '">' + value.first_name+ '</option>';

                    });

                    $('#customerid_'+ index).html(customer_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }

        /////////////ADD NEW LINES END HERE

        function calc_gtotal() {
            var total = 0;
            var net_total = 0;

            $('.unit_price').each(function() {
                total += parseFloat($(this).val());
            });

            net_total = (total ? total : 0);
            // net_total = (total - total_discount + total_tax ? total - total_discount + total_tax : 0);

            //ASSIGN VALUE TO TEXTBOXES
            $('#sub_total_txt').val(parseFloat(total));
            $('#net_total_txt').val(parseFloat(net_total));
            /////////////

            $('#top_net_total').html('Grand Total:<h2 style="margin:0">'+parseFloat(net_total).toLocaleString('en-US', 2)+'</h2>');
            $('#net_total').text(parseFloat(net_total).toLocaleString('en-US', 2));
            $('#sub_total').text(parseFloat(total).toLocaleString('en-US'));
           
        }

        $("#sale_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;
           
            var confirmSale = confirm('Are you sure you want to save?');
           
            if (confirmSale) {
                
                if(formValues.length > 0)
                {
                   $.ajax({
                        type: "POST",
                        url: site_url + "banking/"+module+"/bank_deposit_transaction",
                        data: formValues,
                        success: function(data) {
                            if(data == '1')
                            {
                                toastr.success("Invoice saved successfully",'Success');
                                if(submit_btn == 'close')
                                {
                                    window.location.href = site_url+"banking/"+module+"/all";
                                }
                            }else{
                                toastr.error("Invoice not saved successfully",'Error');
                            }
                            clearall();
                            console.log(data);
                        }
                    });
                }else{
                        toastr.warning("Please select item",'Warning');
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