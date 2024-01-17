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
            <?php echo anchor('pos/C_estimate/index/estimate', lang('add_new'), 'class="btn btn-success hidden-print"'); ?>
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

                <table class="table table-bordered table-striped table-condensed flip-content" id="sample_1">
                    <thead class="flip-content">
                        <tr>
                            <th>S.No</th>
                            <th>Inv #</th>
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('customer'); ?></th>
                            <th class="text-right"><?php echo lang('amount'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th class="hidden-print"><?php echo lang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach ($estimate as $key => $list) {
                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('pos/C_sales/receipt/'.$list['invoice_no']).'" class="hidden-print">'.$list['invoice_no'].'</a></td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>' . $list['invoice_no'] . '</td>';
                            echo '<td>' . date('m/d/Y', strtotime($list['sale_date'])) . '</td>';
                            $name = $this->M_customers->get_CustomerName($list['customer_id']);
                            echo '<td>' . @$name . '</td>';
                            //echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';

                            echo '<td class="text-right">' . number_format($list['total_amount'], 2) . '</td>';
                            //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');
                            if (strtolower($list['status']) == 'in_progress') {

                                $label = "btn btn-info btn-sm";
                            } else if (strtolower($list['status']) == 'rejected') {
                                $label = "btn btn-danger btn-sm";
                            } else {
                                $label = "btn btn-success btn-sm";
                            }
                            // echo '<td><span class="'.$label.'">'.$list['payment_status'].'</span></td>';
                            echo '<td class="text-center">' . anchor('#', '<span class="' . $label . '">' . lang(strtolower($list['status'])) . '</span>', ' data-toggle="modal" data-target="#status_' . $sno . '"') . '</td>';

                            echo '<td>';

                        ?>
                            <div class="btn-group">
                                <button id="btnGroupVerticalDrop3" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop3">
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_estimate/updateStatus/' . $list['invoice_no'] . '/accept" title="Accept" >Accept</i></a>';
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_estimate/updateStatus/' . $list['invoice_no'] . '/reject" title="Reject" >Reject</i></a>';
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_estimate/editEstimate/' . $list['invoice_no'] . '" title="Edit Estimate" >Edit</i></a>';
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_invoices/index/credit/' . $list['customer_id'] . '/' . $list['invoice_no'] . '" title="Convert to Invoice" >Convert to invoice</a>';
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_estimate/delete/' . $list['invoice_no'] . '" onclick="return confirm(\'Are you sure you want to delete?\')"; title="Permanent Delete">Delete</a>';

                                        ?>
                                    </li>

                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/pos/C_estimate/printReceipt/' . $list['invoice_no'] . '" title="Print Invoice" target="_blank" >PDF</a>';
                                        ?>
                                    </li>

                                </ul>
                            </div>
                            <?php
                            echo '</td>';
                            echo '</tr>';
                            ?>

                            <!--delivery_status_ Modal -->
                            <div class="modal fade" id="status_<?php echo $sno; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('estimate') . ' ' . lang('no'); ?> <?php echo $list['invoice_no']; ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form-vertical" action="<?php echo site_url('pos/C_estimate/updateStatus'); ?>" method="post">

                                                <div class="form-group">
                                                    <label class="control-label col-sm-3" for="no"><?php echo lang('estimate') . ' ' . lang('no'); ?>:</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="<?php echo $list['invoice_no']; ?>" name="invoice_no" class="form-control" readonly>

                                                    </div>
                                                </div>
                                                </br>
                                                </br>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3" for="status"><?php echo lang('status'); ?>:</label>
                                                    <div class="col-sm-9">
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="in_progress"><?php echo lang('in_progress'); ?></option>
                                                            <option value="approved"><?php echo lang('approved'); ?></option>
                                                            <option value="rejected"><?php echo lang('rejected'); ?></option>
                                                        </select>
                                                    </div>
                                                </div>

                                                </br>
                                                </br>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary"><?php echo lang('save'); ?></button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close'); ?></button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->