@php($ar = $channel === 'guest' && $guestRequest->locale === 'ar')
<!doctype html>
<html lang="{{ $ar ? 'ar' : 'en' }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head><meta charset="utf-8"><title>{{ $ar ? 'استلمنا طلبك' : 'Request received' }}</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.7;color:#173e35;max-width:600px;margin:40px auto;padding:24px">
<h1 style="font-size:24px">{{ $ar ? 'منتجع جوهرة بدية' : 'Jawharat Bidiyah Resort' }}</h1>
@if($channel === 'staff')
<p>A new {{ $guestRequest->kind === 'booking' ? 'booking request' : 'enquiry' }} is waiting for staff follow-up.</p>
<p><a href="{{ url('/admin/guest-requests') }}">Open the staff inbox</a></p>
@else
<p>{{ $ar ? 'شكراً لتواصلك معنا. استلمنا طلبك وسيقوم فريقنا بالرد عليك.' : 'Thank you for getting in touch. We have received your request and our team will follow up with you.' }}</p>
@if($guestRequest->kind === 'booking')
<p>{{ $ar ? 'طلب الحجز بانتظار مراجعة الفريق وتأكيد التوافر والتفاصيل معك.' : 'Your booking request is awaiting staff review. Our team will confirm availability and the details with you.' }}</p>
@endif
@endif
<p>{{ $ar ? 'رقم الطلب:' : 'Request reference:' }} <bdi dir="ltr">{{ $guestRequest->reference }}</bdi></p>
</body>
</html>
