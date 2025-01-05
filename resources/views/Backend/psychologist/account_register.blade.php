<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" type="image/png" href="img/favi.png">
  <title>Quicake | Default</title>

</head>

<body>
  <div style="max-width:472px; margin:0 auto;background:#fff;font-family: Montserrat, sans-serif;">
    <div style="width:100%;float:left;">
      <!--header-->
      <div style="width:100%;float:left;background:#fff;height:80px;background-size: cover;padding-left:35px;box-sizing: border-box;">
        <!-- <img src="logo.png" style="width:200px;float:left;margin-top: 30px;display:block;" /> -->
      </div>

      <!--content-->
      <div style="width:100%;float:left;box-sizing: border-box;padding:35px;padding-bottom:10px;">
        <p style="font-family: Montserrat-Bold, sans-serif;font-size: 14px;color: #1E1E1E;letter-spacing: 0.78px;line-height: 22px;margin-top:00px;margin-bottom:10px;">Dear {{ $psychologist->first_name." ".$psychologist->last_name ?? ''}}.
        </p>
        <p style="font-family: Montserrat-Light, sans-serif; font-size: 12px;color: #787878;letter-spacing: 0;line-height: 20px;margin:0px;margin-top:0px;">Your Account has been created as a Psychologist on Happimynd.
        </p>
        <h4>Your login details:- </h4>
        <p>Email:-     {{ $psychologist->email ?? ''}}</p>
        <p>Username:-     {{ $psychologist->username ?? ''}}</p>
        <p>Password:- {{ $psychologist->password ?? ''}}</p>
      </div>
    </div>
</body>

</html>
