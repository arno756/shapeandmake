<?php

class WPBDP_RegionsFrontend {

    private $selector = true;

    public function __construct() {
        add_filter( 'wpbdp_shortcodes', array( &$this, 'add_shortcodes' ) );

        add_filter('page_rewrite_rules', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('template_redirect', array($this, 'template_redirect'));

        add_filter('wpbdp_template_vars', array($this, 'render_region_sidelist'), 10, 2);
        add_filter('wpbdp_template_vars', array($this, 'render_region_selector'), 9, 2);

        add_action('wp_ajax_wpbdp-regions-get-regions', array($this, 'ajax'));
        add_action('wp_ajax_nopriv_wpbdp-regions-get-regions', array($this, 'ajax'));
    }

    public function add_shortcodes( $shortcodes ) {
        $shortcodes += array_fill_keys( array( 'wpbdp-region',
                                               'businessdirectory-regions-region',
                                               'businessdirectory-region',
                                               'business-directory-regions-region',
                                               'business-directory-region',
                                               'business-directory-regions' ),
                                        array( &$this, 'shortcode' ) );
        $shortcodes += array_fill_keys( array( 'wpbdp_regions_browser',
                                               'businessdirectory-regions-browser',
                                               'business-directory-regions-browser' ),
                                        array( &$this, 'regions_browser_shortcode' ) );
        return $shortcodes;
    }

    public function query_vars($vars) {
        array_push($vars, 'bd-module');
        array_push($vars, 'bd-action');
        array_push($vars, 'region-id');

        return $vars;
    }

    public function rewrite_rules($rules) {
        add_rewrite_rule('wpbdp/api/regions/set-location', 'index.php?bd-module=wpbdp-regions&bd-action=set-location', 'top');
        return $rules;
    }

    public function template_redirect() {
        $module = get_query_var('bd-module');
        $action = get_query_var('bd-action');

        if ($module != 'wpbdp-regions') return;

        if ($action != 'set-location') return;

        $regions = wpbdp_regions_api();

        if (isset($_POST['set-location'])) {
            $regionfields = wpbdp_regions_fields_api();
            $data = wpbdp_getv($_POST, 'listingfields', array());
            $region = false;

            foreach ($regionfields->get_fields('desc') as $level => $id) {
                if (isset($data[$id]) && $data[$id] > 0) {
                    $region = $data[$id];
                    break;
                }
            }

            if ($region) {
                $regions->set_active_region($region);
            }

        } else if (isset($_POST['clear-location'])) {
            $regions->clear_active_region();

        } else if (isset($_REQUEST['region-id'])) {
            $regions->set_active_region((int) $_REQUEST['region-id']);
        }

        $redirect = isset($_REQUEST['redirect']) ? trim($_REQUEST['redirect']) : '';
        wp_redirect($redirect ? $redirect : wp_get_referer());
        exit();
    }

    public function search_where($where, $args) {
        global $wpdb;

        $regionfields = wpbdp_regions_fields_api();
        $fields = $regionfields->get_visible_fields();

        $terms = array();
        // fields are sorted from top to bottom in Region hierarchy,
        // consider the Region selected in the greatest (deeper) level only
        foreach ($fields as $field) {
            foreach ($args['fields'] as $query) {
                if ($query['field_id'] == $field->get_id() && !empty($query['q']))
                    $terms = array($query['q']);
            }
        }

        if (empty($terms)) return $where;

        $query = "SELECT rp.ID FROM {$wpdb->posts} AS rp ";
        $query.= "JOIN {$wpdb->term_relationships} AS rtr ON (rp.ID = rtr.object_id) ";
        $query.= "JOIN {$wpdb->term_taxonomy} AS rtt ";
        $query.= sprintf("ON (rtr.term_taxonomy_id = rtt.term_taxonomy_id AND rtt.term_id IN (%s))", join(',', $terms));

        return sprintf("%s AND {$wpdb->posts}.ID IN (%s)", $where, $query);
    }

    public function url() {
        if (get_option('permalink_structure'))
            return home_url('/wpbdp/api/regions/set-location');
        return home_url('index.php?bd-module=wpbdp-regions&bd-action=set-location');
    }

    private function get_current_location() {
        $regions = wpbdp_regions_api();
        $active = $regions->get_active_region();
        $hierarchy = array();

        $level = $regions->get_region_level($active, $hierarchy);
        $min = wpbdp_regions_fields_api()->get_min_visible_level();

        $text = _x('Displaying listings from %s.', 'region-selector', 'wpbdp-regions');

        if (is_null($active) || $level < $min) {
            return sprintf($text, _x('all locations', 'region-selector', 'wpbdp-regions'));
        }

        $names = array();
        for ($i = $min; $i <= $level; $i++) {
            $names[] = $regions->find_by_id($hierarchy[$level - $i])->name;
        }

        return sprintf($text, sprintf('<strong>%s</strong>', join('&nbsp;&#8594;&nbsp;', $names)));
    }

    private function _render_region_sidelist($regions, $children) {
        $api = wpbdp_regions_api();
        $show_counts = wpbdp_get_option('regions-sidelist-counts');
        $hide_empty = wpbdp_get_option('regions-sidelist-hide-empty');

        if ($show_counts)
            $item = '<a href="#" data-url="%s">%s</a> (%d)';
        else
            $item = '<a href="#" data-url="%s">%s</a>';

        $baseurl = $this->url();

        if (!empty($regions)) {
            $regions = $api->find(array('include' => $regions, 'hide_empty' => 0));
        }

        $html = '';
        foreach ($regions as $region) {
            if ($hide_empty && $region->count == 0)
                continue;

            $url = add_query_arg('region-id', $region->term_id, $baseurl);
            if ( is_paged() )
                $url = add_query_arg( 'redirect', get_pagenum_link(1, true), $url );

            $html .= '<li>';
            $html .= $show_counts ? sprintf($item, $url, $region->name, intval($region->count)) : sprintf($item, $url, $region->name);

            if (isset($children[$region->term_id]) && is_array($children[$region->term_id])) {
                $html .= '<a class="js-handler" href="#"><span></span></a>';
                $html .= sprintf( '<ul data-collapsible="true" data-collapsible-default-mode="%s">%s</ul>',
                                  wpbdp_get_option( 'regions-sidelist-autoexpand' ) ? 'open' : '',
                                  $this->_render_region_sidelist($children[$region->term_id], $children) );
            }

            $html .= '</li>';
        }

        return $html;
    }

    public function render_region_sidelist($vars, $template) {
        static $search = false;
        static $processed = false;

        if (!wpbdp_get_option('regions-show-sidelist')) return $vars;

        // only one region sidelist per request
        if ($processed) return $vars;

        // businessdirectory-listings is rendered from the search template,
        // however, we don't want to show the sidelist in that case
        $match = array_intersect((array) $template, array('search'));
        if (!empty($match)) $search = true;
        if ($search) return $vars;

        // only show sidelist on main page or listings page
        $pages = array('businessdirectory-main-page', 'businessdirectory-listings');
        $match = array_intersect((array) $template, $pages);
        if (empty($match)) return $vars;

        $children = wpbdp_regions_api()->get_sidelisted_regions_hierarchy();
        $level = wpbdp_regions_fields_api()->get_min_visible_level();
        $regions = wpbdp_regions_api()->find_sidelisted_regions_by_level($level);

        $html  = '';
        $html .= '<div class="wpbdp-region-sidelist-wrapper">';
        $html .= '<input type="button" class="sidelist-menu-toggle" value="' . _x( 'Regions Menu', 'sidelist', 'wpbdp-regions' ) . '" />';
        $html .= '<ul class="wpbdp-region-sidelist">%s</ul>';
        $html .= '</div>';
        $html = sprintf($html, $this->_render_region_sidelist($regions, $children));

        $vars['__page__']['class'] = array_merge($vars['__page__']['class'], array('with-region-sidelist'));
        $vars['__page__']['before_content'] = $vars['__page__']['before_content'] . $html;

        $processed = true;

        return $vars;
    }

    public function render_region_selector($vars, $template) {
        static $processed = false;
        static $search = false;

        if ( !$this->selector || wpbdp_get_option( 'regions-hide-selector' ) ||
             wpbdp_starts_with( $template, 'submit-listing', false ) )
            return $vars;

        // only one region sidelist per request
        if ($processed) return $vars;

        // // businessdirectory-listings is rendered from the search template,
        // // however, we don't want to show the sidelist in that case
        // $match = array_intersect((array) $template, array('search'));
        // if (!empty($match)) $search = true;
        // if ($search) return $vars;

        $formfields = wpbdp()->formfields;
        $region_fields = wpbdp_regions_fields_api();

        $fields = array();
        $value = null;

        foreach (wpbdp_regions_fields_api()->get_visible_fields() as $field) {
            if (!is_null($value)) {
                wpbdp_regions()->set('parent-for-' . $field->get_id(), $value);
            }

             // get active region for this field
            $value = $region_fields->field_value(null, null, $field, true);
            $fields[] = $field->render( $value );
        }

        ob_start();
            include(WPBDP_REGIONS_MODULE_DIR . '/templates/region-selector.tpl.php');
            $region_selector = ob_get_contents();
        ob_end_clean();

        $vars['__page__']['before_content'] = $vars['__page__']['before_content'] . $region_selector;

        $processed = true;

        return $vars;
    }

    public function shortcode($attrs) {
        extract(shortcode_atts(array(
            'region' => false,
            'children' => true
        ), $attrs));

        if ( is_numeric( $region ) ) {
            $region = wpbdp_regions_api()->find_by_id($region);
        } else {
            $region = wpbdp_regions_api()->find_by_name( $region );

            if ( ! $region )
                $region = wpbdp_regions_api()->find_by_slug( $region );
        }

        if ( ! $region || is_null( $region ) )
            return _x("The specified Region doesn't exist.", "region shortcode", 'wpbdp-regions');

        $paged = 1;
        if (get_query_var('page'))
            $paged = get_query_var('page');
        else if (get_query_var('paged'))
            $paged = get_query_var('paged');

        query_posts(array(
            'post_type' => WPBDP_POST_TYPE,
            'posts_per_page' => wpbdp_get_option( 'listings-per-page' ) > 0 ? wpbdp_get_option( 'listings-per-page' ) : -1,
            'post_status' => 'publish',
            'paged' => intval($paged),
            'orderby' => wpbdp_get_option('listings-order-by', 'date'),
            'order' => wpbdp_get_option('listings-sort', 'ASC'),
            'tax_query' => array(
                array(
                    'taxonomy' => wpbdp_regions_taxonomy(),
                    'field' => 'id',
                    'terms' => array($region->term_id),
                    'include_children' => $children,
                    'operator' => 'IN'
                )
            )
        ));

        // disable region selector
        $this->selector = false;

        $params = array('excludebuttons' => false);
        $html = wpbdp_render('businessdirectory-listings', $params, true);

        wp_reset_query();

        return $html;
    }

    /*
     * Regions browser shortcode.
     */

    public function regions_browser_shortcode( $args ) {
        $args = wp_parse_args( $args, array(
            'base_region' => null,
            /*'max_level' => null,*/
            'breadcrumbs' => 1
        ) );

        $api = wpbdp_regions_api();
        $forms_api = wpbdp_regions_fields_api();

        extract( $args );

        if ( $base_region ) {
            $base_region = get_term_by( is_numeric ( $base_region ) ? 'id' : 'name', $base_region, wpbdp_regions_taxonomy() );
            $base_level = $api->get_region_level( $base_region->term_id );

            if ( !$base_region )
                return '';
        }

        $current_region = isset( $_GET['r'] ) ? get_term( intval( $_GET['r'] ), wpbdp_regions_taxonomy() ) : $base_region;
        $current_region->link = add_query_arg( array( 'region-id' => $current_region->term_id, 'redirect' => wpbdp_get_page_link( 'main' ) ), $this->url() );

        if ( !$current_region )
            return '';

        $level = $api->get_region_level( $current_region->term_id );
        $api_max_level = $api->get_max_level();
        $next_level_field = $level >= $api_max_level ? null : $forms_api->get_field_by_level( $level + 1 );

        $ids = $api->find_top_level_regions( array( 'parent' => $current_region->term_id ) );
        $regions = $api->find( array( 'include' => $ids ? $ids : array( -1 ), 'orderby' => 'name', 'hide_empty' => false ) );

        foreach ( $regions as &$r ) {
            $children = count( get_term_children( $r->term_id, wpbdp_regions_taxonomy() ) );
            
            if ( $children > 0 )
                $r->link = add_query_arg( 'r', $r->term_id );
            else
                $r->link = add_query_arg( array( 'region-id' => $r->term_id, 'redirect' => wpbdp_get_page_link( 'main' ) ), $this->url() );
        }

        if ( $level > $base_level )
            $regions = $this->regions_browser_classify( $regions );

        $breadcrumbs_text = $breadcrumbs ? $this->regions_browser_breadcrumb( $current_region->term_id, $base_level ) : '';

        return wpbdp_render_page( WPBDP_REGIONS_MODULE_DIR . '/templates/regions-browser.tpl.php',
                                  array( 'breadcrumbs' => $breadcrumbs_text,
                                         'current_region' => $current_region,
                                         'regions' => $regions,
                                         'field' => $next_level_field,
                                         'alphabetically' => $level > $base_level ? true : false ) );
    }

    private function regions_browser_classify( $regions = array() ) {
        $c = array();

        foreach ( $regions as &$r ) {
            $first_char = $r->name[0];

            if ( !isset( $c[ $first_char ] ) )
                $c[ $first_char ] = array();

            $c[ $first_char ][] = $r;
        }

        return $c;
    }

    private function regions_browser_breadcrumb( $region_id, $base_level = 0 ) {
        $parts = array();
        $api = wpbdp_regions_api();

        while ( $region_id ) {
            $term = $api->find_by_id( $region_id );

            if ( $api->get_region_level( $region_id ) >= $base_level )
                $parts[] = sprintf( '<a href="%s">%s</a>', add_query_arg( 'r', $term->term_id ), $term->name );

            $region_id = $term->parent;
        }

        return implode( ' &raquo; ', array_reverse( $parts ) );
    }

    public function ajax() {
        $parent = wpbdp_getv($_REQUEST, 'parent', false);
        $level = wpbdp_getv($_REQUEST, 'level', false);
        $field = wpbdp_getv($_REQUEST, 'field', false);

        // no support for searching by multiple parents
        $parent = is_array($parent) ? array_shift($parent) : $parent;

        $formfields = wpbdp()->formfields;
        $field = $formfields->get_field($field);

        wpbdp_regions()->set('parent-for-' . $field->get_id(), $parent);

        $html = $field->render();

        $response = array('status' => 'ok', 'html' => $html);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

function wpbdp_regions_region_page_title() {
    $term = null;

    if ( get_query_var('taxonomy') == wpbdp_regions_taxonomy() ) {
        if ($id = get_query_var('term_id'))
            $term = wpbdp_regions_api()->find_by_id($id);
        else if ($slug = get_query_var('term'))
            $term = wpbdp_regions_api()->find_by_slug($slug);
    }

    return is_null($term) ? '' : esc_attr($term->name);
}
