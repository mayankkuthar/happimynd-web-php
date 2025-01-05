<style>
  .client-img {
    width: 75px;
    height: 75px;
  }
  .member-img {
    width: 200px;
    height: 200px;
  }
</style>
<x-backend-layout>
  <x-slot name="title">
    Our Client
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="p-5">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @elseif((session('error')))
        <div class="alert alert-danger alert-dismissible fade show terms__addtitle" role="alert">
          {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
      </div>
      <div class="new-section">
        @isset($carousel_data)
          <div class="col">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Clients
                  </p>
                  <div class="x_content">
                  <table id="datatable_client" class="table table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>Preference</th>
                        <th>Id</th>
                        <th>image</th>
                        <th>Title</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($carousel_data as $carousel)
                      <tr class>
                        <td class='priority_client'>{{ $loop->iteration }}</td>
                        <td class="id_client">{{ $carousel->id }}</td>
                        <td><img class="client-img" src='{{ $carousel->getImagewithS3Url("landing_page") }}' alt="{{ $carousel->title }}"></td>
                        <td>{{ $carousel->title }}</td>
                        <td>
                          <a href="{{ route('admin.staticData.editCarouselContent',['id'=>$carousel->id, 'carousel' => Request::segment(4) ]) }}"><i class="fa fa-edit"></i> </span></a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                </div>
              </div>
            </div>
          </div>
        @endisset
      </div>
    </div>
    <x-slot name="js">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
      <script src="https://cdn.ckeditor.com/ckeditor5/24.0.0/classic/ckeditor.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
    </x-slot>
  </x-slot>
</x-backend-layout>
<style type="text/css">
.ui-sortable tr {
	cursor:pointer;
}
.ui-sortable tr:hover {
	background:rgba(244,251,17,0.45);
}
</style>
