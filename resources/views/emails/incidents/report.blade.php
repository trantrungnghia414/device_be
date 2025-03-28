{{-- @component('mail::message')
# Báo cáo sự cố mới  

**Thông tin chi tiết:**  

Người báo cáo: {{ $user->name }}  
Phòng: {{ $classroom->name }}  
Mô tả: {{ $description }}  
Thời gian: {{ $report_time }}  

Trân trọng,  
Hệ thống Quản lý Thiết bị
@endcomponent 
 --}}
 @component('mail::message')

<div style="color:#000000;">
  <p style="font-size: 15px; line-height: 1.5;  ">
     <strong>Hệ thống Quản lý Thiết bị thông báo có sự cố mới!</strong>
  </p>
  
  <p style="font-size: 13px; line-height: 1.5;">
    <strong>Người báo cáo:</strong> {{ $user->name }}<br>
      <strong>Thời gian báo cáo:</strong> {{ \Carbon\Carbon::parse($report_time)->format('H:i - d/m/Y') }}<br>
      <strong>Phòng:</strong> {{ $classroom->name }}<br>
      <strong>Mô tả:</strong> {{ $description }}
  </p>
  
  <hr style="border: 1px solid #e2e8f0; margin: 10px 0; width: 30%;">
  
  <p style="font-size: 12px; ">
      <strong>HỆ THỐNG QUẢN LÝ THIẾT BỊ</strong><br>
      Địa chỉ: Số 227, Đường Phạm Ngũ Lão,Khóm 4, Phường 1, TPTV, Trà Vinh<br>
      Điện thoại: <a href="tel:0294.3853223" style="color: #3182ce;">(+84) 294.3853223</a><br>
      Website: <a href="https://www.clbhtsv.com/" style="color: #3182ce;">https://www.htqltb.com/</a>
  </p>
</div>
@endcomponent
