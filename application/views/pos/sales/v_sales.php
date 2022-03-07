<div class="row">
    <div class="col-sm-12">
        <h1 class="page-header lead"><?php echo $main; ?></h1>
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-sm-12">

        <?php

        $attributes = array('class' => 'form-horizontal', 'role' => 'form', 'enctype' => "multipart/form-data");
        echo form_open('pos/C_sales/addCart', $attributes); ?>

        <div class="form-group">
            <label class="control-label col-sm-2" for="">Select Product:</label>
            <div class="col-sm-4">
                <?php //echo form_dropdown('item_id',$itemDDL,'','id="item_id" class="form-control"'); 
                ?>
            </div>

            <label class="control-label col-sm-2" for="">Select Customer:</label>
            <div class="col-sm-4">
                <?php echo form_dropdown('customer_id', $customersDDL, '', 'id="customer_id" class="form-control"'); ?>
            </div>
        </div>

        <div class="form-group" id="product-price-panel">
            <label class="control-label col-sm-2" for="">Unit / Sale Price:</label>
            <div class="col-sm-4">

                <input type="text" class="form-control" name="sale_price" id="sale_price" autocomplete="off" />
            </div>

            <label class="control-label col-sm-2" for="">Product Options (color,size,qty):</label>
            <div class="col-sm-4">
                <select name="item_options" id="item_options" class="form-control">
                </select>

            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="Discount">Discount in Percent:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="discount_percent" name="discount_percent" placeholder="Discount Percent" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for=""></label>
            <div class="col-sm-4">
                <input type="submit" name="add_item" class="btn btn-success" value="Add Product" />
            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
    <!-- /.col-sm-12 -->
</div>
<hr />
<?php $i = 1; ?>
<div id="cart_body">
    <?php echo form_open('pos/C_sales/update_cart'); ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th></th>
                <th>Product Description</th>
                <th>Qty</th>
                <th>Sale/Unit Price</th>
                <th>Discount</th>
                <th>Sub-Total</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody class="create_table">
            <tr>
                <td><?php echo $i; ?></td>
                <td>
                    <select class="form-control select2me product_id0" name="product_id[]">
                    </select>
                </td>
                <td><input type="text" name="qty[]" class="form-control qty" id="qty0" style="width: 50px;"></td>
                <td><input type="text" name="unit_price[]" class="form-control unit_price" id="unit_price0" style="width: 100px;"></td>
                <td><input type="text" name="discount[]" class="form-control discount" id="discount0" style="width: 100px;"></td>
                <td></td>
                <td><?php echo anchor('pos/C_sales/destroyCart/', 'Destroy', 'class="btn btn-danger btn-xs"'); ?></td>
            </tr>
            <tr>
                <th><?php echo $i; ?></th>
                <td>
                    <select class="form-control select2me product_id1" name="product_id[]">
                    </select>
                </td>
                <td><input type="text" name="qty[]" class="form-control qty" id="qty0" style="width: 50px;"></td>
                <td><input type="text" name="unit_price[]" class="form-control unit_price" id="unit_price0" style="width: 100px;"></td>
                <td><input type="text" name="discount[]" class="form-control discount" id="discount0" style="width: 100px;"></td>
                <td></td>
                <td><?php echo anchor('pos/C_sales/destroyCart/', 'Destroy', 'class="btn btn-danger btn-xs"'); ?></td>
            </tr>

        </tbody>
        <tfoot>
            
            <tr>
                <td><a href="#" class="btn btn-info add_new" name="add_new">Add lines</a>
                </td>
                <td colspan="3"></td>
                <td class="right"><strong>Total</strong></td>
                <td class="right"></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p><?php echo form_submit('', 'Update your Cart', 'class="btn btn-success"'); ?></p>
    <?php echo form_close(); ?>

</div><!-- close main_div here -->
<script>
 $(document).ready(function() {
 
 const site_url = '<?php echo site_url($langs); ?>/';
 const path = '<?php echo base_url(); ?>';
    
 
    /////////////ADD NEW LINES IN JOURNAL ENTRY
    let counter = 1;//counter is used for id of the debit / credit textbox to enable and disable 8 textboxs already used so start from 8 here
    $('.add_new').on('click',function(event){
        event.preventDefault();
        //productDDL();
        counter++;
        var div = '<tr><td>'+counter+'</td><td><select  class="form-control select2me product_id'+counter+'" name="product_id[]"></select></td>'+
                '<td style="text-align: right;"><input type="text" class="form-control qty" id="qty'+counter+'" name="qty[]" autocomplete="off"></td>'+
                '<td style="text-align: right;"><input type="text" class="form-control unit_price" id="unit_price'+counter+'" name="unit_price[]" autocomplete="off"></td>'+
                '<td style="text-align: right;"><input type="text" class="form-control discount" name="discount[]"  ></td></tr>';
        $('.create_table').append(div);
        
        debit_keypress();
        credit_keypress();
        //SELECT 2 DROPDOWN LIST
    //$('.select2').select2();
    ///
    });
    //$( ".add_new" ).trigger( "click" );//ADD NEW LINE WHEN PAGE LOAD BY DEFAULT
    
    /////////////////////////////////

    ///////////////////
    productDDL();
    ////////////////////////
    //GET product DROPDOWN LIST
    function productDDL() {
    
        let product_ddl = '';
        $.ajax({
            url: site_url+"pos/Items/productDDL",
            type: 'GET',
            dataType: 'json', // added data type
            success: function(data) 
            {
                //console.log(data);
                let i =0;
                product_ddl += '<option value="0">Select Product</option>';
                
                $.each(data,function(index, value){
                    
                    product_ddl += '<option value="'+value.id+'">'+value.name+'</option>';
                    
                });

                $('.product_id').html(product_ddl);
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    }
    ///////////////////
});
</script>