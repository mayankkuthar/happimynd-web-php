<style>
  .file-dis {
    display: block;
    margin-top: 20px;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    edit landing cms
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    
    <div class="right_col" role="main">
      <br />
      <h3 align="center">edit landing cms</h3>
      <br />
       @isset($landing_buttons)
       @foreach($landing_buttons as $landing_button)
        <form method="POST" action="{{ route('admin.staticData.saveEditableLandingButton') }}" id="term", enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="id" value="{{ $landing_button->id }}">
            <h1 class="terms__addtitle">{{ $landing_button->button_name }}</h1>
            <input class="terms__input_field" type="text" name="button_content" value="{{ $landing_button->button_content }}">
            <br>
            <button type="submit" class="btn btn-primary terms__update__btn">Update</button>
          </form>
        @endforeach
      @endisset
  </x-slot>
</x-backend-layout>
