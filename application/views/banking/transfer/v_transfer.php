<form id="sale_form" action="">
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for=""><?php echo lang('transfer').' '.lang('fund').' '.lang('from');?>:</label>
            <div class="col-sm-4">
                <select name="transfer_from" id="transfer_from" class="form-control select2me"></select>
            </div>

            <label class="control-label col-sm-2" for="sale_date"><?php echo lang('sale').' '.lang('date');?>:</label>
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

            <label class="control-label col-sm-2" for=""><?php echo lang('transfer').' '.lang('fund').' '.lang('to');?>:</label>
            <div class="col-sm-4">
                <select name="transfer_to" id="transfer_to" class="form-control select2me"></select>
            </div>

            <label class="control-label col-sm-2" for="Amount"><?php echo lang('amount');?>:</label>
            <div class="col-sm-4">
                <input type="number" class="form-control" id="amount" name="amount" value="<?php echo set_value("amount") ?>" />
            </div>
            
            
        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            
        </div>

    </div>
    <div class="row">
        <div class="col-sm-10">

            <label class="control-label col-sm-2" for="description"><?php echo lang('description');?>:</label>
            <div class="col-sm-4">
                <input type="text" name="description" id="description" class="form-control" />
            </div>

        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            
        </div>

    </div>
    <br>
    <div class="row">
        <div class="col-sm-10">

            <?php echo form_submit('', lang('save').' '.lang('and').' '.lang('new'), 'id="new" class="btn btn-success"'); ?>
            <?php echo form_submit('', lang('save').' '.lang('and').' '.lang('close'), 'id="close" class="btn btn-success"'); ?>
        </div>
        <!-- /.col-sm-12 -->
        
        <div class="col-sm-2 text-right">
            
        </div>

    </div>
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
       
        ////////// CLEAR ALL TABLE
        $(".clear_all").on("click", function() {
            clearall();
        });
        
        function clearall()
        {
            counter = 0;
            const  date = new Date();
            //calc_gtotal();
            $('#amount').val();
            $('#transfer_from').val('').trigger('change');
            $('#transfer_to').val('').trigger('change');

            $('#description').val('');
            $('#sale_date').val();

            // $(".add_new").trigger("click");//add new line
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
                        url: site_url + "banking/"+module+"/transfer_transaction",
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
        transfer_fromDDL();
        ////////////////////////
        //GET transfer_from DROPDOWN LIST
        function transfer_fromDDL() {

        let transfer_from_ddl = '';
        var account_type = ['liability','equity','cos','revenue','expense','asset'];
        $.ajax({
            url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
            type: 'POST',
            dataType: "JSON",
            data: {account_types:account_type},
            //dataType: 'json', // added data type
            success: function(data) {
                //console.log(data);
                let i = 0;
                transfer_from_ddl += '<option value="0">Select Account</option>';

                $.each(data, function(index, value) {

                    transfer_from_ddl += '<option value="' + value.account_code + '">' + value.title+ '</option>';

                });

                $('#transfer_from').html(transfer_from_ddl);

            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
        }
        ///////////////////
        ////
        transfer_toDDL();
        ////////////////////////
        //GET transfer_from DROPDOWN LIST
        function transfer_toDDL() {

        let transfer_to_ddl = '';
        var account_type = ['liability','equity','cos','revenue','expense','asset'];
        $.ajax({
            url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
            type: 'POST',
            dataType: "JSON",
            data: {account_types:account_type},
            //dataType: 'json', // added data type
            success: function(data) {
                //console.log(data);
                let i = 0;
                transfer_to_ddl += '<option value="0">Select Account</option>';

                $.each(data, function(index, value) {

                    transfer_to_ddl += '<option value="' + value.account_code + '">' + value.title+ '</option>';

                });

                $('#transfer_to').html(transfer_to_ddl);

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