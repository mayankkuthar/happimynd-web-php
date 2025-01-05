$(function(){
  if(window.location.pathname == '/readblog' || window.location.href.indexOf("/readblog/") > -1) {
    var aud = $('#audio-1')[0];
    $('.readblog__related__audio__content__controls__playpause').on('click', function(){
      if (aud.paused) {
        aud.play();
        $('.audio-pause').css("display", 'none');
        $('.audio-play').css("display", 'block');
      }
      else {
        aud.pause();
        $('.audio-pause').css("display", 'block');
        $('.audio-play').css("display", 'none');
      }
    })
    $(".readblog__related__audio__content__progress__bar__fill").on('click', function(e) {
      var percent = e.offsetX / this.offsetWidth;
      aud.currentTime = percent * aud.duration;
      $('.readblog__related__audio__content__progress__bar__filled').css('width', aud.currentTime / aud.duration * 100 + '%')
      $('.readblog__related__audio__content__progress__bar__filled-dot').css('left', (aud.currentTime / aud.duration * 100) - 0.4 + '%')
      $(".running_time").text((Math.round(aud.currentTime) / 100).toFixed(2));
      $(".total_time").text((Math.round(aud.currentTime) / 100).toFixed(2));
    })
    aud.ontimeupdate = function(){
      $('.readblog__related__audio__content__progress__bar__filled').css('width', aud.currentTime / aud.duration * 100 + '%')
      $('.readblog__related__audio__content__progress__bar__filled-dot').css('left', (aud.currentTime / aud.duration * 100) - 0.4 + '%')
      $(".running_time").text((Math.round(aud.currentTime) / 100).toFixed(2));
      $(".total_time").text((Math.round(aud.duration) / 100).toFixed(2));
      if(aud.currentTime == aud.duration) {
        $('.audio-pause').css("display", 'block');
        $('.audio-play').css("display", 'none');
        aud.pause();
      }
    }
  }
});
$(window).on('load resize', function(){
  if(window.location.pathname == '/readblog' || window.location.href.indexOf("/readblog/") > -1) {
    $(".footer").css('padding-bottom', $(".readblog__related__audio").height());
    if($(window).width() <= 767) {
      $(".mobile-text p").css("max-width", $(".readblog__related__audio").width() - $(".readblog__related__audio__content__image").width() - $(".readblog__related__audio__content__controls").width() - 40);
    }
  }
});