<!DOCTYPE html>
<html>
<head>
    <title>Course Rejection Notification</title>
</head>
<body>
<h1>Course Rejected</h1>
<p>Dear {{ $course->creator->name ?? 'Instructor' }},</p>
<p>We regret to inform you that your course titled "{{ $course->title }}" has been rejected.</p>
<p>Please review the course content and make necessary adjustments before resubmitting.</p>
<p>If you have any objection, please contact us</p>
<p>Thank you.</p>
</body>
</html>
