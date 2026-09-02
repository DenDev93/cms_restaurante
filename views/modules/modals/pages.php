<!-- The Modal -->
<div class="modal fade" id="myPage">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded">

      <form method="POST" class="needs-validation" novalidate>

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title text-capitalize">Páginas</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body px-4">

          <div class="form-group mb-3">

            <label for="title_page">Título<sup>*</sup></label>

            <input 
            type="text"
            class="form-control rounded form-control-sm"
            id="title_page"
            name="title_page"
            required
            >

            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

          <div class="form-group mb-3">

            <label for="url_page">URL<sup>*</sup></label>

            <input 
            type="text"
            class="form-control rounded form-control-sm"
            id="url_page"
            name="url_page"
            required
            >
            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

          <div class="form-group mb-3">

            <label for="icon_page">Icono<sup>*</sup></label>

            <input 
            type="text"
            class="form-control rounded form-control-sm cleanIcon"
            id="icon_page"
            name="icon_page"
            required
            >
            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

          <div class="form-group mb-3">

            <label for="type_page">Tipo de Página<sup>*</sup></label>

            <select
            class="form-select form-select-sm rounded" 
            name="type_page" 
            id="type_page">
              
              <option value="modules">Modular</option>
              <option value="custom">Personalizable</option>
              <option value="external_link">Enlace Externo</option>
              <option value="internal_link">Enlace Interno</option>
              <option value="submenu">Submenú</option>

            </select>

            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

          <div class="form-group mb-3">

            <label for="menu_type_page">Tipo de Menú<sup>*</sup></label>

            <select
            class="form-select form-select-sm rounded" 
            name="menu_type_page" 
            id="menu_type_page"
            onchange="changeMenuType(this)">
              
              <option value="0">Principal</option>
              <option value="1">Padre</option>
              <option value="2">Hija</option>

            </select>

            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

          <div class="form-group mb-3" id="parent_id" style="display:none">

            <label for="parent_id_page">Selecciona página Padre<sup>*</sup></label>

            <select
            class="form-select form-select-sm rounded" 
            name="parent_id_page" 
            id="parent_id_page">
              
              <option value="0">Seleccione</option>
              <?php if (!empty($pages)): ?>
                <?php foreach ($pages as $key => $value): ?>
                  <?php if ($value->menu_type_page == 1): ?>
                    <option value="<?php echo $value->id_page ?>"><?php echo $value->title_page ?></option>
                  <?php endif ?>
                  
                <?php endforeach ?>
                
              <?php endif ?>

            </select>

            <div class="valid-feedback">Válido.</div>
            <div class="invalid-feedback">Campo inválido.</div>

          </div>

        </div>

        <!-- Modal footer -->
        <div class="modal-footer d-flex justify-content-between">
          
          <div><button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cerrar</button></div>
          <div><button type="submit" class="btn btn-default backColor rounded">Guardar</button></div>
          
        </div>

      </form>

    </div>
  </div>
</div>