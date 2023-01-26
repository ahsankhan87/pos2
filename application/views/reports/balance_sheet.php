<!-- <div class="note note-warning hidden-print">
    <p>
        - If you want to distribute the profit among multiple partners then press below button and run Retained Earning Report.<br />
        - All Profit or Loss will be credted or debited accordingly to Retained Earning Account.<br />
        - Then Distribute profit/loss through Journal Entry.
        <a href="<?php echo site_url('reports/C_profitloss/run_pl_report') ?>" class="btn btn-success">Run Retained Earning Report</a>
    </p>
</div> -->
<div class="row hidden-print">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i> Select From and To Dates
                </div>
                <div class="tools">
                    <a href="" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="" class="reload"></a>
                    <a href="" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body">
                <form class="form-inline" method="post" action="<?php echo site_url('reports/C_balancesheet') ?>" role="form">
                    <div class="form-group">
                        <label for="exampleInputEmail2"><?php echo lang('from') . ' ' . lang('date') ?></label>
                        <input type="date" class="form-control" name="from_date" value="<?php echo date("Y-m-d"); ?>" placeholder="From Date">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword2"><?php echo lang('to') . ' ' . lang('date') ?></label>
                        <input type="date" class="form-control" name="to_date" value="<?php echo date("Y-m-d"); ?>" placeholder="To Date">
                    </div>

                    <button type="submit" class="btn btn-default"><?php echo lang('search') ?></button>
                </form>
            </div>
        </div>
        <!-- END SAMPLE FORM PORTLET-->
    </div>
</div>
<!-- END PAGE CONTENT-->

<div class="row">
    <div class="col-sm-8 col-sm-offset-2 border">
        <div class="text-center">
            <?php echo anchor('reports/C_balancesheet/printPDF/' . $from_date . '/' . $to_date, "<i class='fa fa-print'></i> Print", "target='_blank'"); ?>
            <h3><?php echo ucfirst($this->session->userdata("company_name")); ?></h3>
            <h4 style="margin-bottom:2px;"><?php echo $main; ?></h4>
            <p><?php echo date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)); ?></p>
        </div>
        <h3>Assets</h3>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><?php echo lang('account') ?></th>
                    <th class="text-right"><?php echo lang('total') ?></th>
                </tr>
            </thead>
            <tbody>

                <?php
                $asset_total = 0;

                foreach ($parentGroups4Assets as $key => $list) {
                    $bl_report = $this->M_groups->get_GroupsByParent($list['account_code']);

                    echo '<tr><td colspan="2">';
                    echo '<strong>' . (count($bl_report) > 0 ? ($langs == 'en' ? $list['title'] : $list['title_ur']) : '') . '</strong>';
                    echo '</td></tr>';

                    ///////
                    //$bl_report = $this->M_reports->get_Assets4BalanceSheet($_SESSION['company_id'],$list['account_code'],$from_date,$to_date);

                    foreach ($bl_report as $key => $values) :

                        $dr = $this->M_entries->balanceByAccount($values['account_code'], $from_date, $to_date)[0]['debit'];
                        $cr = $this->M_entries->balanceByAccount($values['account_code'], $from_date, $to_date)[0]['credit'];
                        $balance = ($dr + $values['op_balance_dr']) - ($values['op_balance_cr'] + $cr);

                        if ($balance != 0) {
                            echo '<tr><td>';
                            echo '&nbsp;&nbsp;';
                            echo ($langs == 'en' ? $values['title'] : $values['title_ur']);
                            echo '</td>';

                            echo '<td class="text-right">';
                            echo number_format($balance, 2);
                            echo '</td>';

                            //echo '<td>';
                            $asset_total += $balance;
                            //echo '</td>
                            echo '</tr>';
                        }
                    endforeach;
                    /////
                }
                ?>

            </tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>

                    <td class="text-right"><strong><?php echo '<small>' . $_SESSION['home_currency_symbol'] . '</small>'; ?><?php echo number_format($asset_total, 2); ?></strong></td>
                </tr>
            </tfoot>

        </table>

    </div>
    <!-- /.col-sm-6 -->
    <div class="col-sm-8 col-sm-offset-2 border">
        <h3>Liabilities and Equity</h3>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><?php echo lang('account') ?></th>
                    <th class="text-right"><?php echo lang('total') ?></th>
                </tr>
            </thead>
            <tbody>

                <?php
                $total = 0;

                foreach ($Liability4BalanceSheet as $key => $list) {
                    $bl_report = $this->M_groups->get_GroupsByParent($list['account_code']);

                    echo '<tr><td colspan="2">';
                    echo '<strong>' . (count($bl_report) > 0 ? ($langs == 'en' ? $list['title'] : $list['title_ur']) : '') . '</strong>';
                    echo '</td></tr>';

                    ///////
                    //$bl_report = $this->M_reports->get_Liability4BalanceSheet($_SESSION['company_id'],$list['account_code'],$from_date,$to_date);
                    foreach ($bl_report as $key => $values) :

                        $dr = $this->M_entries->balanceByAccount($values['account_code'], $from_date, $to_date)[0]['debit'];
                        $cr = $this->M_entries->balanceByAccount($values['account_code'], $from_date, $to_date)[0]['credit'];
                        $balance = ($values['op_balance_cr'] + $cr) - ($dr + $values['op_balance_dr']);

                        //if ($balance != 0) {
                        echo '<tr><td>';
                        echo '&nbsp;&nbsp;';
                        echo ($langs == 'en' ? $values['title'] : $values['title_ur']);
                        echo '</td>';

                        echo '<td class="text-right">';
                        echo number_format($balance, 2);
                        echo '</td>';

                        //echo '<td>';
                        $total += $balance;
                        //echo '</td>
                        echo '</tr>';
                    //}
                    endforeach;
                    /////
                }

                echo '<tr><td>';
                echo 'Net Income';
                echo '</td>';

                echo '<td class="text-right">';
                echo number_format((float)abs($net_income), 2);
                echo '</td>';

                //echo '<td>';
                $total += abs($net_income);
                //echo '</td>';
                echo '</tr>';
                ?>

            </tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>

                    <td class="text-right"><?php echo '<small>' . $_SESSION['home_currency_symbol'] . '</small>'; ?><strong><?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tfoot>

        </table>

    </div>
    <!-- /.col-sm-6 -->
</div>
<!-- /.row -->