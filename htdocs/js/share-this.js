(function($){

  var __share_this = function(){

    this.init = function(){
      self.initEvents();
    };
    this.initEvents = function(){
      $(document).on('click', 'a.share-this', self.events.click.share);
      $(document).on('click', 'a.link-btn-share', self.events.click.copy);
      $(document).on('click', '.share-form-modal input.text-wrap', self.events.click.input);
    };

    this.events = {
      click: {
        share: function(e){
          e.preventDefault();
          var linkName = $('.link-name', this).html();
          var el = this;
          var link; var error;
          $.ajax($(this).attr('href'), {
            async: false,
            dataType: 'json',
            method: 'POST',
            success: function(data){
              error = data.error;
              link  = data.link;
              if (data.error){
                $(el).siblings('.errors').html('errors');
              } else {
                $(el).siblings('.errors').html('');
                $('.modal-share').modal('hide');
              }
            }
          });

          if (!error) {
            $(this).after('<input type="text" class="hidden11" id="temporary-for-clipboard" value="'+link+'">');
            var
              inp = document.getElementById('temporary-for-clipboard'),
              cpyrs = false;

            if (inp && inp.select) {
              inp.select();
              try {
                cpyrs = document.execCommand('copy');
                inp.remove();
              }
              catch (err) {
                cpyrs = false;
              }
            }
            var text = '<p>Ccылка для <a href="'+link+'"> '+linkName+' </a></p>';
            if (cpyrs) {
              text += '<p> создана и скопирована в буфер обмена </p>';
            } else {
              text += '<p> создана </p>';
            }
            $('.dashboard-information').append('<li><div class="dashboard-link-name"><div class="icon-block"><div class="icon-circle"><img src="/img/icon-notification-share.svg" alt="chain"></div></div><div class="text-link-name">'+text+'</div><div class="exit-block"><i class="fa fa-times" aria-hidden="true"></i></div></div></li>');
          }
        },
        copy: function(e){
          e.preventDefault();
          $(this).after('<input type="text" class="hidden11" id="temporary-for-clipboard" value="'+$(this).data('url')+'">');
          var
            inp = document.getElementById('temporary-for-clipboard'),
            cpyrs = false;

          if (inp && inp.select) {
            inp.select();
            try {
              cpyrs = document.execCommand('copy');
            }
            catch (err) {
              cpyrs = false;
            }
          }

          var name = $(this).data('name');
          var text = '<p>Ccылка для <a href="'+$(this).data('url')+'"> '+name+' </a></p>';
          text += '<p> скопирована в буфер обмена </p>';
          $('.dashboard-information').append('<li><div class="dashboard-link-name"><div class="icon-block"><div class="icon-circle"><img src="/img/icon-notification-share.svg" alt="chain"></div></div><div class="text-link-name">'+text+'</div><div class="exit-block"><i class="fa fa-times" aria-hidden="true"></i></div></div></li>');
          $('.modal-share').modal('hide');
          inp.remove();
        },
        input: function(e){
          e.preventDefault();
          $(this).focus().select();
        }
      }
    };

    var self = this;
    this.init();
  };

  window.share_this = new __share_this();

})(jQuery);