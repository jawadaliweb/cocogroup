<?php
/**
 * Plugin Name: ACF Features Widget for Elementor
 * Description: Displays ACF repeater features (icon or image + text) as a drag-and-drop Elementor widget.
 * Version: 1.2
 * Author: Custom
 * Requires Plugins: elementor, advanced-custom-fields-pro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Dependency check ──────────────────────────────────────────────────────────
add_action( 'admin_notices', function() {
    $missing = [];
    if ( ! did_action( 'elementor/loaded' ) )                              $missing[] = '<strong>Elementor</strong>';
    if ( ! function_exists( 'acf' ) && ! function_exists( 'have_rows' ) ) $missing[] = '<strong>Advanced Custom Fields (ACF)</strong>';
    if ( ! empty( $missing ) ) {
        echo '<div class="notice notice-error"><p><strong>ACF Features Widget</strong> requires: ' . implode( ' and ', $missing ) . '.</p></div>';
    }
});

// ── Boot only when Elementor is ready ────────────────────────────────────────
add_action( 'elementor/loaded', function() {

    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_style( 'acf-features-widget', plugin_dir_url( __FILE__ ) . 'acf-features-widget.css', [], '1.2' );
    });

    add_action( 'elementor/widgets/register', function( $widgets_manager ) {

        if ( ! function_exists( 'have_rows' ) ) return;

        class ACF_Features_Widget extends \Elementor\Widget_Base {

            public function get_name()       { return 'acf_features'; }
            public function get_title()      { return 'Property Features'; }
            public function get_icon()       { return 'eicon-bullet-list'; }
            public function get_categories() { return [ 'general' ]; }

            // ── Controls ──────────────────────────────────────────────────────
            protected function register_controls() {

                // ── Content ───────────────────────────────────────────────────
                $this->start_controls_section( 'section_content', [
                    'label' => 'Features Settings',
                    'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                ]);

                $this->add_control( 'acf_repeater_key', [
                    'label'       => 'ACF Repeater Field Name',
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => 'features',
                    'description' => 'The field name of your ACF repeater (e.g. "features")',
                ]);

                // ── Media type toggle ─────────────────────────────────────────
                $this->add_control( 'media_type', [
                    'label'   => 'Media Type',
                    'type'    => \Elementor\Controls_Manager::CHOOSE,
                    'options' => [
                        'icon'  => [ 'title' => 'Icon',  'icon' => 'eicon-star' ],
                        'image' => [ 'title' => 'Image', 'icon' => 'eicon-image' ],
                    ],
                    'default' => 'icon',
                    'toggle'  => false,
                ]);

                // Icon sub-field (shown when media_type = icon)
                $this->add_control( 'acf_icon_key', [
                    'label'     => 'Icon Sub-field Name',
                    'type'      => \Elementor\Controls_Manager::TEXT,
                    'default'   => 'feature_icon',
                    'condition' => [ 'media_type' => 'icon' ],
                ]);

                // Image sub-field (shown when media_type = image)
                $this->add_control( 'acf_image_key', [
                    'label'       => 'Image Sub-field Name',
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => 'feature_image',
                    'description' => 'The ACF Image sub-field name inside your repeater.',
                    'condition'   => [ 'media_type' => 'image' ],
                ]);

                // Image size (shown when media_type = image)
                $this->add_control( 'image_size', [
                    'label'     => 'Image Size',
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'default'   => 'thumbnail',
                    'options'   => [
                        'thumbnail'    => 'Thumbnail (150×150)',
                        'medium'       => 'Medium (300×300)',
                        'medium_large' => 'Medium Large (768w)',
                        'large'        => 'Large (1024×1024)',
                        'full'         => 'Full Size',
                    ],
                    'condition' => [ 'media_type' => 'image' ],
                ]);

                $this->add_control( 'acf_text_key', [
                    'label'   => 'Text Sub-field Name',
                    'type'    => \Elementor\Controls_Manager::TEXT,
                    'default' => 'feature_text',
                ]);

                $this->add_responsive_control( 'columns', [
                    'label'   => 'Columns',
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => '2',
                    'options' => [ '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4' ],
                    'selectors' => [
                        '{{WRAPPER}} .acf-features-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    ],
                ]);

                $this->add_control( 'gap', [
                    'label'      => 'Gap between items',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
                    'default'    => [ 'unit' => 'px', 'size' => 12 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-features-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->end_controls_section();

                // ── Style — Icon (visible when media_type = icon) ─────────────
                $this->start_controls_section( 'section_icon_style', [
                    'label'     => 'Icon',
                    'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [ 'media_type' => 'icon' ],
                ]);

                $this->add_control( 'icon_color', [
                    'label'     => 'Icon Color',
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#555555',
                    'selectors' => [
                        '{{WRAPPER}} .acf-feat-icon'     => 'color: {{VALUE}};',
                        '{{WRAPPER}} .acf-feat-icon svg' => 'fill: {{VALUE}};',
                    ],
                ]);

                $this->add_control( 'icon_size', [
                    'label'      => 'Icon Size',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => [ 'min' => 10, 'max' => 80 ] ],
                    'default'    => [ 'unit' => 'px', 'size' => 18 ],
                    'selectors'  => [
                        '{{WRAPPER}} .acf-feat-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .acf-feat-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    ],
                ]);

                $this->add_control( 'icon_bg_color', [
                    'label'     => 'Icon Background',
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => 'transparent',
                    'selectors' => [ '{{WRAPPER}} .acf-feat-icon' => 'background-color: {{VALUE}};' ],
                ]);

                $this->add_control( 'icon_padding', [
                    'label'      => 'Icon Padding',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
                    'default'    => [ 'unit' => 'px', 'size' => 0 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-icon' => 'padding: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->add_control( 'icon_border_radius', [
                    'label'      => 'Icon Border Radius',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range'      => [ 'px' => ['min'=>0,'max'=>50], '%' => ['min'=>0,'max'=>50] ],
                    'default'    => [ 'unit' => 'px', 'size' => 0 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-icon' => 'border-radius: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->add_control( 'icon_gap', [
                    'label'      => 'Space between icon and text',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
                    'default'    => [ 'unit' => 'px', 'size' => 8 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-item' => 'gap: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->end_controls_section();

                // ── Style — Image (visible when media_type = image) ───────────
                $this->start_controls_section( 'section_image_style', [
                    'label'     => 'Image',
                    'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [ 'media_type' => 'image' ],
                ]);

                $this->add_control( 'image_width', [
                    'label'      => 'Image Width',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range'      => [ 'px' => ['min'=>10,'max'=>200], '%' => ['min'=>5,'max'=>100] ],
                    'default'    => [ 'unit' => 'px', 'size' => 40 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-image img' => 'width: {{SIZE}}{{UNIT}}; height: auto;' ],
                ]);

                $this->add_control( 'image_border_radius', [
                    'label'      => 'Border Radius',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range'      => [ 'px' => ['min'=>0,'max'=>100], '%' => ['min'=>0,'max'=>50] ],
                    'default'    => [ 'unit' => 'px', 'size' => 0 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-image img' => 'border-radius: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->add_control( 'image_bg_color', [
                    'label'     => 'Image Background',
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => 'transparent',
                    'selectors' => [ '{{WRAPPER}} .acf-feat-image' => 'background-color: {{VALUE}};' ],
                ]);

                $this->add_control( 'image_padding', [
                    'label'      => 'Image Padding',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => ['min'=>0,'max'=>30] ],
                    'default'    => [ 'unit' => 'px', 'size' => 0 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-image' => 'padding: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->add_control( 'image_gap', [
                    'label'      => 'Space between image and text',
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range'      => [ 'px' => ['min'=>0,'max'=>30] ],
                    'default'    => [ 'unit' => 'px', 'size' => 8 ],
                    'selectors'  => [ '{{WRAPPER}} .acf-feat-item' => 'gap: {{SIZE}}{{UNIT}};' ],
                ]);

                $this->end_controls_section();

                // ── Style — Text ──────────────────────────────────────────────
                $this->start_controls_section( 'section_text_style', [
                    'label' => 'Text',
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                ]);

                $this->add_control( 'text_color', [
                    'label'     => 'Text Color',
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#333333',
                    'selectors' => [ '{{WRAPPER}} .acf-feat-label' => 'color: {{VALUE}};' ],
                ]);

                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(), [
                        'name'     => 'text_typography',
                        'selector' => '{{WRAPPER}} .acf-feat-label',
                    ]
                );

                $this->end_controls_section();

                // ── Style — Item Box ──────────────────────────────────────────
                $this->start_controls_section( 'section_item_style', [
                    'label' => 'Item Box',
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                ]);

                $this->add_control( 'item_bg', [
                    'label'     => 'Background Color',
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => 'transparent',
                    'selectors' => [ '{{WRAPPER}} .acf-feat-item' => 'background-color: {{VALUE}};' ],
                ]);

                $this->add_control( 'item_padding', [
                    'label'      => 'Padding',
                    'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em'],
                    'selectors'  => [
                        '{{WRAPPER}} .acf-feat-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]);

                $this->add_control( 'item_border_radius', [
                    'label'      => 'Border Radius',
                    'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors'  => [
                        '{{WRAPPER}} .acf-feat-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]);

                $this->add_group_control(
                    \Elementor\Group_Control_Border::get_type(), [
                        'name'     => 'item_border',
                        'selector' => '{{WRAPPER}} .acf-feat-item',
                    ]
                );

                $this->add_group_control(
                    \Elementor\Group_Control_Box_Shadow::get_type(), [
                        'name'     => 'item_shadow',
                        'selector' => '{{WRAPPER}} .acf-feat-item',
                    ]
                );

                $this->end_controls_section();
            }

            // ── Render ────────────────────────────────────────────────────────
            protected function render() {
                $s            = $this->get_settings_for_display();
                $repeater_key = $s['acf_repeater_key'] ?: 'features';
                $media_type   = $s['media_type']        ?: 'icon';
                $icon_key     = $s['acf_icon_key']      ?: 'feature_icon';
                $image_key    = $s['acf_image_key']     ?: 'feature_image';
                $image_size   = $s['image_size']        ?: 'thumbnail';
                $text_key     = $s['acf_text_key']      ?: 'feature_text';

                if ( ! function_exists( 'have_rows' ) ) {
                    echo '<p style="color:red;">ACF is not active.</p>';
                    return;
                }

                if ( ! have_rows( $repeater_key ) ) {
                    if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                        echo '<p style="color:#aaa;font-size:13px;">No features found. Check the repeater field name: <strong>' . esc_html( $repeater_key ) . '</strong></p>';
                    }
                    return;
                }

                echo '<div class="acf-features-grid">';

                while ( have_rows( $repeater_key ) ) : the_row();

                    $label = get_sub_field( $text_key );

                    echo '<div class="acf-feat-item">';

                    if ( $media_type === 'image' ) {

                        // ── Image field ───────────────────────────────────────
                        $image = get_sub_field( $image_key );

                        if ( ! empty( $image ) ) {
                            echo '<span class="acf-feat-image">';

                            if ( is_array( $image ) ) {
                                // ACF returns array when Return Format = Image Array
                                $url = $image['sizes'][ $image_size ] ?? $image['url'] ?? '';
                                $alt = $image['alt'] ?? '';
                                if ( $url ) {
                                    echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">';
                                }
                            } elseif ( is_numeric( $image ) ) {
                                // ACF returns ID when Return Format = Image ID
                                echo wp_get_attachment_image( (int) $image, $image_size );
                            } else {
                                // ACF returns URL when Return Format = Image URL
                                echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $label ) . '" loading="lazy">';
                            }

                            echo '</span>';
                        }

                    } else {

                        // ── Icon field ────────────────────────────────────────
                        $icon_data = get_sub_field( $icon_key );

                        if ( ! empty( $icon_data ) ) {
                            echo '<span class="acf-feat-icon">';
                            if ( is_array( $icon_data ) ) {
                                \Elementor\Icons_Manager::render_icon( $icon_data, [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<span class="dashicons ' . esc_attr( $icon_data ) . '"></span>';
                            }
                            echo '</span>';
                        }
                    }

                    if ( $label ) {
                        echo '<span class="acf-feat-label">' . esc_html( $label ) . '</span>';
                    }

                    echo '</div>';

                endwhile;

                echo '</div>';
            }
        } // end class

        $widgets_manager->register( new ACF_Features_Widget() );
    });

}); // end elementor/loaded
