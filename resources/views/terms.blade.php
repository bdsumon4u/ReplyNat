<x-legal-layout title="Terms of Service">
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Terms of Service</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-gray-600 dark:text-gray-300">
            <p class="leading-relaxed">
                Welcome to <strong>{{ config('app.name', 'ReplyNat') }}</strong>. These Terms of Service ("Terms", "Agreement") govern your access to and use of our website, software, and services (collectively, the "Services").
            </p>
            <p class="leading-relaxed">
                By accessing or using our Services, you agree to be bound by these Terms. If you disagree with any part of these Terms, you do not have permission to access or use the Services.
            </p>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Accounts and Registration</h2>
                <p class="leading-relaxed mb-3">
                    To use certain features of our Services, you must register for an account. When you register, you agree to:
                </p>
                <ul class="list-disc pl-6 space-y-2 mb-3">
                    <li>Provide accurate, current, and complete account information.</li>
                    <li>Maintain and promptly update your account details.</li>
                    <li>Keep your password secure and confidential.</li>
                    <li>Accept all responsibility for any activity that occurs under your account.</li>
                </ul>
                <p class="leading-relaxed">
                    You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Subscriptions, Fees, and Payments</h2>
                <p class="leading-relaxed mb-3">
                    Some parts of the Services are billed on a subscription basis. You will be billed in advance on a recurring and periodic cycle (such as monthly or annually), depending on the subscription plan you select.
                </p>
                <ul class="list-disc pl-6 space-y-2 mb-3">
                    <li><strong>Billing:</strong> A valid payment method is required to process payment. You shall provide accurate and complete billing information. By submitting payment information, you authorize {{ config('app.name', 'ReplyNat') }} to charge all subscription fees incurred through your account to any such payment instruments.</li>
                    <li><strong>Automatic Renewal:</strong> At the end of each billing cycle, your subscription will automatically renew under the exact same conditions unless you cancel it or we cancel it.</li>
                    <li><strong>Cancellation:</strong> You may cancel your subscription renewal either through your online account management page or by contacting our customer support team.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. Acceptable Use Policy</h2>
                <p class="leading-relaxed mb-3">
                    You agree not to use the Services for any unlawful purpose or in any way that violates these Terms. Prohibited conduct includes, but is not limited to:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Using the Services in any manner that could disable, overburden, damage, or impair the Services or interfere with any other party's use.</li>
                    <li>Attempting to gain unauthorized access to, interfere with, damage, or disrupt any parts of the Services or database.</li>
                    <li>Engaging in automated scraping, data extraction, or similar activities.</li>
                    <li>Using the Services to send unsolicited communications, spam, or malicious software.</li>
                    <li>Impersonating or attempting to impersonate {{ config('app.name', 'ReplyNat') }}, our employees, or any other user.</li>
                </ul>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. Intellectual Property</h2>
                <p class="leading-relaxed">
                    The Services and their original content, features, and functionality (excluding user-provided content) are and will remain the exclusive property of {{ config('app.name', 'ReplyNat') }} and its licensors. Our trademarks, trade dress, and brand assets may not be used in connection with any product or service without our prior written consent.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Limitation of Liability & Warranty Disclaimers</h2>
                <p class="leading-relaxed mb-3">
                    Our Services are provided on an "AS IS" and "AS AVAILABLE" basis without warranties of any kind, whether express or implied.
                </p>
                <p class="leading-relaxed">
                    To the maximum extent permitted by applicable law, in no event shall {{ config('app.name', 'ReplyNat') }}, its affiliates, directors, or employees, be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of, or inability to access or use, the Services.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. Termination</h2>
                <p class="leading-relaxed">
                    We may terminate or suspend your account and access to the Services immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever, including without limitation if you breach the Terms. Upon termination, your right to use the Services will immediately cease.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Governing Law</h2>
                <p class="leading-relaxed">
                    These Terms shall be governed and construed in accordance with the laws of our operating jurisdiction, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">8. Changes to Terms</h2>
                <p class="leading-relaxed">
                    We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. By continuing to access or use our Services after those revisions become effective, you agree to be bound by the revised terms.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">9. Contact Us</h2>
                <p class="leading-relaxed">
                    If you have any questions about these Terms of Service, please contact us by email at:
                    <a href="mailto:support@replynat.com" class="text-[#f53003] dark:text-[#FF4433] hover:underline font-medium">support@replynat.com</a>.
                </p>
            </div>
        </div>
    </div>
</x-legal-layout>
