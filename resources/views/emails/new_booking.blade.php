<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>New HappiTALK Booking</title>
</head>
<body>
  <div style="max-width:472px; margin:0 auto;background:#fff;font-family: Montserrat, sans-serif;">
    <div style="width:100%;float:left;">
      <div style="width:100%;float:left;box-sizing: border-box;padding:35px;padding-bottom:10px;">
        <p style="font-family: Montserrat-Bold, sans-serif;font-size: 14px;color: #1E1E1E;letter-spacing: 0.78px;line-height: 22px;margin-top:00px;margin-bottom:10px;">
          Dear {{ $psychologist->first_name }} {{ $psychologist->last_name }},
        </p>
        <p style="font-family: Montserrat-Light, sans-serif; font-size: 12px;color: #787878;letter-spacing: 0;line-height: 20px;margin:0px;margin-top:0px;">
          You have a new HappiTALK session booking.
        </p>
        <h4>Booking Details:</h4>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Time:</strong> {{ $time }}</p>
      </div>
    </div>
  </div>
</body>
</html>
