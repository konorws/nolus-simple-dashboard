(function ($) {

  var DATA = {
    elements: {
      $height: null,
      $time: null,
      $heightCount: null
    },
    API: null,
    startHeight: 0,
    trackTime: 0,
    up: 0,
  }

  $(document).ready(function () {
    let $height = $('#js-data');
    DATA.elements.$height = $height;
    DATA.elements.$time = $('#js-time-view');
    DATA.elements.$heightCount = $('.height-up');
    DATA.startHeight = parseInt($height.data('height'));
    DATA.API = $height.data('api');


    setInterval(function () {
      $.ajax({
        type: 'GET',
        url: '/api/block/last',
        data: {
          api: DATA.API,
        },
        success: function (data) {
          DATA.elements.$height.text(data.height);
          let blocks = parseInt(data.height);
          DATA.elements.$time.text(data.time + ' s');

          let diffBlock = blocks - DATA.startHeight;
          DATA.startHeight = blocks;

          if(diffBlock > 0) {
            DATA.elements.$heightCount.text('+' +diffBlock);
            DATA.elements.$heightCount.fadeIn(600);
            DATA.elements.$heightCount.fadeOut(1200);
          }

        },
        error: function (data) {
          console.log(data);
        }
      })
    }, 5000)
  });

})(jQuery);
