<!-- <div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-note"></i><span id="print_title">Bill Summary</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body flip-scroll text-center">
                <?php $billSummary = $this->M_receivings->bill_summary(); ?>

                <table>
                    <tr class="text-center">
                        <td colspan="2">
                            <?php echo '<h5 style="color:#003463;margin-bottom:0">Total Collected</h5> <h2 style="color:#003463;margin-top:0;font-weight: 900;">' . $_SESSION['home_currency_symbol'] . number_format($billSummary[0]['paid'], 2) . "</h2>"; ?>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>
                            <?php echo '<h5 style="color:#003463;margin-bottom:0">Total Outstanding:</h5>  <h4 style="font-weight: 900;margin-top:0;">' . $_SESSION['home_currency_symbol'] . number_format($billSummary[0]['pending'], 2) . "</h4>"; ?>
                        </td>
                        <td>
                            <?php echo '<h5 style="color:#003463;margin-bottom:0">Total Overdue:</h5>  <h4 style="font-weight: 900;margin-top:0;">' . $_SESSION['home_currency_symbol'] . number_format($billSummary[0]['overdue'], 2) . "</h4>";       ?>
                        </td>
                    </tr>
                </table>

            </div>
        </div>

    </div>
</div> -->
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
        <p>
            <?php if ($purchaseType == "cash") {
                echo anchor('trans/C_receivings/index/' . $purchaseType, lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"');
            } else {
                echo anchor('trans/C_bills/index/' . $purchaseType, lang('new') . ' ' . lang('transaction'), 'class="btn btn-success" id="sample_editable_1_new"');
            }
            ?>
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

                <table class="table table-striped table-bordered table-condensed flip-content" id="invoices_and_bills"><!-- id="sample_receivings_1122" -->
                    <thead class="flip-content">
                        <tr>
                            <th>S.No</th>
                            <th>Inv #</th>
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('due_date'); ?></th>
                            <th><?php echo lang('supplier'); ?></th>
                            <th class="text-right"><?php echo lang('tax'); ?></th>
                            <th class="text-right"><?php echo lang('amount'); ?></th>
                            <th class="text-center"><?php echo lang('status'); ?></th>
                            <th class="hidden-print"><?php echo lang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach ($receivings as $key => $list) {
                            $total = ($list['total_amount'] + $list['total_tax']);
                            $paid = ($list['paid']);

                            echo '<tr>';
                            //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
                            //echo '<td><a href="'.site_url('trans/C_receivings/receipt/'.$list['invoice_no']).'">'.$list['invoice_no'].'</a></td>';
                            echo '<td>' . $sno++ . '</td>';
                            echo '<td>' . $list['invoice_no'] . '</td>';
                            echo '<td>' . date('m/d/Y', strtotime($list['receiving_date'])) . '</td>';
                            echo '<td>' . date('m/d/Y', strtotime($list['due_date'])) . '</td>';
                            $supplier_name = $this->M_suppliers->get_supplierName($list['supplier_id']);
                            echo '<td>' . @$supplier_name . '</td>';
                            echo '<td class="text-right">' . round($list['total_tax'], 2) . '</td>';
                            echo '<td class="text-right">' . round($total, 2) . '</td>';

                            // if ($paid >= $total) {
                            //     $label = "label label-success";
                            //     $status = 'Paid';
                            // } elseif ($paid < $total && $paid > 0) {
                            //     $label = "label label-warning";
                            //     $status = 'Partialy Paid';
                            // } else {
                            //     $label = "label label-danger";
                            //     $status = 'Unpaid';
                            // }
                            if ($paid >= $total) {
                                $label = "btn btn-success btn-sm";
                                $status = 'Paid';
                            } elseif ($paid < $total && $paid > 0) {
                                $label = "btn btn-warning btn-sm";
                                $status = 'Partialy Paid';
                            } else {
                                $label = "btn btn-danger btn-sm";
                                $status = 'Unpaid';
                            }
                            echo '<td class="text-center"> <span class="' . $label . '">' . lang(strtolower($status)) . '</span></td>';

                            echo '<td>';
                        ?>
                            <div class="btn-group">
                                <button id="btnGroupVerticalDrop3" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop3">
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/trans/' . ($purchaseType == "cash" ? "C_receivings" : "C_bills") . '/edit/' . $list['invoice_no'] . '" title="Edit Sales" >Edit</a>';

                                        ?>
                                    </li>
                                    <li>
                                        <?php if ($purchaseType == "credit" && $status != 'Paid') {
                                            echo '<a href="' . site_url($langs) . '/trans/' . ($purchaseType == "cash" ? "C_receivings" : "C_bills") . '/receivePayment/' . $list['supplier_id'] . '/' . $list['invoice_no'] . '">' . lang('payment') . '</a>';
                                        } ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/trans/' . ($purchaseType == "cash" ? "C_receivings" : "C_bills") . '/delete/' . $list['invoice_no'] . '" onclick="return confirm(\'Are you sure you want to permanent delete? All entries will be deleted permanently\')"; title="Permanent Delete">Delete</a>';

                                        ?>
                                    </li>

                                    <li>
                                        <?php
                                        echo '<a href="' . site_url($langs) . '/trans/' . ($purchaseType == "cash" ? "C_receivings" : "C_bills") . '/printReceipt/' . $list['invoice_no'] . '" title="Print Invoice" target="_blank">PDF</a>';


                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo '<a href="' . base_url() . 'images/purchases/' . $list['document'] . '" target="_blank" >Attachment</a>';
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        <?php

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
                            <th><?php echo lang('total') ?></th>
                            <th class="text-right"></th>
                            <th class="text-right"></th>
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