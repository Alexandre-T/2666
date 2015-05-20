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
    //$('#example').popover(options)
    $('[data-toggle="popover"]').popover();
    var racine="http://2666.jeuforum.fr/jeuderole/styles/2666/theme/images/background/";
    var backgrounds=[
   	  'absences.jpg',
   	  'aide-de-jeu.jpg',
   	  'ange.jpg',
   	  'biseau.jpg',
   	  'coursive.jpg',
   	  'decheance.jpg',
   	  'demon.jpg',
   	  'description-du-jeu.jpg',
   	  'description.jpg',
   	  'enerim.jpg',
   	  'espace-de-jeu.jpg',
   	  'espace-detente.jpg',
   	  'espace-virtuel.jpg',
   	  'fantome.jpg',
   	  'holodeck.jpg',
   	  'index.jpg',
   	  'lidrya.jpg',
   	  'liens.jpg',
   	  'navette.jpg',
   	  'ou-suis-je.jpg',
   	  'partenaires.jpg',
   	  'passerelle.jpg',
   	  'personnages.jpg',
   	  'presentation.jpg',
   	  'quais.jpg',
   	  'raven.jpg',
   	  'reacteur.jpg',
   	  'resumes.jpg',
   	  'san-djinn-barr.jpg',
   	  'service-medical.jpg',
   	  'soutes.jpg',
   	  'vaisseau-monde.jpg',
   	  'visioconference.jpg',
    ];
    var rotateEvery = 20; //seconds
    setInterval(delayFunction, rotateEvery*1000);
    function delayFunction() {
    	var img2 = new Image();
        img2.onload = function(){    	
        	$('div#bg').fadeOut('slow',function(){
            	var x = Math.floor((Math.random() * backgrounds.length));
                $(this).css("background-image",'url('+racine+backgrounds[x]+')').fadeIn('slow');          
              });
        };
        var x = Math.floor((Math.random() * backgrounds.length));
        img2.src= racine+backgrounds[x];
    }
    delayFunction();
    
    
    
  });
  

  
  
}(window.jQuery);