<x-backend-layout>
  <x-slot name="title">
    Contact Leads
  </x-slot>
  <x-slot name="content">
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="row">
          <div class="col-md-12 ">
            <div class="x_panel">
              <div class="x_title">
                <div class="clearfix"></div>
              </div>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 16px; color: #333; background-color: #f9f9f9; border: 1px solid #ddd;">
                <thead>
                    <tr style="background-color:rgb(36, 165, 159);; color: #fff;">
                        <th style="padding: 12px; border: 1px solid #ddd;">Id</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">First Name</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Last Name</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Phone Number</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Reason</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Message</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Referral</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                    <tr style="background-color: {{ $loop->index % 2 === 0 ? '#fff' : '#f2f2f2' }};">
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->id }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->first_name }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->last_name }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->phone_number }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->reason }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->message }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->referral }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $contact->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

    @if ($contacts->isEmpty())
        <p style="text-align: center; color: #999;">No contacts found.</p>
    @endif

            </div>
          </div>
        </div>
      </div>
    </div>
  </x-slot>
</x-backend-layout>
<script>
</script>