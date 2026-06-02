<?php
namespace Mi_Elementor_Addon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Salir si se accede directamente
}

/**
 * Widget de Tarjeta Avanzada.
 * Un widget flexible y legal que no infringe derechos de autor.
 */
class Advanced_Card_Widget extends Widget_Base {

	/**
	 * Obtener nombre del widget.
	 */
	public function get_name() {
		return 'mea_advanced_card';
	}

	/**
	 * Obtener título del widget.
	 */
	public function get_title() {
		return esc_html__( 'Tarjeta Avanzada', 'mi-elementor-addon' );
	}

	/**
	 * Obtener icono del widget.
	 */
	public function get_icon() {
		return 'eicon-card-image';
	}

	/**
	 * Obtener categorías del widget.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Obtener palabras clave del widget.
	 */
	public function get_keywords() {
		return [ 'card', 'tarjeta', 'imagen', 'contenido', 'box' ];
	}

	/**
	 * Registrar controles del widget.
	 */
	protected function register_controls() {

		// ==========================================
		// SECCIÓN DE CONTENIDO
		// ==========================================
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contenido', 'mi-elementor-addon' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		// Selector de imagen
		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Imagen', 'mi-elementor-addon' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => true,
			]
		);

		// Título
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Título', 'mi-elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Título de la tarjeta', 'mi-elementor-addon' ),
				'dynamic'     => true,
				'label_block' => true,
			]
		);

		// Descripción
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Descripción', 'mi-elementor-addon' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Descripción breve de la tarjeta.', 'mi-elementor-addon' ),
				'dynamic'     => true,
				'label_block' => true,
				'rows'        => 4,
			]
		);

		// Botón (opcional)
		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Texto del botón', 'mi-elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Leer más', 'mi-elementor-addon' ),
				'dynamic'     => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Enlace del botón', 'mi-elementor-addon' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => true,
				'placeholder'   => esc_html__( 'https://tu-sitio.com', 'mi-elementor-addon' ),
				'label_block'   => true,
			]
		);

		// Icono (opcional)
		$this->add_control(
			'selected_icon',
			[
				'label'            => esc_html__( 'Icono', 'mi-elementor-addon' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECCIÓN DE ESTILOS - IMAGEN
		// ==========================================
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Imagen', 'mi-elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'image_background',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .mea-card-image',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			'image_height',
			[
				'label'      => esc_html__( 'Altura de la imagen', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
					'vh' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 250,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_object_fit',
			[
				'label'   => esc_html__( 'Ajuste de objeto', 'mi-elementor-addon' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'fill'     => esc_html__( 'Rellenar', 'mi-elementor-addon' ),
					'contain'  => esc_html__( 'Contener', 'mi-elementor-addon' ),
					'cover'    => esc_html__( 'Cubrir', 'mi-elementor-addon' ),
					'none'     => esc_html__( 'Ninguno', 'mi-elementor-addon' ),
					'scale-down' => esc_html__( 'Escalar hacia abajo', 'mi-elementor-addon' ),
				],
				'selectors' => [
					'{{WRAPPER}} .mea-card-image img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECCIÓN DE ESTILOS - CONTENIDO
		// ==========================================
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Contenido', 'mi-elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Relleno del contenido', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Estilos del título
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color del título', 'mi-elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .mea-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Tipografía del título', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card-title',
			]
		);

		// Estilos de la descripción
		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color de la descripción', 'mi-elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => [
					'{{WRAPPER}} .mea-card-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__( 'Tipografía de la descripción', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card-description',
			]
		);

		// Espaciado entre elementos
		$this->add_control(
			'content_gap',
			[
				'label'      => esc_html__( 'Espacio entre elementos', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card-content > *' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mea-card-content > *:last-child' => 'margin-bottom: 0;',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECCIÓN DE ESTILOS - BOTÓN
		// ==========================================
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Botón', 'mi-elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_alignment',
			[
				'label'   => esc_html__( 'Alineación', 'mi-elementor-addon' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Izquierda', 'mi-elementor-addon' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centro', 'mi-elementor-addon' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Derecha', 'mi-elementor-addon' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .mea-card-button-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Color del texto', 'mi-elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .mea-card-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Color de fondo', 'mi-elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0073aa',
				'selectors' => [
					'{{WRAPPER}} .mea-card-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color de fondo (hover)', 'mi-elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#005a87',
				'selectors' => [
					'{{WRAPPER}} .mea-card-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Tipografía del botón', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border',
				'label'    => esc_html__( 'Borde', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card-button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Radio del borde', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'      => 5,
					'right'    => 5,
					'bottom'   => 5,
					'left'     => 5,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Relleno del botón', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'      => 10,
					'right'    => 20,
					'bottom'   => 10,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECCIÓN DE ESTILOS - CONTENEDOR PRINCIPAL
		// ==========================================
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Contenedor', 'mi-elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'container_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mea-card',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'label'    => esc_html__( 'Borde', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card',
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label'      => esc_html__( 'Radio del borde', 'mi-elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .mea-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'container_box_shadow',
				'label'    => esc_html__( 'Sombra', 'mi-elementor-addon' ),
				'selector' => '{{WRAPPER}} .mea-card',
			]
		);

		$this->add_control(
			'container_transition',
			[
				'label'   => esc_html__( 'Duración de la transición', 'mi-elementor-addon' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}} .mea-card' => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label'        => esc_html__( 'Animación al pasar el mouse', 'mi-elementor-addon' ),
				'type'         => Controls_Manager::HOVER_ANIMATION,
				'prefix_class' => 'mea-hover-',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Renderizar la salida del widget en el frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Construir clases dinámicas
		$card_classes = 'mea-card';
		if ( ! empty( $settings['hover_animation'] ) ) {
			$card_classes .= ' elementor-animation-' . $settings['hover_animation'];
		}
		?>
		<div class="<?php echo esc_attr( $card_classes ); ?>">
			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<div class="mea-card-image">
					<img src="<?php echo esc_url( $settings['image']['url'] ); ?>" 
						 alt="<?php echo esc_attr( $settings['title'] ); ?>" />
				</div>
			<?php endif; ?>

			<div class="mea-card-content">
				<?php if ( ! empty( $settings['selected_icon']['value'] ) || ! empty( $settings['icon'] ) ) : ?>
					<div class="mea-card-icon">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h3 class="mea-card-title"><?php echo esc_html( $settings['title'] ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="mea-card-description"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['button_text'] ) && ! empty( $settings['button_link']['url'] ) ) : ?>
					<div class="mea-card-button-wrapper">
						<a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" 
						   class="mea-card-button"
						   <?php if ( ! empty( $settings['button_link']['is_external'] ) ) : ?>target="_blank"<?php endif; ?>
						   <?php if ( ! empty( $settings['button_link']['nofollow'] ) ) : ?>rel="nofollow"<?php endif; ?>>
							<?php echo esc_html( $settings['button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renderizar la salida del widget en el editor.
	 */
	protected function content_template() {
		?>
		<#
		var cardClasses = 'mea-card';
		if ( settings.hover_animation ) {
			cardClasses += ' elementor-animation-' + settings.hover_animation;
		}
		#>
		<div class="{{{ cardClasses }}}">
			<# if ( settings.image.url ) { #>
				<div class="mea-card-image">
					<img src="{{{ settings.image.url }}}" alt="{{{ settings.title }}}" />
				</div>
			<# } #>

			<div class="mea-card-content">
				<# if ( settings.selected_icon.value || settings.icon ) { #>
					<div class="mea-card-icon">
						{{{ elementor.helpers.renderIcon( view, settings.selected_icon, {}, 'i', 'object' ).value }}}
					</div>
				<# } #>

				<# if ( settings.title ) { #>
					<h3 class="mea-card-title">{{{ settings.title }}}</h3>
				<# } #>

				<# if ( settings.description ) { #>
					<p class="mea-card-description">{{{ settings.description }}}</p>
				<# } #>

				<# if ( settings.button_text && settings.button_link.url ) { #>
					<div class="mea-card-button-wrapper">
						<a href="{{{ settings.button_link.url }}}" 
						   class="mea-card-button"
						   <# if ( settings.button_link.is_external ) { #>target="_blank"<# } #>
						   <# if ( settings.button_link.nofollow ) { #>rel="nofollow"<# } #>>
							{{{ settings.button_text }}}
						</a>
					</div>
				<# } #>
			</div>
		</div>
		<?php
	}
}
