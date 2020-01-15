'use strict';

$(document).ready(function(){

    var sidebarVar = 'sidebar-state';
    var sidebarState = Cookies.get(sidebarVar);
    sidebarState = parseInt(sidebarState?sidebarState:0);

    function setSidebarState(state){
        state = parseInt(state);
        Cookies.set(sidebarVar, state, { expires: 7, path: '/' });
        if (state) {
          $('.left-nav').addClass('open');
          $('body').addClass('open-menu');
          $('.nav-link span').addClass('rotateIcon');
        } else {
          $('.left-nav').removeClass('open');
          $('body').removeClass('open-menu');
          $('.nav-link span').removeClass('rotateIcon');
        }
        sidebarState = state;
    }
    setSidebarState(sidebarState);

    $(document).on('click', '.nav-link', function(){
        if (sidebarState) {
          setSidebarState(0);
        } else {
          setSidebarState(1);
        }
    });

    $('.evaluation .rate').on('click', function(e){
        e.preventDefault();
        $(this).parents('.evaluation').removeAttr('class').addClass('evaluation rate-wrapper rate-'+$(this).data('value'));
        $(this).parents('.evaluation-item').find('.set-rate').removeClass('hidden');
        $(this).parents('.evaluation-item').data('rate', $(this).data('value'));
    });

    $('.evaluation-item a.evaluation-now').on('click', function(e){
        e.preventDefault();
        var data = {
            rate: $(this).parents('.evaluation-item').data('rate'),
            id   : $(this).parents('.item-content').data('id')
        };
        var self = this;
        $.post('/backend/knowledge/save-rate', data, function(data){
            $(self).parents('.evaluation-item').find('.rate-wrapper').removeAttr('class').addClass('evaluation rate-wrapper rate-'+data.rate);
            $(self).parents('.evaluation-item').find('p.useful').html(data.text);
            $(self).parents('.evaluation-item').find('.set-rate').addClass('hidden');
        }, 'json');
    });

    $('.textarea-notes').on('blur', function(){
        var self = this;
        var data = {
            text : $(this).val(),
            id   : $(this).parents('.item-content').data('id')
        };
        $.post('/backend/knowledge/save-private-comment', data, function(data){
            $(self).parents('.item-content').find('.update-text').html(data);
        });
    });
    if ($('.textarea-notes').text().length > 1) {
        $('.text-note').hide();
        $('.textarea-notes').show();
    } else {
        $('.text-note').on('click', function () {
            $(this).fadeOut(400, function () {
                $('.textarea-notes').fadeIn(300, function () {
                    $(this).focus();
                    $(this).on('blur', function () {
                        if($(this).val() != '' && !($('.note-background').hasClass('not-empty-notes'))) {
                            $('.note-background').addClass('not-empty-notes');
                        }
                    })
                });
            })
        });
    }

    var viewStyle = Cookies.get('view-style');
    viewStyle = viewStyle?viewStyle:'list';
    Cookies.set('view-style', viewStyle);

    $('body').addClass('vs-'+viewStyle);
    $('.viewStyleControl .view-style-'+viewStyle).parents('li').addClass('active');

    $(document).on('click', '.viewStyleControl a.view-style', function(){
        $(this).parents('.viewStyleControl').find('li').removeClass('active');
        $(this).parents('li').addClass('active');
        $('body').removeClass('vs-tile vs-list').addClass('vs-'+$(this).data('style'));
        Cookies.set('view-style', $(this).data('style'));
    });

    /*
      $(document).on('click', 'a[data-target]', function(){
        console.log($(this).data('target'));
        $($(this).data('target')).modal('show');
      });

    $(document).on('click', 'a.ajaxify', function(e){
        e.preventDefault();
        var self = this;
        $('.flexBlockAll>.content').load($(this).attr('href'), function(){
            var viewStyle = Cookies.get('view-style');
            viewStyle = viewStyle?viewStyle:'list';
            $('.viewStyleControl .view-style-'+viewStyle).parents('li').addClass('active');
            History.pushState({createtime: new Date().getTime()}, $(document).attr('title'), $(self).attr('href'));
        });
    });

    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        var State = History.getState(); // Note: We are using History.getState() instead of event.state
        var createtime = State.data.createtime;
        var timestamp = new Date().getTime();
        var diff = timestamp - createtime;

        if(diff>500){
            //manage back and forward button here
            window.location.reload();
        }
    });
     */

    /*notice-link*/
    $(document).on('click', '.notice-link', function(e){
        e.preventDefault();
        $('#notifications').slideToggle(300);
    });
    /*notice-link*/

    /*evaluation-hover*/

    $('li.rate').hover(function(){
        var elem = $(this);
        var positionNum = Number(elem.data('value'));
        $('li.rate').each(function(i){
            $(this).children().addClass('hover-bg');
            if(i === positionNum - 1) {
                return false;
            }
        })
    }, function(){
        $('li.rate').children().removeClass('hover-bg');
    });
    /*evaluation-hover*/

    /*slick*/
    $('.slick-gallery').slick({
        infinite: true,
        arrows: false,
        dots: true
    });
    /*slick*/

    $(document).on('click', '.exit-block', function(){
        $(this).parents('li').remove();
    });

    /*custom radiobutton*/
    $('.field-user-theme').find('input').each(function(){
        if($(this).prop('checked')){
            $(this).parent().addClass('checked');
        }
    });
    $('.field-user-theme label').on('click',function(){
        $('.field-user-theme label').removeClass('checked');
        $(this).addClass('checked');
    });
    /*custom radiobutton*/

    $(document).on('change', '.notice-counter', function(e){
      var val = $(this).html();
      $('.notice-counter').addClass('hidden');
      if (val > 0) {
        $('.notice-counter').removeClass('hidden');
      }
    });
    $(document).on('click', 'a.close-notification', function(e){
        e.preventDefault();
        var el = this;
        $.get('/site/notify-seen', $(el).data(), function(result){
            var sel = '.notification-'+$(el).data('type')+'-'+$(el).data('id');
            $(sel).parents('.growl-message').hide();
            $(sel).parents('.notification').hide();
          $('.notice-counter').html(result.count).addClass('hidden');
          if (result.count > 0) {
            $('.notice-counter').removeClass('hidden');
          }
        });
    });
    $('select').on('select2:select', function (evt) {
        if($(this).val() === "0"){
            $('.wrap-select-access').addClass('public');
        } else {
            $('.wrap-select-access').removeClass('public');
        }
    });


});
$(window).on("load",function(){
    if($(window).width() > 768){
        $(".content-wrapper").mCustomScrollbar({
            theme:"minimal-dark"
        });
    }
});
$(window).load(function(){
    $(".focus-input").inputFocus({});
    $('.wrap-focus-input input').on('click', function(){
        $(this).inputFocus({})
    });
    $('.add-hashtag label').on('click', function(){
        var elem = $(this).parents('.wrap-focus-input').children('.bootstrap-tagsinput').children('input');
        elem.inputFocus({});
        elem.focus();
    })
});

(function($){
    $.fn.inputFocus = function(){
        $(this).each(function(){
            var elem = $(this),
                elemParent = elem.parents('.wrap-focus-input');
            if(elem.val() !== "") {
                elemParent.addClass('active-input');
            }
            elemParent.children('.focus-input-label').css('opacity', 1);

            if (!elem.hasClass('al-if')) {
                $(this).on('focus', function(){
                    var elem = $(this),
                        elemParent = elem.parents('.wrap-focus-input');
                    if(!elemParent.hasClass("active-input")){
                        elemParent.removeClass('disactive-input').addClass('active-input');
                    } else {
                        return false;
                    }
                }).on('blur', function(){
                    var elem = $(this),
                        elemParent = elem.parents('.wrap-focus-input');
                    if(elemParent.hasClass("active-input") && elem.val() === ''){
                        elemParent.removeClass('active-input').addClass('disactive-input');
                    } else {
                        return false;
                    }
                });
            }

            elem.addClass('al-if');

        });
    }
})(jQuery);



