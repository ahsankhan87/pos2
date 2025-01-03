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
            <?php echo anchor('banking/C_bankDeposit/', lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id=""'); ?>
        </p>


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
                        foreach ($bank_deposit as $key => $list) {
                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('pos/C_bank_deposit/receipt/'.$list['invoice_no']).'" class="hidden-print">'.$list['invoice_no'].'</a></td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>' . $list['invoice_no'] . '</td>';
                            echo '<td>' . date('m/d/Y', strtotime($list['sale_date'])) . '</td>';
                            //$name = $this->M_customers->get_CustomerName($list['customer_id']);
                            //echo '<td>'.@$name.'</td>';
                            //echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';

                            echo '<td class="text-right">' . number_format($list['total_amount'], 2) . '</td>';
                            //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');
                            echo '<td>';
                            //    echo '<a href="'.site_url($langs).'/banking/C_bankDeposit/editbank_deposit/' . $list['invoice_no'] .'" title="Edit bank_deposit" ><i class=\'fa fa-pencil-square-o fa-fw\'></i></a>';
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