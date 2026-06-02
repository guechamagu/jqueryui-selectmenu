/**
 * Scripts personalizados para Mi Addon de Elementor
 */
(function($) {
	'use strict';

	// Inicialización cuando el documento está listo
	$(document).ready(function() {
		
		// Animaciones personalizadas si son necesarias
		$('.mea-card').each(function() {
			var $card = $(this);
			
			// Efecto hover adicional (opcional)
			$card.on('mouseenter', function() {
				// Aquí puedes agregar lógica personalizada si lo necesitas
			}).on('mouseleave', function() {
				// Limpieza al salir
			});
		});

		// Soporte para actualizaciones dinámicas de Elementor
		if (typeof elementorFrontend !== 'undefined') {
			elementorFrontend.hooks.addAction('frontend/element_ready/mea_advanced_card.default', function($scope) {
				// Inicializar cualquier funcionalidad específica del widget
				var $card = $scope.find('.mea-card');
				
				// Ejemplo: Agregar interactividad personalizada
				$card.each(function() {
					// Lógica personalizada por instancia
				});
			});
		}
	});

})(jQuery);
