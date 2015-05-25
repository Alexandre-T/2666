!function ($) {
  $(function(){
    var $window = $(window);
    // tooltip demo
    $('.tooltip-link').tooltip({      
      container : 'body',
      placement : 'bottom'
    });
    $('.tooltip-left').tooltip({      
        container : 'body',
        placement : 'left'
      });
    $('.tooltip-top').tooltip({      
        container : 'body'
      });
    
    $("a").attr("target","_blank");
  });
}(window.jQuery);