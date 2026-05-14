const pro_select_fields = [
    'muia_btn_btn_effect', 'muia_text_ani'
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
      var currentView = elementor.getPanelView().getCurrentPageView();
      if ( ! currentView || ! currentView.$el ) return; // safety check
      pro_select_fields.forEach(function (field) {
          var $field = currentView.$el.find('[data-setting="' + field + '"]');
          if ( $field.length ) {
              $field.find('option').each(function () {
                  if ( jQuery(this).text().includes('Pro') ) {
                      jQuery(this).prop('disabled', true);
                  }
              });
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
