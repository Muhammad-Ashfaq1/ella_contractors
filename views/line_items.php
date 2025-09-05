<?php 
init_head(); 
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Line Items Management</h4>
                        <hr class="hr-panel-heading" />
                        
                        <!-- Create Line Item Form -->
                        <h5>Add New Line Item</h5>
                        <?php echo form_open_multipart(admin_url('ella_contractors/create_line_item')); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_name">Group <span class="text-danger">*</span></label>
                                        <input type="text" name="group_name" class="form-control" list="group_names" required>
                                        <datalist id="group_names">
                                            <?php foreach ($group_names as $group): ?>
                                                <option value="<?= htmlspecialchars($group); ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cost">Cost ($)</label>
                                        <input type="number" name="cost" class="form-control" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" name="quantity" class="form-control" step="0.01" min="0" value="1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unit_type">Unit Type <span class="text-danger">*</span></label>
                                        <select name="unit_type" class="form-control" required>
                                            <option value="">Select Unit</option>
                                            <?php foreach ($unit_types as $key => $value): ?>
                                                <option value="<?= $key; ?>"><?= $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <small class="text-muted">JPG, PNG, GIF (Max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox">
                                    <input type="checkbox" name="is_active" value="1" checked>
                                    <label>Active</label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Add Line Item</button>
                        <?php echo form_close(); ?>
                        
                        <hr />
                        
                        <!-- Line Items Table -->
                        <h5>Line Items</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Group</th>
                                        <th>Description</th>
                                        <th>Cost</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($line_items as $item): ?>
                                        <tr>
                                            <td>
                                                <?php if ($item['image']): ?>
                                                    <img src="<?= site_url('uploads/ella_line_items/' . $item['image']); ?>" 
                                                         alt="<?= htmlspecialchars($item['name']); ?>" 
                                                         class="img-thumbnail" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="img-thumbnail d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px; background-color: #f8f9fa;">
                                                        <i class="fa fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($item['name']); ?></td>
                                            <td>
                                                <span class="label label-info"><?= htmlspecialchars(isset($item['group_name']) ? $item['group_name'] : 'General'); ?></span>
                                            </td>
                                            <td>
                                                <?= $item['description'] ? htmlspecialchars(substr($item['description'], 0, 30)) . '...' : '-'; ?>
                                            </td>
                                            <td>
                                                <?= $item['cost'] ? '$' . number_format($item['cost'], 2) : 'N/A'; ?>
                                            </td>
                                            <td><?= number_format($item['quantity'], 2); ?></td>
                                            <td><?= htmlspecialchars($item['unit_type']); ?></td>
                                            <td>
                                                <strong>
                                                    <?= $item['cost'] ? '$' . number_format($item['cost'] * $item['quantity'], 2) : 'N/A'; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($item['is_active']): ?>
                                                    <span class="label label-success">Active</span>
                                                <?php else: ?>
                                                    <span class="label label-default">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="#editModal_<?= $item['id']; ?>" class="btn btn-default btn-xs" data-toggle="modal">Edit</a>
                                                    <a href="<?= admin_url('ella_contractors/toggle_line_item_active/' . $item['id']); ?>" 
                                                       class="btn btn-<?= $item['is_active'] ? 'warning' : 'success'; ?> btn-xs"
                                                       onclick="return confirm('Are you sure?')">
                                                        <?= $item['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/delete_line_item/' . $item['id']); ?>" 
                                                       class="btn btn-danger btn-xs"
                                                       onclick="return confirm('Are you sure you want to delete this line item?')">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Edit Modals for each line item -->
                        <?php foreach ($line_items as $item): ?>
                            <div id="editModal_<?= $item['id']; ?>" class="modal fade">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <?php echo form_open_multipart(admin_url('ella_contractors/update_line_item/' . $item['id'])); ?>
                                            <div class="modal-header">
                                                <h4 class="modal-title">Edit Line Item: <?= htmlspecialchars($item['name']); ?></h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="edit_name_<?= $item['id']; ?>">Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" id="edit_name_<?= $item['id']; ?>" class="form-control" value="<?= htmlspecialchars($item['name']); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="edit_group_name_<?= $item['id']; ?>">Group <span class="text-danger">*</span></label>
                                                            <input type="text" name="group_name" id="edit_group_name_<?= $item['id']; ?>" class="form-control" value="<?= htmlspecialchars($item['group_name']); ?>" list="group_names_edit" required>
                                                            <datalist id="group_names_edit">
                                                                <?php foreach ($group_names as $group): ?>
                                                                    <option value="<?= htmlspecialchars($group); ?>">
                                                                <?php endforeach; ?>
                                                            </datalist>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="edit_description_<?= $item['id']; ?>">Description</label>
                                                    <textarea name="description" id="edit_description_<?= $item['id']; ?>" class="form-control" rows="3"><?= htmlspecialchars($item['description']); ?></textarea>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit_cost_<?= $item['id']; ?>">Cost ($)</label>
                                                            <input type="number" name="cost" id="edit_cost_<?= $item['id']; ?>" class="form-control" step="0.01" min="0" value="<?= $item['cost']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit_quantity_<?= $item['id']; ?>">Quantity</label>
                                                            <input type="number" name="quantity" id="edit_quantity_<?= $item['id']; ?>" class="form-control" step="0.01" min="0" value="<?= $item['quantity']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit_unit_type_<?= $item['id']; ?>">Unit Type <span class="text-danger">*</span></label>
                                                            <select name="unit_type" id="edit_unit_type_<?= $item['id']; ?>" class="form-control" required>
                                                                <option value="">Select Unit</option>
                                                                <?php foreach ($unit_types as $key => $value): ?>
                                                                    <option value="<?= $key; ?>" <?= $item['unit_type'] == $key ? 'selected' : ''; ?>><?= $value; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit_image_<?= $item['id']; ?>">Image</label>
                                                            <input type="file" name="image" id="edit_image_<?= $item['id']; ?>" class="form-control" accept="image/*">
                                                            <small class="text-muted">Leave empty to keep current image</small>
                                                            <?php if ($item['image']): ?>
                                                                <div class="mt-2">
                                                                    <img src="<?= site_url('uploads/ella_line_items/' . $item['image']); ?>" class="img-thumbnail" style="max-width: 100px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="is_active" id="edit_is_active_<?= $item['id']; ?>" value="1" <?= $item['is_active'] ? 'checked' : ''; ?>>
                                                        <label>Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Line Item</button>
                                            </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>