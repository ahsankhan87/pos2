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

        </p>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i><span id="print_title"><?php echo $main; ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body flip-scroll">

                <table class="table table-striped table-condensed table-bordered flip-content" id="">
                    <thead class="flip-content">
                        <tr>
                            <th>Id</th>
                            <th>Bank Trans Code</th>
                            <th>Name</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th class="text-right">Description</th>
                            <th>created at</th>
                            <!-- <th><?php echo lang('action'); ?></th> -->
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
                <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif")?>" alt="loader"></div>

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
            <form class="form-horizontal" id="customerForm" action="<?php echo site_url($langs).'/pos/Main/ponto_request_access_tokens'; ?>">

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
        const date = '<?php echo date("Y-m-d") ?>';

        $(".loader").show();
        ////
        get_transaction_list();
        ////////////////////////
        //GET get_ponto_list_accounts
        function get_transaction_list() {

            $.ajax({
                url: site_url + "banking/C_connections/get_transaction_lists_api/",
                type: 'POST',
                dataType: 'json', // added data type
                success: function(response) {
                    console.log(response);
                    var grand_total =0;
                    
                    if (response.error_code != undefined && Object.keys(response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(response.error_code);
                        $('#modal_message').html(response.error_message);

                    } else {
                        let i = 0;
                        $.each(response.accounts, function(index, value) {
                            
                            grand_total +=value.balances.current;
                            var div = '<tr>' +
                                '<td>' + value.name + '</td>' +
                                '<td>' + value.type + '</td>' +
                                '<td>' + value.subtype + '</td>' +
                                '<td class="text-right">' + value.balances.current + value.balances.iso_currency_code + '</td>' +
                                '<td><a href="' + site_url + 'banking/C_connections/get_transactions/' + value.account_id + '">Transaction</a></td>' +
                                '</tr>';

                            $('.create_table').append(div);
                            
                            i++;
                        });
                        $(".loader").hide();
                        $(".grand_total").html(grand_total.toFixed(2));

                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////


        // $('#get_new_access_token').on('click', function(e) {
        //     $.ajax({
        //         url: site_url + "pos/Main/ponto_request_access_tokens",
        //         type: 'POST',
        //         dataType: 'json', // added data type
        //         success: function(response) {
        //             console.log(response.errors);
        //             window.location.reload(true);
        //         },
        //         error: function(xhr, ajaxOptions, thrownError) {
        //             console.log(xhr.status);
        //             console.log(thrownError);
        //         }
        //     });
        // });
    });
</script>