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
                            <th><?php echo lang('name') ?></th>
                            <!-- <th><?php echo lang('account') ?></th> -->
                            <th class="text-right"><?php echo lang('amount') ?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach ($search_result as $key => $list) {
                            $total = ($list['total_amount'] + $list['total_tax']);
                            // $paid = ($list['paid']);
                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>';
                            $inv_prefix = substr($list['invoice_no'], 0, 1);
                            if (ucwords($inv_prefix) === 'S') {
                                echo '<a href="' . site_url('pos/C_sales/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                            } elseif (ucwords($inv_prefix) === 'R') {
                                echo '<a href="' . site_url('trans/C_receivings/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                            } elseif (ucwords($inv_prefix) === 'J') {
                                echo '<a href="' . site_url('accounts/C_entries/receipt/' . $list['invoice_no']) . '" title="Print Invoice" target="_blank" >' . $list['invoice_no'] . '</a>';
                            } else {
                                echo $list['invoice_no'];
                            }
                            echo '</td>';
                            //echo '<td>'.$list['invoice_no'].'</td>';
                            echo '<td>' . date('d-m-Y', strtotime($list['date'])) . '</td>';
                            $name = $this->M_customers->get_CustomerName($list['customer_id']);
                            echo '<td>' . @$name . '</td>';
                            //echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';

                            echo '<td class="text-right">' . number_format($list['net_amount'], 2) . '</td>';
                            //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');


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