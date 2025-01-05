<x-backend-layout>
  <x-slot name="title">
    Plans
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>HappiCHAT and HappiTALK plans Bought by users</h3>
          </div>
        </div>
        <div class="clearfix"></div>

      </div>
      <div class="x_content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30">
              </p>
              <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>username</th>
                    <th>email</th>
                    <th>Organization</th>
                    <th>Plans, availed on</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bundles as $bundle)
                    <tr>
                      <td>
                        {{ $loop->iteration }}
                      </td>
                      <td>
                        {{ $bundle[0]->user->username }}(User id: {{ $bundle[0]->user->id }})
                      </td>
                      <td>
                        {{ $bundle[0]->user->email }}
                      </td>
                      <td>
                        @if($bundle[0]->user->isOrganizationUser())
                          {{ $bundle[0]->user->userToken->token->organization->name ?? 'Individual user' }}
                        @else
                          <b>Individual user</b>
                        @endif
                      </td>
                      <td>
                        @foreach($bundle as $bundleStatus)
                          {{ $bundleStatus->plans->package->name }} @if($bundleStatus->plans->package->name == "HappiTALK")(Sessions:  @if($bundleStatus->user->isOrganizationUser() && $bundleStatus->user->organizationHasHappiTalkPlan()) {{ $bundleStatus->user->getOrganizationHappiTalkSessions() }} @else {{ $bundleStatus->plans->duration->frequency }} @endif) @endif => {{ $bundleStatus->created_at->format("d-M-y g:i a") }}<br>
                        @endforeach

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
    <!-- /page content -->
  </x-slot>
</x-backend-layout>
