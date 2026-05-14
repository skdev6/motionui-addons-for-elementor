<?php 
namespace Themeic\MotionUI_Addons\Widgets;

class Motionui_Placeholder extends \Elementor\Widget_Base {
    private $widget_name;
    private $widget_data;

    public function __construct($name, $data) {
        $this->widget_name = $name;
        $this->widget_data = $data;
        parent::__construct([], ['widget_type' => 'muia-' . $name]);
    }

    public function get_name() { return 'muia-' . $this->widget_name; }
    public function get_title() { return $this->widget_data['title']; }
    public function get_icon() { return $this->widget_data['icon'] . ' themeic-muia-logo'; }
    public function get_categories() { return ['motionui_addons']; }
    public function is_editable() { return false; }
    public function is_enabled() { return false; }

    protected function get_upsale_data(): array {
        return [
            'condition'   => true,
            'image'       => THEMEIC_MUIA_ASSETS . 'assets/images/get-pro-sm.png',
            'image_alt'   => esc_attr__( 'Upgrade to Pro', 'motionui-addons' ),
            'title'       => sprintf(
                esc_html__( 'Get %s Widget', 'motionui-addons' ),
                $this->get_title()
            ),
            'description' => sprintf(
                esc_html__( 'Unlock the %s widget and 50+ Pro widgets by upgrading to MotionUI Addons Pro.', 'motionui-addons' ),
                $this->get_title()
            ),
            'upgrade_url'  => esc_url( 'https://motionuiaddons.com/pricing/' ), 
            'upgrade_text' => esc_html__( 'Upgrade to Pro', 'motionui-addons' ),
        ];
    }

    protected function render() {}
}  