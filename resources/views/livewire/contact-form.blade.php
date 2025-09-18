<!-- Quick Contact Form -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg border border-slate-200 dark:border-gray-700 max-w-2xl mx-auto">
<h4 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 text-center">Quick Inquiry</h4>

<!-- Success Message -->
@if (session()->has('contact_success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('contact_success') }}
        </div>
    </div>
@endif

<!-- Error Message -->
@if (session()->has('contact_error'))
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ session('contact_error') }}
        </div>
    </div>
@endif

<form wire:submit.prevent="submit" class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="contact-name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Name</label>
            <input type="text" id="contact_name" wire:model="contact_name" class="w-full px-4 py-3 rounded-lg border @error('contact_name') border-red-500 @else border-slate-300 dark:border-gray-600 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors" placeholder="Your name">
            @error('contact_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="contact-email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
            <input type="email" id="contact_email" wire:model="contact_email" class="w-full px-4 py-3 rounded-lg border @error('contact_email') border-red-500 @else border-slate-300 dark:border-gray-600 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors" placeholder="your@email.com">
            @error('contact_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div>
        <label for="contact-subject" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Subject</label>
        <select wire:model="contact_subject" id="contact-subject" class="w-full px-4 py-3 rounded-lg border @error('contact_subject') border-red-500 @else border-slate-300 dark:border-gray-600 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
            <option value="General Inquiry">General Inquiry</option>
            <option value="Booking Question">Booking Question</option>
            <option value="The Light House">The Light House</option>
            <option value="Saras">Saras</option>
            <option value="Group Booking">Group Booking</option>
            <option value="Special Requirements">Special Requirements</option>
        </select>
        @error('contact_subject')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="contact-message" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Message</label>
        <textarea wire:model="contact_message" rows="4" class="w-full px-4 py-3 rounded-lg border @error('contact_message') border-red-500 @else border-slate-300 dark:border-gray-600 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors resize-none" placeholder="How can we help you plan your perfect coastal getaway?"></textarea>
        @error('contact_message')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <div class="mt-1 text-sm text-slate-500 dark:text-slate-400 text-right">
            {{ strlen($contact_message ?? '') }}/1000 characters
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-8 py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl" wire:loading.attr="disabled">
            <span wire:loading.remove>Send Message</span>
            <span wire:loading>Sending...</span>
        </button>
    </div>
</form>
</div>
