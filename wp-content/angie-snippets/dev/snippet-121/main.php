<?php
/**
 * ACF Property Features Repeater Display
 * 
 * Displays the ACF repeater field 'features' with icons and text
 */

namespace AngieSnippets\PropertyFeaturesRepeater_d007b6f3;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PROPERTY_FEATURES_ASSETS_VERSION_d007b6f3', '1.0.0' );

/**
 * Enqueue styles
 */
function enqueue_assets() {
    wp_enqueue_style(
        'property-features-repeater-d007b6f3',
        angie_cs_get_snippet_asset_url( __FILE__, 'style.css' ),
        array(),
        PROPERTY_FEATURES_ASSETS_VERSION_d007b6f3
    );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );

/**
 * Render the features repeater
 */
function render_features_repeater( $atts ) {
    if ( ! function_exists( 'get_field' ) ) {
        return '<p>Advanced Custom Fields plugin is required.</p>';
    }

    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return '';
    }

    $features = get_field( 'features', $post_id );
    
    if ( ! $features || ! is_array( $features ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="property-features-repeater-d007b6f3">
        <h2 class="features-title-d007b6f3">Property Features</h2>
        <div class="features-grid-d007b6f3">
            <?php foreach ( $features as $feature ) : 
                $icon = isset( $feature['feature_1'] ) ? $feature['feature_1'] : '';
                $text = isset( $feature['feature_text_1'] ) ? $feature['feature_text_1'] : '';
                
                if ( empty( $text ) ) {
                    continue;
                }
            ?>
                <div class="feature-item-d007b6f3">
                    <?php if ( ! empty( $icon ) ) : 
                        if ( is_array( $icon ) && isset( $icon['type'] ) ) {
                            if ( $icon['type'] === 'dashicons' && isset( $icon['value'] ) ) {
                                echo '<span class="feature-icon-d007b6f3 dashicons ' . esc_attr( $icon['value'] ) . '"></span>';
                            } elseif ( $icon['type'] === 'url' && isset( $icon['value'] ) ) {
                                echo '<img src="' . esc_url( $icon['value'] ) . '" alt="" class="feature-icon-img-d007b6f3" />';
                            }
                        } elseif ( is_string( $icon ) ) {
                            if ( strpos( $icon, 'dashicons-' ) === 0 ) {
                                echo '<span class="feature-icon-d007b6f3 dashicons ' . esc_attr( $icon ) . '"></span>';
                            } else {
                                echo '<span class="feature-icon-d007b6f3">' . esc_html( $icon ) . '</span>';
                            }
                        }
                    endif; ?>
                    <h4 class="feature-text-d007b6f3"><?php echo esc_html( $text ); ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'property_features_d007b6f3', __NAMESPACE__ . '\render_features_repeater' );