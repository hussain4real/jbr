<?php

namespace App\Http\Requests;

use App\Actions\Website\WebsiteData;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Throwable;

abstract class GuestRequestForm extends FormRequest
{
    abstract public function kind(): string;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone');
        if (! is_string($phone)) {
            return;
        }
        $phone = strtr($phone, array_combine(
            ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        ));
        $this->merge(['phone' => preg_replace('/[\s().-]/u', '', $phone)]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'email' => ['required', 'string', 'email', 'max:254'],
            'phone' => ['bail', 'required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'message' => [$this->kind() === 'enquiry' ? 'required' : 'nullable', 'string', 'max:5000'],
            'privacy' => ['accepted'],
            'company' => ['nullable', 'string', 'max:0'],
            'submission_token' => ['required', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $ar = app()->getLocale() === 'ar';

        return [
            '*.required' => $ar ? 'يرجى إكمال هذا الحقل.' : 'Please complete this field.',
            '*.string' => $ar ? 'يرجى إدخال نص صالح.' : 'Please enter valid text.',
            '*.max' => $ar ? 'القيمة المدخلة أطول من المسموح.' : 'This entry is too long.',
            'email.email' => $ar ? 'يرجى إدخال بريد إلكتروني صالح.' : 'Please enter a valid email address.',
            'phone.regex' => $ar ? 'أدخل رقم الهاتف مع رمز الدولة، بدءاً بعلامة +.' : 'Include the country code, starting with +.',
            'privacy.accepted' => $ar ? 'يرجى قراءة إشعار الخصوصية والموافقة عليه.' : 'Please acknowledge the privacy notice.',
            'arrival.after_or_equal' => $ar ? 'اختر تاريخ اليوم أو تاريخاً لاحقاً.' : 'Choose today or a later date.',
            'departure.after' => $ar ? 'يجب أن يكون المغادرة بعد الوصول.' : 'Departure must be after arrival.',
            '*.date_format' => $ar ? 'يرجى اختيار تاريخ صالح.' : 'Please choose a valid date.',
            'guests.integer' => $ar ? 'أدخل عدد الضيوف بالأرقام.' : 'Enter a whole number of guests.',
            'guests.max' => $ar ? 'عدد الضيوف أكبر من الحد المسموح للنموذج.' : 'The guest count exceeds this form’s limit.',
            'accommodation_id.integer' => $ar ? 'اختر إقامة من القائمة.' : 'Choose an accommodation from the list.',
            'guests.min' => $ar ? 'أدخل ضيفاً واحداً على الأقل.' : 'Enter at least one guest.',
            'accommodation_id.exists' => $ar ? 'اختر إقامة منشورة أو اطلب المساعدة في الاختيار.' : 'Choose a listed accommodation or ask us to help you choose.',
        ];
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (WebsiteData::localPreview() || ! app(WebsiteData::class)->acceptsRequests()) {
                $validator->errors()->add('form', app()->getLocale() === 'ar' ? 'الطلبات عبر الموقع غير متاحة حالياً.' : 'Online requests are not open yet.');
            }
            try {
                $token = json_decode(Crypt::decryptString((string) $this->input('submission_token')), true, flags: JSON_THROW_ON_ERROR);
                if (! is_array($token) || ($token['session'] ?? '') !== hash('sha256', $this->session()->getId()) || ($token['kind'] ?? '') !== $this->kind() || ($token['expires'] ?? 0) < now()->timestamp) {
                    throw new \RuntimeException('Invalid submission token.');
                }
            } catch (Throwable) {
                $validator->errors()->add('form', app()->getLocale() === 'ar' ? 'انتهت صلاحية النموذج. حدّث الصفحة وحاول مجدداً.' : 'This form has expired. Refresh the page and try again.');
            }
        }];
    }
}
