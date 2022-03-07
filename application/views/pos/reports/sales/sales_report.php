<div class="row">
    <div class="col-sm-12">
        <h1 class="page-header lead"><span id="print_title"><?php echo $main; ?></span></h1>
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-sm-6">
    
        <form role="form" action="<?php echo site_url('pos/Report_pos/salesReport')?>" method="post" class="form-horizontal">
            <div class="form-group">
               <label class="control-label col-sm-4">Select From Date:</label>
               <div class="input-group date">
                <input type="text" class="form-control" id="datepicker" name="from_date" autocomplete="off"  />
                <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                
               </div>
              
            </div>
            
            <div class="form-group">
               <label class="control-label col-sm-4">Select To Date</label>
               <div class="input-group date">
                <input type="text" class="form-control" id="Todatepicker" name="to_date" autocomplete="off" />
                <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
               </div>
            </div>
            
             <div class="form-group">
                <div class="col-sm-offset-10 col-sm-12">
                  <button type="submit" class="btn btn-default">Submit</button>
                </div>
             </div>
        </form>
    </div>
</div>

<?php
if(count(@$sales_report))
{
?>
<div class="row">
    <div class="col-sm-12">
        <h2 class="page-header lead"><span id="print_title">Sale Report From <strong><?php echo $from_date . ' To ' . $to_date; ?></strong></h2></span>
    </div>
    <!-- /.col-sm-12 -->
</div>

<div class="row">
    <div class="col-sm-12">
    
        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice ID</th>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Qty Sold</th>
                    <th>Cost Price</th>
                    <th>Unit Price</th>
                    
                    
                </tr>
            </thead>
            
            <tbody>
        <?php
         $unit_price =0;
         $cost_price=0;
        foreach($sales_report as $key => $list)
        {
            // echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
            echo '<td>'.$list['sale_date'].'</td>';
            echo '<td>'.anchor('pos/c_sales/receipt/'.$list['sale_id'],$list['sale_id'],'target="_blank"').'</td>';
            echo '<td>'.$this->M_items->get_ItemName($list['item_id']).'</td>';
            
            //if($list['color_id'] != 0)
//            {
//                $color = $this->m_colors->get_activeColors($list['color_id']);
//                echo '<td>'.@$color[0]['name'].'</td>';
//            }
//            else
//            {
//                echo '<td>--</td>';
//            }
            if($list['size_id'] != 0)
            {
                $size = $this->m_sizes->get_activeSizes($list['size_id']);
                echo '<td>'.@$size[0]['name'].'</td>';
            }
            else
            {
                echo '<td>--</td>';
            }
            echo '<td>'.$list['quantity_sold'].'</td>';
            echo '<td>'.$list['item_cost_price'].'</td>';
            echo '<td>'.$list['item_unit_price'].'</td>';
            
            
            $cost_price += $list['item_cost_price']*$list['quantity_sold'];
            $unit_price  += $list['item_unit_price']*$list['quantity_sold'];
            
           // echo  anchor(site_url('up_property_images/upload_images/'.$list['id']),' upload Images'). '</td>';
            echo '</tr>';
        }
        $u_price = $unit_price;
        $c_price = $cost_price;
        $income = ($u_price-$c_price);
        echo '</tbody>';
        echo '</table>';
        
        ?>
        
        
    </div>
    <hr />
</div>

<div class="row">
    <div class="col-sm-3">
        <table class="table table-striped">
            <tr>
                <th>Total Sales</th>
                <th><?php echo ceil($u_price); ?></th>
            </tr>
            <tr>
                <th>Total Income</th>
                <th><?php echo ceil($income); ?></th>
            </tr>
        </table>
    </div>
        <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
<?php } ?>