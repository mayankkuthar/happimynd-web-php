<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscribe to HappiMynd</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #e0f2f1);
            margin: 0;
            padding: 40px 20px;
            color: #2a2a2a;
        }

        h1 {
            text-align: center;
            color: #006064;
            margin-bottom: 40px;
            font-size: 36px;
        }

        .plans {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .plan {
            background: linear-gradient(160deg, #ffffff, #f0fdfd);
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.07);
            padding: 30px;
            flex: 1 1 340px;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .plan:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.12);
        }

        .plan h3 {
            color: #004d40;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .plan p {
            color: #555;
            margin: 10px 0;
            line-height: 1.5;
        }

        .plan strong {
            font-size: 22px;
            color: #00796b;
        }

        .btn {
            background: linear-gradient(to right, #4dd0e1, #4db6ac);
            color: white;
            border: none;
            padding: 12px 0;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            margin-top: 18px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(to right, #26c6da, #26a69a);
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 92%;
            padding: 12px;
            border: 1px solid #b2dfdb;
            border-radius: 6px;
            font-size: 15px;
            background-color: #ffffff;
            transition: border-color 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            outline: none;
            border-color: #4db6ac;
        }

        .error {
            color: #d32f2f;
            background: #ffcdd2;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 28px;
                margin-bottom: 30px;
            }

            .plans {
                flex-direction: column;
                align-items: center;
            }

            .plan {
                max-width: 90%;
                padding: 24px;
            }

            .plan h3 {
                font-size: 20px;
            }

            .btn {
                font-size: 15px;
                padding: 10px 0;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }

            h1 {
                font-size: 24px;
                margin-bottom: 20px;
            }

            .plan {
                padding: 20px;
            }

            .plan p,
            .plan strong {
                font-size: 16px;
            }

            .btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<h1>Subscribe to HappiMynd</h1>

@if (session('error'))
    <div class="error">{{ session('error') }}</div>
@endif

<div class="plans">
    @foreach ($plans as $plan)
        <div class="plan">
            <h3>{{ $plan['name'] }}</h3>
            <p><strong>{{ $plan['price'] }}</strong></p>
            <p>{{ $plan['description'] }}</p>
            <form method="post" action="/subscribe">
                @csrf
                <div class="form-group">
                    <label for="name_{{ $plan['id'] }}">Name</label>
                    <input type="text" id="name_{{ $plan['id'] }}" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email_{{ $plan['id'] }}">Email</label>
                    <input type="email" id="email_{{ $plan['id'] }}" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone_{{ $plan['id'] }}">Phone</label>
                    <input type="tel" id="phone_{{ $plan['id'] }}" name="phone" required>
                </div>
                <input type="hidden" name="plan" value="{{ $plan['id'] }}">
                <button type="submit" class="btn">Subscribe Now</button>
            </form>
        </div>
    @endforeach
</div>

</body>
</html>
