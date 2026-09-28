<?php
/**
 * Animate Button Widget
 *
 * @package     MotionUI_Addons
 * @subpackage  Widgets
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Widgets;

use Themeic\MotionUI_Addons\Traits\Button_Controls;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Animate_Button
 *
 * @since 1.0.0
 */
class Animated_Button extends Muia_Base{

    use Button_Controls;

    // Scripts and styles come from this widget's `js` and `css` in
    // widgets-map.php, resolved by Muia_Base. Overriding them here would shadow
    // that, so the entry is the one place to edit.
    /**
     * Register widget controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function register_controls() {
        $this->_register_muia_btn_content_controls();
        $this->_register_muia_btn_style_controls();
    }

    /**
     * Render the widget output on the frontend.
     *
     * @since  1.0.0
     * @return void
     */
    protected function render() {
        $this->_render_muia_btn();
    }

} 