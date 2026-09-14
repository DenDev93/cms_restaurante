$(function () {

	 const $toggle = $('#darkModeToggle');
	 const $body   = $('body');

	 const DARK_LOGO = "views/assets/files/68a7aa14d042556.png";
	 const LIGHT_LOGO = "views/assets/files/68a7aa14cd30056.png";

	function applyState(enabled) {

		$body.toggleClass('dark-mode', enabled);

		if ($toggle.length) {

			$toggle.html(enabled ? '<i class="bi bi-moon"></i>' : '<i class="bi bi-sun"></i>');
		}

		$(".logo img").attr("src", enabled ? DARK_LOGO : LIGHT_LOGO)

		localStorage.setItem('darkMode', enabled ? 'enabled' : 'disabled');

	}


    //Estado inicial desde localStorage
	applyState(localStorage.getItem('darkMode') === 'enabled');

	// Toggle al hacer click
  	if ($toggle.length) {
	    
	    $toggle.on('click', function (e) {

	      e.preventDefault();
	      applyState(!$body.hasClass('dark-mode'));

	    });
  	}

})