<div class="row">
    <div class="col-sm-6">


        <label class="control-label col-sm-2" for="">Select Customer:</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('customer_id', $customersDDL, '', 'id="customer_id" class="form-control"'); ?>
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
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody class="create_table">


            </tbody>
            <tfoot>

                <tr>
                    <td colspan="5">
                        <a href="#" class="btn btn-default add_new" name="add_new">Add lines</a>
                        <a href="#" class="btn btn-default clear_all" name="clear_all">Clear all</a>
                    </td>
                    <td class="right"><strong>Total</strong></td>
                    <td class="right"><span id="net_total"></span></td>
                    <td class="right"></td>

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
            
            var div = '<tr><td>' + counter + '</td><td width="25%"><select  class="form-control product_id" id="product_id_' + counter + '" name="product_id[]"></select></td>' +
                '<td class="text-right" width="5%"><input type="number" class="form-control qty" id="qty_' + counter + '" name="qty[]" value="1" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control unit_price" id="unitprice_' + counter + '" name="unit_price[]" autocomplete="off"></td>' +
                '<td class="text-right"><input type="number" class="form-control discount" id="discount_' + counter + '"name="discount[]" value="0"  ></td>' +
                '<td></td>' +
                '<td class=""><input type="text" class="form-control text-right total" id="total_' + counter + '" readonly="" /></td>' +
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
                $('#total_'+ curId).val((qty * price ? qty * price - discount : 0).toFixed(2));
                
            });
            $(".unit_price").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseInt($('#qty_'+ curId).val());
                var discount = parseInt($('#discount_'+ curId).val());
                var price = parseFloat($(this).val());
                $('#total_'+ curId).val((qty * price ? qty * price - discount : 0).toFixed(2));
                
            });
            $(".discount").on("keyup change", function(e) {
                var curId = this.id.split("_")[1];
                var qty = parseInt($('#qty_'+ curId).val());
                var price = parseFloat($('#unitprice_'+ curId).val());
                var discount = parseFloat($(this).val());
                $('#total_'+ curId).val((qty * price ? qty * price - discount : 0).toFixed(2));
                
            });

        });
        $(".add_new").trigger("click"); //ADD NEW LINE WHEN PAGE LOAD BY DEFAULT

        /////////////////////////////////
        $("#sale_table").on("click", "#removeItem", function() {
            $(this).closest("tr").remove();
        });
        
        $(".clear_all").on("click", function() {
            counter=0;
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
        
        $('.qty').change(function() {
            var qty = parseInt($('.qty').val());
            var price = parseFloat($('.unit_price').val());
            $('#net_total').html((qty * price ? qty * price : 0).toFixed(2));
            
        });
        
    });
</script>