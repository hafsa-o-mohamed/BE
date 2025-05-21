<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سياسة الخصوصية - تمهر</title>
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

            <h1 class="text-3xl font-bold mb-6 text-gray-800">سياسة الخصوصية لتطبيق "تمهر"</h1>
            
            <div class="space-y-6 text-gray-600">
                <p class="mb-6">نحن في تطبيق "تمهر" نقدر خصوصيتك ونلتزم بحماية معلوماتك الشخصية. تشرح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية بياناتك عند استخدامك للتطبيق.</p>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">١. المعلومات التي نجمعها</h2>
                    <p class="mb-2">نقوم بجمع المعلومات التالية عند استخدامك لتطبيق "تمهر":</p>
                    <ul class="list-disc list-inside space-y-2 mr-4">
                        <li>المعلومات الشخصية: مثل اسمك، بريدك الإلكتروني، ورقم الهاتف إذا قمت بتسجيل حسابك.</li>
                        <li>المعلومات التقنية: مثل عنوان الـ IP، نوع الجهاز، ونظام التشغيل، والموقع الجغرافي.</li>
                        <li>المحتوى الذي تقدمه: مثل البيانات التي تقوم بتحميلها أو مشاركتها عبر التطبيق.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٢. كيفية استخدام المعلومات</h2>
                    <p class="mb-2">نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
                    <ul class="list-disc list-inside space-y-2 mr-4">
                        <li>لتوفير وتحسين خدمات التطبيق.</li>
                        <li>للتواصل معك بشأن التحديثات أو العروض المتعلقة بالتطبيق.</li>
                        <li>لتحليل كيفية استخدام التطبيق وتحسين أدائه.</li>
                        <li>لضمان أمان التطبيق وحمايته.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٣. مشاركة المعلومات</h2>
                    <p class="mb-2">لن نقوم ببيع أو تأجير أو مشاركة معلوماتك الشخصية مع أي طرف ثالث دون موافقتك، إلا في الحالات التالية:</p>
                    <ul class="list-disc list-inside space-y-2 mr-4">
                        <li>عندما نكون ملزمين قانونيًا بالكشف عن المعلومات.</li>
                        <li>عندما يتم تقديم خدمات من قبل أطراف ثالثة بموافقتك (مثل خدمات الدفع أو التحليل).</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٤. حماية المعلومات</h2>
                    <p class="mb-4">نحن نتخذ تدابير أمنية مناسبة لحماية معلوماتك الشخصية من الوصول غير المصرح به أو التلاعب. ومع ذلك، يجب أن تكون على علم أنه لا توجد طريقة آمنة 100% على الإنترنت.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٥. حقوقك</h2>
                    <ul class="list-disc list-inside space-y-2 mr-4">
                        <li>الحق في الوصول: يمكنك طلب الوصول إلى معلوماتك الشخصية التي نحتفظ بها.</li>
                        <li>الحق في التعديل: يمكنك تعديل معلوماتك الشخصية في أي وقت.</li>
                        <li>الحق في الحذف: يمكنك طلب حذف حسابك وبياناتك الشخصية من التطبيق.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٦. التغييرات في سياسة الخصوصية</h2>
                    <p class="mb-4">قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر. سيتم نشر التحديثات على هذه الصفحة، ويجب عليك مراجعتها بانتظام.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">٧. التواصل معنا</h2>
                    <p class="mb-4">إذا كانت لديك أي أسئلة أو استفسارات حول سياسة الخصوصية هذه، يمكنك الاتصال بنا عبر البريد الإلكتروني: <a href="mailto:info@tmahur.com" class="text-teal-600 hover:text-teal-700">info@tmahur.com</a></p>
                </section>
            </div>

            <div class="mt-8 text-sm text-gray-500 text-center">
                آخر تحديث: {{ date('Y-m-d') }}
            </div>
        </div>
    </div>
</body>
</html>