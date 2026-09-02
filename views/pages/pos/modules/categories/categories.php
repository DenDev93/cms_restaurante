<div class="category-tabs">
    <?php foreach ($categories as $key => $value): ?>
        <button class="category-tab <?php if ($key == 0): ?>active<?php endif ?>" data-category="<?php echo $value->id_category ?>">
            <img src="<?php echo urldecode($value->img_category) ?>" loading="lazy" decoding="async" onerror="this.src='https://placehold.co/50x50/28a745/ffffff?text=+';">
            <?php echo urldecode($value->title_category) ?>
        </button>
    <?php endforeach; ?>
</div>