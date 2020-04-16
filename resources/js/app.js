/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'jquery-ui/ui/widgets/datepicker.js';

$(document).ready(function(){              //$(this)等同$(e.currentTarget)

  var source = $('#event-template').html();
  var eventTemplate = Handlebars.compile(source);

  $.each(events, function(index, event){
      var eventUI = eventTemplate(event) ;
      var date = event['date'];
      $('#calendar').find('.date-block[data-date="' + date +'"]').find('.events').append(eventUI);
  });

  var panel = {
      el: '#info-panel',
      selectedDateBlock: null,
      selectedEvent: null,
      init: function(isNew, e) {
          panel.clear();    //clear form data
          panel.updateDate(e);  //update panel的日期

          if(isNew) {
              $(panel.el).addClass('new').removeClass('update');
              panel.selectedDateBlock = $(e.currentTarget);   //將一開始點的date-block記錄(存)下來
          }
          else {
              $(panel.el).addClass('update').removeClass('new');
              panel.selectedDateBlock = $(e.currentTarget).closest('.date-block');
          }
      },
      clear: function(){
          $(panel.el).find('input').val('');
          $(panel.el).find('textarea').val('');
      },
      open: function(isNew, e) {
          panel.init(isNew, e);
          panel.hideError();
          $(panel.el).addClass('open').css({       //panel.el等同#info-panel
              top: '200px',
              left: '570px',
          }).find('.title [type]').focus();   //[]是找屬性
      },
      close: function() {
          $(panel.el).removeClass('open');
      },
      updateDate: function(e) {
          //get date from .data-block
          if ($(e.currentTarget).is('.date-block'))          //如果點到的是data-block
              var date = $(e.currentTarget).data('date');
          else
              var date = $(e.currentTarget).closest('.date-block').data('date');
          //get month from calendar
          var year = $('#calendar').data('year');
          var month = $('#calendar').data('month');

          $(panel.el).find('.month').text(month);             //panel介面顯示正確的月份和日期
          $(panel.el).find('.date').text(date);

          $(panel.el).find('[name="year"]').val(year);
          $(panel.el).find('[name="month"]').val(month);     //塞值/更新到input裡(存到form裡) 要用console.log來確認是否成功
          $(panel.el).find('[name="date"]').val(date);        //注意[]寫法
      },
      showError: function(msg) {
          $(panel.el).find('.error-msg').addClass('open')
              .find('.alert').text(msg);
      },
      hideError: function() {
          $(panel.el).find('.error-msg').removeClass('open');
      },
  };

  $('.date-block')
  .dblclick(function(e){
      panel.open(true, e);
  }).on('dblclick', '.event', function(e){
      e.stopPropagation();       //防止事件向父層傳遞 網頁的事件預設會一直往上傳播 date-block也有綁dblclick所以會觸發
      panel.open(false, e);     //開update的

      panel.selectedEvent = $(e.currentTarget);  //記錄點到的event, for delete用

      var id = $(this).data('id');
      //AJAX call -get event detail
      let actionUrl = '/events/' + id;
      $.get(actionUrl, {}, function(data, textStatus, xhr){
          $(panel.el).find('[name="id"]').val(data.id);
          $(panel.el).find('[name="title"]').val(data.title);
          $(panel.el).find('[name="start_time"]').val(data.start_time);
          $(panel.el).find('[name="end_time"]').val(data.end_time);
          $(panel.el).find('[name="description"]').val(data.description);

      }).fail(function(){
          panel.showError('Wrong event.');
      });
      //load detail back to panel
  });

  $(panel.el)
  .on('click', 'button', function(e){
     if ($(this).is('.create') || $(this).is('.update')){

         var data = $(panel.el).find('form').serialize();
         // var action = '/events';
         if ($(this).is('.create')) {
              var action = '/events';
         }
         if ($(this).is('.update')) {
              var id = $(panel.el).find('[name="id"').val();
              var action = '/events/' +id;
              data = "_method=put&" + data;   //這邊要湊出傳過去格式 Controller才讀得到
         }
      //collect data
      $.post(action, data)
          .done(function (data, textStatus, jqXHR) {
              if ($(e.currentTarget).is('.update')){
                       panel.selectedEvent.remove();  //remove event from calendar
              }

              var eventUI = eventTemplate(data);    //生成完之後要插回去 插在哪? 用selectedDateBlock記錄當初點的是誰

              panel.selectedDateBlock.find('.event').each(function(index, event){
                  var eventFromTime = $(event).data('from').split(':');
                  var newEventFromTime = data.start_time.split(':');

                  if (eventFromTime[0]>newEventFromTime[0] ||
                      (eventFromTime[0]==newEventFromTime[0] && eventFromTime[1]>newEventFromTime[1])) {
                      $(event).before(eventUI);
                      return false;  //break的意思 若要continue 寫return
                  }
              });

              if (panel.selectedDateBlock.find('.event[data-id="'+data.id+'"]').length == 0) {
                  panel.selectedDateBlock.find('.events').append(eventUI);
              }

              panel.close();
          })
          .fail(function(xhr, textStatus, errorThrown){
              panel.showError(xhr.responseText);
          });
     }

     if ($(this).is('.cancel')){
          panel.close();
     }

     if ($(this).is('.delete')){
         var result = confirm('Do you want to delete?');
         if (result){
              var id = panel.selectedEvent.data('id');  //取得id
              let actionUrl = '/events/' + id;
                   $.post(actionUrl, {_method: 'delete'})
                      .done(function(){
                          panel.selectedEvent.remove();  //remove event from calendar
                          panel.close();
                      });
         }
     }
  })
  .on('click', '.close', function(e){
      $('button.cancel').click();
  });
});
