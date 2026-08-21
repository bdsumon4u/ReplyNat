<x-legal-layout title="User Data Deletion Instructions">
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">User Data Deletion Instructions</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Last updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-gray-600 dark:text-gray-300">
            <p class="leading-relaxed">
                At <strong>{{ config('app.name', 'ReplyNat') }}</strong>, we respect your privacy and provide you with complete control over your data. In compliance with data protection regulations and platform policies (including Facebook's Platform Terms), this page outlines the step-by-step instructions to delete your user data and de-authorize connected applications.
            </p>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Option 1: De-authorize the Facebook App (Facebook Login Users)</h2>
                <p class="leading-relaxed mb-3">
                    If you signed up for {{ config('app.name', 'ReplyNat') }} using Facebook Login and want to remove our access to your Facebook profile data, you can do so through Facebook:
                </p>
                <ol class="list-decimal pl-6 space-y-2">
                    <li>Go to your Facebook account's <strong>Settings & Privacy</strong> &gt; <strong>Settings</strong>.</li>
                    <li>In the left sidebar, click on <strong>Apps and Websites</strong>.</li>
                    <li>Find and select <strong>{{ config('app.name', 'ReplyNat') }}</strong> from the list.</li>
                    <li>Click the <strong>Remove</strong> button next to the app name.</li>
                    <li>(Optional) Check the box to delete all your posts, videos, or events that {{ config('app.name', 'ReplyNat') }} may have published on your behalf, and click <strong>Remove</strong> again to confirm.</li>
                </ol>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Option 2: Manual Deletion Request</h2>
                <p class="leading-relaxed mb-3">
                    If you cannot access your account dashboard or would like our team to manually purge your data from our systems, please send a deletion request:
                </p>
                <ul class="list-disc pl-6 space-y-2 mb-3">
                    <li><strong>Email:</strong> Send an email to <a href="mailto:support@replynat.com" class="text-[#f53003] dark:text-[#FF4433] hover:underline font-medium">support@replynat.com</a>.</li>
                    <li><strong>Subject Line:</strong> Use the subject line <code>"User Data Deletion Request"</code>.</li>
                    <li><strong>Details:</strong> Provide the email address associated with the account you wish to delete. To verify your identity, we may ask you to send the request from the email address registered on the account.</li>
                </ul>
                <p class="leading-relaxed">
                    Once verified, we will process your deletion request and purge all your data within <strong>30 days</strong>. You will receive a confirmation email once the deletion is complete.
                </p>
            </div>

            <hr class="border-[#19140015] dark:border-[#3E3E3A]/50">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">What Data is Deleted?</h2>
                <p class="leading-relaxed mb-3">
                    Upon account deletion (either self-initiated or via request), the following data is permanently erased from our active databases:
                </p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Your personal profile (Name, email address, profile picture URL, credentials).</li>
                    <li>Connected social media OAuth profiles and access tokens.</li>
                    <li>Integrations and setting preferences.</li>
                </ul>
                <p class="leading-relaxed mt-3">
                    <em>Note: We may retain certain transaction records or payment histories where required by law for accounting, tax compliance, or audit purposes. Such retained data is securely archived and isolated from any active services.</em>
                </p>
            </div>
        </div>
    </div>
</x-legal-layout>
