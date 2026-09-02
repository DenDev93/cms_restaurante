<?php 

if($_SESSION["admin"]->id_office_admin > 0){

     /*=============================================
    Traer mesas
    =============================================*/
    $url = "tables?linkTo=id_office_table&equalTo=".$_SESSION["admin"]->id_office_admin;
     $method = "GET";
    $fields = array();

    $getTables = CurlController::request($url,$method,$fields);

    if($getTables->status == 200){

        $tables = $getTables->results;

    }else{

       $tables = array(); 
    }

    /*=============================================
    Traer reservas
    =============================================*/

    $url = "relations?rel=books,tables&type=book,table&linkTo=id_office_book&equalTo=".$_SESSION["admin"]->id_office_admin;

    $getBooks = CurlController::request($url,$method,$fields);

    if($getBooks->status == 200){

        $books = $getBooks->results;
        
        /*=============================================
        Organizar la data de las reservas por fechas en un JSON
        =============================================*/

        $reservationsDatabase = [];

        foreach ($books as $key => $value) {
            
            $dateBook = $value->date_book;

            $reservation = [
                'id' => $value->id_book,
                'customerClient' => $value->client_book,
                'time' => TemplateController::formatDate(6, $value->time_book),
                'table' => urldecode($value->title_table).". Capacidad: ".$value->people_table.' personas',
                'phone' => $value->phone_book
            ];

            $reservationsDatabase[$dateBook][] = $reservation;
           
        }

    }else{

        $books = array();
        $reservationsDatabase = null;
    }

}else{

  echo '<script>
    setTimeout(()=>{

        $("#myOffices").modal("show");

    },100);
  </script>';
}


 ?>

<?php if (!empty($tables)): ?>

    <?php if (!empty($books)): ?>

        <input type="hidden" id="reservationsDatabase" value='<?php echo json_encode($reservationsDatabase)  ?>'>
        
    <?php endif ?>

    <link rel="stylesheet" href="/views/assets/css/calendar/calendar.css"> 

    <div class="container-fluid py-3 p-lg-4">
        
        <div class="row">
            
            <!--==============================
              Breadcrumb
             ================================-->
            <div class="col-12 mb-3 position-relative">
                <div class="d-lg-flex justify-content-lg-between mt-2">
                    <div class="text-capitalize h5 ps-2">Gestión de Reservas</div>
                    <div class="pe-0">
                        <ul class="nav justify-content-lg-end">
                            <li class="nav-item">
                                <a class="nav-link py-0 px-0 text-dark" href="/">Inicio</a>
                            </li>
                            <li class="nav-item ps-3">/</li>
                            <li class="nav-item">
                                <a class="nav-link py-0 disabled text-capitalize" href="#">Reservas</a>
                            </li> 
                        </ul>
                    </div>
                </div>
            </div>

            <!--==============================
              Panel de Reservas
             ================================-->
            
            <!-- Calendario y Formulario -->
            <div class="col-12 col-lg-8 mb-3 rounded">
                <div class="card rounded reservation-card">
                    <div class="card-header reservation-header rounded-top">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Nueva Reserva</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Calendario -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="reservation-subtitle">Seleccionar Fecha</h6>
                                <div id="reservationCalendar" class="reservation-calendar"></div>
                            </div>
                        </div>

                        <!-- Formulario de Reserva -->
                        <form id="reservationForm" method="POST">

                            <input type="hidden" id="date_book" name="date_book" >
                            <div class="row">
                                
                                <!-- Información del Cliente -->
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="reservation-subtitle">Información del Cliente</h6>
                                    
                                    <div class="mb-3">
                                        <label for="client_book" class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control reservation-input" id="client_book" name="client_book" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone_book" class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control reservation-input" id="phone_book" name="phone_book" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email_book" class="form-label">Email</label>
                                        <input type="email" class="form-control reservation-input" id="email_book" name="email_book">
                                    </div>
                                </div>

                                <!-- Detalles de la Reserva -->
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="reservation-subtitle">Detalles de la Reserva</h6>
                                    
                                    <div class="mb-3">
                                        <label for="time_book" class="form-label">Hora *</label>
                                        <select class="form-select reservation-input" id="time_book" name="time_book" required>
                                            <option value="">Seleccionar hora</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="13:00">1:00 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                            <option value="15:00">3:00 PM</option>
                                            <option value="16:00">4:00 PM</option>
                                            <option value="17:00">5:00 PM</option>
                                            <option value="18:00">6:00 PM</option>
                                            <option value="19:00">7:00 PM</option>
                                            <option value="20:00">8:00 PM</option>
                                        </select>
                                    </div>
                                     
                                    <div class="mb-3">
                                        <label for="id_table_book" class="form-label">Selección de Mesa *</label>
                                        <select class="form-select reservation-input" id="id_table_book" name="id_table_book" required>
                                            <option value="">Seleccionar</option>

                                            <?php foreach ($tables as $key => $value): ?>

                                                <option value="<?php echo $value->id_table ?>"><?php echo urldecode($value->title_table) ?>. Capacidad: <?php echo $value->people_table ?> personas</option>
                                                                                                    
                                            <?php endforeach ?>

                                        </select>
                                    </div>
                                </div>

                                <!-- Comentarios Especiales -->
                                <div class="col-12 mb-3">
                                    <label for="description_book" class="form-label">Comentarios Especiales</label>
                                    <textarea class="form-control reservation-input" id="description_book" name="description_book" rows="3" placeholder="Alergias, celebraciones, preferencias especiales..."></textarea>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-secondary reservation-btn-secondary rounded" id="clearForm">
                                            <i class="fas fa-times me-1"></i>Limpiar
                                        </button>
                                        <button type="submit" class="btn backColor reservation-btn-primary rounded" id="saveReservation">
                                            <i class="fas fa-save me-1"></i>Guardar Reserva
                                        </button>
                                    </div>
                                </div>

                                <?php 

                                require_once "controllers/books.controller.php";
                                $books = new BooksController();
                                $books->manageBooks();

                                ?>

                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Panel de Reservas del Día -->
            <div class="col-12 col-lg-4 mb-3">
                <div class="card rounded reservation-card">
                    <div class="card-header reservation-header rounded-top">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Reservas de Hoy</h5>
                    </div>
                    <div class="card-body">
                        <div id="todayReservations" class="today-reservations">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                <p>No hay reservas para hoy</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="/views/assets/js/calendar/calendar.js"></script>

<?php else: include "views/pages/welcome/welcome.php" ?>

<?php endif ?>  
