<x-backend-layout>
  <x-slot name="title">
    Bundles Detail
  </x-slot>
  <x-slot name="css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="{{ asset('assets/Backend/css/plugins/dataTables.bootstrap.min.css') }}" rel="stylesheet">
  </x-slot>
  <x-slot name="js">
      <script src="{{ asset('assets/Backend/js/plugins/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.keyTable.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.responsive.min.js') }}"></script>
      <script src="{{ asset('assets/Backend/js/plugins/dataTables.scroller.min.js') }}"></script>
  </x-slot>
  <x-slot name="content">
    <div class="right_col" role="main">
      <div class="x_content">
        <div class="x_panel">
          @if(isset($packages))
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    Packages Details
                  </p>
                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>Package Number</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Duration</th>
                          <th>Regular Price</th>
                          <th>Discount</th>
                          <th>in-offer Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($packages as $package)

                          @if($package->package)

                            @if($package->package->name=="HappiTALK")
                                @continue;
                            @endif

                            @if($package->package->name != 'HappiAPP' && $package->package->name != 'HappiBUDDY+ HappiAPP')
                              <tr>
                                <td>{{ $package->id }}</td>

                                <?php  
                                  $name = $package->package->name;
                                  if($name == 'HappiLIFE Summary Reading'){
                                    $name = 'HappiLEARN';
                                  }
                                  if($name == 'HappiLIFE Screening'){
                                    $name = 'HappiLIFE Awareness Tool';
                                  }
                                ?>
                                
                                <td>{{ $name }}</td>
                                <td>{{ $package->package->description }}</td>
                                <td>{{ $package->duration->name }}</td>
                                <td>{{ $package->price }}&nbsp;&nbsp<button type="button" onclick="OpenBundlePriceUpdateModal({{ $package->id }},{{ $package->price }},{{ $package->offer->discount }},{{ $package->offer->price }})" style="border:0px;"><i class="fa fa-edit" style="font-size:16px;color:red;"></i></button></td>
                                @if($package->offer)
                                <td> {{ $package->offer->discount }}</td>
                                <td>{{ $package->offer->price }}</td>
                                @else
                                <td>No Offer</td>
                                <td>{{ $package->price }}</td>
                                @endif
                              </tr>
                            @endif
                            
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
<div class="modal" id="UpdatePrice" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title modal-title-1" id="exampleModalLabel">Update Price</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="form-group">
                 <label for="reg_price">Enter Regular Price</label>
                 <input type="number" class="form-control" name="reg_price" oninput="validateInput(reg_price,discount,inofferprice)" id="reg_price" min="0" value="" required/>
                 <div id="reg_price_vali" style="color:red;"></div>
            </div>
            <div class="form-group">
                 <label for="discount">Enter Discount</label>
                 <input type="number" class="form-control" name="discount" min="0" max="100" oninput="validateInput(reg_price,discount,inofferprice)" id="discount" value="" required/>
                 <div id="discount_vali" style="color:red;"></div>
            </div>
            <div class="form-group">
                 <label for="in-offer-price">In-Offer-Price</label>
                 <input type="number" class="form-control" name="inofferprice" oninput="validateInput(reg_price,discount,inofferprice)" id="inofferprice" value="" required>
                 <div id="inoffer_vali" style="color:red;"></div>
            </div>

          </div>
          <div class="update-price">
            <button type="button" data-dismiss="modal" id="update-price-button" class="btn btn-primary" style="margin-left:15px;" onclick="updatePrice(reg_price,discount,inofferprice)">Update Price</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
  function OpenBundlePriceUpdateModal(package_id,reg_price,discount,inofferprice){
    $("#UpdatePrice").modal("show");
    $("#reg_price").attr("value",reg_price);
    $("#discount").attr("value",discount);
    $("#inofferprice").attr("value",inofferprice);
    sessionStorage.setItem("package_id",package_id);
  }
  function updatePrice(regular_price,discount,inofferprice){
    var $package_id=sessionStorage.getItem("package_id");
    var $regular_price=regular_price.value;
    var $discount=discount.value;
    var $inofferprice=inofferprice.value;
    if ($regular_price <0 ||$regular_price=='') {
      alert("Regular Price must be filled and greater than 0");
      return false;
    }
    if ($discount== ""||$discount<0||$discount>100) {
      alert("Discount must be filled and between 0 and 100");
      return false;
    }
    if ($inofferprice <0 ||$inofferprice=='') {
      alert("In Offer Price must be filled and greater than or equal to 0");
      return false;
    }

    $.ajax({
      type: "post",
      url:'{{url("/")}}/admin/bundle-update',
      data:{
        'id':$package_id,
        'regular_price':$regular_price,
        'discount':$discount,
        'inoffer_price':$inofferprice
      },
      dataType: "json",
      success: function(result) {
        if(result.message.notify.type=="success"){
          location.reload();
        }
      }
    })
  }
  function validateInput(regular_price,discount,inofferprice){
    var $regular_price=regular_price.value;
    var $discount=discount.value;
    var $inofferprice=inofferprice.value;
    if ($regular_price <0 ||$regular_price=='') {
      $("#reg_price_vali").html("Regular Price must be filled and greater than or equal to 0");
    }
    else{
      $("#reg_price_vali").html("");
    }
    if ($discount== ""||$discount<0||$discount>100) {
      $("#discount_vali").html("Discount must be filled and between 0 and 100 including both");
    }
    else{
      $("#discount_vali").html("");
    }
    if ($inofferprice <0 ||$inofferprice=='') {
      $("#inoffer_vali").html("In Offer Price must be filled and greater than or equal to 0");
    }
    else{
      $("#inoffer_vali").html("");
    }

  }
  </script>
