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
            <button class="btn btn-success" id="link-button">Add bank</button>
        </p><!-- BEGIN ACCORDION PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>Banks
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a> -->
                    <!-- <a href="javascript:;" class="reload"></a> -->
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                <h2><?php echo count($plaidItems); ?> Banks Linked</h2>
                Below is a list of all your connected banks. Click on a bank to view its associated accounts.
                </p>
                <div class="row">
                    <div class="col-md-6">

                        <?php foreach ($plaidItems as $values) {
                        ?>
                            <!-- BEGIN Portlet PORTLET-->
                            <div class="portlet">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-bank"></i><?php echo $values['institution_name']; ?>
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="expand"></a>
                                        <!-- <a href="#portlet-config" data-toggle="modal" class="config"></a>
                                        <a href="javascript:;" class="reload"></a> -->
                                    </div>
                                    <div class="actions">
                                        <a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Remove</a>
                                    </div>
                                </div>
                                <div class="portlet-body display-hide">

                                    <?php
                                    $palid_accounts = $this->M_institution->retrieveAccountsByItemID($values['plaid_item_id']);
                                    echo '<ul>';
                                    foreach ($palid_accounts as $palid_accounts_values) {

                                        echo '<li>';
                                        echo $palid_accounts_values['name'];
                                        echo '</br>';
                                        echo $palid_accounts_values['subtype'] . ' ' . $palid_accounts_values['iso_currency_code'] . ' ' . $palid_accounts_values['current_balance'];
                                        // echo '<span class=text-right>Transactions</span>';
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                    ?>
                                </div>
                            </div>
                            <!-- END Portlet PORTLET-->
                        <?php } ?>
                        <!-- END Portlet PORTLET-->
                    </div>
                </div>
            </div>
        </div>
        <!-- END ACCORDION PORTLET-->
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
            <form class="form-horizontal" id="customerForm" action="">
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

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script type="text/javascript">
    (async function($) {
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        let no_of_banks = 0;

        const get_link_token = JSON.parse(await $.post(site_url + 'banking/C_connections/create_link_token')).link_token;
        var handler = Plaid.create({
            // Create a new link_token to initialize Link
            token: get_link_token,
            //receivedRedirectUri: window.location.href,
            onLoad: function() {
                // Optional, called when Link loads
            },
            onSuccess: function(public_token, metadata) {
                // Send the public_token to your app server.
                // The metadata object contains info about the institution the
                // user selected and the account ID or IDs, if the
                // Account Select view is enabled.
                //console.log('public_token: ' + public_token);
                //
                $.post(site_url + 'banking/C_connections/exchange_public_token/', {
                        public_token: public_token,

                    })
                    .done(function(data) {
                        //console.log(data);
                        //console.log('access_token: ' + data.access_token);
                        var json_data = JSON.parse(data);
                        //Store access token into database
                        $.post(site_url + 'banking/C_connections/store_access_token/', {
                            access_token: json_data.access_token,
                            item_id: json_data.item_id
                        });
                        ////////////


                        get_list_institutions();

                    });

            },
            onExit: function(err, metadata) {
                // The user exited the Link flow.
                if (err != null) {
                    // The user encountered a Plaid API error prior to exiting.
                }
                // metadata contains information about the institution
                // that the user selected and the most recent API request IDs.
                // Storing this information can be helpful for support.
            },
            onEvent: function(eventName, metadata) {

            }
        });

        $('#link-button').on('click', function(e) {
            handler.open();
        });

        $(".loader").show();
        ////
        // get_list_institutions();
        ////////////////////////
        //GET get_ponto_list_institutions
        function get_list_institutions() {
            $(".loader").show();

            $.ajax({
                url: site_url + "banking/C_connections/get_items_json",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    console.log(data);
                    let i = 0;
                    var div = '';
                    $.each(data, function(index, value) {
                        div += '<div class="portlet">' +
                            '<div class="portlet-title">' +
                            '<div class="caption">' +
                            '<i class="fa fa-reorder"></i>' + value.institution_name +
                            '</div>' +
                            '<div class="tools">' +
                            '<a href="javascript:;" class="expand"></a>' +
                            '</div>' +
                            '<div class="actions">' +
                            '<a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Remove</a>' +
                            '</div>' +
                            '</div>' +
                            '<div class="portlet-body display-none">' +
                            //'<div class="scroller" style="height:200px">' +
                            '<p id="create_table">' +

                            '</p>' +
                            // '</div>' +
                            '</div>' +
                            '</div>';
                        i++;
                        no_of_banks = i;
                    });
                    // $('.create_table').html(div);
                    $('#all_plaid_accounts').append(div);
                    $('#no_of_banks').html(no_of_banks);
                    $(".loader").hide();

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
        ///////////////////
        ////
        get_list_accounts(plaid_item_id);
        ////////////////////////
        //GET get_ponto_list_accounts
        function get_list_accounts(plaid_item_id) {

            $.ajax({
                url: site_url + "banking/C_connections/retrieveAccountsByItemID/" + plaid_item_id,
                type: 'GET',
                dataType: 'json', // added data type
                success: function(response) {
                    console.log(response);

                    var grand_total = 0;
                    if (response.error_code != undefined && Object.keys(response.error_code).length > 0) {

                        $('#popupModal').modal('toggle');
                        $('#modal_title').html(response.error_code);
                        $('#modal_message').html(response.error_message);

                    } else {
                        let i = 0;
                        $.each(response, function(index, value) {

                            grand_total += value.balance;
                            var div =
                                '<p>' + value.name + '</p>' +
                                // '<td>' + value.type + '</td>' +
                                // '<td>' + value.subtype + '</td>' +
                                // '<td class="text-right">' + account_balance + '</td>' +
                                '<a href="' + site_url + 'banking/C_connections/get_plaid_transactions/' + value.plaid_account_id + '/' + value.item_id + '" class="btn btn-success btn-sm">Transaction</a>' +
                                // '</div>';


                                i++;

                        });
                        $('.create_table').append(div);
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
    })(jQuery);
</script>