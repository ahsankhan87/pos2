<p><?php echo anchor('banking/C_transfer', lang('add_new') . ' <i class="fa fa-plus"></i>', 'class="btn btn-success"'); ?>&nbsp;
</p>
<?php
if (count(@$transfer)) {
?>
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

                    <table class="table table-striped table-bordered table-condensed flip-content" id="sample_1">
                        <thead class="flip-content">
                            <tr>
                                <th>S.No</th>
                                <th><?php echo lang('date'); ?></th>
                                <th><?php echo lang('invoice'); ?></th>
                                <!-- <th><?php echo lang('acc_code'); ?></th> -->
                                <th><?php echo lang('account'); ?></th>
                                <th><?php echo lang('from'); ?></th>
                                <th><?php echo lang('to'); ?></th>
                                <th width="20%"><?php echo lang('description'); ?></th>
                                <th><?php //echo lang('action'); 
                                    ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            $dr_amount = 0.00;
                            $cr_amount = 0.00;

                            foreach ($transfer as $key => $list) {
                                echo '<tr>';
                                echo '<td>' . $sno++ . '</td>';
                                echo '<td>' .  date('m/d/Y', strtotime($list['date'])) . '</td>';

                                echo '<td>';
                                // $inv_prefix = substr($list['invoice_no'], 0, 1);
                                // if (ucwords($inv_prefix) === 'S') {
                                //     echo '<a href="' . site_url('trans/C_sales/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                                // } elseif (ucwords($inv_prefix) === 'R') {
                                //     echo '<a href="' . site_url('trans/C_receivings/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                                // } elseif (ucwords($inv_prefix) === 'J') {
                                //     echo '<a href="' . site_url('accounts/C_transfer/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                                // } else {
                                //     echo $list['invoice_no'];
                                // }
                                echo $list['invoice_no'];
                                echo '</td>';

                                // echo '<td>'.$list['account_code'].'</td>';
                                echo '<td>';
                                echo '<a href="' . site_url('accounts/C_groups/accountDetail/' . $list['account_code']) . '">' . $list['account_code'] . ' ' . ($langs == 'en' ? $list['title'] : $list['title_ur']) . '</a>';
                                if ($list['is_cust'] == 1 && $list['ref_account_id'] != 0) {
                                    echo ' <small><a href="' . site_url('pos/C_customers/customerDetail/' . $list['ref_account_id']) . '">(' . trim($this->M_customers->get_CustomerName($list['ref_account_id'])) . ')</a></small>';
                                }
                                if ($list['is_supp'] == 1 && $list['ref_account_id'] != 0) {
                                    echo ' <small><a href="' . site_url('pos/Suppliers/supplierDetail/' . $list['ref_account_id']) . '">(' . trim($this->M_suppliers->get_supplierName($list['ref_account_id'])) . ')</a></small>';
                                }
                                if ($list['is_bank'] == 1 && $list['ref_account_id'] != 0) {
                                    echo ' <small><a href="' . site_url('pos/C_banking/bankDetail/' . $list['ref_account_id']) . '">(' . trim($this->M_banking->get_bankName($list['ref_account_id'])) . ')</a></small>';
                                }
                                echo '</td>';
                                echo '<td class="text-right">' . number_format($list['credit'], 2) . '</td>';
                                echo '<td class="text-right">' . number_format($list['debit'], 2) . '</td>';
                                echo '<td>' . $list['narration'] . '</td>';
                                echo '<td>';
                                echo '<span class="required text-danger"><a href="' . site_url('banking/C_transfer/delete/' . $list['invoice_no']) . '" title="Delete" onclick="return confirm(\'Are you sure you want to permanent delete?\')"><i class="fa fa-trash-o fa-fw"></i></a></span>';
                                echo '</td>';

                                echo '</tr>';
                            }
                            echo '</tbody>';
                            // echo '<tfoot>';
                            // echo '<tr><th></th><th></th><th></th>';
                            // echo '<th>Total</th>';
                            // echo '<th class="text-right"></th>';
                            // echo '<th class="text-right"></th>';
                            // echo '<th></th>';
                            // echo '<th></th>';
                            // echo '</tr>';
                            // echo '</tfoot>';
                            echo '</table>';

                            ?>
                </div>
            </div>
        </div>
        <!-- /.col-sm-12 -->
    </div>
    <!-- /.row -->
<?php } ?>