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
  /**
   * Add pro widgets placeholder
   */
  if (typeof elementor !== 'undefined' && elementor.hooks) {
    elementor.hooks.addFilter("panel/elements/regionViews", function (regionViews) {
      if (typeof MotionUIEditor === 'undefined' || MotionUIEditor.hasPro || _.isEmpty(MotionUIEditor.placeholder_widgets)) {
        return regionViews;
      }
      var CATEGORY_NAME = "motionui_addons_pro",
        elementsView = regionViews.elements.view,
        categoriesView = regionViews.categories.view,
        elementsCollection = regionViews.elements.options.collection,
        categoriesCollection = regionViews.categories.options.collection,
        proWidgets = [],
        ElementView,
        freeCategoryIndex;
      _.each(MotionUIEditor.placeholder_widgets, function (widget, name) {
        elementsCollection.add({
          name: "muia-" + name,
          title: widget.title,
          icon: widget.icon + ' themeic-muia-logo',
          categories: [CATEGORY_NAME],
          editable: false
        });
      });
      elementsCollection.each(function (element) {
        if (element.get("categories")[0] === CATEGORY_NAME) {
          proWidgets.push(element);
        }
      });
      freeCategoryIndex = categoriesCollection.findIndex({
        name: "motionui_addons"
      });
      if (freeCategoryIndex !== -1) {
        categoriesCollection.add({
          name: "motionui_addons_pro_category",
          title: "MotionUI Addons Pro",
          icon: "themeic-muia-logo",
          defaultActive: false,
          sort: true,
          hideIfEmpty: true,
          items: proWidgets,
          promotion: false
        }, {
          at: freeCategoryIndex + 1
        });
      }
      ElementView = {
        className: function className() {
          var className = this.constructor.__super__.className.call(this);
          if (!this.isEditable() && this.isHappyWidget()) {
            className += " muia-element--promotion";
          }
          return className;
        },
        isHappyWidget: function isHappyWidget() {
          var widgetName = this.model.get("name");
          return widgetName != undefined && widgetName.indexOf("muia-") === 0;
        },
        onMouseDown: function onMouseDown() {
          var title = this.model.get("title");

          if (!this.isHappyWidget()) {
            this.constructor.__super__.onMouseDown.call(this);
            return;
          }
          elementor.promotion.showDialog({
            title: MotionUIEditor.i18n.promotionDialogHeader.replace('%s', title),
            content: MotionUIEditor.i18n.promotionDialogMessage.replace('%s', title),
            targetElement: this.el,
            position: {
              blockStart: '-7'
            },
            actionButton: {
              url: "https://motionuiaddons.com/",
              text: MotionUIEditor.i18n.promotionDialogBtnTxt,
              classes: ['elementor-button', 'muia-btn--promotion', 'go-pro']
            }
          });
        }
      };
      regionViews.elements.view = elementsView.extend({
        childView: elementsView.prototype.childView.extend(ElementView)
      });
      regionViews.categories.view = categoriesView.extend({
        childView: categoriesView.prototype.childView.extend({
          childView: categoriesView.prototype.childView.prototype.childView.extend(ElementView)
        })
      });
      return regionViews;
    });
  }

})(jQuery);
