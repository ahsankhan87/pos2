<div class="row">
    <div class="col-sm-12">
        <div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-reorder"></i>User Form
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
                    $attributes = array('class' => 'horizontal-form','enctype'=>"multipart/form-data");
                    echo validation_errors();
                    echo form_open('setting/users/C_users/create',$attributes);
                    ?>
					<div class="form-body">
						<h3 class="form-section"><?php echo lang('user') . ' ' .lang('information'); ?></h3>
                        
                        <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label><?php echo lang('role'); ?></label>
									<select class="form-control" name="role">
                                        <option value="">Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select>
								</div>
							</div>
							<!--/span-->
                            
                        </div>
                        
                        <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label><?php echo lang('full'). ' '. lang('name'); ?></label>
									<input type="text" name="name" class="form-control" value="<?php echo set_value('name'); ?>"/>
								</div>
							</div>
							<!--/span-->
                            <div class="col-md-6">
								<div class="form-group">
									<label><?php echo lang('username'); ?></label>
									<input type="text" name="username" class="form-control" value="<?php echo set_value('username'); ?>"/>
								</div>
							</div>
							<!--/span-->
                        </div>
                        <div class="row">
							
							<div class="col-md-6">
								<div class="form-group">
									<label><?php echo lang('password'); ?></label>
									<input type="password" name="password" class="form-control" value="<?php echo set_value('password'); ?>"/>
								</div>
							</div>
							<!--/span-->
                            <div class="col-md-6">
								<div class="form-group">
									<label><?php echo lang('confirm'). ' '.lang('password'); ?></label>
									<input type="password" name="confirm_password" class="form-control" value="<?php echo set_value('confirm_password'); ?>"/>
								</div>
							</div>
							<!--/span-->
							
						</div>
						<!--/row-->
                        
                        <h3 class="form-section"><?php echo lang('module'). ' '. lang('permission'); ?></h3>
                        <?php $url1 = $this->uri->segment(1); ?>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>All Modules</label>
									<div class="checkbox-list">
                                    <?php foreach($activeModules as $values): ?>
										<label>
										<div class="col-sm-12">
                                            <span>
                                            <input name="modules[]" value="<?php echo $values['id'] ?>" <?php ($values['id'] == 1 ? 'required=""' : '') ?> type="checkbox">&nbsp;<?php echo ($url1 == 'tr' ? $values['title_ur'] : $values['title']) ?>
                                            </span>
                                        </div>
                                        
                                        </label>
                                        
                                        <?php  $sub_modules = $this->M_modules->get_modulesByParent($values['id']); 
                                        //var_dump($sub_modules);
                                        $i = 0;
                                        foreach($sub_modules as $sub_module): 
                                        
                                            
                                        ?>
                                        <label>
                                        <div class="col-sm-offset-1 col-sm-4">
                                            <span>
                                            
                                            <input name="sub_module[<?php echo $i; ?>]" value="<?php echo $sub_module['id'] ?>" type="checkbox">&nbsp;<?php echo ($url1 == 'tr' ? $sub_module['title_ur'] : $sub_module['title'])  ?>
                                                                                                                                    
                                            
                                            </span>
                                        </div>
                                        <!--
                                        <div class="col-sm-7">    
                                            <span>
                                            <input name="can_create[<?php echo $i; ?>]" value="1" type="checkbox" id="can_create[]">&nbsp;<label for="can_create">can create</label>
                                            </span>
                                            
                                            <span>
                                            <input name="can_update[<?php echo $i; ?>]" value="1" type="checkbox" id="can_update[]">&nbsp;<label for="can_update">can update</label>
                                            </span>
                                            
                                            <span>
                                            <input name="can_delete[<?php echo $i; ?>]" value="1" type="checkbox" id="can_delete[]">&nbsp;<label for="can_delete">can delete</label>
                                            </span>
                                        </div>  
                                        -->
                                        </label>
									<?php $i++;
                                            endforeach; // end sub module loop
                                            
									 endforeach; ?>	
									</div>
								</div>
							</div>
							<!--/span-->
						</div>
						<!--/row-->
                        
					</div>
					<div class="form-actions right">
						<button type="submit" class="btn btn-info"><i class="fa fa-check"></i> <?php echo lang('save'); ?></button>
						<button type="button" onclick="window.history.back()" class="btn btn-default"><?php echo lang('back'); ?></button>
						
					</div>
				<?php echo form_close(); ?>
				<!-- END FORM-->
			</div>
		</div>

    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->