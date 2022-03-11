<div class="row">
    <div class="col-sm-6">


        <label class="control-label col-sm-2" for="">Select Customer:</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('customer_id', $customersDDL, '', 'id="customer_id" class="form-control select2me"'); ?>
        </div>

        <label class="control-label col-sm-2" for="sale_date">Sale Date:</label>
        <div class="col-sm-4">
            <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date("Y-m-d") ?>" />
        </div>

    </div>
    <!-- /.col-sm-12 -->
</div>
<hr />
<?php $i = 1; ?>
<div class="row">
    <div class="col-sm-12">

        <?php echo form_open(''); ?>

        <table class="table table-striped table-bordered" id="sale_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Sale/Unit Price</th>
                    <th>Discount</th>
                    <th>VAT</th>
                    <th>Sub-Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="create_table">


            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5">
                        <a href="#" class="btn btn-default btn-sm add_new" name="add_new">Add lines</a>
                        <a href="#" class="btn btn-default btn-sm clear_all" name="clear_all">Clear all</a>
                    </th><th class="text-right" >Sub Total</th>
                    <th class="text-right" id="sub_total">0</th>
                    <th><input type="hidden" name="sub_total" value="0"></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="6">Discount</th>
                    <th class="text-right" id="total_discount">0</th>
                    <th><input type="hidden" name="total_discount" value=""></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="6">Tax</th>
                    <th class="text-right" id="total_tax">0</th>
                    <th><input type="hidden" name="total_tax" value=""></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="6">Total</th>
                    <th class="text-right" id="net_total">0</th>
                    <th><input type="hidden" name="net_total" value=""></th>
                </tr>
            </tfoot>
        </table>

        <p><?php echo form_submit('', 'Save', 'class="btn btn-success"'); ?></p>
        <?php echo form_close(); ?>

    </div>
</div><!-- close main_div here -->

<script>
    $(document).ready(function() {

        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';

        /////////////ADD NEW LINES
        let counter = 0; //counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
        
        $('.add_new').on('click', function(event) {
            event.preventDefault();
            counter++;
            productDDL(counter);
            
            var div = '<tr><td>' + counter + '</td>'+
                '<td width="25%"><select  class="form-control product_id" id="product_id_' + counter + '" name="product_id[]"></select></td>' +
                '<td class="text-right" width="10%"><input type="number" class="form-control qty" id="qty_' + counter + '" name="qty[]" value="1" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control unit_price" id="unitprice_' + counter + '" name="unit_price[]" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control discount" id="discount_' + counter + '" name="discount[]" value="0"  ></td>' +
                '<td class="" id=""></td>' +
                '<td class="text-right total" id="total_' + counter + '"></td>' +
                '<td><i id="removeItem" class="fa fa-trash-o fa-1x"  style="color:red;"></i></td></tr>';
            $('.create_table').append(div);

            //SELECT 2 DROPDOWN LIST   
            $('#product_id_' + counter).select2();
            ///

            $(".qty").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseInt($(this).val());
                var price = parseFloat($('#unitprice_'+ curId).val());
                var discount = parseInt($('#discount_'+ curId).val());
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_'+ curId).text(total);

                calc_gtotal();
            });
            $(".unit_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseInt($('#qty_'+ curId).val());
                var discount = parseInt($('#discount_'+ curId).val());
                var price = parseFloat($(this).val());
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_'+ curId).text(total);
                
                calc_gtotal();
            });
            $(".discount").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseInt($('#qty_'+ curId).val());
                var price = parseFloat($('#unitprice_'+ curId).val());
                var discount = parseFloat($(this).val());
                var total = (qty * price ? qty * price - discount : 0).toFixed(2);
                $('#total_'+ curId).text(total);
                
                calc_gtotal();
            });

            
        });
        $(".add_new").trigger("click"); //ADD NEW LINE WHEN PAGE LOAD BY DEFAULT

        /////////////////////////////////
        $("#sale_table").on("click", "#removeItem", function() {
            $(this).closest("tr").remove();
            calc_gtotal();
        });
        
        $(".clear_all").on("click", function() {
            counter=0;
            calc_gtotal();
            $('#net_total').html(parseInt('0').toFixed(2));
            $("#sale_table > tbody").empty();
        }); 
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

                    $('#product_id_' + index).html(product_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////
        /////////////ADD NEW LINES END HERE
        
        function calc_gtotal(){
            var total =0;
            var total_discount = 0;
            var total_tax = 0;
            
            $('.total').each(function() {
                total += parseFloat($(this).text());
            });
            
            // $('.tax').each(function() {
            //     total_tax += parseFloat($(this).text());
            // });
            // $('.discount').each(function() {
            //     total_discount += parseFloat($(this).text());
            // });
            
            $('#net_total').text(parseFloat(total).toFixed(2));
            //$('#sub_total').text(parseFloat(total).toLocaleString('en-US'));
            //$('#total_discount').text(parseFloat(total_discount).toLocaleString('en-US'));
            //$('#total_tax').text(parseFloat(total_tax).toLocaleString('en-US'));
            // console.log(total);
        }
        
        $('#customer_id').on('change', function(event) {
            // event.preventDefault();
            console.log($(this).val());
        });
    });

    
</script>