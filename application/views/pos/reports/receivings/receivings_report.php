<div class="row">
    <div class="col-sm-12">
        <h1 class="page-header lead"><span id="print_title"><?php echo $main; ?></span></h1>
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-sm-6">
    
        <form role="form" action="<?php echo site_url('pos/Report_pos/receivingsReport')?>" method="post" class="form-horizontal">
            <div class="form-group">
               <label class="control-label col-sm-4">Select From Date:</label>
               <div class="input-group date">
                <input type="text" class="form-control" id="datepicker" name="from_date" autocomplete="off" />
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
             
                 <label class="control-label col-sm-2" for="">Employee</label>
                 <div class="col-sm-4">
                    <?php echo form_dropdown('emp_id',$emp_DDL,'','class="form-control select2me"') ?>
                    
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
if(count(@$receivings))
{
?>
<div class="row">
    <div class="col-sm-12">
        <h2 class="page-header lead">Receivings Report From <strong><?php echo $from_date . ' To ' . $to_date; ?></strong></h2>
    </div>
    <!-- /.col-sm-12 -->
</div>

<div class="row">
    <div class="col-sm-12">
    
        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Inv#</th>
                    <th>Emp</th>
                    <th>Product</th>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Qty Purchased</th>
                    <th>Cost Price</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            
            <tbody>
        <?php
        $unit_price =0;
        $cost_price=0;
        
        foreach($receivings as $key => $list)
        {
            // echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
            echo '<td>'.$list['receiving_date'].'</td>';
            //echo '<td>'.anchor(''.$list['receiving_id'],$list['receiving_id']).'</td>';
            echo '<td><a href="'.site_url('pos/c_receivings/receipt/'.$list['receiving_id']).'" target="_blank">'.$list['receiving_id'].'</a></td>';
            echo '<td>'.$this->M_employees->get_empName($list['employee_id']).'</td>';
            
            echo '<td>'.$this->M_items->get_ItemName($list['item_id']).'</td>';
            
            if($list['color_id'] != 0)
            {
                $color = $this->m_colors->get_activeColors($list['color_id']);
                echo '<td>'.@$color[0]['name'].'</td>';
            }
            else
            {
                echo '<td>--</td>';
            }
            if($list['size_id'] != 0)
            {
                $size = $this->m_sizes->get_activeSizes($list['size_id']);
                echo '<td>'.@$size[0]['name'].'</td>';
            }
            else
            {
                echo '<td>--</td>';
            }
            echo '<td>'.$list['quantity_purchased'].'</td>';
            echo '<td>'.$list['item_cost_price'].'</td>';
            echo '<td>'.$list['item_unit_price'].'</td>';
          
            
            
            $cost_price  += $list['item_cost_price']*$list['quantity_purchased'];
            $unit_price += $list['item_unit_price']*$list['quantity_purchased'];
           // echo  anchor(site_url('up_property_images/upload_images/'.$list['id']),' upload Images'). '</td>';
            echo '</tr>';
        }
        $u_price = $unit_price;
        $c_price = $cost_price;
        //$income = ($c_price-$u_price);
        
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
                <th>Total Purchases</th>
                <th><?php echo ceil($c_price); ?></th>
            </tr>
            
        </table>
    </div>
   <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
<?php } ?>