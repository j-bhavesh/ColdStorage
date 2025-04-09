<x-mail::message>
# Hello,

You’ve received a new quote inquiry.

Here are the details:

---

**Name:** {{ $data['name'] ?? 'N/A' }}  
**Email:** [{{ $data['email'] ?? 'N/A' }}](mailto:{{ $data['email'] ?? '' }})  
**Mobile:** {{ $data['full_number'] ?? $data['mobile_code'] ?? 'N/A' }}  
**Message:**  
{{ $data['comments'] ?? 'N/A' }}

---

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
