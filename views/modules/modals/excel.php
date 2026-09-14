<!-- The Modal -->
<div class="modal fade" id="myExcel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded">

      <form method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title text-capitalize">Subir Excel</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body p-4">

          <input type="hidden" id="titleTable" name="titleTable">

          <label for="archivo_excel" class="form-label">Selecciona archivo Excel:</label>
          <input type="file" class="form-control" id="archivo_excel" name="archivo_excel" accept=".xls,.xlsx" required>

          <div class="valid-feedback">Válido.</div>
          <div class="invalid-feedback">Campo inválido.</div>

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