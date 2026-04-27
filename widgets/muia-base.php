<?php

namespace Themeic\MotionUI_Addons\Widgets;
use Elementor\Widget_Base;

abstract class Muia_Base extends Widget_Base{

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        /**
         * Automatically generate widget name from class
         */
        $name = str_replace(strtolower(__NAMESPACE__), '', strtolower($this->get_class_name()));
        $name = str_replace('_', '-', $name);
        $name = ltrim($name, '\\');
        return 'muia-' . $name;
    }
    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['motionui_addons'];
    }
    /**
     * Overriding default function to add custom html class.
     *
     * @return string
     */
    public function get_html_wrapper_class() {
        $html_class = parent::get_html_wrapper_class();
        $html_class .= ' motionui-addons';
        $html_class .= ' ' . $this->get_name();
        return rtrim($html_class);
    }
}