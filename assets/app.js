/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
// import $ from './controllers/js/vendor/jquery-1.12.4.min.js';
// import './controllers/js/vendor/modernizr-2.8.3.min.js';
import './controllers/js/vendor/popper.min.js';
import './controllers/js/vendor/bootstrap.min.js';
import './controllers/js/plugins/slick.min.js';
import './controllers/js/plugins/countdown.js';
import './controllers/js/plugins/jquery.barrating.min.js';
import './controllers/js/plugins/jquery.counterup.js';
import './controllers/js/plugins/jquery.nice-select.js';
import './controllers/js/plugins/jquery.sticky-sidebar.js';
import './controllers/js/plugins/jquery-ui.min.js';
import './controllers/js/plugins/jquery.ui.touch-punch.min.js';
import './controllers/js/plugins/lightgallery.min.js';
import './controllers/js/plugins/scroll-top.js';
import './controllers/js/plugins/theia-sticky-sidebar.min.js';
// import './controllers/js/plugins/lightzoom.js';
import './controllers/js/plugins/jquery.zoomit.js';
// import './controllers/js/plugins/waypoints.min.js';
import './controllers/js/plugins/instafeed.min.js';
import './controllers/js/plugins/jquery.elevateZoom-3.0.8.min.js';
import './controllers/js/plugins/timecircles.js';
import './controllers/js/main.js';
import Cart from './controllers/modules/Cart'
import Filter from './controllers/modules/Filter'
new Cart(document.querySelector('.js-cart-form'));
new Filter(document.querySelector('.js-filter'));


let checkout = document.querySelector('.js-checkout');
if(checkout){
    let type1 = document.querySelector('.type1');
    let type2 = document.querySelector('.type2');
    document.querySelector('#payment_method_paymentMethod').firstChild.textContent = 'Choose a payment method';
    type1.addEventListener('click', function(){
        this.classList.add('selected');
        this.nextElementSibling.classList.remove('selected');
        $('#payment_method_paymentMethod').val('');
        $('.nice-select').niceSelect('update');
        document.querySelector('.select-wrapper').style.display = 'none';
        document.querySelector('#payment_method_paymentMethod').required = false;

    })
    type2.addEventListener('click', function(){
        if(!this.classList.contains('disabled')) {
        this.classList.add('selected');
        this.previousElementSibling.classList.remove('selected');
        document.querySelector('.select-wrapper').style.display = 'block';
        document.querySelector('#payment_method_paymentMethod').required = true;
        }
    })
}


$('.js-show-modal1').on('click',function(e){
    console.log('CLICK')
    e.preventDefault();

    let discount = $(this).data('discount');
    if(discount === '0.00 KD' || discount === '0.00 د.ك'){
        $('#price-block1').css('display','none')
        $('#price-block2').css('display','block')
    }else{
        $('#price-block2').css('display','none')
        $('#price-block1').css('display','block')
    }
    $('#name').text($(this).data('name'));
    $('#price').text($(this).data('price'));
    $('#price2').text($(this).data('price'));
    $('#discount').text($(this).data('discount'));
    $('.add-to_cart').attr("href", $(this).data('slug'))
    $('#description').text($(this).data('description'));
    // $('#cart-add-button').attr('href', $(this).data('href-a'));
    var i = 1;
    var access = true;

    var $spimgslider2 =  $('.sp-img_area .sp-img_slider-2');
    var $spimgslidernav =  $('.sp-img_area .sp-img_slider-nav');

    $spimgslider2.empty()
    $spimgslidernav.empty()
    $('.hiraola-slick-slider').slick('removeSlide', null, null, true);
    while (access){

        var picture = $(this).data('image' + i);
        console.log(picture)
        if(!picture)
            break;
        var picturePath = "/media/images/product/"+picture;
        console.log(picturePath)
        $spimgslider2.append('<div class="single-slide '+i+'"><img src="'+ picturePath +'" alt="Shine Aurora Product Image"></div>');
        $spimgslidernav.append('<div class="single-slide '+i+'"><img src="'+ picturePath +'" alt="Shine Aurora Product Image"></div>');
        // $('div.img'+i).data('thumb', picturePath);
        // $('img.img'+i).attr('src', picturePath);
        // $('a.img'+i).attr('href', picturePath);
        i++;
    }

    var $colorList = $('.color-list');
    $colorList.empty();
    i = 1;
    while (access){

        var color = $(this).data('color' + i);
        if(!color)
            break;

        $colorList.append('<div class="single-color"><span style="display:block;background-color: '+color+'"></span></div>');
        i++;
    }
    var $sizeList = $('.size-list');
    $sizeList.empty();
    i = 1;
    while (access){

        var size = $(this).data('size' + i);
        if(!size)
            break;

        $sizeList.append('<div class="single-size"><span style="display:block;">'+size+'</span></div>');
        i++;
    }
    var $tagList = $('.hiraola-tag-line');
    $tagList.empty();
    $tagList.append('<h6>Tags:</h6>')
    i = 1;
    while (access){

        var tag = $(this).data('tag' + i);
        if(!tag)
            break;

        $tagList.append(' <a href="javascript:void(0)">'+tag+'</a>,');
        i++;
    }
    (function ($) {
    /*----------------------------------------*/

    var $elementCarousel = $('.hiraola-slick-slider');
    // Check if element exists
        $elementCarousel.slick("refresh");
    /*----------------------------------------*/
    })(jQuery);
    $('#wich-icon a').attr('href', $(this).data('href')).attr('id', $(this).data('id')).addClass($(this).data('disabled'));
    $('#cart-add-button').attr('href', $(this).data('href-a'));

     i = 1;

    $('#product_cart_size').empty();
    $('#product_cart_size').append("<option value selected>Choose an option</option>");

    $('#product_cart_color').append("<option value='1'>Red</option>");

});

$('#exampleModalCenter').on('hidden', function(e){
    console.log('HIDE')
})

// $('img.light-zoom').lightzoom({
//     zoomPower   : 1.5,    //Default
//     glassSize   : 200,  //Default
// });
let i = 1;
$('.zoomit-target').each(function(){
    $('#zoomit-target'+i).zoomIt();
    i++
})


