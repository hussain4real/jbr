<?php

namespace App\Actions\Website;

class WebsiteCopy
{
    /** @return array<string, array{en: string, ar: string}> */
    public static function defaults(): array
    {
        return [
            'brand_name' => ['en' => 'Jawharat Bidiyah Resort', 'ar' => 'منتجع جوهرة بدية'],
            'home_title' => ['en' => 'A stay to make your own.', 'ar' => 'إقامة على طريقتك.'],
            'accommodation_title' => ['en' => 'Find your place to stay.', 'ar' => 'اختر إقامتك.'],
            'gallery_title' => ['en' => 'A closer look.', 'ar' => 'نظرة عن قرب.'],
            'plan_title' => ['en' => 'Plan your stay.', 'ar' => 'خطط لإقامتك.'],
            'contact_title' => ['en' => 'Let’s start a conversation.', 'ar' => 'لنبدأ الحديث.'],
            'booking_title' => ['en' => 'Your stay starts here.', 'ar' => 'إقامتك تبدأ من هنا.'],
            'privacy_title' => ['en' => 'Your privacy.', 'ar' => 'خصوصيتك.'],
            'received_title' => ['en' => 'Thank you. We have your request.', 'ar' => 'شكراً لك. لقد استلمنا طلبك.'],
            'navigation_accommodation' => ['en' => 'Accommodation', 'ar' => 'الإقامة'],
            'navigation_gallery' => ['en' => 'Gallery', 'ar' => 'الصور'],
            'navigation_plan' => ['en' => 'Plan your stay', 'ar' => 'خطط لإقامتك'],
            'navigation_contact' => ['en' => 'Contact', 'ar' => 'تواصل معنا'],
            'accessibility_skip' => ['en' => 'Skip to content', 'ar' => 'انتقل إلى المحتوى'],
            'accessibility_home' => ['en' => 'Home', 'ar' => 'الرئيسية'],
            'accessibility_navigation' => ['en' => 'Main navigation', 'ar' => 'التنقل الرئيسي'],
            'actions_booking' => ['en' => 'Request a booking', 'ar' => 'طلب حجز'],
            'accessibility_close_menu' => ['en' => 'Close menu', 'ar' => 'إغلاق القائمة'],
            'accessibility_open_menu' => ['en' => 'Open menu', 'ar' => 'فتح القائمة'],
            'accessibility_mobile_navigation' => ['en' => 'Mobile navigation', 'ar' => 'قائمة التنقل'],
            'home_welcome' => ['en' => 'WELCOME TO JAWHARAT BIDIYAH', 'ar' => 'أهلاً بكم في جوهرة بدية'],
            'home_preparing' => ['en' => 'Our website is being prepared. We look forward to welcoming you.', 'ar' => 'نعمل على تجهيز موقعنا ونتطلع إلى الترحيب بكم.'],
            'actions_explore_stays' => ['en' => 'Explore our stays', 'ar' => 'استكشف الإقامة'],
            'home_scroll' => ['en' => 'Take a look around', 'ar' => 'تعرّف على المكان'],
            'actions_gallery' => ['en' => 'View gallery', 'ar' => 'معرض الصور'],
            'home_accommodation_label' => ['en' => 'OUR ACCOMMODATION', 'ar' => 'خيارات الإقامة'],
            'home_accommodation_heading' => ['en' => 'A place for your next stay.', 'ar' => 'مكان لإقامتك القادمة.'],
            'actions_all_stays' => ['en' => 'Explore all accommodation', 'ar' => 'استكشف جميع خيارات الإقامة'],
            'accommodation_capacity_prefix' => ['en' => 'Up to', 'ar' => 'حتى'],
            'accommodation_capacity_suffix' => ['en' => 'guests', 'ar' => 'ضيوف'],
            'home_invitation_label' => ['en' => 'LET’S PLAN YOUR VISIT', 'ar' => 'لنخطط لزيارتك'],
            'home_invitation_heading' => ['en' => 'Tell us about your stay.', 'ar' => 'أخبرنا عن إقامتك.'],
            'home_invitation_description' => ['en' => 'Share your preferred dates and contact details. Our team will help with the next steps.', 'ar' => 'شاركنا التواريخ المفضلة وبيانات التواصل، وسيساعدك فريقنا في الخطوات التالية.'],
            'accommodation_description' => ['en' => 'Explore the photographs and details, then share your preferred dates with us.', 'ar' => 'استكشف الصور والتفاصيل، ثم شاركنا تواريخ إقامتك المفضلة.'],
            'gallery_description' => ['en' => 'Discover the spaces at Jawharat Bidiyah Resort.', 'ar' => 'تعرّف على مساحات منتجع جوهرة بدية.'],
            'requests_description' => ['en' => 'A little planning begins with a conversation. Send your details below.', 'ar' => 'تبدأ رحلتك بحديث معنا. أرسل بياناتك أدناه.'],
            'actions_request_stay' => ['en' => 'Request this stay', 'ar' => 'طلب هذه الإقامة'],
            'accessibility_enlarge' => ['en' => 'Enlarge photograph', 'ar' => 'تكبير الصورة'],
            'accommodation_inclusions' => ['en' => 'Included in your stay', 'ar' => 'ما تتضمنه إقامتك'],
            'accommodation_booking_label' => ['en' => 'PLAN YOUR STAY', 'ar' => 'خطط لإقامتك'],
            'accommodation_booking_description' => ['en' => 'Send your preferred dates. Staff will confirm availability and the details of your stay.', 'ar' => 'أرسل تواريخك المفضلة. سيؤكد فريقنا التوفر وتفاصيل الإقامة.'],
            'actions_question' => ['en' => 'Have a question?', 'ar' => 'لديك استفسار؟'],
            'accessibility_enlarge_prefix' => ['en' => 'Enlarge: ', 'ar' => 'تكبير: '],
            'accommodation_policies' => ['en' => 'Before you book', 'ar' => 'قبل الحجز'],
            'plan_directions' => ['en' => 'Getting here', 'ar' => 'الوصول إلينا'],
            'actions_directions' => ['en' => 'Open directions', 'ar' => 'عرض الاتجاهات'],
            'plan_policies' => ['en' => 'Good to know', 'ar' => 'معلومات تهمك'],
            'plan_preparing' => ['en' => 'Practical information for your stay will be available here soon.', 'ar' => 'ستتوفر هنا قريباً معلومات تساعدك في التخطيط لإقامتك.'],
            'plan_faqs' => ['en' => 'Your questions, answered.', 'ar' => 'إجابات عن أسئلتك.'],
            'plan_help_heading' => ['en' => 'Here to help.', 'ar' => 'نحن هنا لمساعدتك.'],
            'plan_help_description' => ['en' => 'Have a question before your visit? Share it with our team.', 'ar' => 'لديك سؤال قبل زيارتك؟ شاركه مع فريقنا.'],
            'actions_enquiry' => ['en' => 'Make an enquiry', 'ar' => 'إرسال استفسار'],
            'requests_steps_label' => ['en' => 'WHAT HAPPENS NEXT', 'ar' => 'ماذا بعد؟'],
            'requests_step_one_title' => ['en' => 'Tell us your plans', 'ar' => 'أخبرنا بخططك'],
            'requests_step_one_description' => ['en' => 'Your email and contact number help us get back to you.', 'ar' => 'يساعدنا بريدك الإلكتروني ورقم التواصل في الرد عليك.'],
            'requests_step_two_title' => ['en' => 'We review your request', 'ar' => 'نراجع طلبك'],
            'requests_step_two_description' => ['en' => 'Our team checks the details and, for stays, availability.', 'ar' => 'يراجع فريقنا التفاصيل ويتحقق من التوفر لطلبات الإقامة.'],
            'requests_step_three_title' => ['en' => 'We get in touch', 'ar' => 'نتواصل معك'],
            'requests_step_three_description' => ['en' => 'Staff will answer your questions and explain the next steps.', 'ar' => 'سيجيب فريقنا عن أسئلتك ويوضح الخطوات التالية.'],
            'contact_heading' => ['en' => 'Contact the resort', 'ar' => 'تواصل مع المنتجع'],
            'actions_whatsapp' => ['en' => 'Message on WhatsApp', 'ar' => 'تواصل عبر واتساب'],
            'received_reference' => ['en' => 'YOUR REQUEST REFERENCE', 'ar' => 'الرقم المرجعي لطلبك'],
            'actions_home' => ['en' => 'Back to the resort', 'ar' => 'العودة إلى الرئيسية'],
            'footer_welcome' => ['en' => 'We look forward to welcoming you.', 'ar' => 'نتطلع إلى الترحيب بكم.'],
            'accessibility_footer' => ['en' => 'Footer navigation', 'ar' => 'روابط إضافية'],
            'actions_contact' => ['en' => 'Get in touch', 'ar' => 'تواصل معنا'],
            'actions_privacy' => ['en' => 'Privacy', 'ar' => 'الخصوصية'],
            'footer_tagline' => ['en' => 'Every stay begins with a conversation.', 'ar' => 'كل إقامة تبدأ بالتواصل.'],
            'accessibility_close_photo' => ['en' => 'Close photograph', 'ar' => 'إغلاق الصورة'],
            'form_booking_legend' => ['en' => 'Booking request details', 'ar' => 'بيانات طلب الحجز'],
            'form_enquiry_legend' => ['en' => 'Enquiry details', 'ar' => 'بيانات الاستفسار'],
            'form_accommodation' => ['en' => 'Accommodation preference', 'ar' => 'الإقامة المفضلة'],
            'form_choose' => ['en' => 'Help me choose', 'ar' => 'ساعدوني في الاختيار'],
            'form_arrival' => ['en' => 'Arrival date', 'ar' => 'تاريخ الوصول'],
            'form_departure' => ['en' => 'Departure date', 'ar' => 'تاريخ المغادرة'],
            'form_guests' => ['en' => 'Number of guests', 'ar' => 'عدد الضيوف'],
            'form_name' => ['en' => 'Full name', 'ar' => 'الاسم الكامل'],
            'form_email' => ['en' => 'Email address', 'ar' => 'البريد الإلكتروني'],
            'form_phone' => ['en' => 'Contact number', 'ar' => 'رقم التواصل'],
            'form_phone_hint' => ['en' => 'Include + and your country code.', 'ar' => 'يرجى إدخال + ورمز الدولة.'],
            'form_booking_message' => ['en' => 'Anything you would like us to know? (optional)', 'ar' => 'هل تود إخبارنا بأي تفاصيل؟ (اختياري)'],
            'form_enquiry_message' => ['en' => 'How can we help? *', 'ar' => 'كيف يمكننا مساعدتك؟ *'],
            'form_consent' => ['en' => 'I have read the privacy notice and agree to being contacted about this request.', 'ar' => 'قرأت إشعار الخصوصية وأوافق على التواصل معي بشأن هذا الطلب.'],
            'actions_privacy_notice' => ['en' => 'Privacy notice', 'ar' => 'إشعار الخصوصية'],
            'form_send_booking' => ['en' => 'Send booking request', 'ar' => 'إرسال طلب الحجز'],
            'form_send_enquiry' => ['en' => 'Send enquiry', 'ar' => 'إرسال الاستفسار'],
            'footer_instagram' => ['en' => 'Instagram', 'ar' => 'Instagram'],
            'footer_facebook' => ['en' => 'Facebook', 'ar' => 'Facebook'],
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return array<string, string>
     */
    public static function forLocale(array $profile, string $locale): array
    {
        $copy = [];
        foreach (self::defaults() as $key => $translations) {
            $value = data_get($profile, 'copy.'.$key.'.'.$locale);
            $copy[$key] = is_string($value) && filled($value) ? $value : $translations[$locale];
        }

        return $copy;
    }
}
