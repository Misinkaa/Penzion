<!-- Core CSS file -->
<link rel="stylesheet" href="./vendor/photoswipe/photoswipe.css"> 

<!-- Skin CSS file (styling of UI - buttons, caption, etc.)
     In the folder of skin CSS file there are also:
     - .png and .svg icons sprite, 
     - preloader.gif (for browsers that do not support CSS animations) -->
<link rel="stylesheet" href="./vendor/photoswipe/default-skin/default-skin.css"> 

<!-- Core JS file -->
<script src="./vendor/photoswipe/photoswipe.min.js"></script> 

<!-- UI JS file -->
<script src="./vendor/photoswipe/photoswipe-ui-default.min.js"></script> 



<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe. 
         It's a separate element as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides. 
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don't modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                <button class="pswp__button pswp__button--share" title="Share"></button>

                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

        </div>

    </div>

</div>

<script>
    //pole vsech obrazku ktere chceme zobrazit ve photoswipe
    var items = [];

    //pomoci jquery zamerime vsechny odkazy na strance
    //jquery funkce .each proiteruje vsechny nase zamerene elementy 
    $('section a').each(function() {
        //toto je prave iterovany element
        var odkaz = this;

        //pomoci funkce .find hledame v odkazu element img
        if ($(this).find('img').length > 0)
        {
            //vytvorena instance Imgage, ktera je zatim prazdna
            var obrazek = new Image();
            //k instanci pridal src, kteoru precetl z atributu odkazu
            obrazek.src = $(odkaz).attr('href');

            //instance uz zna src
            //spustil nactenni obrazku do instance
            obrazek.onload = function()
            {
                //precte dimenze obrazku
                var item = {
                    src: $(odkaz).attr('href'),
                    w: obrazek.width,
                    h: obrazek.height,
                    title: $(odkaz).attr('title')
                };
                //vlozi obrazek do pole items
                items.push(item);
                console.log(item);
            }

            //na tento odkaz pripoji event klik
            $(odkaz).on('click', function(udalost) {
                //zabrani puvodnimu chovani click
                udalost.preventDefault();

                //kazdy obrazek v galerii musi mit svou pozici
                var index = 0;
                //pozici obrazku v galerii urcime podle pozice odkazu
                for (var i = 0; i < items.length; i++)
                {
                    if (items[i].src == $(this).attr('href'))
                    {
                        index = i;
                    }
                }

                var options = {
                    index: index // start at first slide
                };

                //zde otevreme pohotoswipe s obrazky
                var gallery = new PhotoSwipe(document.querySelector('.pswp'), PhotoSwipeUI_Default, items, options);
                gallery.init();
            });
        }
        
    });

</script>