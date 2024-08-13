<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .certificate-container {
            width: 100%;
            max-width: 850px;
            border: 12px solid #003366;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            margin: auto; /* محاذاة الحاوية أفقياً */
            box-sizing: border-box;
            border-radius: 12px;
            overflow: hidden;
            text-align: center; /* محاذاة النصوص داخل الحاوية */
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .certificate-header .logo {
            max-width: 200px;
            margin: 0 auto 20px auto;
        }
        .certificate-header h1 {
            margin: 0;
            font-size: 2.5em;
            color: #003366;
            font-weight: bold;
        }
        .certificate-body {
            text-align: center;
            margin-bottom: 30px;
        }
        .certificate-body p {
            font-size: 1.2em;
            color: #333333;
            margin: 0;
        }
        .certificate-body .recipient-name {
            font-size: 2em;
            font-weight: bold;
            color: #003366;
            margin-top: 20px;
        }
        .certificate-body .course-title {
            font-size: 1.8em;
            color: #003366;
            margin-top: 10px;
        }
        .certificate-body .date {
            font-size: 1.2em;
            color: #666666;
            margin-top: 20px;
        }
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .certificate-footer .signature,
        .certificate-footer .seal {
            width: 45%;
            text-align: center;
            color: #003366;
        }
        .certificate-footer .signature img,
        .certificate-footer .seal img {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .certificate-footer .signature p,
        .certificate-footer .seal p {
            font-size: 1em;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="certificate-container">
    <div class="certificate-header">
        {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo"> --}}
        <h1>Certificate of Completion</h1>
    </div>
    <div class="certificate-body">
        <p>This is to certify that</p>
        <p class="recipient-name">{{ $user->name }}</p>
        <p>has successfully completed the course</p>
        <p class="course-title">{{ $course->title }}</p>
        <p class="date">on {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    </div>
    <div class="certificate-footer">
        <div class="signature">
            {{-- <img src="{{ asset('public/uploads/signature.png') }}" alt="Signature"> --}}
            <p>{{ \App\Models\User::find($course->creator_id)->name }}</p>
        </div>
        <!-- seal -->
    </div>
</div>
</body>
</html>
