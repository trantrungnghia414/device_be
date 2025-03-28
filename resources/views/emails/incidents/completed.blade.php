<x-mail::message>
# Thông báo sự cố đã được xử lý hoàn tất

Xin chào {{ $incident->user->name }},

Sự cố bạn đã báo cáo tại phòng **{{ $classroom->name }}** đã được xử lý hoàn tất.

**Thời gian báo cáo:** {{ \Carbon\Carbon::parse($incident->report_time)->format('H:i d/m/Y') }}
**Mô tả sự cố:** {{ $incident->description }}

**Chi tiết xử lý:**
@foreach($completionDetails as $detail)
- {{ $detail['user_name'] }} đã hoàn thành lúc {{ \Carbon\Carbon::parse($detail['completion_time'])->format('H:i d/m/Y') }}
@if($detail['notes'])
  Ghi chú: {{ $detail['notes'] }}
@endif
@endforeach

Trân trọng!
</x-mail::message>