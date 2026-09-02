$(document).ready(function() {

	/*=============================================
    Variables globales
    =============================================*/

    let selectedDate = null;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let reservationsDatabase = {};

    /*=============================================
    Capturar reservas existentes
    =============================================*/

    if($("#reservationsDatabase").length > 0){

        reservationsDatabase = JSON.parse($("#reservationsDatabase").val());
        console.log("reservationsDatabase", reservationsDatabase);
    }

	/*=============================================
    Función que  genera el calendario
    =============================================*/

    function generateCalendar() {

    	/*=============================================
        Desplazamiento entre meses
        =============================================*/

        const calendarContainer = $('#reservationCalendar');

        const monthNames = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        // Limpiar calendario
        calendarContainer.empty();

        // Header del calendario
        const header = $(`
            <div class="calendar-header">
                <button type="button" class="calendar-nav-btn" id="prevMonth">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h6 class="mb-0">${monthNames[currentMonth]} ${currentYear}</h6>
                <button type="button" class="calendar-nav-btn" id="nextMonth">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `);

        calendarContainer.append(header);

        /*=============================================
        Desplazamiento entre días
        =============================================*/

        const dayNames = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

        // Días de la semana
        const weekHeader =  $('<div class="calendar-grid mb-2"></div>');

        dayNames.forEach(day => {

        	weekHeader.append(`<div class="text-center fw-bold py-2">${day}</div>`)
        })

        calendarContainer.append(weekHeader);

         // Grid de días
        const daysGrid =  $('<div class="calendar-grid" id="calendarDays"></div>');

        calendarContainer.append(daysGrid);

        // Generar días del mes
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();

        // Días vacíos del mes anterior
        for (let i = 0; i < firstDay; i++) {
			daysGrid.append('<div class="calendar-day disabled"></div>');
        }

        // Días del mes actual
        for (let day = 1; day <= daysInMonth; day++) {

        	const date = new Date(currentYear, currentMonth, day);
        	const isToday = date.toDateString() === today.toDateString();
        	const isPast = date < today && !isToday;
  			const dateKey = date.toISOString().split('T')[0];
            const dayReservations =  reservationsDatabase[dateKey] || [];
            const hasReservations = dayReservations.length > 0;
  			
  			let classes = 'calendar-day';
  			if (isToday) classes += ' today';
  			if (isPast) classes += ' disabled';
            if (hasReservations) classes += ' has-reservations';

  			let dayContent = `${day}`;

            if (hasReservations && dayReservations.length > 1) {

                dayContent += `<span class="reservation-count">${dayReservations.length}</span>`;
            }

  			const dayElement = $(`
                <div class="${classes}" data-date="${date.toISOString()}">
                    ${dayContent}
                </div>
            `);

            daysGrid.append(dayElement);
        }

    }

    /*=============================================
    Función para los eventos
    =============================================*/

    function setupEventListeners() {

    	// Retroceder Meses
    	$(document).on('click', '#prevMonth', function() {

   			currentMonth--;

   			if (currentMonth < 0) {
   			  currentMonth = 11;
   			  currentYear--;
   			}

   			generateCalendar();

    	})

    	// Avanzar meses
    	$(document).on('click', '#nextMonth', function() {

   			currentMonth++;

   			if (currentMonth > 11) {
   			  currentMonth = 0;
   			  currentYear++;
   			}

   			generateCalendar();

    	})

    	// Selección de fecha
        $(document).on('click', '.calendar-day:not(.disabled)', function() {
            const dateStr = $(this).data('date');
            if (dateStr) {
                const date = new Date(dateStr);
                selectDate(date);
            }
        });

         // Limpiar formulario
        $('#clearForm').on('click', function() {
            clearReservationForm();
        });

    }

    /*=============================================
    Función para seleccionar dia
    =============================================*/
    function selectDate(date) {

    	selectedDate = date;

    	// Actualizar visual del calendario
        $('.calendar-day').removeClass('selected');
        $(`.calendar-day[data-date="${date.toISOString()}"]`).addClass('selected');

        $("#date_book").val(date.toLocaleDateString());

        loadSelectedDateReservations();

    }

     /*=============================================
    Función Para mostrar las reservas del día
    =============================================*/

    function loadSelectedDateReservations() {

        const container = $('#todayReservations');
        const headerTitle = container.closest('.card').find('.card-header h5');

        if (!selectedDate) return;

        const dateKey = selectedDate.toISOString().split('T')[0];
        const dayReservations = reservationsDatabase[dateKey] || [];
        const isToday = selectedDate.toDateString() === new Date().toDateString();

         // Actualizar título del panel
        const dateStr = selectedDate.toLocaleDateString('es-ES', {
            weekday: 'long', 
            year: 'numeric', 
            month: 'long',
            day: 'numeric'  
        }) 

        headerTitle.html(`<i class="fas fa-list me-2"></i>Reservas - ${dateStr}`);

        if (dayReservations.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <p>No hay reservas para este día</p>
                </div>
            `);
            return;

        }

        container.empty();

         // Ordenar reservas por hora
        const sortedReservations = dayReservations.sort((a, b) => {
            return a.time.localeCompare(b.time);
        });

        sortedReservations.forEach(reservation => { 

            const item = $(`
                <div class="reservation-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${reservation.customerClient}</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>${reservation.time}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-table me-1"></i>${reservation.table}
                            </small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">${reservation.phone}</small>
                            <br>
                            <span class="badge bg-success">Confirmada</span>
                        </div>
                    </div>
                </div>


             `);

            container.append(item);

        });
    }


    /*=============================================
    Función Global
    =============================================*/

    function initializeReservationSystem() {

    	generateCalendar();
    	setupEventListeners();
        loadSelectedDateReservations();

    	// Seleccionar fecha de hoy por defecto
        const today = new Date();
        selectDate(today);

    }

    /*=============================================
    Inicializamos las funciones del sistema
    =============================================*/
    initializeReservationSystem();

    /*=============================================
    Limpiar el formulario
    =============================================*/
    function clearReservationForm() {
        $('#reservationForm')[0].reset();
    }
    
})