<x-legal-layout title="Privacy Policy">
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Privacy Policy</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-gray-600 dark:text-gray-300">
            <p class="leading-relaxed">
                Welcome to <strong>{{ config('app.name', 'ReplyNat') }}</strong>. We value your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our website, mobile application, and services (collectively, the "Services").
            </p>
            <p class="leading-relaxed">
                Please read this Privacy Policy carefully. By accessing or using our Services, you agree to the collection and use of your information in accordance with this policy.
            </p>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Information We Collect</h2>
                <p class="mb-3">We collect several different types of information for various purposes to provide and improve our Services to you:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Personal Data:</strong> While using our Services, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you, including your name, email address, phone number, and billing information.</li>
                    <li><strong>Social Media Credentials (OAuth):</strong> If you choose to log in using third-party services (such as Google or Facebook), we receive and store user information from those services (including your public profile data, email address, avatar, and authentication tokens) to facilitate login and set up your account.</li>
                    <li><strong>Usage Data:</strong> We may also collect information on how the Services are accessed and used. This may include your IP address, browser type, browser version, the pages of our Services that you visit, the time and date of your visit, and other diagnostic data.</li>
                    <li><strong>Cookies and Tracking Technologies:</strong> We use cookies and similar tracking technologies to track the activity on our Services and hold certain information to enhance your experience.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. How We Use Your Information</h2>
                <p class="mb-3">We use the collected data for various purposes, including to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Provide, operate, and maintain our Services.</li>
                    <li>Notify you about changes to our Services or updates to our terms.</li>
                    <li>Allow you to participate in interactive features of our Services when you choose to do so.</li>
                    <li>Provide customer support and respond to your inquiries.</li>
                    <li>Process payments and manage your subscriptions.</li>
                    <li>Monitor the usage of our Services and detect, prevent, and address technical or security issues.</li>
                    <li>Fulfill any other purpose for which you provide it.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. How We Share Your Information</h2>
                <p class="mb-3">We do not sell your personal data. We may share your information only under the following circumstances:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Service Providers:</strong> We may employ third-party companies and individuals to facilitate our Services (e.g., database hosts, payment processors, analytics providers). These third parties have access to your Personal Data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</li>
                    <li><strong>Business Transactions:</strong> If {{ config('app.name', 'ReplyNat') }} is involved in a merger, acquisition, or asset sale, your personal data may be transferred.</li>
                    <li><strong>Compliance with Laws:</strong> We may disclose your personal data where required to do so by law or in response to valid requests by public authorities.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. Security of Data</h2>
                <p class="leading-relaxed">
                    The security of your data is important to us, but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Data Retention & Deletion</h2>
                <p class="leading-relaxed mb-3">
                    We will retain your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy.
                </p>
                <p class="leading-relaxed">
                    If you wish to request deletion of your account and associated personal data, please visit our <a href="{{ route('data-deletion') }}" class="text-[#f53003] dark:text-[#FF4433] hover:underline">Data Deletion Instructions</a> page.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. Your Data Protection Rights</h2>
                <p class="mb-3">Depending on your location, you may have the following data protection rights:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Access & Portability:</strong> The right to access, update, or receive a copy of the information we have on you.</li>
                    <li><strong>Rectification:</strong> The right to have your information rectified if that information is inaccurate or incomplete.</li>
                    <li><strong>Objection & Restriction:</strong> The right to object to or restrict our processing of your personal data.</li>
                    <li><strong>Withdraw Consent:</strong> The right to withdraw your consent at any time where we relied on your consent to process your personal information.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Contact Us</h2>
                <p class="leading-relaxed">
                    If you have any questions about this Privacy Policy or our practices, please contact us by email at:
                    <a href="mailto:support@replynat.com" class="text-[#f53003] dark:text-[#FF4433] hover:underline font-medium">support@replynat.com</a>.
                </p>
            </div>
        </div>
    </div>
</x-legal-layout>
