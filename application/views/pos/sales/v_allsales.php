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
            <!-- <div class="btn-group">
            <button type="button" class="btn btn-success"><?php echo lang('new') . ' ' . lang('transaction') ?></button>
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <?php echo anchor('pos/C_sales/index/cash', lang('cash') . ' ' . lang('sales'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('pos/C_sales/index/credit', lang('credit') . ' ' . lang('sales'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('pos/C_sales/index/cashReturn', lang('cash') . ' ' . lang('return'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('pos/C_sales/index/creditReturn', lang('credit') . ' ' . lang('return'), 'class="hidden-print"'); ?>
                </li>
            </ul>
        </div>
        </p> -->
            <?php if ($sale_type == "cash") {
                echo anchor('pos/C_sales/index/' . $sale_type, lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"');
            } else {
                echo anchor('pos/C_invoices/index/' . $sale_type, lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"');
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

                <table class="table table-striped table-bordered table-condensed flip-content" id="sales_and_purchases">
                    <thead class="flip-content">
                        <tr>
                            <th>S.No</th>
                            <th>Inv #</th>
                            <th><?php echo lang('date') ?></th>
                            <th><?php echo lang('customer') ?></th>
                            <th class="text-right"><?php echo lang('tax') ?></th>
                            <th class="text-right"><?php echo lang('amount') ?></th>
                            <th class="hidden-print"><?php echo lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        $sno = 1;
                        foreach ($sales as $key => $list) {
                            $total = ($list['total_amount'] + $list['total_tax']);
                            $paid = ($list['paid']);
                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('pos/C_sales/receipt/'.$list['invoice_no']).'" class="hidden-print">'.$list['invoice_no'].'</a></td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>' . $list['invoice_no'] . '</td>';
                            echo '<td>' . date('d-m-Y', strtotime($list['sale_date'])) . '</td>';
                            $name = $this->M_customers->get_customer_store_name($list['customer_id']);
                            echo '<td>' . @$name . '</td>';
                            //echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';

                            echo '<td class="text-right">' . round($list['total_tax'], 2) . '</td>';
                            echo '<td class="text-right">' . round($total, 2) . '</td>';
                            //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');
                            echo '<td>';
                            echo '<a href="' . site_url($langs) . '/pos/' . ($sale_type == "cash" ? "C_sales" : "C_invoices") . '/editSales/' . $list['invoice_no'] . '" title="Edit Sales" ><i class=\'fa fa-pencil-square-o fa-fw\'></i></a>
                                    | <a href="' . site_url($langs) . '/pos/' . ($sale_type == "cash" ? "C_sales" : "C_invoices") . '/printReceipt/' . $list['invoice_no'] . '" title="Print Invoice" target="_blank" ><i class=\'fa fa-print fa-fw\'></i></a>
                                    | <a href="'. site_url($langs) .'/pos/' . ($sale_type == "cash" ? "C_sales" : "C_invoices") . '/send_email_inv/'. $list['customer_id'] . '/' . $list['invoice_no'] . '" title="Email Invoice">Email</a>
                                    | <a href="' . site_url($langs) . '/pos/' . ($sale_type == "cash" ? "C_sales" : "C_invoices") . '/delete/' . $list['invoice_no'] . '" onclick="return confirm(\'Are you sure you want to permanent delete? All entries will be deleted permanently\')"; title="Permanent Delete"><i class=\'fa fa-trash-o fa-fw\'></i></a>';
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
                            <th><?php echo lang('total') ?></th>
                            <th class="text-right"></th>
                            <th class="text-right"></th>
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