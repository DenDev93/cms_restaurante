<div class="category-tabs mb-4">

    <?php foreach ($categories as $key => $value): ?>

        <button class="category-tab <?php if ($key == 0): ?>active<?php endif ?>" data-category="<?php echo $value->id_category ?>">
            <img src="<?php echo urldecode($value->img_category) ?>" loading="lazy" decoding="async" class="rounded float-left me-2" style="width:50px; height:50px; object-fit: cover; object-position: center;"><?php echo urldecode($value->title_category) ?>
        </button>
        
    <?php endforeach ?>
    
</div>