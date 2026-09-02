<?php if ($module->columns[$i]->type_column == "html"): ?>

	<textarea 
	class="form-control rounded"
	id="<?php echo $module->columns[$i]->title_column ?>" 
	name="<?php echo $module->columns[$i]->title_column ?>"><?php if (!empty($data)): ?><?php echo urldecode($data[$module->columns[$i]->title_column]) ?><?php endif ?></textarea>

<?php endif ?>