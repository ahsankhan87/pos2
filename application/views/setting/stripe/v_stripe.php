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
        } ?>
        <p>
            <a href="<?php echo site_url('setting/C_stripePayment/create_account'); ?>" class="btn btn-primary">Create Stripe Account</a>
            <a href="#" class="btn btn-warning" id="setting-stripe"><i class="fa fa-gear"></i> Setting</a>

        </p>
        <div class="text-center loader"><img src="<?php echo base_url("assets/img/loading-spinner-grey.gif") ?>" alt="loader"></div>

        <?php

        if (count($list_all)) {
        ?>
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
                                <th><?php echo lang('id'); ?></th>
                                <th><?php echo lang('name'); ?></th>
                                <th><?php echo lang('type'); ?></th>
                                <th><?php echo lang('status'); ?></th>
                                <th><?php echo lang('action'); ?></th>
                            </tr>
                        </thead>

                        <tbody class="flip-content">
                            <?php
                            foreach ($list_all->data as $account) {
                                //echo '<pre>';
                                // print_r($account); // Output the account object for debugging
                                //echo '</pre>';


                                echo '<tr>';
                                echo '<td>' . $account->id . '</td>';
                                echo '<td>' . $account->business_profile->name . '</td>';
                                echo '<td>' . $account->type . '</td>';

                                echo '<td>';
                                if (!$account->details_submitted) {
                                    echo '<form method="post" action="' . site_url('setting/C_stripePayment/update_link') . '">';
                                    echo '<input type="hidden" name="account_id" value="' . $account->id . '">';
                                    echo '<input type="submit"  value="Finish onboarding" class="btn btn-primary">';

                                    echo '</form>';
                                }

                                echo '</td>';
                                echo '<td>';
                                echo '<a href="' . site_url('setting/C_stripePayment/delete_account/' . $account->id) . '">Delete</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
                <!-- /.portlet body -->
            </div>
            <!-- /.portlet -->
        <?php
        }
        ?>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="settingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" id="setting_form" action="">
                <div class="modal-body">
                    <p id="modal_message"></p>
                </div>
                <div class="modal-footer">

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="stripe_key">Stripe Key:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="stripe_key" id="stripe_key" value="<?php echo set_value('stripe_key') ?>" />
                        </div>

                        <label class="control-label col-sm-3" for="stripe_secret_key">Stripe Secret Key:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="stripe_secret_key" id="stripe_secret_key" value="<?php echo set_value('stripe_secret_key') ?>" />
                        </div>

                    </div>


                    <button type="submit" id="save" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- modal end -->
<script type="text/javascript">
    $(document).ready(function() {

        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        $(".loader").hide();
        
        $('#setting-stripe').on('click', function(e) {
            $('#settingModal').modal('toggle');
            $('#modal_title').html("Stipe Account Setting");
            // $('#modal_message').html(response.error_message);
        });

        $("#setting_form").on("submit", function(e) {
            var formValues = $(this).serialize();
            //console.log(formValues);
            // alert(formValues);
            var submit_btn = document.activeElement.id;
            // return false;

            var confirmSale = confirm('Are you sure you want to save ?');

            if (confirmSale) {

                if (formValues.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: site_url + "/setting/C_stripePayment/save_stripe_setting",
                        data: formValues,
                        success: function(data) {
                            if (data == '1') {
                                toastr.success("transaction saved successfully", 'Success');
                                $('#settingModal').modal('toggle');
                            } else {
                                toastr.error("Not saved, please try again.", 'Error');
                            }

                            console.log(data);
                        }
                    });
                } else {
                    toastr.warning("not save", 'Warning');
                }
            }
            e.preventDefault();
        });
        ///////////////////
    });
</script>