(function($){

  window.__login = function(){

    this.init = function(){
      self.initEvents();
    };
    this.initEvents = function(){
//      $(document).on('submit.login', 'form', self.handlers.submit);
    };
    this.handlers = {
      submit: function(e){
        e.preventDefault();
        self.hide();
        var form = $(this).blur();
        var url = form.attr('action');
        var method = form.attr('method') ? form.attr('method') : 'post';
        var data = new FormData();
        $('input, select, textarea',this).each(function(){
          data.append($(this).attr('name'), $(this).val());
        });
        $('input[type=file]',this).each(function(){
          data.append($(this).attr('name'), $(this).prop('files')[0]);
        });
        self.processRequest(url, method, data);
      }
    };
    this.processRequest = function (url, method, postData) {
      method = method ? method : 'post';
      $.ajax({
        url: url,
        data: postData,
        type: method,
        processData: false,
        contentType: false,
        success: function (data) {
          self.setContent(data);
        },
        error: function (responce) {
          self.setContent('<div class="alert alert-danger" role="alert">'+responce.responseText+'</div>');
        }
      });
    };
    this.show = function(){
      $('.ajax-content').fadeIn(500);
    };
    this.hide = function(){
      $('.ajax-content').hide();
    };
    this.setContent = function(content) {
      self.show();
      $('.ajax-content').html(content);
    };
    var self = this;
    this.init();
  };

  $(document).ready(function(){
    var login = new window.__login();
  });
})(jQuery);