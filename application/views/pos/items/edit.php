<div class="row">
    <div class="col-md-12">
        <div class="portlet">
        	<div class="portlet-title">
        		<div class="caption">
        			<i class="fa fa-reorder"></i><?php echo lang('update').' '.lang('product'); ?>
        		</div>
        		<div class="tools">
        			<a href="javascript:;" class="collapse"></a>
        			<a href="#portlet-config" data-toggle="modal" class="config"></a>
        			<a href="javascript:;" class="reload"></a>
        			<a href="javascript:;" class="remove"></a>
        		</div>
        	</div>
        	<div class="portlet-body form">
        		<!-- BEGIN FORM-->
        		<?php 
				// var_dump($Item);
                foreach($Item as $keys => $values):
                $attributes = array('class' => 'form-horizontal', 'role' => 'form','enctype'=>"multipart/form-data");
                echo validation_errors();
                echo form_open('pos/Items/edit',$attributes);
                echo  form_hidden('item_id',$values['id']);
                echo  form_hidden('id',$values['id']);
                ?>
               
                	<div class="form-body">
                        <!--/row-->
        				<div class="row">
        					<div class="col-md-6">
                    	            
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo lang('select').' '.lang('picture'); ?></label>
                                        <div class="col-md-9">
                                        <?php
                                        
                                        if($values['picture'] == '' || empty($values['picture']))
                                        {
                                            echo '<img src="'. base_url('images/default-photo.png') .'" width="100" height="100" class="img-rounded" alt="picture"/>';
                                        }else{
                                            echo '<img src="'. base_url('images/items/thumb/'.$values['picture']) .'" width="100" height="100" class="img-rounded" alt="picture"/>';    
                                        }
                                        
                                        ?>
                                        
                                        <input type="hidden" name="picture" value="<?php echo $values['picture']; ?>" />
                                        <input type="file" class="form-control" value="" name="upload_pic" />
                                        </div>
                                    </div>
                                </div>
        					<!--/span-->
							
        				</div>
        				<!--/row-->
                        
        				<!--/row-->
        				<!-- <div class="row">
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Category</label>
        							<div class="col-md-9">
        								<?php echo form_dropdown('category_id',$categoryDDL,$values['category_id'],'class="form-control select2me"'); ?>
        								
        							</div>
        						</div>
        					</div>
        					
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Location</label>
        							<div class="col-md-9">
        								<?php echo form_dropdown('location_code',$locationDDL,$values['location_code'],'class="form-control select2me"'); ?>
        							</div>
        						</div>
        					</div>
        					
        				</div> -->
        				<!--/row-->
                        
                        <div class="row">
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3"><?php echo lang('product').' '.lang('name'); ?></label>
        							<div class="col-md-9">
        								<input type="text" name="name" value="<?php echo $values['name'] ?>" class="form-control" placeholder="Product Name">
        								
        							</div>
        						</div>
        					</div>
        					<!-- <div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Item Type</label>
        							<div class="col-md-9">
										<?php 
											$data = array('purchased' =>'Purchased','service'=>'Service');//,'manufactured'=>'Manufactured' 
											echo form_dropdown('item_type',$data,$values['item_type'],'class="form-control item_type"'); ?>
										</div>
        							</div>
        						</div>
        					</div> -->
        					<!--/span-->
        				</div>
        				<!--/row-->
        				<div class="row">
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3"><?php echo lang('cost') . ' '. lang('price'); ?></label>
        							<div class="col-md-9">
        								<input type="number" name="avg_cost" value="<?php echo $values['avg_cost'] ?>" readonly="" class="form-control">
        							</div>
        						</div>
        					</div>
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3"><?php echo lang('unit') . ' '. lang('price'); ?></label>
        							<div class="col-md-9">
        								<input type="number" name="unit_price" step="0.001" value="<?php echo $values['unit_price'] ?>" class="form-control">
        							</div>
        						</div>
        					</div>
        				</div>
                        <!--/row-->
        				<div class="row">
                            
                            <!-- <div class="col-md-6">
        						<div class="form-group">
                                  <label class="control-label col-sm-3">Units:</label>
                                  <div class="col-sm-9">
                                    <?php echo form_dropdown('unit_id',$unitsDDL,$values['unit_id'],'class="form-control select2me"'); ?>
                                  </div>
                                </div>
                            </div> -->
        					<!--/span-->
                            
        					<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3"><?php echo lang('description'); ?></label>
        							<div class="col-md-9">
        								<textarea class="form-control" name="description"><?php echo $values['description'] ?></textarea>
        							</div>
        						</div>
        					</div>
        					
        					
        				</div>
                        <!--/row-->
        				<div class="row">
                            <!-- <div class="col-md-6">
        						<div class="form-group">
                                  <label class="control-label col-sm-3">Tax:</label>
                                  <div class="col-sm-9">
                                    <?php echo form_dropdown('tax_id',$taxesDDL,$values['tax_id'],'class="form-control select2me"'); ?>
                                  </div>
                                </div>
                            </div> -->
        					<!--/span-->
                            <!-- <div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Re-Stock Level</label>
        							<div class="col-md-9">
        								<input type="number" name="reorder_level" value="<?php echo $values['re_stock_level'] ?>" class="form-control">
        							</div>
        						</div>
        					</div> -->
        					
        				</div>
                        <!-- <div class="row">
							<div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Barcode</label>
        							<div class="col-md-9">
        								<input type="text"  name="barcode" value="<?php echo $values['barcode'] ?>" class="form-control" />
        							</div>
        						</div>
        					</div>
						</div> -->
                    <!--    
        				<h3 class="form-section">Select Sizes and Enter Barcode</h3>
        				
                        <div class="row">
                            <div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Size</label>
        							<div class="col-md-9">
        								<?php echo form_dropdown('size_id',$sizesDDL,$values['size_id'],'class="form-control"'); ?>
        							</div>
        						</div>
        					</div>
                            <div class="col-md-6">
        						<div class="form-group">
        							<label class="control-label col-md-3">Barcode</label>
        							<div class="col-md-9">
        								<input type="text" name="barcode" value="<?php echo $values['barcode'] ?>" class="form-control">
        							</div>
        						</div>
        					</div>
     					
                        </div>
        				/row-->
        			</div>
        			<div class="form-actions">
        				<div class="row">
        					<div class="col-md-6">
        						<div class="row">
        							<div class="col-md-offset-3 col-md-9">
        								<button type="submit" class="btn btn-success"><?php echo lang('update'); ?></button>
        								<button type="button" onclick="window.history.back();" class="btn btn-default"><?php echo lang('back'); ?></button>
        							</div>
        						</div>
        					</div>
        					<div class="col-md-6">
        					</div>
        				</div>
        			</div>
        		<?php endforeach;
                echo form_close();?>
        		<!-- END FORM-->
        	</div>
        </div>
  </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
