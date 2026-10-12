/**
 * Selects whose Pro-only options are disabled without Pro.
 *
 * Matched on the setting name's ending, not the whole name: the button trait
 * registers "<prefix>_btn_effect" and every widget picks its own prefix, so
 * muia_btn_btn_effect, pss_button_btn_effect, filter_btn_btn_effect and any
 * future one are all covered without listing them.
 */

console.log("Has Pro");

const pro_select_suffixes = [
    '_btn_effect',
    'muia_motion_trigger_mode',
]; 

/** Selects matched by their exact name. */
const pro_select_fields = [
    'muia_text_ani',
];

(function ($) {  
    "use strict";

    $(window).on('elementor:init', function () { 

        elementor.hooks.addAction('panel/open_editor/widget', function (panel) {
            processOptions(panel.$el.find('select'));
        });

        // Sections render their controls when opened, after the hook above,
        // so a saved Pro value in a closed section is caught here.
        elementor.channels.editor.on('section:activated', function (sectionName, editor) {
            if (editor && editor.$el) {
                processOptions(editor.$el.find('select'));
            }
        });

        // Delegated: only reacts to selects, not every click
        $(document).on('mousedown focusin', 'select', function () {
            processOptions($(this));
        });
    });

    function processOptions($selects) {
        $selects.find('option').each(function () {
            const text = this.textContent;

            if (text.includes('Muia Pro')) {
                this.textContent = text.replace('Muia Pro', 'Pro');
                this.disabled = true;
            }
        });

        $selects.each(function () {
            resetLockedValue(this);
        });
    }

    /**
     * A value saved while Pro was active (or picked before the option was
     * locked) stays selected after Pro goes away. Move it to the control's
     * default — or the first free option, if the default is Pro too — and
     * trigger change so Elementor saves it, not just the dropdown.
     */
    function resetLockedValue(select) {
        const selected = select.options[select.selectedIndex];

        if (!selected || !selected.disabled) {
            return;
        }

        const name = select.getAttribute('data-setting');
        const defaultValue = getControlDefault(name);

        const usable = (value) => Array.prototype.some.call(select.options, (option) =>
            option.value === value && !option.disabled
        );

        let value = (typeof defaultValue === 'string' && usable(defaultValue)) ? defaultValue : null;

        if (value === null) {
            const firstFree = Array.prototype.find.call(select.options, (option) => !option.disabled);
            if (!firstFree) {
                return;
            }
            value = firstFree.value;
        }

        $(select).val(value).trigger('change');
    }

    /**
     * Default of a control on the element being edited, read from the
     * element's settings model, which carries the registered controls.
     */
    function getControlDefault(name) {
        if (!name) {
            return undefined;
        }

        try {
            const page = elementor.getPanelView().getCurrentPageView();
            const model = page.model || (page.getOption && page.getOption('editedElementView') && page.getOption('editedElementView').model);
            const controls = model && model.get('settings') && model.get('settings').controls;

            return controls && controls[name] ? controls[name].default : undefined;
        } catch (e) {
            return undefined;
        }
    }


function customizeDialog() {
    let dialog = document.querySelector('#elementor-element--promotion__dialog');
    if ( ! dialog ) return;
    dialog.classList.add('muia-dilog-content');
    let oldBtn = dialog.querySelector('.elementor-promotion-dialog__button, .dialog-buttons-action, button.go-pro');
    if ( oldBtn ) {
        let newBtn = document.createElement('a');
        newBtn.href        = MotionUIEditor.upgradeUrl;
        newBtn.target      = '_blank';
        newBtn.textContent = MotionUIEditor.btnText;
        newBtn.className   = oldBtn.className;
        newBtn.classList.remove('go-pro')
        newBtn.classList.add('muia-btn--upgrade');

        oldBtn.parentNode.replaceChild( newBtn, oldBtn );
    }
    // Title
    let title = dialog.querySelector('.elementor-promotion-dialog__title, .dialog-header .dialog-title');
    if ( title && ! title.textContent.includes('MotionUI') ) {
        title.textContent = title.textContent.replace('Upgrade', 'Get MotionUI Pro');
    }
    // Description
    let desc = dialog.querySelector('.elementor-promotion-dialog__description, .dialog-message');
    if ( desc ) {
        desc.textContent = MotionUIEditor.desc;
    }
    // Image
  let img = dialog.querySelector('.elementor-promotion-dialog__image img, .dialog-widget-content img');

  if ( img ) {
      if ( MotionUIEditor.proImage ) {
          img.src = MotionUIEditor.proImage;
          img.alt = 'MotionUI Addons Pro';
      }
  } else if ( MotionUIEditor.proImage ) {
      let newImg = document.createElement('img');
      newImg.src = MotionUIEditor.proImage;
      newImg.alt = 'MotionUI Addons Pro';

      let wrap = document.createElement('div');
      wrap.className = 'elementor-promotion-dialog__image';
      wrap.appendChild( newImg );

      if ( desc ) desc.parentNode.insertBefore( wrap, desc );
  }
    dialog.querySelector('button.go-pro')?.remove(); 
}

})(jQuery);
