(function($){

  var __commentar = function(){
    this.page = 1;
    this.cardId = null;
    this.wrapper = null;

    this.init = function(){
      $('.comment-input').twemojiPicker({
        height: 32,
        iconSize: 22,
        categorySize: 22,
        size: 18
      });

      self.wrapper = $('.comments-item-block');
      self.cardId = $(self.wrapper).data('id');
      self.loadPage();

      self.initEvents();
    };
    this.initEvents = function(){
      $('.addit-text-comment-form', self.wrapper).on('submit', self.handlers.submit);
      $('.even-comments', self.wrapper).on('click', self.handlers.click.more);
      $(document).on('click', '.comments-item-block .reply', self.handlers.click.reply);
    };

    this.loadPage = function(){
      $.get('/backend/knowledge/load-comments?id='+self.cardId+'&page='+self.page, function(data){
        if (data){
          if (data.count > 0) {
            $('.event-cnt', self.wrapper).html(data.count);
            $('.even-comments', self.wrapper).show();
          } else $('.even-comments', self.wrapper).hide();
          $('.card-comments', self.wrapper).prepend(twemoji.parse(data.content));
        }
      }, 'json');
    };

    this.handlers = {
      'submit': function(e){
        e.preventDefault();
        var param = $('meta[name=csrf-param]').attr("content");
        var token = $('meta[name=csrf-token]').attr("content");

        var text = $('.twemoji-textarea-duplicate', self.wrapper).text();
        var data = { text: text };
        data[param] = token;
        $.post('/backend/knowledge/publish-comment?id='+self.cardId, data, function(){
          self.page = 1;
          $('.card-comments', self.wrapper).empty();
          $('.twemoji-textarea-duplicate', self.wrapper).empty();
          $('.twemoji-textarea', self.wrapper).empty();
          self.loadPage();
        });
      },
      'click': {
        'more': function(e){
          e.preventDefault();
          self.page++;
          self.loadPage();
        },
        'reply': function(e){
          e.preventDefault();
          $('.addit-text-comment .twemoji-textarea, .addit-text-comment .twemoji-textarea-duplicate', self.wrapper).append($(this).data('href')).focus();
          cursorFocus (e, ".twemoji-textarea-duplicate");
          cursorFocus (e, ".twemoji-textarea");
        }
      }
    };

    var self = this;
    this.init();
  };
    function cursorFocus (e, element) {
        e.preventDefault();

        var myElem = $(element).get(0);
        myElem.focus();

        if (window.getSelection && document.createRange) {
            // IE 9 and non-IE
            var sel = window.getSelection();
            var range = document.createRange();
            range.setStart(myElem, 1);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.body.createTextRange) {
            // IE < 9
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(myElem);
            textRange.collapse(true);
            textRange.select();
        }
        myElem.focus();
    };
  window.commentar = new __commentar();

})(jQuery);

