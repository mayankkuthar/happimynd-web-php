$(function () {

  $('.loop').on('initialized.owl.carousel translate.owl.carousel', function (e) {
    idx = e.item.index;
    $('.owl-item.big').removeClass('big');
    $('.owl-item.medium').removeClass('medium');
    $('.owl-item').eq(idx).addClass('big');
    $('.owl-item').eq(idx - 1).addClass('medium');
    $('.owl-item').eq(idx + 1).addClass('medium');
  });

  $('.loop').owlCarousel({
    center: true,
    items: 1,
    loop: true,
    nav: false,
    margin: 30,
    dots: false,
    mouseDrag: false,
    touchDrag: false,
    responsive: {
      767: {
        items: 3
      }
    }
  });

  $(".section7__content-carousel-prevbtn").on("click", function () {
    $(".owl-prev").click();
  })
  $(".section7__content-carousel-nextbtn").on("click", function () {
    $(".owl-next").click();
  })
});


function switchTabs(e) {
  $(".happilife").removeClass("active-tab");
  $(".happiapp").removeClass("active-tab");
  $(".happitalk").removeClass("active-tab");
  $(".happispace").removeClass("active-tab");
  $(".happichat").removeClass("active-tab");

  $(".happiguide").removeClass("active-tab");


  $(".section9__ourservices-tabs__happilife").hide()
  $(".section9__ourservices-tabs__happiapp").hide()
  $(".section9__ourservices-tabs__happitalk").hide()
  $(".section9__ourservices-tabs__happispace").hide()
  $(".section9__ourservices-tabs__happichat").hide()

  $(".section9__ourservices-tabs__happiguide").hide()


  $(".section9__ourservices-tabs__" + e).fadeIn(600);
  $("." + e).addClass("active-tab");

  // if($(window).width()<=576) {
  $('html, body').animate({
    scrollTop: $(".section9__ourservices-tabs__" + e).offset().top - $(".navbar").outerHeight()
  }, 500);
  // }
}

function clickToHover() {
  $('html, body').animate({
    scrollTop: $("#ourservices").offset().top - $(".navbar").outerHeight()
  }, 1500);
  showMenuBar();
}

if (window.location.pathname == '/') {
  var count = 0;
  $(window).scroll(function () {

    var oTop = $('#counter').offset().top - window.innerHeight;
    if (count == 0 && $(window).scrollTop() > oTop) {
      $('.counter-value').each(function () {
        var $this = $(this),
          countTo = $this.attr('data-count');
        $({
          countNum: $this.text()
        }).animate({
          countNum: countTo
        },
          {
            duration: 1500,
            easing: 'swing',
            step: function () {
              $this.text(Math.floor(this.countNum));
            },
            complete: function () {
              $this.text(this.countNum);
            }
          });
      });
      count = 1;
    }
  });
}
$(window).on("load resize", function () {
  $(".section2__play-intro-video video").css("width", $(".section2__play-intro-video").width());
});

$(window).on("load", function () {
  if (window.location.pathname === "/services") {
    let hashvalue = window.location.href;
    hashvalue = hashvalue.split("#");
    getHashValue(hashvalue[1]);
  }
});

function getHashValue(v) {
  if (v) {
    switchTabs(v);
    $('html, body').animate({
      scrollTop: $("#" + v).offset().top - $(".navbar").outerHeight()
    }, 1500);
  }
}
if ($("div").hasClass('modal fade landing-modal')) {
  let stored = localStorage.getItem('lastOpened');
  if (stored) {
    if (Number.parseInt(stored) != (new Date()).setHours(0, 0, 0, 0)) {
      open_landing_modal();
    }
  }
  else {
    open_landing_modal();
  }
  localStorage.setItem('lastOpened', (new Date()).setHours(0, 0, 0, 0));
}

function open_landing_modal() {
  $('#exampleModal').modal({ backdrop: 'static', keyboard: false })
  $("#exampleModal").modal('show');
  $('.main__header').addClass('blur');
  $('.section2__play-intro-video ').addClass('blur');
  $('.section1').addClass('blur');
  $('.landingpage__navigation-menu').addClass('d-none');
}


$('#close-landing-modal').click(function () {
  $('.main__header').removeClass('blur');
  $('.section1').removeClass('blur');
  $('.section2__play-intro-video ').removeClass('blur');
  $('.landingpage__navigation-menu').removeClass('d-none');
});