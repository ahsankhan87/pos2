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
        <?php
        echo anchor('banking/C_bankDeposit/', lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id=""');
        ?>

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

                <table class="table table-striped table-bordered table-condensed flip-content" id="sample_1">
                    <thead class="flip-content">
                        <tr>
                            <th>S.No</th>
                            <th>Inv #</th>
                            <th><?php echo lang('date') ?></th>
                            <!-- <th><?php echo lang('customer') ?></th> -->
                            <!-- <th><?php echo lang('account') ?></th> -->
                            <th class="text-right"><?php echo lang('amount') ?></th>
                            <th class="hidden-print"><?php echo lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach ($connections as $key => $list) {
                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('pos/C_connections/receipt/'.$list['invoice_no']).'" class="hidden-print">'.$list['invoice_no'].'</a></td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>' . $list['invoice_no'] . '</td>';
                            echo '<td>' . date('d-m-Y', strtotime($list['sale_date'])) . '</td>';
                            //$name = $this->M_customers->get_CustomerName($list['customer_id']);
                            //echo '<td>'.@$name.'</td>';
                            //echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';

                            echo '<td class="text-right">' . number_format($list['total_amount'], 2) . '</td>';
                            //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');
                            echo '<td>';
                            //    echo '<a href="'.site_url($langs).'/banking/C_bankDeposit/editconnections/' . $list['invoice_no'] .'" title="Edit connections" ><i class=\'fa fa-pencil-square-o fa-fw\'></i></a>';
                            //    echo ' | <a href="'.site_url($langs).'/banking/C_bankDeposit/receipt/' . $list['invoice_no'] .'" title="Print Invoice" ><i class=\'fa fa-print fa-fw\'></i></a>';
                            echo ' <a href="' . site_url($langs) . '/banking/C_bankDeposit/delete/' . $list['invoice_no'] . '" onclick="return confirm(\'Are you sure you want to permanent delete? All entries will be deleted permanently\')"; title="Permanent Delete"><i class=\'fa fa-trash-o fa-fw\'></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
<button id="link-button1">Link Account</button>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script type="text/javascript">
    (async function($) {
        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        
        const get_link_token = JSON.parse(await $.post(site_url + 'banking/C_connections/create_link_token')).link_token;
        console.log(get_link_token);
        
        const handler1 = Plaid.create({
            token: get_link_token,
            receivedRedirectUri: window.location.href,
            onSuccess: (public_token, metadata) => {
                console.debug("onSuccess");
                console.log('public_token: ' + public_token);
                console.log('account ID: ' + metadata.account_id);
            },
            onLoad: () => {
                console.debug("onLoad");
            },
            onExit: (err, metadata) => {
                console.debug("onExit");
                console.log('metadata ' + metadata);
            },
            onEvent: (eventName, metadata) => {
                console.debug("onEvent");
            },
            
        });
        $('#link-button1').on('click', function(e) {
            handler1.open();
        });

        var handler = Plaid.create({
            // Create a new link_token to initialize Link
            token: (await $.post(site_url + 'banking/C_connections/create_link_token')).link_token,
            receivedRedirectUri: window.location.href,
            onLoad: function() {
                // Optional, called when Link loads
            },
            onSuccess: function(public_token, metadata) {
                // Send the public_token to your app server.
                // The metadata object contains info about the institution the
                // user selected and the account ID or IDs, if the
                // Account Select view is enabled.
                $.post('/exchange_public_token', {
                    public_token: public_token,
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
                // Optionally capture Link flow events, streamed through
                // this callback as your users connect an Item to Plaid.
                // For example:
                // eventName = "TRANSITION_VIEW"
                // metadata  = {
                //   link_session_id: "123-abc",
                //   mfa_type:        "questions",
                //   timestamp:       "2017-09-14T14:42:19.350Z",
                //   view_name:       "MFA",
                // }
            }
        });

        $('#link-button').on('click', function(e) {
            handler.open();
        });
    })(jQuery);
</script>