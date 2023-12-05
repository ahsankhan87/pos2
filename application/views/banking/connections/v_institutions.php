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

        <button class="btn btn-success" id="link-button">Link Account</button>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i><span id="print_title"><?php echo $title; ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body flip-scroll">

                <table class="table table-striped table-bordered table-condensed flip-content" id="sample_">
                    <thead class="flip-content">
                        <tr>
                            <th>Ins id</th>
                            <th>Name</th>
                            <th class="hidden-print"><?php echo lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody class="create_table">
                    </tbody>

                </table>
                <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif") ?>" alt="loader"></div>

            </div>
        </div>

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
        get_list_institutions();
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

                        div += '<tr>' +
                            '<td>' + value.plaid_institution_id + '</td>' +
                            '<td>' + value.institution_name + '</td>' +
                            '<td><a href="' + site_url + 'banking/C_connections/all/' + value.plaid_item_id + '" class="btn btn-success btn-sm">Accounts</a>' +
                            ' <a href="' + site_url + 'banking/C_connections/remove_plaid_item/' + value.plaid_item_id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure, you want to delete?\')">Remove</a></td>' +
                            '</tr>';
                        i++;

                    });
                    $('.create_table').html(div);
                    $(".loader").hide();

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