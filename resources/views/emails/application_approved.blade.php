<!DOCTYPE html>
<html>
<head>
    <title>ผลการพิจารณาใบสมัคร</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<h2>เรียนคุณ {{ $application->user->name }},</h2>

<p>ยินดีด้วยครับ! 🎉 ใบสมัครของคุณในหมวดหมู่ <strong>"{{ $application->applicationCategory->name }}"</strong> ได้ผ่านการอนุมัติจากคณะกรรมการในขั้นสุดท้ายเป็นที่เรียบร้อยแล้ว</p>

<p>
    <strong>รายละเอียดรอบการพิจารณา:</strong><br>
    ปีการศึกษา: {{ $application->applicationRound->academic_year + 543 }}<br>
    ภาคการศึกษา: {{ $application->applicationRound->semester->label() }}
</p>

<br>
<p>ขอแสดงความยินดีด้วย</p>
<p><em>ระบบจัดการรางวัลนิสิตดีเด่น</em></p>
</body>
</html>
