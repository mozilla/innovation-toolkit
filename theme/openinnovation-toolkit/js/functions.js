/**
 * Provides helper functions to enhance the theme experience.
 */

var $ = jQuery;
var emailfilter = /^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i;
var hash;

var windowWidth;
var containerWidth;

var scrollSpeed = 50; // speed in milliseconds
var current = 0; // set the default position
var scrollOffset = -80;

var siteURL;
var jsonURL;
var ajaxURL;
var ajaxInProcess = false;
var currentPage = 1;

siteURL = get_hostname(document.location.href);
jsonURL = siteURL + '/json';
ajaxURL = siteURL + '/ajax';


var _dntStatus = navigator.doNotTrack || navigator.msDoNotTrack;
var fxMatch = navigator.userAgent.match(/Firefox\/(\d+)/);
var ie10Match = navigator.userAgent.match(/MSIE 10/i);
var w8Match = navigator.appVersion.match(/Windows NT 6.2/);


if (fxMatch && Number(fxMatch[1]) < 32) {
 // Can't say for sure if it is 1 or 0, due to Fx bug 887703
 _dntStatus = 'Unspecified_0';
} else if (ie10Match && w8Match) {
 // IE10 on Windows 8 does not Enable based on user intention
 _dntStatus = 'Unspecified_1';
} else {
 _dntStatus = { '0': 'Disabled', '1': 'Enabled' }[_dntStatus] || 'Unspecified_3';
}
console.log(_dntStatus);
if (_dntStatus !== 'Enabled'){
  var captchaContainer = null;
  var loadCaptcha = function() {
    if(jQuery('#captcha_container').length > 0) {
      var siteKey = jQuery('#captcha_container').data("sitekey");
      captchaContainer = grecaptcha.render('captcha_container', {
        'sitekey' : siteKey,
        'callback' : function(response) {
          console.log(response);
        }
      });
    }
  };
}

( function( $ ) {
  $.fn.equalizeHeights = function(){
    return this.height( Math.max.apply(this, $(this).map(function(i,e){return $(e).height()}).get() ) )
  }
  
  function scrollToElement(selector, time, verticalOffset, callback) {
    time = typeof(time) != 'undefined' ? time : 500;
    verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
    element = $(selector);
    offset = element.offset();
    offsetTop = offset.top + verticalOffset;
    t = ($(window).scrollTop() - offsetTop);
    if (t <= 0) t *= -1
    t = parseInt(t * .5);
    if (t < time) t=time;
    if (t > 1500) t=1500;
    $('html, body').animate({
      scrollTop: offsetTop
    }, t, 'easeInOutCirc', callback);
  }
  
  function showHideTopButton() {
    $btnTop = $('#goTop')
    if ($(window).scrollTop() > 300) { 
			$btnTop.fadeIn(); 
		} else { 
			$btnTop.fadeOut(); 
		}
  }
  
  function verticalCenter() {
    if($('.vertical-center').length > 0) {
      $('.vertical-center').each(function() {
        var $item = $(this);
        var parentClass = $item.attr('data-parent');
        var $parent = $item.parents($('.'+parentClass));
        if($parent) {
          itemPadding = ($parent.height() - $item.height())/2;
          $item.css({'padding-top':itemPadding+'px', 'padding-bottom':itemPadding+'px'});
        }
      });
    }
  }
  
  function configureExternalLinkTarget() {
    $('a').not('[href*="mailto:"]').each(function () {
      var isInternalLink = new RegExp('/' + window.location.host + '/');
      if ( ! isInternalLink.test(this.href) ) {
        $(this).attr('target', '_blank');
      }
    });
  }
  
  
  function loadWindow() {
    configureExternalLinkTarget();
    $('.section-methods').imagesLoaded().done( function( instance ) {
      $('.method-card').equalizeHeights();
      $('#methods-content, .method-filters').equalizeHeights();
    });
  }
  
  function resizeWindow() {
    $('.method-card').css('height', 'auto').equalizeHeights();
    $('#methods-content, .method-filters').css('height', 'auto').equalizeHeights();
  }
  
  function scrollWindow() {
    showHideTopButton();
  }


  function showRequest(formData, jqForm, options) {
    var isValid = true;
    var $alert = $('.ajax-msg', jqForm);
    $alert.removeClass('alert-success alert-error');
    $alert.addClass('hidden');
    
    $('.has-error', jqForm).removeClass('has-error');
    
    /* Global validation check for required fields and email */
    $('.field-input.required', jqForm).each(function() {
      if($(this).val().replace(/^\s*|\s*$/g,"")=="") {
        $(this).addClass('has-error');
        isValid=false;
      }
    });
    if(!(isValid)) err_msg = "Please enter all the required fields.";
    
    if(isValid) {
      $('.field-input.email', jqForm).each(function() {
        if(isValid && emailfilter.test($(this).val())==false) {
          $(this).addClass('has-error');
          isValid = false;
        }
      });
      if(!(isValid)) err_msg = "Please enter valid email id.";
    }
    
    if(isValid) {
      $('input[type="checkbox"].required', jqForm).each(function() {
        if(!($(this).prop("checked"))) {
          isValid = false;
        }
      });
      if(!(isValid)) err_msg = "Please agree to the terms of the submission.";
    }
    
    if(!(isValid)) {
      $alert.empty().append("<strong>ERROR:</strong> "+err_msg);
      $alert.addClass('alert-error');
      $alert.removeClass('hidden');
      scrollToElement($alert, 400, -100);
    } else {
      $('input[type="submit"]', jqForm).addClass('disabled');
      $('body').showLoading();
    }
    return isValid;
  }


  function showResponse(responseText, statusText, xhr, $form){
    $form.hideLoading();
    var $alert = $('.ajax-msg', $form);
    scrollToElement($('body'), 400, 0);
    // alert (JSON.stringify(responseText));
    
    if (statusText===" success" || statusText==="success"){
      if (parseInt(responseText.success) === 1) {
        $form.trigger("reset");
        $alert.empty().html(responseText.message).addClass('alert-success').fadeIn();
        $('.form-fields').hide();
      } else {
        $alert.empty().html(responseText.message).addClass('alert-error');
      }
      $alert.removeClass('hidden');
      $('body').hideLoading();
      return false;
    } 
  }
  
  function extractDomain(url) {
    var domain;
    alert(url.indexOf("://"));
    //find & remove protocol (http, ftp, etc.) and get domain
    if (url.indexOf("://") > -1) {
      alert(url);
      domain = url.split('/')[2];
    }
    else {
      domain = url.split('/')[0];
    }
    
    //find & remove port number
    domain = domain.split(':')[0];
    return domain;
  }
  

  var body    = $( 'body' ), _window = $( window );
  ( function() {
    loadWindow();
    $(window).resize(resizeWindow);
    $(window).scroll(scrollWindow);
//    $('#site-navigation').toggleClass('open-menu');
    
    // Menu Toggle on small screen sizes
    $('.menu-toggle').click(function(e) {
      e.preventDefault();
      $('.pushmenu-push').toggleClass('pushmenu-push-toleft');
      $(this).toggleClass('menu-toggle--active');
      $('.site-navigation').toggleClass('open--menu');
    });
    
    $(document).bind('click touchstart', function(e) {
      var $clicked = $(e.target);
      var $nav = $('.site-navigation');
      if($nav.hasClass('open--menu')) {
        if ($clicked.parents('.site-navigation').length > 0 || 
          $clicked.attr('class')==='site-navigation' || 
          $clicked.parents('.menu-toggle').length > 0 || 
          $clicked.hasClass('menu-toggle'))  {
  //          Do nothing
        } else {
          $('.pushmenu-push').toggleClass('pushmenu-push-toleft');
          $nav.toggleClass('open--menu');
          $('.menu-toggle').toggleClass('menu-toggle--active');
        }
      }

      if($('.method-sidebar').length > 0) {
        if($('.method-sidebar').hasClass('dropdown--open')) {
          if ($clicked.parents('.method-sidebar').length > 0 || 
            $clicked.attr('class')==='method-sidebar')  {
    //          Do nothing
          } else {
            $('.method-sidebar ul').removeClass('dropdown--show');
            $('.method-sidebar ul').addClass('dropdown--hide');
            $('.method-sidebar').removeClass('dropdown--open');
          }
        }
      }


      $('.method-filters .dropdown--open').each(function() {
        if ($clicked.parents('.filter-wrapper').length > 0 || 
          $clicked.attr('class')==='filter-wrapper')  {
  //          Do nothing
        } else {
          $('ul', $(this)).removeClass('dropdown--show');
          $('ul', $(this)).addClass('dropdown--hide');
          $(this).removeClass('dropdown--open');
        }
      });


      if($('.questions-dropdown').length > 0) {
        if($('.questions-dropdown').hasClass('dropdown--open')) {
          if ($clicked.parents('.questions-dropdown').length > 0 || 
            $clicked.attr('class')==='questions-dropdown')  {
    //          Do nothing
          } else {
            $('.questions-dropdown .dropdown-list').stop().fadeOut();
            $('.questions-dropdown').removeClass('dropdown--open');
          }
        }
      }
    });
    
    // Go to top button
    if($('#goTop').length > 0) {
      $('#goTop').click(function(e) {
        e.preventDefault();
        scrollToElement($('body'), 600, -55);
      });
    }
    
    
    $('[data-toggle="tooltip"]').tooltip({
//      trigger: 'click',
      title: $('.tooltip-text').html(),
      html: true,
      placement: 'top'
    });
    
    
    $('a[href="#toolkit-submission-terms"]').click(function(e) {
      $("#modalSubmissionTerms").modal('show');
    })
    


    /* MEDTHOS */
    if($('.method-filters').length > 0) {
      $('.method-filters').each(function() {
        var $container = $(this);

        $('.filter-wrapper', $container).each(function() {
          var $filterContainer = $(this);
          var $toggle = $('.filter-toggle', $filterContainer);
          var $list = $('ul', $filterContainer);
          var $items = $('a', $list);
          var $itemHeader = $('.filter-header', $filterContainer)

          $toggle.click(function(e) {
            e.preventDefault();
            toggleMethodFilters($filterContainer, $list);
          });

          $itemHeader.click(function(e) {
            e.preventDefault();
            toggleMethodFilters($filterContainer, $list);
          });

          $('a', $list).click(function(e) {
            e.preventDefault();
            var $item = $(this);
            var $itemWrapper = $item.parent('li');
            if($itemWrapper.hasClass('active')) return;
            $('.active', $filterContainer).removeClass('active');
            $itemWrapper.addClass('active');

            var idx = $items.index($item);
            if(idx===0) {
              $('.filter-header', $filterContainer).empty().append($('h4', $filterContainer).text());
            } else {
              $('.filter-header', $filterContainer).empty().append($item.text());
            }

            currentPage = 1;
            $('#search_text').val('');
            getMethodsJsonData();

            if($(window).width() <= 767) {
              toggleMethodFilters($filterContainer, $list);  
            }
          });
        });
      });

      if($('.load-more-button').length > 0) {
        $('.load-more-button').click(function(e){
          e.preventDefault();
          currentPage++;
          getMethodsJsonData();
        });
      }
    }

    function getMethodsFilters() {
      var process_name = $('.filter-process .active a').attr('href').replace(/^.*#/, '');
      var difficulty_level = $('.filter-difficulty .active a').attr('href').replace(/^.*#/, '');
      var duration = $('.filter-duration .active a').attr('href').replace(/^.*#/, '');
      var outcomes = $('.filter-outcomes .active a').attr('href').replace(/^.*#/, '');
      var search_text = $('#search_text').val();

      var filterData = '&pageid='+currentPage+'&process='+process_name+'&difficulty='+difficulty_level+'&duration='+duration+'&outcomes='+outcomes+'&search_text='+search_text;
      return filterData;
    }

    function getMethodsJsonData() {
      var $methodsWrapper = $('.methods-wrapper');
      var filterData = getMethodsFilters();

      if(currentPage==1) {
        $methodsWrapper.empty();
      }

      $.ajax({
        url: jsonURL+'/methods/?'+filterData,
        type: 'GET',
        success: function(result) {
          var data = result;
          if(data){
            if(data.load_more) {
              $('.load-more-button').show();
            } else {
              $('.load-more-button').hide();
            }

            if(data.items) {
              $.each(data.items, function(i, item) {
                var $cardWrapper = $('<div class="method-card-wrapper">');
                var $methodCard = $('<a class="method-card '+item.process+'" href="'+item.post_link+'">');
                var $methodHeader = $('<header class="item-header">');
                var $methodContent = $('<div class="item-content">');
                var $methodFooter = $('<footer class="item-footer">');

                $methodHeader.append('<img src="'+item.image_url+'" class="img-fluid" /><i class="item-icon" title=""></i>');
                $methodContent.append('<h3>'+item.post_title+'</h3>'+item.post_excerpt+'');
                $methodFooter.append('<div class="difficulty-level '+item.difficulty_level+'">'+item.difficulty_level+'</div><div class="duration">'+item.duration+'</div>');
                
                $methodCard.append($methodHeader);
                $methodCard.append($methodContent);
                $methodCard.append($methodFooter);
                
                $cardWrapper.append($methodCard);
                $methodsWrapper.append($cardWrapper);
              });
              
              $methodsWrapper.imagesLoaded().done( function( instance ) {
                $('.method-card').css('height', 'auto').equalizeHeights();
                $('#methods-content, .method-filters').css('height', 'auto').equalizeHeights();
              });
              
            } else {
              $methodsWrapper.append('<p class="no-results">Woops, look like we don&rsquo;t have anything listed for filters slected.</p>');
              $('.load-more-button').hide();
            }
          }
        }, error: function(e) {
          alert("Error Occured");
          // $ajaxLoading.addClass('hidden');
          // $newsListWrapper.removeClass('ajax-loading');
          // $newsList.empty().append('Error while fetching results.');
        }
      });
          
      ajaxInProcess = false;
      


    }

    function toggleMethodFilters($container, $list) {
      $('.method-filters .dropdown--open').not($container).each(function() {
        $('ul', $(this)).removeClass('dropdown--show');
        $('ul', $(this)).addClass('dropdown--hide');
        $(this).removeClass('dropdown--open');
      });

      if($container.hasClass('dropdown--open')) {
        $list.removeClass('dropdown--show');
        $list.addClass('dropdown--hide');
        $container.removeClass('dropdown--open');
      } else {
        $list.removeClass('dropdown--hide');
        $list.addClass('dropdown--show');
        $container.addClass('dropdown--open');
      }
    }


    /* INDIVIDUAL MEDTHOS */
    if($('[data-bgimg]').length > 0) {
      $('[data-bgimg]').each(function() {
        bgimg = $(this).attr('data-bgimg');
        $(this).css({
          'background-image': 'url('+bgimg+')'
        });
      });
    }
    if($('[data-bgcolor]').length > 0) {
      $('[data-bgcolor]').each(function() {
        bgcolor = $(this).attr('data-bgcolor');
        $(this).css({
          'background-color': bgcolor
        });
      });
    }
    
    if($('.method-sidebar').length > 0) {
      $('.method-sidebar').each(function() {
        var $container = $(this);
        var $toggle = $('.sidebar-toggle', $container);
        var $list = $('ul', $container);
        var $items = $('a', $list);
        
        $toggle.click(function(e) {
          e.preventDefault();
          toggleMethodMenues($container, $list);
        });
        
        $('a', $container).click(function(e) {
          e.preventDefault();
          var $item = $(this);
          var sectionId = $item.attr('href');
          var idx = $items.index($item);
          $('.active', $container).not($item).removeClass('active');
          $item.addClass('active');
          $toggle.text($item.text());
          
          if(idx===0) {
            offset = -30;
          } else {
            offset= 0;
          }
          
          if($(window).width() <= 767) {
            scrollToElement($(sectionId), 600, (-140 + offset));
            toggleMethodMenues($container, $list);
          } else {
            scrollToElement($(sectionId), 600, (-80 + offset));
          }
        });
      });
    }
    
    function toggleMethodMenues($container, $list) {
      if($container.hasClass('dropdown--open')) {
        $list.removeClass('dropdown--show');
        $list.addClass('dropdown--hide');
        $container.removeClass('dropdown--open');
      } else {
        $list.removeClass('dropdown--hide');
        $list.addClass('dropdown--show');
        $container.addClass('dropdown--open');
      }
    }
    
    
    
    /* HOME PROCESSES */
    if($('#section-processes').length > 0) {
      $('#section-processes').each(function() {
        var $container = $(this);
        var $pager = $('.pager-links', $container);
        var $pagerLinks = $('a', $pager);
        
        $('.next', $container).click(function(e) {
          e.preventDefault();
          var $activePage = $('a.active', $pager);
          var idx = $pagerLinks.index($activePage);
          var nextIdx = 0;
          if(idx<0) idx=0;
          if(idx==2) {
            nextIdx = 0;
          } else {
            nextIdx = idx+1;
          }
          $('a:eq('+nextIdx+')', $pager).trigger('click');
        });
        
        $('.prev', $container).click(function(e) {
          e.preventDefault();
          var $activePage = $('a.active', $pager);
          var idx = $pagerLinks.index($activePage);
          var nextIdx = 0;
          if(idx<0) idx=0;
          if(idx==0) {
            nextIdx = 2;
          } else {
            nextIdx = idx-1;
          }
          $('a:eq('+nextIdx+')', $pager).trigger('click');
        });
      });
    }
    
    
    
    if($('.switch-process').length > 0) {
      $('.switch-process').each(function() {
        var $container = $('#section-processes');
        var $contentWrapper = $('.process-illustration-content', $container);
        var $list = $(this);
        
        $('a', $list).each(function() {
          var $item = $(this);
          
          $item.click(function(e) {
            e.preventDefault();
            className = $item.attr('data-class');
            if($container.hasClass(className)) return false;
            $container.attr('class', className);
            $('.active', $container).removeClass('active');
            $('a[data-class="'+className+'"]', $container).addClass('active');
            $('.'+className, $contentWrapper).addClass('active');
          });
        });
      });
    }

    /* HOME MESSAGE SLIDER */
    if($('.section-messages-slider').length > 0) {
      $('.section-messages-slider ul').bxSlider({
        auto: true,
        controls: true,
        pager: false,
        pause: 7000,
        speed: 800
      });
    }

    
    /* PROCESS SLIDER */
    if($('#process-slider').length > 0) {
      $('#process-slider').each(function() {
        $('#process-slider > ul').bxSlider({
          startSlide: 0,
          auto: false,
          controls: true,
          adaptiveHeight: true,
          pager: false,
          pause: 5000,
          speed: 800,
        });
      });
      
    }
    
//    history.pushState(null, null, "http://localhost:8888/openinnovation-toolkit/ideate/");
    /* QUESTIONS */
    function toggleQuestionsDropdown($container, $list) {
      if($container.hasClass('dropdown--open')) {
        $list.stop().fadeOut();
        $container.removeClass('dropdown--open');
      } else {
        $list.stop().fadeIn();
        $container.addClass('dropdown--open');
      }
    }
    
    if($('.questions-dropdown').length > 0) {
      if(window.location.hash) {
        hash = window.location.hash.replace(/^.*#/, '');
        
        $el = $('a[data-id="'+hash+'"]').trigger('click');
        if($el.length > 0) {
          $el.addClass('selected');
          $target = $('.question-item-'+hash);
          if($(window).width() < 768) {
            scrollToElement($target, 600, -140);
          } else {
            scrollToElement($target, 600, -280);
          }
        }
      }
      
      $('.questions-dropdown').each(function(e) {
        var $container = $(this);
        var $header = $('.dropdown-header', $container);
        var $toggleButton = $('.dropdown-toggle', $container);
        var $list = $('.dropdown-list', $container);
        
        $header.click(function(e) {
          e.preventDefault();
          toggleQuestionsDropdown($container, $list);
        });
        
        $toggleButton.click(function(e) {
          e.preventDefault();
          toggleQuestionsDropdown($container, $list);
        });
        
        if($list.parents('.questions-header').length > 0) {        
          $('a', $list).each(function(e) {
            var $item = $(this);
            $item.click(function(e) {
              e.preventDefault();
              if($item.hasClass('selected')) {
                toggleQuestionsDropdown($container, $list);
              } else {
                $('.selected', $list).removeClass('selected');
                $item.addClass('selected');
                toggleQuestionsDropdown($container, $list);

                quesId = $item.attr('data-id');
                if($(window).width() < 768) {
                  scrollToElement($('.question-item-'+quesId), 600, -140);
                } else {
                  scrollToElement($('.question-item-'+quesId), 600, -280);
                }
                
              }
            });
          });
        }
      });
    }
    
    
    /* GLOSSARY */
    if($('.glossary-letters').length > 0) {
      $('.glossary-letters').each(function() {
        var $container = $(this);
        var $list = $('.glossary-terms');
        $('a', $container).click(function(e) {
          e.preventDefault();
          var $item = $(this);
          $target = $('.'+$item.attr('href').replace(/^.*#/, '')+':eq(0)', $list);
          scrollToElement($target, 600, -220);
        });
      })
    }


    /* CONTRIBUTION */
    if($('#txt_attachment').length > 0 ){
      //Select File
      $('#txt_attachment').change(function(e){
        $item = $(this);
        var fileName = e.target.files[0].name;
        if(fileName!==''){
          $('#txt_filename').val(fileName);
        }
      });
    }

    if($('.ajax-form').length > 0) {
      $('.ajax-form').each(function() {
        var options = {
          beforeSubmit:  showRequest, 
          success: showResponse
        };
        $(this).submit(function() {
          $(this).ajaxSubmit(options);
          return false;
        });
      });
    }
    
    
    
    if($(".custom-dropdown").length > 0){
      $(".custom-dropdown").each(function () {
        var $this = $(this), numberOfOptions = $(this).children('option').length;
        var selected = $this.find("option[selected]");
        var placeholder = $this.attr('placeholder');
          
        // Hides the select element
        $this.addClass('s-hidden');

        // Wrap the select element in a div
        $this.wrap('<div class="select"></div>');

        // Insert a styled div to sit over the top of the hidden select element
        $this.after('<div class="styledSelect"></div>');

         // Cache the styled div
        var $styledSelect = $this.next('div.styledSelect');

        // Show the first select option in the styled div
        if(selected.length > 0) {
          $styledSelect.text(selected.text());//$this.children('option').eq(0).text());
        } else if (placeholder && placeholder.length > 0){
          $styledSelect.text($this.attr('placeholder'));
        } else {
          $styledSelect.text($this.children('option').eq(0).text());
        }

        // Insert an unordered list after the styled div and also cache the list
        var $list = $('<ul />', {'class': 'options'}).insertAfter($styledSelect);

        // Insert a list item into the unordered list for each select option
        for (var i = 0; i < numberOfOptions; i++) {
          $('<li />', {text: $this.children('option').eq(i).text(), rel: $this.children('option').eq(i).val()}).appendTo($list);
        }

        // Cache the list items
        var $listItems = $list.children('li');

        // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
        $styledSelect.click(function (e) {
          e.stopPropagation();
          var $item = $(this);
          var $list = $(this).next('ul.options').hide();
          
          if($item.hasClass('active')) {
            $item.removeClass('active');
            $list.stop().hide();
          } else {
            $item.addClass('active');
            $list.stop().show();
          }
        });

        // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
        // Updates the select element to have the value of the equivalent option
        $listItems.click(function (e) {
          e.stopPropagation();
          $styledSelect.text($(this).text()).removeClass('active');
          $this.val($(this).attr('rel'));
          $list.hide();
        });

        // Hides the unordered list when clicking outside of it
        $(document).click(function () {
           $styledSelect.removeClass('active');
           $list.hide();
        });
      });
    }
  } )();
} )( jQuery );


function get_hostname(url) {
  var m = ((url||'')+'').match(/^https?:\/\/[^/]+/);
  return m ? m[0] : null;
}