<?php if ($module->columns[$i]->type_column == "double" || $module->columns[$i]->type_column == "money"): ?>

 	<input 
	type="number" 
	step="any"
	class="form-control rounded <?php if ($module->columns[$i]->title_column == "qty_purchase" || $module->columns[$i]->title_column == "cost_purchase"): ?> changePurchase <?php endif ?><?php if($module->columns[$i]->title_column == "utility_food" || $module->columns[$i]->title_column == "products_food"): ?> changeUtilityFood <?php endif?>"
	<?php if ($module->columns[$i]->title_column == "unit_cost_purchase" || $module->columns[$i]->title_column == "stock_purchase" || $module->columns[$i]->title_column == "cost_food" || $module->columns[$i]->title_column == "price_food"): ?> readonly <?php endif ?>
	id="<?php echo $module->columns[$i]->title_column ?>"
	name="<?php echo $module->columns[$i]->title_column ?>"
	value="<?php if (!empty($data)): ?><?php echo urldecode($data[$module->columns[$i]->title_column]) ?><?php endif ?>">
 	
<?php endif ?>
