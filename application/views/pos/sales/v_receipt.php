<button class="hidden-print"><a href="<?php echo site_url('trans/C_sales/editSales/' . $invoice_no) ?>" title="edit">Edit</a></button>
<button class="hidden-print"><a href="#" onclick="window.print()" title="print">Print</a></button>
<button class="hidden-print"><a href="<?php echo site_url('trans/C_sales/allSales') ?>">Sales</a></button>
<style>
    .invoice{
        background-color: #fff;
        border: solid 3px #0d83dd;
        padding: 10px;
    }
    .invoice-header{
        background-color: #0d83dd;
        padding: 10px;
        margin:-10px -10px 0 -10px;

    }  
    .invoice-header{
        color: #a9d0ec;
    }    

    .grey{
        color: gray;
    }
    .blue-title{
        color: #0d83dd;
    }
    
    .table thead tr{
        border-top:#0d83dd solid 3px;
    }
    .item-desc{
        font-size: 11px;
        color: grey;
    }
</style>
<?php 
    $counter = 1;
    $total = 0;
    $discount = 0;
    $discount_total = 0;
    $total_tax_amount = 0;
?>

<!-- BEGIN PAGE CONTENT-->
<div class="invoice">
    <div class="invoice-header">
        <div class="row">
            <div class="col-sm-4 col-xs-4">
                <h1>INVOICE</h1>
            </div>
            <div class="col-sm-5 col-xs-5">

            </div>
            <div class="col-sm-3 col-xs-3">
                <h3><?php echo $Company[0]['name']; ?></h3>
                <span class="text-capitalize"><?php echo $Company[0]['address']; ?></span><br />
                <span class=""><?php echo $Company[0]['contact_no']; ?></span>
            </div>
        </div>
    </div> 
    
    <br>
    <div class="row invoice-logo">
        <!-- <div class="col-sm-2 col-xs-2 invoice-logo-space">
            <?php if (!empty($Company[0]['image']) || $Company[0]['image'] != '') {
                echo '<img src="' . base_url('images/company/thumb/' . $Company[0]['image']) . '" width="100" height="100" class="img-rounded" alt="picture"/>';
            } else {

                echo '<img src="' . base_url('images/company/thumb/default-logo.png') . '" width="100" height="100" class="img-rounded" alt="picture"/>';
            }
            ?>

        </div> -->
        <div class="col-sm-4 col-xs-4">
            <div class="grey">Billed To</div>
            <div>
                <?php $customer =  @$this->M_customers->get_customers(@$sales_items[0]['customer_id']); ?>
                <?php echo @$customer[0]['store_name']; ?><br />
                <?php echo @$customer[0]['address']; ?> <br />
                <?php echo @$customer[0]['mobile_no']; ?><br />
            </div>
        </div>
        <div class="col-sm-2 col-xs-2">
            <div class="grey">Invoice Number</div>
            <div class=""><?php echo $invoice_no ?></div>
            <br>
            <div class="grey">Date of Issue</div>
            <div class=""><?php echo date('m/d/Y', strtotime(@$sales_items[0]['sale_date'])) ?></div>
            
        </div>
        <div class="col-sm-6 col-xs-6">
            <p>
                <!-- TAX INVOICE <br> -->
                
                <span style="font-size: 26px; color:grey">Invoice Total<br />
                    <php echo $total; ?>
                </span>
            </p>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped table-hover">
                <thead class="blue-title">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            <?php echo lang('product'); ?>
                        </th>
                        <th class="hidden-480">
                            <?php echo lang('description'); ?>

                        </th>
                        <th class="hidden-480 text-right">
                            <?php echo lang('quantity'); ?>

                        </th>
                        <th class="hidden-480 text-right">
                            <?php echo lang('price'); ?>

                        </th>
                        <th class="hidden-480 text-right">
                            <?php echo lang('disc'); ?>

                        </th>
                        <th class="hidden-480 text-right">
                            <?php echo lang('tax'); ?>

                        </th>
                        <th class="text-right">
                            <?php echo lang('total'); ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    foreach ($sales_items as $key => $list) {

                        $total_cost = ($list['item_unit_price'] * $list['quantity_sold']) - $list['discount_value'];
                        $discount += $list['discount_value'];
                        $tax_amount = $total_cost * $list['tax_rate'] / 100;
                        //$item = $this->M_items->get_items($list['item_id']);
                        $account_name = $this->M_groups->get_accountName($list['account_code']);

                        $symbol = $_SESSION['home_currency_symbol'];

                        echo '<tr>';
                        echo '<td style="text-align:center;" >' . $counter++ . '</td>';
                        //echo '<td>' . $account_name . '<div class="item-desc">'.$list['item_desc']. '</div></td>';
                        echo '<td>' . $account_name . '</div></td>';
                        echo '<td style="text-align:left;" class="hidden-480">' . $list['item_desc'] . '</td>';
                        echo '<td style="text-align:right;" class="hidden-480">' . $list['quantity_sold'] . ' ' . $this->M_units->get_unitName($list['unit_id']) . '</td>';
                        echo '<td style="text-align:right;" class="hidden-480">' . $symbol . round($list['item_unit_price'], 2) . '</td>';
                        echo '<td style="text-align:right;" class="hidden-480">' . round($list['discount_value'], 2) . '</td>';
                        echo '<td style="text-align:right;" class="hidden-480">' . round($tax_amount, 2) . '</td>';
                        echo '<td style="text-align:right;" >' . $symbol . round($total_cost + $tax_amount, 2) . '</td>';

                        echo '</tr>';

                        $total += ($list['item_unit_price'] * $list['quantity_sold']);
                        //$discount_total += (($list['item_unit_price']*$list['quantity_sold'])*$list['discount_value']/100);
                        $total_tax_amount += $tax_amount;
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <?php if (@$sales_items[0]['description']) { ?>
                <br />
                <div class="well">
                    <?php echo @$sales_items[0]['description']; ?>
                </div>
            <?php } ?>
        </div>
        <div class="col-xs-4 invoice-block">
            <ul class="list-unstyled amounts">
                <!--
                            <li>
								<strong>Sub - Total amount:</strong> <?php echo $symbol . round($total, 2); ?>
							</li>
							-->
                <li>
                    <strong class="blue-title"><?php echo lang('total') . ' ' . lang('disc'); ?>:</strong> <?php echo $symbol . round($discount, 2); ?>
                </li>
                <li>
                    <strong class="blue-title"><?php echo lang('total') . ' ' . lang('tax'); ?>:</strong> <?php echo $symbol . round($total_tax_amount, 2); ?>
                </li>
                <li>
                    <strong class="blue-title"><?php echo lang('grand') . ' ' . lang('total'); ?>:</strong> <?php echo $symbol . round(@$total - $discount + $total_tax_amount, 2); ?>
                </li>
            </ul>
        </div>
    </div>
    <!--
                <table class="table table-condensed" style="font-size: 10px;">
                    <tbody>
                        <tr>
                            <td>Claims<br />
                            Please check your sold items properly before leaving our Customer Counter, we will not be responsible for any kind of claim or damage of the product
                            once you have taken the goods out of office premises.
                            </td>
                        </tr>
                        <tr>
                            <td>
                            Warranty<br />
                            Warranty products will be handled according to our warranty policy, we usually take 5 to 6 days to process any warranty item. Ask the sales representative
                            for individual item warranty policy
                            </td>
                        </tr>
                        <tr>
                            <td>
                            Cheque payments<br />
In case your cheque is dishonored by bank, we will charge you an extra Rs 400/- per cheque presentation and if this becomes a regular case, we may close
your account. Your non-payment of these charges can also harm our relations which may result in non-acceptance of your cheques in future.
                            </td>
                        </tr>
                        <tr>
                            <td>
                            Return<br />
The goods are not allowed to be taken for demonstration purposes. A product will be considered sold as soon as it is taken out of the office premises.
In case you wish to return the product which is still sealed pack, we will charge restocking and processing fee which may vary between 10-50% depending on
the duration after which you wish to return.<br />
In case you wish to return the product which has been used, we will charge restocking and processing fee which may vary between 25-75% depending on the
condition of the product.<br />
It is completely on our discretion to accept any return or not.
                            </td>
                        </tr>
                        <tr>
                            <td>
                            We expect your complete co-operation in these matters which will help us to serve you in the best possible manner.
                            </td>
                        </tr>
                        <tr>
                            <td>
                            UBNT AND MIKROTIK PRODUCTS : 3 Months warranty and No LAN PORT Warranty
                            </td>
                        </tr>
                    </tbody>
                </table>-->

    <!-- <br />
    <div class="text-left">Powered by: <i>khybersoft.com</i> </div> -->

</div>
<!-- END PAGE CONTENT-->