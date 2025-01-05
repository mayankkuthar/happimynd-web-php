@php
use Illuminate\Support\Str;
@endphp
<x-backend-layout>
   <x-slot name="title">
    Create User
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
          <form method="POST" action="{{ route('admin.EducationServices.post') }}" id="term" enctype="multipart/form-data">
          @csrf
            <input type="hidden" name="id" value="">
            <h1 class="terms__addtitle compulsory">Title:</h1>
            <input class="terms__input_field @error('title') is-invalid @enderror" type="text" name="title" value="{{ old('title') ?? '' }}" style="height: 35px;">
            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="mb-3">
              <h1 class="terms__addtitle compulsory">Author:</h1>
                <select name="author" id="author-select" class="">
                  <option value="0">Select Author</option>
                  @foreach ($authors as $author)
                  <option value="{{ $author->id }}" {{ old('author') == $author->id ? 'selected':''}}>{{ $author->name }}</option>
                  @endforeach
                </select>
                <button type="button" class="btn btn-info" id="add-author" data-toggle="modal" data-target="#authorInput">Add</button>
              @error('author')
                  <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="" class="font-bold compulsory">Thumbnail :</label>
              <input type="hidden" name="image" value="">
              <input type="file" name="thumbnail" id="" placeholder = "thumbnail" class="@error('thumbnail') is-invalid @enderror" value="{{ old('thumbnail') ??  ''}}">
              @error('thumbnail')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <span class="text-danger mb-4 py-4">Image size 200 X 200</span>
            <div class="">
              <h1 class="terms__addtitle compulsory">Price</h1>
              <div class="form-group @error('price') is-invalid @enderror">
                <input type="text"  name="price" value="{{ old('price') ?? '' }}" style="height: 25px;" class="@error('price') is-invalid @enderror">
              </div>
              @error('price')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <div class="">
              <h1 class="terms__addtitle compulsory">Discounted Price</h1>
              <div class="form-group @error('discounted_price') is-invalid @enderror">
                <input type="text" name="discounted_price" value="{{ old('discounted_price') ?? '' }}" style="height: 25px;" class="@error('discounted_price') is-invalid @enderror" >
              </div>
              @error('discounted_price')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <div class="">
              <h1 class="terms__addtitle compulsory">Rating</h1>
              <div class="form-group @error('rating') is-invalid @enderror">
                <input min= 1 max=5 type="number" step="any" name="rating" value="{{ old('rating') ?? '' }}" style="height: 25px;" class="@error('rating') is-invalid @enderror" >
              </div>
              @error('rating')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <div class="">
              <h1 class="terms__addtitle compulsory">Downloads</h1>
              <div class="form-group @error('downloads') is-invalid @enderror">
                <input  type="text" name="downloads" value="{{ old('downloads') ?? '' }}" style="height: 25px;" class="@error('downloads') is-invalid @enderror" >
              </div>
              @error('downloads')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            <div class="">
              <h1 class="terms__addtitle">Link</h1>
              <div class="form-group @error('buy_link') is-invalid @enderror">
                <input type="text" placeholder = "https://happimynd.com" name="buy_link" value="{{ old('buy_link') ?? '' }}" style="height: 25px; width:370px;" class="@error('buy_link') is-invalid @enderror" >
                Note* if not empty user will be redirected to this link
              </div>
              @error('buy_link')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            @if(isset($serviceTypes))
            <div class="">
              <h1 class="terms__addtitle compulsory">Service Type</h1>
              <div class="form-group @error('service_type') is-invalid @enderror">
                @foreach ($serviceTypes as $serviceType)
                  <input type="radio" id = "{{ $serviceType->id  }}" value="{{ $serviceType->id }} " name="service_type" {{ old('service_type') == $serviceType->id ? 'checked':'' }}>
                  <label for="{{ $serviceType->id  }}" class="form-check-label mr-3">{{ $serviceType->name }}</label>
                @endforeach
              </div>
              @error('service_type')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
            @endif
            <div class="">
              <h1 class="terms__addtitle compulsory">Publish</h1>
              <div class="form-group @error('publish_status') is-invalid @enderror">
                  <input type="radio" value=1 id = "yes" name="publish_status" {{ old('publish_status') == 1 ? 'checked':'' }}>
                  <label for="yes" class="form-check-label mr-3">Yes</label>
                  <input type="radio" id = "no" value=0 name="publish_status" {{ old('publish_status') == 0 ? 'checked':'' }}>
                  <label for="no" class="form-check-label mr-3">No</label>
              </div>
              @error('publish_status')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary terms__update__btn">Save</button>
            </div>
          </form>

      <div class="new-section">
        @isset($services)
        {{-- @foreach($services as $service) --}}
          <div class="col">

            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                      Services
                  </p>
                  <div class="x_content">
                  <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Service Type</th>
                        <th>Thumbnail</th>
                        <th>Published</th>
                        <th>Price</th>
                        <th>Discounted Price</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($services as $service_list)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $service_list->title }}</td>
                        <td>{{ $service_list->educationService->author->name }}</td>
                        <td>{{ $service_list->type->name }}</td>
                        <td>{{ $service_list->thumbnail }}</td>
                        <td>{{ $service_list->status() }}</td>
                        <td>{{ $service_list->price }}</td>
                        <td>{{ $service_list->educationService->discounted_price }}</td>
                        <td>
                            <a href="{{ route('admin.educationalservices.edit',['slug'=>$service_list->slug]) }}"><i class="fa fa-edit"></i> </span></a>
                            <a href="{{ route('admin.educationalservices.delete',['id'=>$service_list->id]) }}"><i class="fa fa-trash-o"></i></a>
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
        {{-- @endforeach --}}
        @endisset
      </div>

    </div>

    <x-slot name="js">
      <script src="{{ asset('assets/Frontend/js/services.js') }}"></script>
    </x-slot>
  </x-slot>
</x-backend-layout>

<div class="modal fade" id="authorInput" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Author</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <div class="col-md-4">Name</div>
          <div class="colmd-6">
            <input type="text" name="name" id="name">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addAuthor();">Save changes</button>
      </div>
    </div>
  </div>
</div>

