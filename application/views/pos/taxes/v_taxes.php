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
        <p><?php echo anchor('setting/C_taxes/create', lang('add_new') . ' <i class="fa fa-plus"></i>', 'class="btn btn-success"'); ?></p>

        <?php
        if (count($taxes)) {
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

                    <table class="table table-bordered table-striped table-condensed flip-content" id="sample_2">
                        <thead class="flip-content">
                            <tr>
                                <th>ID</th>
                                <th><?php echo lang('account'); ?></th>
                                <th><?php echo lang('name'); ?></th>
                                <th><?php echo lang('rate'); ?></th>
                                <th><?php echo lang('description'); ?></th>
                                <th><?php echo lang('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($taxes as $key => $list) {
                                echo '<tr valign="top">';
                                echo '<td>' . $list['id'] . '</td>';
                                echo '<td>' . $this->M_groups->get_accountName($list['account_code']) . '</td>';
                                echo '<td>' . $list['name'] . '</td>';
                                echo '<td>' . $list['rate'] . '</td>';
                                echo '<td>' . $list['description'] . '</td>';
                                //echo '<td><a href="'.site_url('setting/C_taxes/paymentModal/'. $list['id']).'" class="btn btn-warning btn-sm" >Receive Payment</a></td>';
                                // echo '<td><button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#bank-payment-Modal">Receive Payment</button></td>';

                                echo '<td>';
                                //echo  anchor(site_url('up_bank_images/upload_images/'.$list['id']),' upload Images');

                            ?>
                                <?php echo anchor('setting/C_taxes/edit/' . $list['id'], '<i class="fa fa-pencil-square-o fa-fw"></i>'); ?> |
                                <a href="<?php echo site_url('setting/C_taxes/inactivate/' . $list['id']) ?>" onclick="return confirm('Are you sure you want to inactive?')"><i class="fa fa-trash-o fa-fw"></i></a>
                                <!--
                                <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">option
                                <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li></li>
                                    
                                    <li><?php //echo anchor('setting/C_taxes/delete/'.$list['id'],'Delete'); 
                                        ?></li>
                                    <li><?php //echo anchor('setting/C_taxes/activate/'.$list['id'],'Activate'); 
                                        ?></li>
                                    <li><?php //echo anchor('setting/C_taxes/inactivate/'.$list['id'],'In-activate'); 
                                        ?></li>
                                    
                                    <li></li>
                                </ul>
                                </div>
                                -->
                        <?php
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                        }
                        ?>
                </div>
                <!-- /.col-sm-12 -->
            </div>
            <!-- /.row -->