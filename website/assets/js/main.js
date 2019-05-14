$(document).ready(function(){
    //Include slicknav for responsive mobile navbar.
    $('#menu').slicknav({
        prependTo:'.header-mobile'
    });

    //Include slick slider.
    $('.slider').slick({
        infinite: true,
        dots: true,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 3500,
        prevArrow:"<button type='button' class='slick-prev pull-left slick-arrow'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
        nextArrow:"<button type='button' class='slick-next pull-right slick-arrow'><i class='fa fa-angle-right' aria-hidden='true'></i></button>",
        customPaging : function(slider, i) {
            var thumb = $(slider.$slides[i]).data('thumb');
            return '<i class="fa fa-dot-circle-o" aria-hidden="true"></i>';
        }
    });

    //Calculate slider height by checking the screenheight minus header height.
    $('.slide').height(
        $(document).height() - $('.header').height()
    );

    //Check if the page is not homepage to fix contact map height.
    if (window.location.pathname != '/') {
        if(document.getElementById("map")) {
            var map = - ('#map').height();
        } else {
            var map = '';
        }
        $('.content').css('min-height', $('body').height() - $('.header').height() - $('.footer').height() - map);
    }

    //Function that strips the URL to get all the get parameters, use this for the onclick filter functions.
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    //create variables that gets the get variables.
    var orderby = getParameterByName('order');
    var minprice = getParameterByName('minprice');
    var maxprice = getParameterByName('maxprice');

    //If selectbox changes from order, refresh the page and add GET parameters to URL with the correct filter type.
    $('#product-filter').change(function () {
        var locAppend = $(this).find('option:selected').attr("name"),
            locSnip = window.location.href.split('?')[0];

        window.location.href = locSnip + '?order=' + locAppend;
    });

    //Check if a key is pressed in the password fields to set and remove classes if the value is correct to the given Regex.
    $('#password1').keyup(function () {
        if((this.value == $('#password2').val()) && (this.value.length != 0)) {
            $('.same-pass').addClass('green-validation');
        } else {
            $('.same-pass').removeClass('green-validation');
        }

        if(this.value.length >= 8) {
            $('.length').addClass('green-validation');
        } else {
            $('.length').removeClass('green-validation');
        }

        if(this.value.match('(?=.*?[A-Z])')) {
            $('.uppercase').addClass('green-validation');
        } else {
            $('.uppercase').removeClass('green-validation');
        }

        if(this.value.match('(?=.*?[a-z])')) {
            $('.lowercase').addClass('green-validation');
        } else {
            $('.lowercase').removeClass('green-validation');
        }

        var numberRegex = /\d/g;
        if(this.value.match('(?=.*?[#?!@$%^&*-])') || numberRegex.test(this.value)) {
            $('.special').addClass('green-validation');
        } else {
            $('.special').removeClass('green-validation');
        }
    });

    $('#password2').keyup(function () {
        if((this.value == $('#password1').val()) && (this.value.length != 0)) {
            $('.same-pass').addClass('green-validation');
        } else {
            $('.same-pass').removeClass('green-validation');
        }
    });

    //Check if the letters e, c, - or backspace is called so the amount of the product in the cart can NEVER be less then 1.
    document.querySelector("#cart-amount").addEventListener("keypress", function (evt) {
        if (evt.which != 8 && evt.which != 0 && evt.which < 48 || evt.which > 57)
        {
            evt.preventDefault();
        }
    });

    //Create constructer and get all amounts of the products in the cart.
    const numInputs = document.querySelectorAll('#cart-amount');

    //Foreach the above constructor with all the input amounts.
    numInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            if (e.target.value == '') {
                e.target.value = 1
            }
        });


        //Update the total price of cart when the amount of a product in the cart goes up.
        $('.cart-row').each(function (index) {
            var i = index + 1;
            var sum = 0;
            $('.cart-amount-' + i).change(function () {
                $('.item-number-' + i).text(($('.cart-amount-' + i).val() * $('.item-number-' + i).attr("data-price")).toFixed(2));
                calculateSum();
            });
        });
    });
});

//Function to calculate the product total price.
function calculateSum() {
    var sum = 0;
    $('.cart-row').each(function (index) {
        var i = index + 1;
        sum += +$('.item-number-' + i).text();
    });
    $('.item-total').text(sum.toFixed(2));
}

//If window resizes, fix the homepage slider height.
$( window ).resize(function() {
    $('.slide').height(
        $(document).height() - $('.header').height()
    );
});

//Initialize the Google Maps.
function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 52.447351, lng: 5.8343931},
        zoom: 15,
        styles: [
            {
                "featureType": "administrative",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": "-100"
                    }
                ]
            },
            {
                "featureType": "administrative.province",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": 65
                    },
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": "50"
                    },
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": "-100"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "all",
                "stylers": [
                    {
                        "lightness": "30"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "all",
                "stylers": [
                    {
                        "lightness": "40"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "hue": "#ffff00"
                    },
                    {
                        "lightness": -25
                    },
                    {
                        "saturation": -97
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels",
                "stylers": [
                    {
                        "lightness": -25
                    },
                    {
                        "saturation": -100
                    }
                ]
            }
        ]
    });

    marker = new google.maps.Marker({
        map:map,
        // draggable:true,
        // animation: google.maps.Animation.DROP,
        position: new google.maps.LatLng(52.447351, 5.8343931),
        icon: '/assets/images/marker.png' // null = default icon
    });
}




