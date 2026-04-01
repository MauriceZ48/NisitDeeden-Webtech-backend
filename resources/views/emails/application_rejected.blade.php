<!DOCTYPE html>
<html>
<body style="font-family: 'Tahoma', sans-serif; line-height: 1.6; color: #333;">
<h2>เรียนคุณ {{ $application->user->name }},</h2>

<p>ตามที่คุณได้ยื่นใบสมัครรับรางวัลนิสิตดีเด่น ในหมวดหมู่ <strong>"{{ $application->applicationCategory->name }}"</strong> นั้น</p>

<p>คณะกรรมการได้พิจารณาใบสมัครของท่านเรียบร้อยแล้ว และมีความเสียใจที่ต้องแจ้งให้ทราบว่า <strong>ใบสมัครของท่านไม่ผ่านการอนุมัติ</strong> โดยมีรายละเอียดเหตุผลดังนี้:</p>

<div style="background: #f8f9fa; padding: 15px; border-left: 5px solid #dc3545; margin: 20px 0;">
    <strong>เหตุผลการปฏิเสธ:</strong><br>
    {{ $application->rejection_reason ?? 'ไม่ระบุเหตุผล' }}
</div>

<p>ท่านสามารถตรวจสอบรายละเอียดเพิ่มเติม ได้ที่ระบบนิสิตดีเด่น</p>

<p>ขอขอบคุณที่ให้ความสนใจเข้าร่วมโครงการ</p>
<p><em>ระบบจัดการรางวัลนิสิตดีเด่น</em></p>
</body>
</html>
