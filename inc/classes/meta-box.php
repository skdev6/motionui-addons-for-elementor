<?php

namespace Themeic\MotionUI_Addons\Inc\Classes;

/**
 * MetaBoxHelper - WordPress Custom Meta Box Field Helper Class
 *
 * Supports: text, select, checkbox, radio, rich text editor (wp_editor), repeater
 * Design follows WordPress default admin UI conventions.
 *
 * Usage Example:
 * -----------------------------------------------------------------------
 * $mb = new MetaBoxHelper('my_meta_box', 'My Details', 'post');
 *
 * $mb->add_field('text', [
 *     'id'          => 'my_text_field',
 *     'label'       => 'Full Name',
 *     'placeholder' => 'Enter name...',
 *     'description' => 'Your full name.',
 * ]);
 *
 * $mb->add_field('select', [
 *     'id'      => 'my_select_field',
 *     'label'   => 'Status',
 *     'options' => ['draft' => 'Draft', 'published' => 'Published'],
 * ]);
 *
 * $mb->add_field('checkbox', [
 *     'id'      => 'my_checkbox_field',
 *     'label'   => 'Enable Feature',
 *     'options' => ['enable' => 'Enable this feature'],
 * ]);
 *
 * $mb->add_field('radio', [
 *     'id'      => 'my_radio_field',
 *     'label'   => 'Choose Option',
 *     'options' => ['yes' => 'Yes', 'no' => 'No'],
 * ]);
 *
 * $mb->add_field('editor', [
 *     'id'    => 'my_editor_field',
 *     'label' => 'Description',
 * ]);
 *
 * $mb->add_field('repeater', [
 *     'id'    => 'my_repeater_field',
 *     'label' => 'Team Members',
 *     'sub_fields' => [
 *         ['type' => 'text',   'id' => 'name',  'label' => 'Name'],
 *         ['type' => 'text',   'id' => 'role',  'label' => 'Role'],
 *         ['type' => 'select', 'id' => 'dept',  'label' => 'Department',
 *          'options' => ['eng' => 'Engineering', 'hr' => 'HR']],
 *     ],
 * ]);
 *
 * $mb->register();
 * -----------------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Meta_Box{

    /** @var string Unique meta box ID */
    private string $id;

    /** @var string Meta box title shown in admin */
    private string $title;

    /** @var string|array Post type(s) to attach to */
    private $post_types;

    /** @var string Meta box context (normal | side | advanced) */
    private string $context;

    /** @var string Meta box priority */
    private string $priority;

    /** @var array Registered fields */
    private array $fields = [];

    /** @var string Nonce action */
    private string $nonce_action;

    /** @var string Nonce field name */
    private string $nonce_name;

    /**
     * Constructor.
     *
     * @param string       $id         Unique meta box slug (snake_case).
     * @param string       $title      Human-readable title.
     * @param string|array $post_types Post type(s), e.g. 'post' or ['post','page'].
     * @param string       $context    'normal' | 'side' | 'advanced'.
     * @param string       $priority   'default' | 'high' | 'low'.
     */
    public function __construct(
        string $id,
        string $title,
        $post_types = 'post',
        string $context  = 'normal',
        string $priority = 'default'
    ) {
        $this->id           = sanitize_key( $id );
        $this->title        = $title;
        $this->post_types   = (array) $post_types;
        $this->context      = $context;
        $this->priority     = $priority;
        $this->nonce_action = $this->id . '_nonce_action';
        $this->nonce_name   = $this->id . '_nonce_field';
    }

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Add a field to the meta box.
     *
     * @param string $type   Field type: text | select | checkbox | radio | editor | repeater.
     * @param array  $config Field configuration array. See render_* methods for keys.
     */
    public function add_field( string $type, array $config ): self {
        $supported = [ 'text', 'select', 'checkbox', 'radio', 'editor', 'repeater' ];

        if ( ! in_array( $type, $supported, true ) ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            trigger_error(
                sprintf( 'MetaBoxHelper: Unsupported field type "%s".', esc_html( $type ) ),
                E_USER_WARNING
            );
            return $this;
        }

        $this->fields[] = array_merge( $config, [ 'type' => $type ] );

        return $this;
    }

    /**
     * Register all WordPress hooks. Call this after adding all fields.
     */
    public function register(): void {
        add_action( 'add_meta_boxes',  [ $this, '_add_meta_boxes' ] );
        add_action( 'save_post',       [ $this, '_save_post' ], 10, 2 );
        add_action( 'admin_footer',    [ $this, '_repeater_scripts' ] );
        add_action( 'admin_head',      [ $this, '_admin_styles' ] );
    }

    // =========================================================================
    // WordPress Hooks (prefixed with _ to signal internal use)
    // =========================================================================

    /** @internal */
    public function _add_meta_boxes(): void {
        foreach ( $this->post_types as $post_type ) {
            add_meta_box(
                $this->id,
                $this->title,
                [ $this, '_render_meta_box' ],
                $post_type,
                $this->context,
                $this->priority
            );
        }
    }

    /** @internal */
    public function _render_meta_box( \WP_Post $post ): void {
        wp_nonce_field( $this->nonce_action, $this->nonce_name );

        echo '<table class="form-table mbh-form-table" role="presentation">';
        echo '<tbody>';

        foreach ( $this->fields as $field ) {
            $this->render_field_row( $field, $post );
        }

        echo '</tbody>';
        echo '</table>';
    }

    /** @internal */
    public function _save_post( int $post_id, \WP_Post $post ): void {
        // Verify nonce
        if (
            ! isset( $_POST[ $this->nonce_name ] ) ||
            ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $this->nonce_name ] ) ), $this->nonce_action )
        ) {
            return;
        }

        // Bail on autosave / revision / bulk edit
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Permission check
        $post_type_obj = get_post_type_object( $post->post_type );
        if ( ! current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) {
            return;
        }

        foreach ( $this->fields as $field ) {
            $this->save_field( $field, $post_id );
        }
    }

    /** @internal – inline styles matching WP admin */
    public function _admin_styles(): void {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $post_type = $screen->post_type ?? '';
        if ( ! in_array( $post_type, $this->post_types, true ) ) {
            return;
        }
        ?>
        <style id="mbh-styles">
            /* ---- MetaBoxHelper Styles ---- */
            .mbh-form-table td { padding: 10px 0; }
            .mbh-form-table .mbh-label { font-weight: 600; padding-bottom: 4px; display: block; }
            .mbh-form-table .description { color: #646970; font-size: 13px; margin-top: 4px; display: block; }

            /* Repeater */
            .mbh-repeater-wrap .mbh-repeater-rows { margin-bottom: 8px; }
            .mbh-repeater-row {
                background: #f9f9f9;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                padding: 12px 12px 6px;
                margin-bottom: 8px;
                position: relative;
            }
            .mbh-repeater-row .mbh-row-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                font-weight: 600;
                font-size: 13px;
                color: #3c434a;
                border-bottom: 1px solid #dcdcde;
                padding-bottom: 8px;
            }
            .mbh-repeater-row .mbh-remove-row {
                color: #b32d2e;
                cursor: pointer;
                font-size: 13px;
                background: none;
                border: none;
                padding: 0;
                line-height: 1;
                text-decoration: underline;
            }
            .mbh-repeater-row .mbh-remove-row:hover { color: #8a2222; }
            .mbh-repeater-row table { width: 100%; }
            .mbh-repeater-row table td { padding: 6px 0; }
            .mbh-repeater-row .mbh-label { font-size: 13px; }
            .mbh-repeater-row input[type="text"],
            .mbh-repeater-row select { width: 100%; max-width: 100%; }
            .mbh-add-row-btn { margin-top: 4px; }

            /* Checkbox / Radio spacing */
            .mbh-options-list { margin: 0; padding: 0; list-style: none; }
            .mbh-options-list li { margin-bottom: 6px; }
            .mbh-options-list li label { display: flex; align-items: center; gap: 6px; cursor: pointer; }
        </style>
        <?php
    }

    /** @internal – JS for repeater add/remove rows */
    public function _repeater_scripts(): void {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $post_type = $screen->post_type ?? '';
        if ( ! in_array( $post_type, $this->post_types, true ) ) {
            return;
        }

        $has_repeater = ! empty( array_filter( $this->fields, fn( $f ) => $f['type'] === 'repeater' ) );
        if ( ! $has_repeater ) {
            return;
        }
        ?>
        <script type="text/javascript">
        (function($){
            'use strict';

            /**
             * Update row index numbers displayed in row headers.
             */
            function updateRowIndexes($wrap) {
                $wrap.find('.mbh-repeater-row').each(function(i) {
                    $(this).find('.mbh-row-index').text(i + 1);

                    // Re-index name attributes: fieldName[0][sub] -> fieldName[N][sub]
                    $(this).find('[name]').each(function() {
                        var name = $(this).attr('name');
                        $(this).attr('name', name.replace(/\[(\d+)\]/, '[' + i + ']'));
                    });
                });
            }

            $(document).on('click', '.mbh-add-row-btn', function() {
                var $btn   = $(this);
                var $wrap  = $btn.closest('.mbh-repeater-wrap');
                var $rows  = $wrap.find('.mbh-repeater-rows');
                var $tmpl  = $wrap.find('.mbh-repeater-template .mbh-repeater-row').first();
                var newIdx = $rows.find('.mbh-repeater-row').length;

                // Clone template row
                var $newRow = $tmpl.clone(true);

                // Set correct index in name attributes
                $newRow.find('[name]').each(function() {
                    var name = $(this).attr('name');
                    $(this).attr('name', name.replace(/\[(\d+)\]/, '[' + newIdx + ']'));
                });

                // Clear values
                $newRow.find('input[type="text"]').val('');
                $newRow.find('select').prop('selectedIndex', 0);
                $newRow.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

                $rows.append($newRow);
                updateRowIndexes($wrap);
            });

            $(document).on('click', '.mbh-remove-row', function() {
                var $wrap = $(this).closest('.mbh-repeater-wrap');
                $(this).closest('.mbh-repeater-row').remove();
                updateRowIndexes($wrap);
            });

        })(jQuery);
        </script>
        <?php
    }

    // =========================================================================
    // Rendering
    // =========================================================================

    /**
     * Render a single <tr> row wrapping the field.
     */
    private function render_field_row( array $field, \WP_Post $post ): void {
        $label = isset( $field['label'] ) ? $field['label'] : '';
        $desc  = isset( $field['description'] ) ? $field['description'] : '';

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label class="mbh-label" for="' . esc_attr( $field['id'] ?? '' ) . '">';
        echo esc_html( $label );
        echo '</label>';
        echo '</th>';
        echo '<td>';

        $this->render_field( $field, $post );

        if ( $desc ) {
            echo '<span class="description">' . wp_kses_post( $desc ) . '</span>';
        }

        echo '</td>';
        echo '</tr>';
    }

    /**
     * Dispatch rendering to the correct field renderer.
     */
    private function render_field( array $field, \WP_Post $post ): void {
        $type = $field['type'] ?? 'text';

        switch ( $type ) {
            case 'text':
                $this->render_text( $field, $post );
                break;
            case 'select':
                $this->render_select( $field, $post );
                break;
            case 'checkbox':
                $this->render_checkbox( $field, $post );
                break;
            case 'radio':
                $this->render_radio( $field, $post );
                break;
            case 'editor':
                $this->render_editor( $field, $post );
                break;
            case 'repeater':
                $this->render_repeater( $field, $post );
                break;
        }
    }

    // -------------------------------------------------------------------------
    // Field Renderers
    // -------------------------------------------------------------------------

    /**
     * Text field.
     *
     * Config keys:
     *   id          (string)  Required. Meta key & element ID.
     *   label       (string)  Row label.
     *   placeholder (string)  Input placeholder.
     *   description (string)  Help text shown below field.
     *   class       (string)  Extra CSS class(es). Default: 'regular-text'.
     */
    private function render_text( array $field, \WP_Post $post, string $name_override = '' ): void {
        $id          = $field['id'] ?? '';
        $name        = $name_override ?: $id;
        $value       = $name_override ? '' : get_post_meta( $post->ID, $id, true );
        $placeholder = $field['placeholder'] ?? '';
        $class       = $field['class'] ?? 'regular-text';

        printf(
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" placeholder="%4$s" class="%5$s">',
            esc_attr( $id ),
            esc_attr( $name ),
            esc_attr( $value ),
            esc_attr( $placeholder ),
            esc_attr( $class )
        );
    }

    /**
     * Select (dropdown) field.
     *
     * Config keys:
     *   id          (string)         Required.
     *   label       (string)         Row label.
     *   options     (array)          Associative array: value => label.
     *   description (string)         Help text.
     *   placeholder (string)         First empty option label, e.g. '— Select —'.
     */
    private function render_select( array $field, \WP_Post $post, string $name_override = '' ): void {
        $id      = $field['id'] ?? '';
        $name    = $name_override ?: $id;
        $saved   = $name_override ? '' : get_post_meta( $post->ID, $id, true );
        $options = $field['options'] ?? [];
        $first   = $field['placeholder'] ?? '';

        printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );

        if ( $first ) {
            printf( '<option value="">%s</option>', esc_html( $first ) );
        }

        foreach ( $options as $val => $label ) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr( $val ),
                selected( $saved, $val, false ),
                esc_html( $label )
            );
        }

        echo '</select>';
    }

    /**
     * Checkbox field (supports multiple checkboxes).
     *
     * Config keys:
     *   id          (string)         Required.
     *   label       (string)         Row label.
     *   options     (array)          value => label. Single item = single checkbox.
     *   description (string)         Help text.
     */
    private function render_checkbox( array $field, \WP_Post $post, string $name_override = '' ): void {
        $id      = $field['id'] ?? '';
        $options = $field['options'] ?? [];

        if ( $name_override ) {
            // Inside repeater: treat as single checkbox per option
            $saved = [];
        } else {
            $saved = (array) get_post_meta( $post->ID, $id, true );
        }

        $is_single = count( $options ) === 1;
        $name_attr = $is_single
            ? ( $name_override ?: $id )
            : ( $name_override ?: $id . '[]' );

        echo '<ul class="mbh-options-list">';

        foreach ( $options as $val => $opt_label ) {
            $checked = ! $name_override && in_array( $val, $saved, true ) ? 'checked="checked"' : '';

            printf(
                '<li><label><input type="checkbox" name="%s" value="%s" %s> %s</label></li>',
                esc_attr( $is_single ? $name_attr : ( $name_override ?: $id . '[]' ) ),
                esc_attr( $val ),
                $checked,
                esc_html( $opt_label )
            );
        }

        echo '</ul>';
    }

    /**
     * Radio button field.
     *
     * Config keys:
     *   id          (string)         Required.
     *   label       (string)         Row label.
     *   options     (array)          value => label.
     *   description (string)         Help text.
     */
    private function render_radio( array $field, \WP_Post $post, string $name_override = '' ): void {
        $id      = $field['id'] ?? '';
        $name    = $name_override ?: $id;
        $saved   = $name_override ? '' : get_post_meta( $post->ID, $id, true );
        $options = $field['options'] ?? [];

        echo '<ul class="mbh-options-list">';

        foreach ( $options as $val => $opt_label ) {
            printf(
                '<li><label><input type="radio" name="%s" value="%s"%s> %s</label></li>',
                esc_attr( $name ),
                esc_attr( $val ),
                checked( $saved, $val, false ),
                esc_html( $opt_label )
            );
        }

        echo '</ul>';
    }

    /**
     * Rich text editor (wp_editor).
     *
     * Config keys:
     *   id          (string)         Required. Must be lowercase alphanumeric + underscore.
     *   label       (string)         Row label.
     *   description (string)         Help text.
     *   editor_settings (array)      Passed directly to wp_editor(). See WP docs.
     */
    private function render_editor( array $field, \WP_Post $post ): void {
        $id      = $field['id'] ?? '';
        $content = get_post_meta( $post->ID, $id, true );

        $defaults = [
            'textarea_name' => $id,
            'textarea_rows' => 10,
            'teeny'         => false,
            'media_buttons' => true,
        ];

        $settings = array_merge( $defaults, $field['editor_settings'] ?? [] );

        wp_editor( wp_kses_post( $content ), $id, $settings );
    }

    /**
     * Repeater field.
     *
     * Config keys:
     *   id          (string)         Required.
     *   label       (string)         Row label.
     *   sub_fields  (array)          Array of sub-field configs (same structure as add_field).
     *               Each sub-field: [ 'type' => '...', 'id' => '...', 'label' => '...', ... ]
     *   add_label   (string)         Button label. Default: '+ Add Row'.
     *   description (string)         Help text.
     */
    private function render_repeater( array $field, \WP_Post $post ): void {
        $id         = $field['id'] ?? '';
        $sub_fields = $field['sub_fields'] ?? [];
        $add_label  = $field['add_label'] ?? __( '+ Add Row' );
        $rows_data  = get_post_meta( $post->ID, $id, true );

        if ( ! is_array( $rows_data ) ) {
            $rows_data = [];
        }

        echo '<div class="mbh-repeater-wrap" data-repeater-id="' . esc_attr( $id ) . '">';

        // ---- Existing rows ----
        echo '<div class="mbh-repeater-rows">';

        if ( ! empty( $rows_data ) ) {
            foreach ( $rows_data as $row_index => $row_values ) {
                $this->render_repeater_row( $id, $sub_fields, $row_index, $row_values );
            }
        }

        echo '</div>'; // .mbh-repeater-rows

        // ---- Template row (hidden, cloned by JS) ----
        echo '<div class="mbh-repeater-template" style="display:none;">';
        $this->render_repeater_row( $id, $sub_fields, 0, [] );
        echo '</div>';

        // ---- Add row button ----
        printf(
            '<button type="button" class="button mbh-add-row-btn">%s</button>',
            esc_html( $add_label )
        );

        echo '</div>'; // .mbh-repeater-wrap
    }

    /**
     * Render a single repeater row.
     */
    private function render_repeater_row( string $parent_id, array $sub_fields, int $index, array $values ): void {
        echo '<div class="mbh-repeater-row">';

        // Row header
        echo '<div class="mbh-row-header">';
        echo '<span>' . esc_html__( 'Row' ) . ' <span class="mbh-row-index">' . ( $index + 1 ) . '</span></span>';
        echo '<button type="button" class="mbh-remove-row">' . esc_html__( 'Remove' ) . '</button>';
        echo '</div>';

        // Sub-field table
        echo '<table><tbody>';

        foreach ( $sub_fields as $sub ) {
            $sub_id    = $sub['id'] ?? '';
            $sub_type  = $sub['type'] ?? 'text';
            $sub_label = $sub['label'] ?? $sub_id;
            $name_attr = sprintf( '%s[%d][%s]', $parent_id, $index, $sub_id );

            echo '<tr>';
            echo '<th scope="row"><label class="mbh-label" for="' . esc_attr( $parent_id . '_' . $index . '_' . $sub_id ) . '">' . esc_html( $sub_label ) . '</label></th>';
            echo '<td>';

            // Build a pseudo WP_Post so render methods can use get_post_meta fallback (it won't be called in repeater)
            $fake_post    = new \stdClass();
            $fake_post->ID = 0;

            // We pass $name_attr as override so the rendered element uses correct name
            switch ( $sub_type ) {
                case 'text':
                    $sub_merged = array_merge( $sub, [ 'id' => $parent_id . '_' . $index . '_' . $sub_id ] );
                    $val        = isset( $values[ $sub_id ] ) ? esc_attr( $values[ $sub_id ] ) : '';
                    printf(
                        '<input type="text" id="%s" name="%s" value="%s" placeholder="%s" class="regular-text">',
                        esc_attr( $parent_id . '_' . $index . '_' . $sub_id ),
                        esc_attr( $name_attr ),
                        $val,
                        esc_attr( $sub['placeholder'] ?? '' )
                    );
                    break;

                case 'select':
                    $options  = $sub['options'] ?? [];
                    $saved    = $values[ $sub_id ] ?? '';
                    $elem_id  = $parent_id . '_' . $index . '_' . $sub_id;
                    echo '<select id="' . esc_attr( $elem_id ) . '" name="' . esc_attr( $name_attr ) . '">';
                    foreach ( $options as $v => $l ) {
                        printf(
                            '<option value="%s"%s>%s</option>',
                            esc_attr( $v ),
                            selected( $saved, $v, false ),
                            esc_html( $l )
                        );
                    }
                    echo '</select>';
                    break;

                case 'checkbox':
                    $options = $sub['options'] ?? [];
                    echo '<ul class="mbh-options-list">';
                    foreach ( $options as $v => $l ) {
                        $saved_arr = isset( $values[ $sub_id ] ) ? (array) $values[ $sub_id ] : [];
                        $ck        = in_array( $v, $saved_arr, true ) ? 'checked="checked"' : '';
                        printf(
                            '<li><label><input type="checkbox" name="%s" value="%s" %s> %s</label></li>',
                            esc_attr( $parent_id . '[' . $index . '][' . $sub_id . '][]' ),
                            esc_attr( $v ),
                            $ck,
                            esc_html( $l )
                        );
                    }
                    echo '</ul>';
                    break;

                case 'radio':
                    $options = $sub['options'] ?? [];
                    $saved   = $values[ $sub_id ] ?? '';
                    echo '<ul class="mbh-options-list">';
                    foreach ( $options as $v => $l ) {
                        printf(
                            '<li><label><input type="radio" name="%s" value="%s"%s> %s</label></li>',
                            esc_attr( $name_attr ),
                            esc_attr( $v ),
                            checked( $saved, $v, false ),
                            esc_html( $l )
                        );
                    }
                    echo '</ul>';
                    break;
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</div>'; // .mbh-repeater-row
    }

    // =========================================================================
    // Saving
    // =========================================================================

    /**
     * Save a single field's value to post meta.
     */
    private function save_field( array $field, int $post_id ): void {
        $id   = $field['id'] ?? '';
        $type = $field['type'] ?? 'text';

        if ( ! $id ) {
            return;
        }

        switch ( $type ) {
            case 'text':
                $value = isset( $_POST[ $id ] )
                    ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) )
                    : '';
                update_post_meta( $post_id, $id, $value );
                break;

            case 'select':
            case 'radio':
                $value = isset( $_POST[ $id ] )
                    ? sanitize_key( wp_unslash( $_POST[ $id ] ) )
                    : '';
                update_post_meta( $post_id, $id, $value );
                break;

            case 'checkbox':
                if ( isset( $_POST[ $id ] ) ) {
                    $raw = wp_unslash( $_POST[ $id ] );
                    if ( is_array( $raw ) ) {
                        $value = array_map( 'sanitize_key', $raw );
                    } else {
                        $value = [ sanitize_key( $raw ) ];
                    }
                } else {
                    $value = [];
                }
                update_post_meta( $post_id, $id, $value );
                break;

            case 'editor':
                $value = isset( $_POST[ $id ] )
                    ? wp_kses_post( wp_unslash( $_POST[ $id ] ) )
                    : '';
                update_post_meta( $post_id, $id, $value );
                break;

            case 'repeater':
                $this->save_repeater( $field, $post_id );
                break;
        }
    }

    /**
     * Save repeater rows.
     */
    private function save_repeater( array $field, int $post_id ): void {
        $id         = $field['id'] ?? '';
        $sub_fields = $field['sub_fields'] ?? [];

        if ( ! isset( $_POST[ $id ] ) || ! is_array( $_POST[ $id ] ) ) {
            delete_post_meta( $post_id, $id );
            return;
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $raw_rows = wp_unslash( $_POST[ $id ] );
        $clean    = [];

        foreach ( $raw_rows as $row ) {
            if ( ! is_array( $row ) ) {
                continue;
            }

            $clean_row = [];

            foreach ( $sub_fields as $sub ) {
                $sub_id   = $sub['id'] ?? '';
                $sub_type = $sub['type'] ?? 'text';

                if ( ! $sub_id ) {
                    continue;
                }

                switch ( $sub_type ) {
                    case 'text':
                        $clean_row[ $sub_id ] = isset( $row[ $sub_id ] )
                            ? sanitize_text_field( $row[ $sub_id ] )
                            : '';
                        break;

                    case 'select':
                    case 'radio':
                        $clean_row[ $sub_id ] = isset( $row[ $sub_id ] )
                            ? sanitize_key( $row[ $sub_id ] )
                            : '';
                        break;

                    case 'checkbox':
                        if ( isset( $row[ $sub_id ] ) && is_array( $row[ $sub_id ] ) ) {
                            $clean_row[ $sub_id ] = array_map( 'sanitize_key', $row[ $sub_id ] );
                        } else {
                            $clean_row[ $sub_id ] = [];
                        }
                        break;
                }
            }

            $clean[] = $clean_row;
        }

        update_post_meta( $post_id, $id, $clean );
    }
}