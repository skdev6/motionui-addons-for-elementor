/**
 * Selects whose Pro-only options are disabled without Pro.
 *
 * Matched on the setting name's ending, not the whole name: the button trait
 * registers "<prefix>_btn_effect" and every widget picks its own prefix, so
 * muia_btn_btn_effect, pss_button_btn_effect, filter_btn_btn_effect and any
 * future one are all covered without listing them.
 */
const pro_select_suffixes = [
    '_btn_effect',
];

/** Selects matched by their exact name. */
const pro_select_fields = [
    'muia_text_ani',
];

(function ($) {
    "use strict";

  jQuery(window).on('elementor:init', function () {
      
      elementor.hooks.addAction('panel/open_editor/widget', function () {
          setTimeout(disableOptions, 300);
      });

      document.addEventListener('click', function (e) {
          if ( e.target.closest('.elementor-panel-heading,.elementor-component-tab') ) {
              setTimeout(disableOptions, 150);
          }

        if ( e.target.closest('.elementor-element--promotion') ) {
            let el = e.target.closest('.elementor-element--promotion');
            let isMuia = el.querySelector('.themeic-muia-logo');
            if ( isMuia ) setTimeout( customizeDialog, 50 );
        }

      }, true);

  });
  function disableOptions(){  
      // Pro is active: every option is available, so there is nothing to do.
      if ( window.MotionUIEditor && MotionUIEditor.hasPro ) return;

      var currentView = elementor.getPanelView().getCurrentPageView();
      if ( ! currentView || ! currentView.$el ) return; // safety check

      var selector = pro_select_fields
          .map(function (field) { return '[data-setting="' + field + '"]'; })
          .concat(pro_select_suffixes.map(function (suffix) {
              return '[data-setting$="' + suffix + '"]';
          }))
          .join(',');

      currentView.$el.find(selector).find('option').each(function () {
          if ( jQuery(this).text().includes('Pro') ) {
              jQuery(this).prop('disabled', true);
          }
      });
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
