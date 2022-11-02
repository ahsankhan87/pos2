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
        <div class="row">
            <div class="col-md-6">
                <?php echo anchor('trans/C_suppliers/create', lang('add_new') . ' <i class="fa fa-plus"></i>', 'class="btn btn-success" id="sample_editable_1_new"'); ?>
                <!-- <?php echo anchor('trans/C_suppliers/SupplierImport', 'Import Suppliers', 'class="btn btn-success"'); ?> -->
            </div>
        </div>
        <br>
        <?php
        if (count($suppliers)) {
        ?>
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i><span id="print_title"><?php echo $main; ?></span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"></a>
                        <a href="#portlet-config" data-toggle="modal" class="config"></a>
                        <a href="javascript:;" class="reload"></a>
                        <a href="javascript:;" class="remove"></a>
                    </div>
                </div>
                <div class="portlet-body flip-scroll">

                    <table class="table table-striped table-bordered table-condensed flip-content" id="sample_2">
                        <thead class="flip-content">
                            <tr>
                                <th>id</th>
                                <th><?php echo lang('name'); ?></th>
                                <th><?php echo lang('email'); ?></th>
                                <th><?php echo lang('contact'); ?></th>
                                <th><?php echo lang('address'); ?></th>
                                <th><?php echo lang('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            foreach ($suppliers as $key => $list) {
                                
                                echo '<tr>';
                                echo '<td>' . $list['id'] . '</td>';
                                // echo '<td><a href="' . site_url('trans/Suppliers/supplierDetail/' . $list['id']) . '">' . $list['name'] . ' </a></td>';
                                echo '<td>' . $list['name'] . '</td>';
                                echo '<td>' . $list['email'] . '</td>';
                                echo '<td>' . $list['contact_no'] . '</td>';
                                echo '<td>'.$list['address'].'</td>';

                                echo '<td>';
                                //echo  anchor(site_url('up_supplier_images/upload_images/'.$list['id']),' upload Images');
                            ?>
                                <?php echo anchor('trans/C_suppliers/edit/' . $list['id'], '<i class="fa fa-pencil-square-o fa-fw"></i>', 'title="Edit"'); ?> |
                                <a href="<?php echo site_url('trans/C_suppliers/delete/' . $list['id']) ?>" onclick="return confirm('Are you sure you want to permanent delete supplier and his account transactions?')" title="Permanent Delete"><i class="fa fa-trash-o fa-fw"></i></a>

                          <?php
                                echo '</td>';
                                echo '</tr>';
                            }
                           
                        }
                          ?>
                        </tbody>
                        </table>
                </div>
            </div>
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->