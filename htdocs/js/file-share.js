(function($){

  var __fileshare = function(){
    this.icons = [];
    this.errors = [];
    this.model_id = 0;
    this.init = function(){
      self.initEvents();
    };
    this.initEvents = function(){
      $('.fs-tabs .functions-link').on('click', self.events.click.file);
      $(document).on('mouseup', self.events.mouseup.document);
      $('.file-functions-wrapper .btn-file-function-download').on('click', self.events.click.download);
      $('.file-functions-wrapper .btn-file-function-delete').on('click', self.events.click.delete);
      $('.file-functions-wrapper .btn-file-function-rename').on('click', self.events.click.rename);
      $('.file-functions-wrapper .btn-file-function-move').on('click', self.events.click.move);
      $('.category-functions-wrapper .btn-category-function-delete').on('click', self.events.click.category_delete);
      $('.category-functions-wrapper .btn-category-function-rename').on('click', self.events.click.category_rename);
      $('.category-functions-wrapper .btn-category-function-move').on('click', self.events.click.move);
      $('.btn-upload-file').on('click', self.events.click.upload);
      $('.sharing-modal-btn').on('click', self.events.click.share);
      $('.show-pass-btn').on('click', self.events.click.showPass);
      $('.gener-pass-btn').on('click', self.events.click.generPass);
      $('#share-pass-input').on('click', self.events.click.copy);

      $('.fs-row .file-name-editor').on('change blur', self.events.change.filename_editor);
      $('.dropbox').on('progress', self.events.processFile);
    };
    this.getModelId = function(){
      return self.model_id;
    };
    this.setModelId = function(id){
      self.model_id = id;
    };
    this.selectLine = function(el, el_id){
      self.deselectAll();

      var wrapper = $('.'+$(el).data('target'));

      $('.fileshare-functions').addClass('hidden');
//      if (el_id != self.getModelId()){
        self.setModelId(el_id);
        $(wrapper).removeClass('hidden');
//      } else {
//        self.setModelId(null);
//        $(wrapper).toggleClass('hidden');
//      }

      if ($(wrapper).hasClass('hidden')){
        $(el).removeClass('active-line');
      } else {
        $(el).addClass('active-line');
      }
    };
    this.deselectAll = function(){
      $('.active-line').removeClass('active-line');
      $('.fileshare-functions').addClass('hidden');

      $('.file-name-viewer').show();
      $('.file-name-editor').hide();

    };
    this.randomPassword = function (length) {
      var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
      var charsB = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      var charsM = "abcdefghijklmnopqrstuvwxyz";
      var charsN = "1234567890";
      var pass = "";
      var i;

      i = Math.floor(Math.random() * charsM.length);
      pass += charsM.charAt(i);

      i = Math.floor(Math.random() * charsB.length);
      pass += charsB.charAt(i);

      i = Math.floor(Math.random() * charsN.length);
      pass += charsN.charAt(i);

      for (var x = 0; x < length-3; x++) {
        i = Math.floor(Math.random() * chars.length);
        pass += chars.charAt(i);
      }
      return pass.split('').sort(function(){return 0.5-Math.random()}).join('');
    };
    this.getIcon = function(filename){
      var ext = filename.split('.').pop();
      if (self.icons[ext]) return self.icons[ext];

      $.ajax({
        url: '/backend/fileshare/geticon?ext='+ext,
        method: 'get',
        async: false,
        dataType: 'json',
        success: function(data){
          self.icons[ext] = data.ext;
        }
      });

      return self.icons[ext];
    };

    this.events = {
      change: {
        filename_editor: function(e){
          e.preventDefault();
          var el = this;
          var wrapper = $('.'+$(el).data('type')+'-'+self.getModelId());
          if ($(el).val() != $(el).data('value')){
            $.get($(el).data('href'), {name: $(el).val()}, function(){
              $(el).data('value', $(el).val());
              if ($('.file-name-viewer .folder-name', wrapper).size()){
                $('.file-name-viewer .folder-name', wrapper).html($(el).val());
              } else {
                $('.file-name-viewer', wrapper).html($(el).val());
              }
            });
          }
          $('.file-name-editor', wrapper).hide();
          $('.file-name-viewer', wrapper).show();
        }
      },
      click: {
        generPass: function(e){
          e.preventDefault();
          // var pass = Math.random().toString(36).slice(-10);
          var pass = self.randomPassword(10);
          $(this).siblings('input').val(pass);
        },
        showPass: function(e){
          e.preventDefault();
          var el = $(this).siblings('input');
          if (el){
            if ($(el).attr('type') == 'text') {
              $(el).attr('type', 'password');
            } else {
              $(el).attr('type', 'text');
            }
          }
        },
        share: function(e){
          e.preventDefault();
          $.get($(this).attr('href'), function(data){
            var popup = $('#sharing-pass-modal');
            $('#share-pass-input', popup).val(data.url).data({
              url: data.url,
              name: data.url
            });
            $('label.share-pass-input', popup).html(data.ext+'('+data.size+')');
            $('#add-pass', popup).prop('checked', data.password);
            $('#gener-pass', popup).val('');
            $(".focus-input", popup).inputFocus({});
            $("form", popup).attr('action', data.action);

            var param = $('meta[name=csrf-param]').attr("content");
            var token = $('meta[name=csrf-token]').attr("content");

            $("#fspopup_csrf", popup).attr('name', param).val(token);

            $(popup).modal();
          }, 'json');
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
          $('#sharing-pass-modal').modal('hide');
          inp.remove();

        },
        upload: function(e){
          e.preventDefault();
          $('.dropbox').toggleClass('hidden');
        },
        file: function(e){
          if ($(e.target).prop('tagName') != 'INPUT' && !$(e.target).hasClass('link')){
            e.preventDefault();
            var el_id = $(this).data('id');
            self.selectLine(this, el_id);
          }
        },
        download: function(e){
          e.preventDefault();
          document.location.href = $(this).data('href').replace(/%23/g, self.getModelId());
        },
        delete: function(e){
          e.preventDefault();
          if (confirm('Вы уверены?')){
            document.location.href = $(this).data('href').replace(/%23/g, self.getModelId());
          }
        },
        category_delete: function(e){
          e.preventDefault();
          if (confirm('Вы уверены?')){
            document.location.href = $(this).data('href').replace(/%23/g, self.getModelId());
          }
        },
        rename: function(e){
          e.preventDefault();
          var wrapper = $('.file-'+self.getModelId());
          var href = $(this).data('href').replace(/%23/g, self.getModelId());
          $('.file-name-viewer', wrapper).hide();
          $('.file-name-editor', wrapper).data({
            href: href,
            value: $('.file-name-viewer', wrapper).html()
          }).show();
        },
        category_rename: function(e){
          e.preventDefault();
          var wrapper = $('.category-'+self.getModelId());
          var href = $(this).data('href').replace(/%23/g, self.getModelId());
          $('.file-name-viewer', wrapper).hide();
          $('.file-name-editor', wrapper).data({
            href: href,
            value: $('.file-name-viewer', wrapper).html()
          }).show();
        },
        move: function(e){
          e.preventDefault();
          var popup = $('#modal-move');
          $('.entity_id', popup).val(self.getModelId());
          $('.entity_type', popup).val($(this).hasClass('btn-category-function-move')?'category':'file');
          $(popup).modal();
        }
      },
      mouseup: {
        document: function(e){
          var container = $(".fs-tabs .functions-link, .functions-wrap");

          if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
          {
            self.setModelId(null);
            self.deselectAll();
          }
        }
      },
      processFile: function(e, data){
        var hash = self.getHash(data.files[0]);

        if (!self.errors[hash]) {
          var loaded = data.loaded?data.loaded:0;
          var card = self.getCard(hash, data);
          var progress = parseInt(loaded / data.files[0].size * 100, 10);
          $('.wrap-progress-bar__band', card).css('width', progress+'%');
          if (progress == 100) {
            $(card).remove();
          }
        }
      }
    };
    this.setError = function(file){
      var hash = self.getHash(file);
      self.errors[hash] = true;
    };
    this.getHash = function(file){
      return self.sha1(JSON.stringify([
        file.name,
        file.size,
        file.lastModified
      ]));
    };

    this.getCard = function(hash, data){
      var el = $('.wrap-upload-files .file_'+hash);
      if ($(el).size() == 0){
        el = $('#fileshare-upload-file-template').clone();
        $(el).removeAttr('id').removeClass('hidden').addClass('file_'+hash).data('el', data);

        var icon = this.getIcon(data.files[0].name);
        $('img', el).replaceWith(icon);
        $('.fileshare-upload-file__name', el).html(data.files[0].name);
        $('.fileshare-upload-file__cancel', el).on('click', function(e){
          e.preventDefault();
          $(this).parents('.fileshare-upload-file').data('el').abort();
          $(this).parents('.fileshare-upload-file').remove();
          if ($('.wrap-upload-files .fileshare-upload-file').size() == 0){
            $(".dropbox-details").addClass("hidden");
            $(".dropbox").addClass("hidden");
            document.location.href = document.location.href;
          }
        });
        $('.wrap-upload-files').append(el);
      }
      return el;
    };
    this.sha1 = function ( str ) {	// Calculate the sha1 hash of a string
                             //
                             // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
                             // + namespaced by: Michael White (http://crestidg.com)

      var rotate_left = function(n,s) {
        var t4 = ( n<<s ) | (n>>>(32-s));
        return t4;
      };

      var lsb_hex = function(val) {
        var str="";
        var i;
        var vh;
        var vl;

        for( i=0; i<=6; i+=2 ) {
          vh = (val>>>(i*4+4))&0x0f;
          vl = (val>>>(i*4))&0x0f;
          str += vh.toString(16) + vl.toString(16);
        }
        return str;
      };

      var cvt_hex = function(val) {
        var str="";
        var i;
        var v;

        for( i=7; i>=0; i-- ) {
          v = (val>>>(i*4))&0x0f;
          str += v.toString(16);
        }
        return str;
      };

      var blockstart;
      var i, j;
      var W = new Array(80);
      var H0 = 0x67452301;
      var H1 = 0xEFCDAB89;
      var H2 = 0x98BADCFE;
      var H3 = 0x10325476;
      var H4 = 0xC3D2E1F0;
      var A, B, C, D, E;
      var temp;

      str = this.utf8_encode(str);
      var str_len = str.length;

      var word_array = new Array();
      for( i=0; i<str_len-3; i+=4 ) {
        j = str.charCodeAt(i)<<24 | str.charCodeAt(i+1)<<16 |
          str.charCodeAt(i+2)<<8 | str.charCodeAt(i+3);
        word_array.push( j );
      }

      switch( str_len % 4 ) {
        case 0:
          i = 0x080000000;
          break;
        case 1:
          i = str.charCodeAt(str_len-1)<<24 | 0x0800000;
          break;
        case 2:
          i = str.charCodeAt(str_len-2)<<24 | str.charCodeAt(str_len-1)<<16 | 0x08000;
          break;
        case 3:
          i = str.charCodeAt(str_len-3)<<24 | str.charCodeAt(str_len-2)<<16 | str.charCodeAt(str_len-1)<<8	| 0x80;
          break;
      }

      word_array.push( i );

      while( (word_array.length % 16) != 14 ) word_array.push( 0 );

      word_array.push( str_len>>>29 );
      word_array.push( (str_len<<3)&0x0ffffffff );

      for ( blockstart=0; blockstart<word_array.length; blockstart+=16 ) {
        for( i=0; i<16; i++ ) W[i] = word_array[blockstart+i];
        for( i=16; i<=79; i++ ) W[i] = rotate_left(W[i-3] ^ W[i-8] ^ W[i-14] ^ W[i-16], 1);

        A = H0;
        B = H1;
        C = H2;
        D = H3;
        E = H4;

        for( i= 0; i<=19; i++ ) {
          temp = (rotate_left(A,5) + ((B&C) | (~B&D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
          E = D;
          D = C;
          C = rotate_left(B,30);
          B = A;
          A = temp;
        }

        for( i=20; i<=39; i++ ) {
          temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
          E = D;
          D = C;
          C = rotate_left(B,30);
          B = A;
          A = temp;
        }

        for( i=40; i<=59; i++ ) {
          temp = (rotate_left(A,5) + ((B&C) | (B&D) | (C&D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
          E = D;
          D = C;
          C = rotate_left(B,30);
          B = A;
          A = temp;
        }

        for( i=60; i<=79; i++ ) {
          temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
          E = D;
          D = C;
          C = rotate_left(B,30);
          B = A;
          A = temp;
        }

        H0 = (H0 + A) & 0x0ffffffff;
        H1 = (H1 + B) & 0x0ffffffff;
        H2 = (H2 + C) & 0x0ffffffff;
        H3 = (H3 + D) & 0x0ffffffff;
        H4 = (H4 + E) & 0x0ffffffff;
      }

      var temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
      return temp.toLowerCase();
    };
    this.utf8_encode = function ( str_data ) {	// Encodes an ISO-8859-1 string to UTF-8
                                              //
                                              // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)

      str_data = str_data.replace(/\r\n/g,"\n");
      var utftext = "";

      for (var n = 0; n < str_data.length; n++) {
        var c = str_data.charCodeAt(n);
        if (c < 128) {
          utftext += String.fromCharCode(c);
        } else if((c > 127) && (c < 2048)) {
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        } else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }
      }

      return utftext;
    };




    var self = this;
    self.init();
  };


  window._file_share = new __fileshare();
})(jQuery);