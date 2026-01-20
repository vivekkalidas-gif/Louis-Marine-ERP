<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Digital Signature') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Upload your digital signature for use in Service Reports.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Hidden Name & Email to satisfy request validation if needed, assuming patch updates only sent fields? 
             Actually ProfileUpdateRequest requires name/email. 
             So we should likely keep this in the main form OR replicate hidden fields. 
             Better approach: Add the input into the MAIN form (update-profile-information-form.blade.php) 
             instead of a separate form which would require re-sending name/email.
        -->
        <!-- Wait, ProfileUpdateRequest requires name/email. If we use a separate form, validation will fail. 
             Let's put this logic INSIDE update-profile-information-form.blade.php -->
    </form>
</section>
