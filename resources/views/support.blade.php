<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدعم - تمهر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-arabic">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
            <div class="flex justify-center mb-8">
                <img 
                    src="{{ asset('images/logo.png') }}" 
                    alt="تمهر" 
                    class="h-16 w-auto"
                />
            </div>

            <h1 class="text-3xl font-bold mb-6 text-gray-800">مركز الدعم - تمهر</h1>
            
            <div class="space-y-6 text-gray-600">
                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">تواصل معنا</h2>
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <p class="mb-4">إذا كنت بحاجة إلى مساعدة في تطبيقنا، أو لأي طلبات خاصة ببياناتك في التطبيق لا تتردد في الاتصال بنا:</p>
                        <ul class="list-disc list-inside space-y-2 mr-4">
                            <li>البريد الإلكتروني: <a href="mailto:support@tmahur.com" class="text-teal-600 hover:text-teal-700">support@tmahur.com</a></li>
                            <li>الهاتف: <span dir="ltr">+966533363609</span></li>
                            <li>ساعات العمل: الأحد - الخميس، ٩:٠٠ صباحاً - ٥:٠٠ مساءً (توقيت السعودية)</li>
                        </ul>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">الأسئلة الشائعة</h2>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold mb-2">كيف يمكنني البحث عن العقارات؟</h3>
                            <p>يمكنك البحث عن العقارات باستخدام شريط البحث وتصفية النتائج حسب الموقع والسعر ونوع العقار.</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold mb-2">كيف يمكنني التواصل مع مالك العقار؟</h3>
                            <p>انقر على أي إعلان عقاري لعرض تفاصيل الاتصال أو استخدم نظام المراسلة داخل التطبيق.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">الخصوصية والشروط</h2>
                    <p class="mb-2">للحصول على معلومات حول سياسة الخصوصية وشروط الخدمة، يرجى زيارة:</p>
                    <ul class="list-disc list-inside space-y-2 mr-4">
                        <li><a href="/terms" class="text-teal-600 hover:text-teal-700">شروط الخدمة والخصوصية</a></li>
                    </ul>
                </section>
            </div>

            <div class="mt-8 text-sm text-gray-500 text-center">
                © {{ date('Y') }} تمهر للعقارات. جميع الحقوق محفوظة.
            </div>
        </div>
    </div>
</body>
</html>
