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
    var rotateEvery = 5; //seconds
    setInterval(delayFunction, rotateEvery*1000);
    function delayFunction() {
    	var animates=[
      	  'bounce',
      	  'flash',
      	  'shake',
      	  'swing',
      	  'tada',
       ];
    	$('#jeu').removeClass('animated bounceInRight bounce flash shake swing tada');
    	var x = Math.floor((Math.random() * animates.length));
    	$('#jeu').addClass('animated ' + animates[x]);
    }
  });
}(window.jQuery);