@extends('layouts.app')

@section('title', 'Happimynd | Buy Bundles')

@section('content')
    @include('Frontend.includes.dashboard.header')
    @include('Frontend.includes.popups.reportreview.report_free')
    <div class="container">
      <div class="x_content">
        <div class="x_panel">
          @if(isset($packages))            
            <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">
                  <p class="text-muted font-13 m-b-30">
                    Bundles Details
                  </p>
                  <div class="x_content">
                    <table id="datatable-button" class="table table-striped table-bordered" style="width:100%">
                    <form action="{{ route('payment.orderBundle') }}" method="post">
                      @csrf
                      <thead>
                        <tr>
                          <th>Select</th>
                          <th>Package Number</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Duration</th>
                          <th>Regular Price</th>
                          <th>Discount</th>
                          <th>Special Inaugral Price</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach($packages as $package)
                        <tr>
                          <td>
                          <input type="checkbox" name="package-{{$package->id}}" id="{{$loop->iteration}}">
                          </td>
                          <td>{{ $package->id }}</td>
                          <td>{{ $package->name }}</td>
                          <td>{{ $package->description }}</td>
                          <td>{{ $package->duration->name }}</td>
                          <td>{{ $package->regular_price }}</td>
                          @if(count($package->offer))
                            <td> {{ $package->offer[0]->discount }}</td>
                            <td> {{ $package->offer[0]->special_inaugral_price }}</td>
                          @else
                            <td>No Offer</td>
                            <td>{{ $package->regular_price }}</td>
                          @endif
                        </tr>
                        @endforeach
                      </tbody>
                      <button type="submit" class="btn btn-primary pull-right">Buy</button>
                    </form>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
@endsection