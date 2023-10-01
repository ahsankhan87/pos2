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
        <a href="<?php echo site_url('setting/C_stripePayment/create_account'); ?>" class="btn btn-primary">Create Stripe Account</a>
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
                                if(!$account->details_submitted)
                                {
                                    echo '<form method="post" action="'.site_url('setting/C_stripePayment/update_link').'">';
                                    echo '<input type="hidden" name="account_id" value="'.$account->id.'">';
                                    echo '<input type="submit"  value="Finish onboarding" class="btn btn-primary">';

                                    echo '</form>';
                                }
                                
                                echo '</td>';
                                echo '<td>';
                                echo '<a href="'.site_url('setting/C_stripePayment/delete_account/'.$account->id).'">Delete</a>';
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