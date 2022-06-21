<form id="sale_form" action="">
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for="">Select Customer:</label>
            <div class="col-sm-4">
                <select id="customer_id" name="customer_id" class="form-control select2me"></select>
                <!-- <?php echo form_dropdown('customer_id', $customersDDL, '', 'id="customer_id" class="form-control select2me"'); ?> -->
                <br><?php echo anchor('#', 'Add New <i class="fa fa-plus"></i>', ' data-toggle="modal" data-target="#customerModal"'); ?>
            </div>

            <label class="control-label col-sm-2" for="sale_date">Sale Date:</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date("Y-m-d") ?>" />
            </div>
            
        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            <div id="top_net_total"></div>
            
        </div>

    </div>
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for="">Deposit To:</label>
            <div class="col-sm-4">
                <select name="deposit_to_acc_code" id="deposit_to_acc_code" class="form-control select2me"></select>
            </div>

            <label class="control-label col-sm-2" for="due_date">Due Date:</label>
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

            <label class="control-label col-sm-2" for="">Business Address:</label>
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
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Sale/Unit Price</th>
                        <th>Description</th>
                        <th>Tax</th>
                        <th>Sub-Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="create_table">

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" rowspan="3">
                            <a href="#" class="btn btn-info btn-sm add_new" name="add_new">Add lines</a>
                            <a href="#" class="btn btn-info btn-sm clear_all" name="clear_all">Clear all</a>
                            <!-- <textarea name="description" id="description" class="form-control" placeholder="Description" cols="5" rows="6"></textarea> -->
                        </th>
                        <th class="text-right">Sub Total</th>
                        <th class="text-right" id="sub_total">0.00</th>
                        <th><input type="hidden" name="sub_total" id="sub_total_txt" value=""></th>
                    </tr>
                    <tr>
                    
                        <th class="text-right" >Discount</th>
                        <th class="text-right" id="total_discount">0.00</th>
                        <th><input type="hidden" name="total_discount" id="total_discount_txt" value=""></th>
                    </tr>
                    <tr>
                        <th class="text-right">Tax</th>
                        <th class="text-right" id="total_tax">0.00</th>
                        <th><input type="hidden" name="total_tax" id="total_tax_txt" value=""></th>
                    </tr>
                    <tr>
                        <th colspan="5"><?php //echo form_submit('', 'Save & New', 'id="new" class="btn btn-success"'); ?>
                        <?php echo form_submit('', 'Update', 'id="close" class="btn btn-success"'); ?></th>
                        <th class="text-right" >Grand Total</th>
                        <th class="text-right lead" id="net_total">0.00</th>
                        <th><input type="hidden" name="net_total" id="net_total_txt" value=""></th>
                    </tr>
                </tfoot>
            </table>

        </div>
        
    </div><!-- close main_div here -->
</form>
<!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add new customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="customerForm" action="">
                    <!-- <div class="form-group">
                        <label class="control-label col-sm-3" for="Posting">Posting Type:</label>
                        <div class="col-sm-9">
                            <?php
                            $salesPostingTypeDDL = $this->M_postingTypes->get_SalesPostingTypesDDL();
                            echo form_dropdown('posting_type_id', $salesPostingTypeDDL, '', 'id="posting_type_id" class="form-control" required=""'); ?>
                        </div>
                    </div> -->

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email">Name:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter Name" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email">Store Name:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Store Name" required="">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="email">Email:</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="phone_no">Phone No:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter phone no">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="website">Website:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="website" id="website" placeholder="Enter website">
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        const invoice_no = '<?php echo $invoice_no; ?>/';
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
            var div = '<tr><td>' + counter + '</td>' +
                // '<td width="25%"><select  class="form-control product_id" id="productid_' + counter + '" name="product_id[]"></select></td>' +
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
            // $('#productid_' + counter).select2();
            $('#accountid_' + counter).select2();
            ///

            //GET TOTAL WHEN QTY CHANGE
            $(".qty").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($(this).val());
                var price = parseFloat($('#unitprice_' + curId).val());
                var discount = 0;// (parseFloat($('#discount_' + curId).val()) ? parseFloat($('#discount_' + curId).val()) : 0);
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_' + curId).text(total);

                calc_gtotal();
            });
            //GET TOTAL WHEN UNIT PRICE CHANGE
            $(".unit_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseFloat($('#qty_' + curId).val());
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
        //$(".add_new").trigger("click"); //ADD NEW LINE WHEN PAGE LOAD BY DEFAULT

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
        function accountsDDL(index = 0,selected="") {

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
            // net_total = (total - total_discount + total_tax ? total - total_discount + total_tax : 0);

            //ASSIGN VALUE TO TEXTBOXES
            $('#sub_total_txt').val(parseFloat(total));
            $('#total_discount_txt').val(parseFloat(total_discount));
            $('#total_tax_txt').val(parseFloat(total_tax));
            $('#net_total_txt').val(parseFloat(net_total));
            /////////////

            $('#top_net_total').html('Grand Total:<h2 style="margin:0">'+parseFloat(net_total).toLocaleString('en-US', 2)+'</h2>');
            $('#net_total').text(parseFloat(net_total).toLocaleString('en-US', 2));
            $('#sub_total').text(parseFloat(total).toLocaleString('en-US'));
            $('#total_discount').text(parseFloat(total_discount).toLocaleString('en-US'));
            $('#total_tax').text(parseFloat(total_tax).toLocaleString('en-US'));
            //console.log(total_discount);
        }

        $("#sale_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;
           
            var confirmSale = confirm('Are you absolutely sure you want to sale?');
           
            if (confirmSale) {
                
                if(formValues.length > 0)
                {
                   $.ajax({
                        type: "POST",
                        url: site_url + "pos/"+module+"/sale_transaction/"+1+"/"+invoice_no,
                        data: formValues,
                        success: function(data) {
                            if(data == '1')
                            {
                                toastr.success("Invoice saved successfully",'Success');
                                if(submit_btn == 'close')
                                {
                                    window.location.href = site_url+"pos/"+module+"/all";
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
        customerDDL();
        ////////////////////////
        //GET customer DROPDOWN LIST
        function customerDDL() {

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

                $('#customer_id').html(customer_ddl);

            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
        }
        ///////////////////
        $("#customerForm").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();

            /* Serialize the submitted form control values to be sent to the web server with the request */
            var formValues = $(this).serialize();

            console.log($('#item_id').val());

            if ($('#first_name').val() == 0) {
                toastr.error("Please enter name", 'Error!');
            } else {
                // Send the form data using post
                $.post(site_url + "/pos/C_customers/create/", formValues, function(data) {
                    // Display the returned data in browser
                    //$("#result").html(data);
                    toastr.success("Data saved successfully", 'Success');
                    console.log(data);
                    $('#customerModal').modal('toggle');
                    customerDDL();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 2000);

                });
            }
        });
        /////

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
        ////UPDATE PORTION
        //////////////////
        get_accounts_updates(invoice_no);
        //counter = 0;
        function get_accounts_updates(invoice_no)
        {
            //GET SALES 
            $.ajax({
                url: site_url + "pos/C_sales/getSalesJSON/"+invoice_no,
                type: 'GET',
                dataType: "JSON",
                //data: {account_types:account_type},
                dataType: 'json', // added data type
                success: function(data) {
                    console.log(data);
                    $.each(data, function(index, value) {
                        $('#customer_id').val(null).trigger('change'); //Clearing selections
                        $('#customer_id').val(value.customer_id).trigger('change');
                        $('#deposit_to_acc_code').val(null).trigger('change'); // Clearing selections
                        $('#deposit_to_acc_code').val(value.deposit_to_acc_code).trigger('change');
                        $('#business_address').val(value.business_address);
                        $('#due_date').val(value.due_date);
                        $('#sale_date').val(value.sale_date);
                        
                    });

                    
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
            //////

            //GET SALES ITEMS
            $.ajax({
                url: site_url + "pos/C_sales/getSalesItemsJSON/"+invoice_no,
                type: 'GET',
                dataType: "JSON",
                //data: {account_types:account_type},
                dataType: 'json', // added data type
                success: function(data) {
                    console.log(data);
                    
                    $.each(data, function(index, value) {
                        counter++;
                        accountsDDL(counter);
                        
                        var div = '<tr><td>' + counter + '</td>' +
                        // '<td width="25%"><select  class="form-control product_id" id="productid_' + counter + '" name="product_id[]"></select></td>' +
                        '<td width="25%"><select  class="form-control account_id" id="accountid_' + counter + '" name="account_id[]" ></select></td>' +
                        '<td class="text-right" width="10%"><input type="number" min="1" class="form-control qty" id="qty_' + counter + '" name="qty[]" value="'+value.quantity_sold+'" autocomplete="off"></td>' +
                        '<td class="text-right"><input type="number" class="form-control unit_price" id="unitprice_' + counter + '" name="unit_price[]" value="'+value.item_unit_price+'" autocomplete="off">' +
                        '<input type="hidden" cost_price" id="costprice_' + counter + '" name="cost_price[]" value="'+value.item_cost_price+'">'+
                        '<input type="hidden" item_type" id="itemtype_' + counter + '" name="item_type[]" value=""></td>'+
                        '<input type="hidden" tax_id" id="taxid_' + counter + '" name="tax_id[]" value="'+value.tax_id+'"></td>'+
                        '<input type="hidden" tax_rate" id="taxrate_' + counter + '" name="tax_rate[]" value="'+value.tax_rate+'"></td>'+
                        // '<td class="text-right"><input type="number" class="form-control discount" id="discount_' + counter + '" name="discount[]" value=""  ></td>' +
                        '<td class="text-right"><input type="text" class="form-control description" id="description_' + counter + '" name="description[]"  value="'+value.description+'"  ></td>' +
                        '<td class="text-right tax" id="tax_' + counter + '"></td>' +
                        '<td class="text-right total" id="total_' + counter + '">'+(value.quantity_sold*value.item_unit_price)+'</td>' +
                        '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;"></i></td></tr>';
                        $('.create_table').append(div);
                        
                        
                        //SELECT 2 DROPDOWN LIST   
                        // $('#productid_' + counter).select2();
                        $('#accountid_' + counter).select2();
                        ///
                        //$('#accountid_' + counter).val(null).trigger('change');//Clearing selections
                        $('#accountid_' + counter).val(value.account_code).trigger('change');
                        
                        calc_gtotal();
                    });

                    
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
            /////
            
        }

    });
</script>