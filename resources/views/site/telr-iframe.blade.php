
<x-front-layout>

<style>
    /*
    تنسيقات CSS مخصصة لضمان تمركز iFrame وتجاوبه مع شاشات مختلفة
    */
    .iframe-wrapper {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        padding: 20px;
        /* للتأكد من أن الـ wrapper يغطي مساحة كافية داخل الـ layout */
        min-height: 80vh;
        background-color: #f8f8f8; /* خلفية خفيفة لمزيد من التباين */
    }
    .iframe-wrapper > div {
        /* تعيين الارتفاع في هذا الـ div ليتمكن iFrame من ملئه */
        height: 100%;
    }
    iframe {
        border: 1px solid #d1d5db; /* إطار خفيف */
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%; /* للعرض الكامل داخل Wrapper */
        height: 100%;
    }
    @media (min-width: 768px) {
        .iframe-wrapper {
            padding: 40px;
        }
        /* تحديد ارتفاع وعرض iFrame على شاشات الديسكتوب */
        .iframe-wrapper > div {
             max-width: 600px;
             height: 700px;
        }
    }
</style>


<div class="iframe-wrapper">
    <div class="w-full h-full md:max-w-md">
        <!-- رسالة تحذيرية -->
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded-lg" role="alert">
            <p class="font-bold">صفحة دفع مؤمنة</p>
            <p>عملية الدفع تتم بأمان داخل موقعنا بواسطة Telr. بيانات بطاقتك لا تخزن لدينا.</p>
        </div>

        <iframe
            src="{{ $paymentUrl }}"
            frameborder="0"
            allowtransparency="true"
            title="Telr Payment Gateway"
            class="w-full h-full"
        >
        </iframe>
    </div>
</div>

<!-- تذييل (Footer) بسيط -->
<footer class="bg-white border-t border-gray-200 text-center p-4 text-xs text-gray-500">
    جميع الحقوق محفوظة | الشروط والأحكام
</footer>

</x-front-layout>
