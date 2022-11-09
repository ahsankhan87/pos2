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
        <!-- <p>
        <div class="btn-group">
            <button type="button" class="btn btn-success"><?php echo lang('new') . ' ' . lang('transaction') ?></button>
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <?php echo anchor('trans/C_receivings/index/cash', lang('cash') . ' ' . lang('purchases'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('trans/C_receivings/index/credit', lang('credit') . ' ' . lang('purchases'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('trans/C_receivings/index/cashReturn', lang('cash') . ' ' . lang('purchases') . ' ' . lang('return'), 'class="hidden-print"'); ?>
                </li>
                <li>
                    <?php echo anchor('trans/C_receivings/index/creditReturn', lang('credit') . ' ' . lang('purchases') . ' ' . lang('return'), 'class="hidden-print"'); ?>
                </li>
            </ul>
        </div>
        
        </p> -->
        <?php if($purchaseType == "cash")
        {
            echo anchor('trans/C_receivings/index/'.$purchaseType, 'New ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"'); 
        
        }else{
            echo anchor('trans/C_bills/index/'.$purchaseType, 'New ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"'); 
        
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

                <table class="table table-striped table-bordered table-condensed flip-content" id="sample_1" ><!-- id="sample_receivings_1122" -->
                    <thead class="flip-content">
                        <tr>
                            <th>S.No</th>
                            <th>Inv #</th>
                            <th><?php echo lang('date'); ?></th>
                            <!-- <th><?php echo lang('supplier'); ?> Inv #</th>-->
                            <th>Supplier</th>
                            <!-- <th><?php echo lang('account'); ?></th> -->
                            <th class="text-right"><?php echo lang('amount'); ?></th>
                            <!-- <th class="text-right"><?php echo lang('taxes'); ?></th> -->
                            <!-- <th class="text-right"><?php echo lang('grand') . ' ' . lang('total'); ?></th> -->
                            <?php 
                            if($purchaseType == "credit")
                            {
                                echo '<th>'. lang('status') .'</th>';
                            } ?>
                            <th class="hidden-print"><?php echo lang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach($receivings as $key => $list)
                        {
                            $total = ($list['total_amount']+$list['total_tax']);
                            $paid = ($list['paid']);

                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('trans/C_receivings/receipt/'.$list['invoice_no']).'">'.$list['invoice_no'].'</a></td>';
                            echo '<td>'.$sno++.'</td>';
                            echo '<td>'.$list['invoice_no'].'</td>';
                            echo '<td>'.date('d-m-Y',strtotime($list['receiving_date'])).'</td>';
                            //echo '<td><img src="'.base_url('images/supplier-images/thumbs/'.$list['supplier_image']).'" width="40" height="40"/></td>';
                            $supplier_name = $this->M_suppliers->get_supplierName($list['supplier_id']);
                            echo '<td>'.@$supplier_name.'</td>';
                            //    echo '<td>'.$list['supplier_invoice_no'].'</td>';
                            //    echo '<td>'.@$this->M_employees->get_empName($list['employee_id']).'</td>';
                            echo '<td class="text-right">'. number_format($total,2). '</td>';
                            
                            
                            if($paid >= $total){
                                    $label = "label label-success";
                                    $status = 'Paid';
                            }elseif($paid < $total && $paid > 0) {
                                    $label = "label label-warning";
                                    $status = 'Partialy Paid';
                            }else {
                                    $label = "label label-danger";
                                    $status = 'Unpaid';
                            }
                            if($purchaseType == "credit")
                            {
                                echo '<td> <span class="'.$label.'">' . $status . '</span></td>';
                            }
                            echo '<td class="text-right">';
                            if($purchaseType == "credit" && $status != 'Paid')
                            {
                            echo '<a href="'.site_url($langs).'/trans/'.($purchaseType == "cash" ? "C_receivings" : "C_bills").'/receivePayment/' . $list['supplier_id'] .'/'.$list['invoice_no'].'" title="Payment" >Payment</a> | ';
                            }
                            echo '<a href="'.site_url($langs).'/trans/'.($purchaseType == "cash" ? "C_receivings" : "C_bills").'/edit/' . $list['invoice_no'] .'" title="Edit Sales" ><i class=\'fa fa-pencil-square-o fa-fw\'></i></a>';
                            echo '| <a href="'.site_url($langs).'/trans/'.($purchaseType == "cash" ? "C_receivings" : "C_bills").'/printReceipt/' . $list['invoice_no'] .'" title="Print Invoice" target="_blank" ><i class=\'fa fa-print fa-fw\'></i></a>';
                            echo '| <a href="'.site_url($langs).'/trans/'.($purchaseType == "cash" ? "C_receivings" : "C_bills").'/delete/' . $list['invoice_no'] .'" onclick="return confirm(\'Are you sure you want to permanent delete? All entries will be deleted permanently\')"; title="Permanent Delete"><i class=\'fa fa-trash-o fa-fw\'></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        } 
                                   
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th>
                            <th></th><th></th>
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