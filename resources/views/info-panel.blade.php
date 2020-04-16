
<div id="info-panel" class="new">
    <div class="close">X</div>
  <form method="post">
    @csrf
      <input type="hidden" name="id">
      <div class="title">
          <label>event</label><br>
          <input type="text" name="title">
      </div>
      <div class="error-msg">
          <div class="alert alert-danger">error</div>
      </div>
      <div class="time-picker">
          <div class="select-date">
              <span class="month"></span>/<span class="date"></span>
              <input type="hidden" name="year">
              <input type="hidden" name="month">                 <!--非user輸入的 由程式決定 -->
              <input type="hidden" name="date">
          </div>
          <div class="from">
              <label for="from">from</label><br>
              <input type="time" id="from" name="start_time">
          </div>
          <div class="to">
              <label for="to">to</label><br>
              <input type="time" id="to" name="end_time">
          </div>
      </div>
      <div class="description">
          <label>description</label><br>
          <textarea name="description" id="description"></textarea>
      </div>
  </form>
  <div class="buttons clearfix" >
          <button class="create">create</button>
          <button class="update">update</button>
          <button class="cancel">cancel</button>
          <button class="delete">delete</button>
  </div>
</div>

<script id="event-template" type="text/x-handlebars-template">
    <div class="event clearfix" data-id="@{{id}}" data-from="@{{start_time}}">
        <div class="title">@{{title}}</div>
        <div class="from">@{{start_time}}</div>
    </div>
</script>
