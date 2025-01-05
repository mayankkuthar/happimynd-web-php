<!-- Css -->
<style>
select.select2_multiple.form-control {
    height: 300px!important;
}
</style>


<x-backend-layout>
  <x-slot name="title">
    Map Psychologist To Organization
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Map Psychologist To Organization</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                @if(Session::has("error"))
		              <div class="alert alert-danger">{{Session::get("error")}}</div>
		              @endif
		              @if(Session::has("success"))
		              <div class="alert alert-success">{{Session::get("success")}}</div>
		              @endif
		              @if ($errors->any())
		              <div class="alert alert-danger">
		                    @foreach ($errors->all() as $error)
		                      {{$error}}
		                    @endforeach
		              </div>
		              @endif
                <br>
                <form class="form-horizontal form-label-left"  enctype="multipart/form-data" method="POST" >
                  @csrf
                  <div class="form-group row ">
                    <label class="control-label col-md-3 col-sm-3 ">Organization Name</label>
                    <div class="col-md-9 col-sm-9 ">
                      <input type="text" class="form-control" placeholder="first name" name="first_name" value="{{$org_details->name}}" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="control-label col-md-3 col-sm-3 ">Select Psychologist</label>
                    <div class="col-md-9 col-sm-9 ">
                      <select class="select2_multiple form-control" multiple="multiple" name="psychologist_id[]" required>
                        @foreach($psychologist_list as $single_psy)
                          <option value="{{ $single_psy->id }}"  @if(in_array($single_psy->id , $all_mapped_psy_ids_in_array)) selected @endif>{{ $single_psy->username }}
                        @endforeach
                      </select>
                    </div>
                  </div>
                  
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-9 col-sm-9  offset-md-3">
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>


            <div class="x_title">
                <h2>Mapped Psychologist List</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>S.NO</th> 
                    <th>Profile Image</th> 
                    <th>Psychologist Name</th> 
                    <th style="text-align: center;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php  
                    $i=0;
                  ?>
                  @foreach($maped_pay_list as $row)
                  <tr>
                    <td>{{ ++$i }}</td> 
                    <td><img src="{{ $row->psychologist->s3ImageUrl }}" class="img-thumbnail" alt="Avatar" style="height: 80px;width: 80px;border-radius: 90px;">
                    </td>
                    <td>{{ $row->psychologist->first_name }} {{ $row->psychologist->last_name }}</td> 
                    <td style="    text-align: center;">
                      <a href="{{url('admin/un-map-psy-to-org').'/'.$row->id}}/" class="btn btn-primary">Un-Map Psychologist</a>
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
  </x-slot>
</x-backend-layout>
