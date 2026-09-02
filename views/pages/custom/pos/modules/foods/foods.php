<?php foreach ($categories as $key => $value): ?>

  <div class="menu-category <?php if ($key == 0): ?>active<?php endif ?>" id="<?php echo $value->id_category ?>">
                
      <div class="row g-3">

        <?php foreach (($value->foods ?? array()) as $index => $item): ?>

          <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="menu-item" data-item="<?php echo urldecode($item->id_food) ?>" data-price="<?php echo $item->price_food ?>">
                  <div class="menu-item-image">
                      <img src="<?php echo urldecode($item->img_food) ?>" alt="<?php echo urldecode($item->title_food) ?>" onerror="this.src='https://placehold.co/100x100'" style="width: 100%;aspect-ratio: 1 / 1; object-fit: cover; object-position: center; display: block; ">
                  </div>
                  <div class="menu-item-info">
                      <h6 class="menu-item-name"><?php echo urldecode($item->title_food) ?></h6>
                      <span class="menu-item-price"><?php echo fncMoney($item->price_food) ?></span>
                  </div>
              </div>
          </div>
          
        <?php endforeach ?>
          
      </div>
  </div>

<?php endforeach ?>