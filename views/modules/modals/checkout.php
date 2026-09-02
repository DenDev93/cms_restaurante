<link rel="stylesheet" href="/views/assets/css/checkout/checkout.css">

<div class="modal fade" id="myCheckout">
	
	<div class="modal-dialog modal-dialog-centered">

		<div class="modal-content rounded">
			
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo urldecode($titleTable) ?></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body checkout-modal-body">

				<!-- Payment Method Section -->
				<div class="payment-section mb-4">
					
					<h5 class="payment-title mb-3">Método de Pago</h5>

					<div class="payment-methods d-flex gap-3 mb-4">
						
						<div class="payment-option active" data-method="cash">
							
							<i class="fas fa-money-bill-wave"></i>
							<span>Efectivo</span>

						</div>

						<div class="payment-option" data-method="card">
							
							<i class="fas fa-credit-card"></i>
							<span>Tarjeta</span>

						</div>

						<div class="payment-option" data-method="transfer">
							<i class="fas fa-university"></i>
							<span>Transferencia</span>
						</div>

					</div>

					<!-- Currency Section (solo efectivo) -->
					<div class="currency-section" id="currencySection">
						
						<h5 class="payment-title mb-3">Moneda de Pago</h5>

						<div class="payment-methods d-flex gap-3 mb-4">
							
							<div class="payment-option active" data-currency="C$">
								<span>C$ Córdobas</span>
							</div>

							<div class="payment-option" data-currency="US$">
								<span>US$ Dólares</span>
							</div>

						</div>

					</div>

				</div>

				<!-- Desglose de la cuenta -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Resumen de la Cuenta</h5>
					<div class="d-flex justify-content-between small">
						<span>Subtotal</span>
						<span id="modalSubtotal">C$ 0.00</span>
					</div>
					<div class="d-flex justify-content-between small">
						<span>IVA (15%)</span>
						<span id="modalTax">C$ 0.00</span>
					</div>
					<div class="d-flex justify-content-between small">
						<span>Propina</span>
						<span id="modalTip">C$ 0.00</span>
					</div>
					<hr>
					<div class="d-flex justify-content-between fw-bold">
						<span>Total a Pagar</span>
						<span id="modalTotalPagar">C$ 0.00</span>
					</div>
				</div>

				<!--Payment Tip -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Propina</h5>
					<div class="amount-input-container">
						<span class="currency-symbol">C$</span>
						<input type="number" class="form-control amount-input" value="0.0" step="0.01" min="0" id="paymentTip">
					</div>
					<small class="text-muted">Déjala en 0 si el cliente no dio propina, o agrega el monto manualmente</small>
				</div>

				<!-- Amount Section -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Monto a Pagar</h5>
					<div class="amount-input-container">
						<span class="currency-symbol" id="paymentSymbol">C$</span>
						<input type="number" class="form-control amount-input" value="0" step="0.01" min="0" id="paymentAmount">
					</div>
				</div>

				<!-- Change Section -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Vuelto</h5>
					<div class="amount-input-container">
						<span class="currency-symbol" id="changeSymbol">C$</span>
						<input type="number" class="form-control amount-input" value="0.00" step="0.01" min="0" id="paymentChange" readonly>
					</div>
					<small class="text-muted" id="changeEquivalence"></small>
				</div>

				<input type="hidden" id="selectedPaymentMethod" name="payment_method" value="cash">
				<input type="hidden" id="selectedCurrency" value="C$">
				<input type="hidden" id="totalCordobas" value="0">
				<input type="hidden" id="changeAmount" value="0">

				<!-- Process Payment Button -->
				<div class="payment-action">
					<button type="button" class="btn btn-success process-payment-btn w-100 backColor" id="processPaymentBtn">
						Procesar el Pago
					</button>
				</div>

			</div>

			<!-- Modal footer -->
			<div class="modal-footer d-flex justify-content-between">
				<div><button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cerrar</button></div>
			</div>

		</div>
		
	</div>

</div>

<script src="/views/assets/js/checkout/checkout.js"></script>