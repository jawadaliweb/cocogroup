<?php
class ACF_Gallery_Widget extends \Elementor\Widget_Base {

    public function get_name()            { return 'acf_gallery'; }
    public function get_title()           { return 'ACF Filterable Gallery'; }
    public function get_icon()            { return 'eicon-gallery-grid'; }
    public function get_categories()      { return ['general']; }
    public function get_script_depends()  { return ['isotope-js', 'glightbox-js', 'jquery']; }
    public function get_style_depends()   { return ['acf-gallery-css', 'glightbox-css']; }

    protected function register_controls() {

        // =====================
        // CONTENT — Query
        // =====================
        $this->start_controls_section('content_section', [
            'label' => 'Query & Data',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('cpt_slug', [
            'label'   => 'Post Type Slug',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'property-listing',
        ]);

        $this->add_control('repeater_field_name', [
            'label'       => 'Repeater Field Name',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'gallery_sections',
            'description' => 'The ACF Repeater field name (e.g. gallery_sections)',
        ]);

        $this->add_control('section_name_subfield', [
            'label'       => 'Section Name Sub-field',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'section_name',
            'description' => 'Sub-field name for the category/section label (e.g. section_name)',
        ]);

        $this->add_control('section_images_subfield', [
            'label'       => 'Section Images Sub-field',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'section_images',
            'description' => 'Sub-field name for the Gallery field inside each repeater row (e.g. section_images)',
        ]);

        $this->add_control('posts_per_page', [
            'label'   => 'Number of Posts',
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => -1,
        ]);

        $this->end_controls_section();

        // =====================
        // CONTENT — Filter Bar
        // =====================
        $this->start_controls_section('filter_section', [
            'label' => 'Filter Bar',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_all_button', [
            'label'        => 'Show "All" Button',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
        ]);

        $this->add_control('all_button_text', [
            'label'     => '"All" Button Text',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'ALL',
            'condition' => ['show_all_button' => 'yes'],
        ]);

        $this->add_control('filter_align', [
            'label'   => 'Filter Alignment',
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => ['title' => 'Left',   'icon' => 'eicon-text-align-left'],
                'center'     => ['title' => 'Center', 'icon' => 'eicon-text-align-center'],
                'flex-end'   => ['title' => 'Right',  'icon' => 'eicon-text-align-right'],
            ],
            'default'   => 'flex-start',
            'selectors' => ['{{WRAPPER}} .gallery-filters' => 'justify-content: {{VALUE}}'],
        ]);

        $this->add_control('filter_style', [
            'label'   => 'Filter Style',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'underline',
            'options' => [
                'underline' => 'Underline',
                'pill'      => 'Pill / Tag',
                'minimal'   => 'Minimal Text',
            ],
        ]);

        $this->end_controls_section();

        // =====================
        // CONTENT — Layout
        // =====================
        $this->start_controls_section('layout_section', [
            'label' => 'Layout',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout_mode', [
            'label'   => 'Layout Mode',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'fitRows',
            'options' => [
                'fitRows' => 'Grid (Uniform)',
                'masonry' => 'Masonry',
            ],
        ]);

        $this->add_control('columns', [
            'label'   => 'Columns (Desktop)',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '4',
            'options' => ['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'],
        ]);

        $this->add_control('columns_tablet', [
            'label'   => 'Columns (Tablet)',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '2',
            'options' => ['1' => '1', '2' => '2', '3' => '3'],
        ]);

        $this->add_control('columns_mobile', [
            'label'   => 'Columns (Mobile)',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '1',
            'options' => ['1' => '1', '2' => '2'],
        ]);

        $this->add_control('gap', [
            'label'   => 'Gap (px)',
            'type'    => \Elementor\Controls_Manager::SLIDER,
            'default' => ['size' => 8],
            'range'   => ['px' => ['min' => 0, 'max' => 40]],
        ]);

        $this->add_control('image_ratio', [
            'label'     => 'Image Ratio',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => '1/1',
            'options'   => [
                '1/1'  => 'Square',
                '4/3'  => 'Landscape (4:3)',
                '3/4'  => 'Portrait (3:4)',
                '16/9' => 'Wide (16:9)',
                'auto' => 'Auto (original)',
            ],
            'condition' => ['layout_mode' => 'fitRows'],
        ]);

        $this->end_controls_section();

        // =====================
        // CONTENT — Features
        // =====================
        $this->start_controls_section('features_section', [
            'label' => 'Features',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('enable_lightbox', [
            'label'        => 'Enable Lightbox',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
        ]);

        $this->add_control('show_overlay', [
            'label'        => 'Show Hover Overlay',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
        ]);

        $this->add_control('overlay_icon', [
            'label'     => 'Overlay Icon',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'zoom',
            'options'   => [
                'zoom' => 'Zoom / Search',
                'plus' => 'Plus',
                'eye'  => 'Eye',
                'none' => 'None (color only)',
            ],
            'condition' => ['show_overlay' => 'yes'],
        ]);

        $this->add_control('animate_items', [
            'label'        => 'Animate Items on Load',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
        ]);

        $this->add_control('show_section_label', [
            'label'        => 'Show Section Name on Hover',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'no',
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
        ]);

        $this->end_controls_section();

        // =====================
        // STYLE — Filter Buttons
        // =====================
        $this->start_controls_section('style_filters', [
            'label' => 'Filter Buttons',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('filter_color', [
            'label'     => 'Text Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#999999',
            'selectors' => ['{{WRAPPER}} .filter-btn' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('filter_active_color', [
            'label'     => 'Active Text Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#222222',
            'selectors' => ['{{WRAPPER}} .filter-btn.active' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('filter_bg_color', [
            'label'     => 'Background Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => ['{{WRAPPER}} .filter-btn' => 'background: {{VALUE}}'],
        ]);

        $this->add_control('filter_active_bg_color', [
            'label'     => 'Active Background Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => ['{{WRAPPER}} .filter-btn.active' => 'background: {{VALUE}}'],
        ]);

        $this->add_control('filter_border_color', [
            'label'     => 'Border / Underline Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#dddddd',
            'selectors' => [
                '{{WRAPPER}} .gallery-filters.style-underline' => 'border-bottom-color: {{VALUE}}',
                '{{WRAPPER}} .filter-btn.style-pill'           => 'border-color: {{VALUE}}',
            ],
        ]);

        $this->add_control('filter_active_border_color', [
            'label'     => 'Active Border / Underline Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#222222',
            'selectors' => [
                '{{WRAPPER}} .filter-btn.active.style-underline' => 'border-bottom-color: {{VALUE}}',
                '{{WRAPPER}} .filter-btn.active.style-pill'      => 'border-color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'filter_typography',
            'selector' => '{{WRAPPER}} .filter-btn',
        ]);

        $this->add_responsive_control('filter_padding', [
            'label'      => 'Button Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'default'    => ['top' => '10', 'right' => '0', 'bottom' => '10', 'left' => '0', 'unit' => 'px'],
            'selectors'  => ['{{WRAPPER}} .filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'],
        ]);

        $this->add_responsive_control('filter_gap', [
            'label'     => 'Gap Between Buttons',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'default'   => ['size' => 24],
            'range'     => ['px' => ['min' => 4, 'max' => 80]],
            'selectors' => ['{{WRAPPER}} .gallery-filters' => 'gap: {{SIZE}}px'],
        ]);

        $this->add_responsive_control('filter_margin_bottom', [
            'label'     => 'Filter Bar Bottom Spacing',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'default'   => ['size' => 32],
            'range'     => ['px' => ['min' => 0, 'max' => 80]],
            'selectors' => ['{{WRAPPER}} .gallery-filters' => 'margin-bottom: {{SIZE}}px'],
        ]);

        $this->end_controls_section();

        // =====================
        // STYLE — Images
        // =====================
        $this->start_controls_section('style_images', [
            'label' => 'Images',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('border_radius', [
            'label'     => 'Border Radius',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'default'   => ['size' => 0],
            'range'     => ['px' => ['min' => 0, 'max' => 30]],
            'selectors' => ['{{WRAPPER}} .gallery-item-inner' => 'border-radius: {{SIZE}}px; overflow: hidden;'],
        ]);

        $this->add_control('hover_zoom', [
            'label'     => 'Hover Zoom Scale',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'default'   => ['size' => 1.05],
            'range'     => ['px' => ['min' => 1, 'max' => 1.3, 'step' => 0.01]],
            'selectors' => ['{{WRAPPER}} .gallery-item-inner:hover img' => 'transform: scale({{SIZE}})'],
        ]);

        $this->add_control('hover_overlay_color', [
            'label'     => 'Overlay Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.3)',
            'selectors' => ['{{WRAPPER}} .gallery-overlay' => 'background: {{VALUE}}'],
            'condition' => ['show_overlay' => 'yes'],
        ]);

        $this->add_control('overlay_icon_color', [
            'label'     => 'Overlay Icon Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => ['{{WRAPPER}} .gallery-overlay .overlay-icon' => 'color: {{VALUE}}; fill: {{VALUE}}'],
            'condition' => ['show_overlay' => 'yes'],
        ]);

        $this->add_control('overlay_icon_size', [
            'label'     => 'Overlay Icon Size',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'default'   => ['size' => 32],
            'range'     => ['px' => ['min' => 16, 'max' => 80]],
            'selectors' => ['{{WRAPPER}} .gallery-overlay .overlay-icon' => 'width: {{SIZE}}px; height: {{SIZE}}px;'],
            'condition' => ['show_overlay' => 'yes'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'      => 'item_label_typography',
            'label'     => 'Section Label Typography',
            'selector'  => '{{WRAPPER}} .gallery-item-label',
            'condition' => ['show_section_label' => 'yes'],
        ]);

        $this->add_control('item_label_color', [
            'label'     => 'Section Label Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => ['{{WRAPPER}} .gallery-item-label' => 'color: {{VALUE}}'],
            'condition' => ['show_section_label' => 'yes'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings       = $this->get_settings_for_display();
        $cpt            = sanitize_text_field($settings['cpt_slug']);
        $repeater_field = sanitize_text_field($settings['repeater_field_name']);
        $name_subfield  = sanitize_text_field($settings['section_name_subfield']);
        $imgs_subfield  = sanitize_text_field($settings['section_images_subfield']);
        $columns        = intval($settings['columns']);
        $col_tablet     = intval($settings['columns_tablet']);
        $col_mobile     = intval($settings['columns_mobile']);
        $gap            = intval($settings['gap']['size']);
        $ratio          = $settings['image_ratio'];
        $ppp            = intval($settings['posts_per_page']);
        $widget_id      = $this->get_id();
        $layout         = $settings['layout_mode'];
        $filter_style   = $settings['filter_style'];

        // ── Query posts ────────────────────────────────────────────────────
        $query = new WP_Query([
            'post_type'      => $cpt,
            'posts_per_page' => $ppp,
            'post_status'    => 'publish',
        ]);

        // ── Build a flat list of sections (slug → label) across ALL posts ──
        // We need unique sections to build the filter bar.
        $all_sections = []; // slug => label

        // Also collect all image rows for rendering so we only loop once.
        $all_items = []; // [ ['slug'=>…, 'label'=>…, 'img'=>…, 'alt'=>…, 'full_url'=>…, 'thumb_url'=>…], … ]

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id    = get_the_ID();
                $post_title = get_the_title();
                $sections   = get_field($repeater_field, $post_id);

                if (!$sections || !is_array($sections)) continue;

                foreach ($sections as $row) {
                    $raw_label = trim($row[$name_subfield] ?? '');
                    if ($raw_label === '') continue;

                    // Create a CSS-safe slug from the section name.
                    $slug = sanitize_title($raw_label);

                    // Register unique section for filter bar.
                    if (!isset($all_sections[$slug])) {
                        $all_sections[$slug] = $raw_label;
                    }

                    $images = $row[$imgs_subfield] ?? [];
                    if (!is_array($images)) continue;

                    foreach ($images as $img) {
                        if (empty($img['url'])) continue;
                        $all_items[] = [
                            'slug'      => $slug,
                            'label'     => $raw_label,
                            'full_url'  => $img['url'],
                            'thumb_url' => $img['sizes']['large'] ?? $img['url'],
                            'alt'       => $img['alt'] ?: $post_title,
                        ];
                    }
                }
            }
            wp_reset_postdata();
        }

        // ── SVG icons ──────────────────────────────────────────────────────
        $icons = [
            'zoom' => '<svg class="overlay-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
            'plus' => '<svg class="overlay-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'eye'  => '<svg class="overlay-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
            'none' => '',
        ];
        $icon_svg = $icons[$settings['overlay_icon']] ?? $icons['zoom'];

        $animate_class = $settings['animate_items']    === 'yes' ? ' animate-items' : '';
        $gallery_name  = 'gallery-' . $widget_id;
        ?>

        <style>
        #wrap-<?php echo esc_attr($widget_id); ?> .gallery-grid {
            display: block;
            margin: 0 -<?php echo $gap / 2; ?>px;
        }
        #wrap-<?php echo esc_attr($widget_id); ?> .gallery-item {
            float: left;
            width: <?php echo 100 / $columns; ?>%;
            padding: <?php echo $gap / 2; ?>px;
            box-sizing: border-box;
        }
        @media (max-width: 1024px) {
            #wrap-<?php echo esc_attr($widget_id); ?> .gallery-item {
                width: <?php echo 100 / $col_tablet; ?>%;
            }
        }
        @media (max-width: 767px) {
            #wrap-<?php echo esc_attr($widget_id); ?> .gallery-item {
                width: <?php echo 100 / $col_mobile; ?>%;
            }
        }
        </style>

        <div class="glances-gallery-wrap" id="wrap-<?php echo esc_attr($widget_id); ?>">

            <!-- Filter Buttons ─────────────────────────────────────────── -->
            <div class="gallery-filters style-<?php echo esc_attr($filter_style); ?>">

                <?php if ($settings['show_all_button'] === 'yes') : ?>
                    <button class="filter-btn style-<?php echo esc_attr($filter_style); ?> active" data-filter="*">
                        <?php echo esc_html($settings['all_button_text']); ?>
                    </button>
                <?php endif; ?>

                <?php foreach ($all_sections as $slug => $label) : ?>
                    <button class="filter-btn style-<?php echo esc_attr($filter_style); ?>"
                            data-filter=".section-<?php echo esc_attr($slug); ?>">
                        <?php echo esc_html(strtoupper($label)); ?>
                    </button>
                <?php endforeach; ?>

            </div>

            <!-- Gallery Grid ───────────────────────────────────────────── -->
            <div class="gallery-grid<?php echo $animate_class; ?>"
                 id="gallery-isotope-<?php echo esc_attr($widget_id); ?>">

                <?php foreach ($all_items as $item) :
                    $ratio_style = ($ratio !== 'auto') ? 'aspect-ratio:' . esc_attr($ratio) . ';' : '';
                ?>
                    <div class="gallery-item section-<?php echo esc_attr($item['slug']); ?>">
                        <div class="gallery-item-inner" style="<?php echo $ratio_style; ?> overflow:hidden; position:relative; cursor:pointer;">

                        <?php if ($settings['enable_lightbox'] === 'yes') : ?>
                            <a href="<?php echo esc_url($item['full_url']); ?>"
                               class="glightbox"
                               data-gallery="<?php echo esc_attr($gallery_name); ?>"
                               aria-label="<?php echo esc_attr($item['alt']); ?>">
                        <?php endif; ?>

                            <img src="<?php echo esc_url($item['thumb_url']); ?>"
                                 alt="<?php echo esc_attr($item['alt']); ?>"
                                 loading="lazy"
                                 style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;">

                            <?php if ($settings['show_overlay'] === 'yes') : ?>
                                <div class="gallery-overlay">
                                    <?php echo $icon_svg; ?>
                                    <?php if ($settings['show_section_label'] === 'yes') : ?>
                                        <span class="gallery-item-label">
                                            <?php echo esc_html($item['label']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php if ($settings['enable_lightbox'] === 'yes') : ?>
                            </a>
                        <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div><!-- /.gallery-grid -->

            <?php if (empty($all_items)) : ?>
                <p style="color:#999;font-size:13px;">
                    No gallery sections found. Make sure the ACF Repeater field name
                    "<strong><?php echo esc_html($repeater_field); ?></strong>" is correct
                    and posts have data.
                </p>
            <?php endif; ?>

        </div><!-- /.glances-gallery-wrap -->

        <script>
        (function($) {
            var WIDGET_ID = '<?php echo esc_js($widget_id); ?>';
            var LOG = '[ACF Gallery #' + WIDGET_ID + ']';

            function initGallery() {
                console.log(LOG, 'initGallery() called');

                var gridEl = document.getElementById('gallery-isotope-' + WIDGET_ID);

                if (!gridEl) {
                    console.error(LOG, 'FAIL — grid element not found: #gallery-isotope-' + WIDGET_ID);
                    return;
                }
                console.log(LOG, 'Grid element found:', gridEl);

                if (typeof Isotope === 'undefined') {
                    console.error(LOG, 'FAIL — Isotope is not loaded');
                    return;
                }
                console.log(LOG, 'Isotope OK');

                // Prevent double-init
                if (gridEl.dataset.isoInit === '1') {
                    console.warn(LOG, 'Already initialised — skipping');
                    return;
                }
                gridEl.dataset.isoInit = '1';

                // Log all gallery items and their classes
                var allItems = gridEl.querySelectorAll('.gallery-item');
                console.log(LOG, 'Total .gallery-item elements found:', allItems.length);
                allItems.forEach(function(el, i) {
                    console.log(LOG, '  Item[' + i + '] classes:', el.className);
                });

                // Log all filter buttons and their data-filter values
                var filterBtns = document.querySelectorAll('#wrap-' + WIDGET_ID + ' .filter-btn');
                console.log(LOG, 'Total .filter-btn elements found:', filterBtns.length);
                filterBtns.forEach(function(btn, i) {
                    console.log(LOG, '  Btn[' + i + '] text:', btn.textContent.trim(), '| data-filter:', btn.getAttribute('data-filter'));
                });

                function runIsotope() {
                    console.log(LOG, 'runIsotope() — all images settled, initialising Isotope');

                    <?php if ($settings['animate_items'] === 'yes') : ?>
                    var items = gridEl.querySelectorAll('.gallery-item');
                    items.forEach(function(item, i) {
                        var inner = item.querySelector('.gallery-item-inner');
                        if (!inner) return;
                        inner.style.opacity   = '0';
                        inner.style.transform = 'translateY(20px)';
                        inner.style.transition = 'opacity 0.4s ease ' + (i * 0.05) + 's, transform 0.4s ease ' + (i * 0.05) + 's';
                        setTimeout(function() {
                            inner.style.opacity   = '1';
                            inner.style.transform = 'translateY(0)';
                        }, 100);
                    });
                    <?php endif; ?>

                    var iso = new Isotope(gridEl, {
                        itemSelector: '.gallery-item',
                        layoutMode: '<?php echo esc_js($layout); ?>',
                        <?php if ($layout === 'masonry') : ?>
                        masonry: { columnWidth: '.gallery-item' },
                        <?php endif; ?>
                        transitionDuration: '0.4s',
                        hiddenStyle: {
                            opacity: 0,
                            transform: 'scale(0.95)'
                        },
                        visibleStyle: {
                            opacity: 1,
                            transform: 'scale(1)'
                        }
                    });
                    console.log(LOG, 'Isotope instance created:', iso);
                    console.log(LOG, 'Isotope items count:', iso.filteredItems ? iso.filteredItems.length : 'n/a');

                    // Re-layout after any lazy images finish loading
                    $(gridEl).find('img').on('load', function() {
                        console.log(LOG, 'Late image loaded — calling iso.layout()');
                        iso.layout();
                    });

                    // Bind filter buttons
                    var $btns = $('#wrap-' + WIDGET_ID + ' .filter-btn');
                    console.log(LOG, 'Binding click to', $btns.length, 'filter buttons');

                    $btns.off('click.acfgal').on('click.acfgal', function() {
                        var filterVal = $(this).attr('data-filter');
                        console.log(LOG, 'Filter clicked — value:', filterVal);

                        $btns.removeClass('active');
                        $(this).addClass('active');

                        iso.arrange({ filter: filterVal });
                        console.log(LOG, 'iso.arrange() called with filter:', filterVal);

                        // Let Isotope handle the filtering animations natively!
                    });

                    <?php if ($settings['enable_lightbox'] === 'yes') : ?>
                    if (typeof GLightbox !== 'undefined') {
                        GLightbox({
                            selector: '#wrap-' + WIDGET_ID + ' .glightbox',
                            touchNavigation: true,
                            loop: true,
                            autoplayVideos: false,
                        });
                        console.log(LOG, 'GLightbox initialised');
                    } else {
                        console.warn(LOG, 'GLightbox not loaded');
                    }
                    <?php endif; ?>
                }

                // Wait for all images to load so Isotope calculates heights correctly
                var imgs  = gridEl.querySelectorAll('img');
                var total = imgs.length, loaded = 0;
                console.log(LOG, 'Waiting for', total, 'images to load before init');

                if (total === 0) { runIsotope(); return; }

                function onImgLoad(e) {
                    loaded++;
                    console.log(LOG, 'Image settled (' + loaded + '/' + total + ')', e ? e.target.src : '');
                    if (loaded >= total) runIsotope();
                }

                imgs.forEach(function(img) {
                    if (img.complete) {
                        onImgLoad(null);
                    } else {
                        img.addEventListener('load',  onImgLoad);
                        img.addEventListener('error', onImgLoad);
                    }
                });
            }

            // Fire on full page load
            if (document.readyState === 'complete') {
                console.log(LOG, 'Document already complete — calling initGallery() immediately');
                initGallery();
            } else {
                console.log(LOG, 'Waiting for window load event');
                $(window).on('load', initGallery);
            }

            // Fire inside Elementor editor / preview
            $(document).on('elementor/frontend/init', function() {
                console.log(LOG, 'Elementor frontend init detected');
                if (window.elementorFrontend) {
                    elementorFrontend.hooks.addAction(
                        'frontend/element_ready/acf_gallery.default',
                        function() {
                            console.log(LOG, 'Elementor element_ready fired — re-initialising');
                            var gridEl = document.getElementById('gallery-isotope-' + WIDGET_ID);
                            if (gridEl) gridEl.dataset.isoInit = '0';
                            initGallery();
                        }
                    );
                }
            });
        })(jQuery);
        </script>
        <?php
    }
}
