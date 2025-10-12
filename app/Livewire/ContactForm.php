<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\Enquiry;

class ContactForm extends Component
{
    public $contact_name = '';
    public $contact_email = '';
    public $contact_subject = 'General Inquiry';
    public $contact_message = '';

    protected $rules = [
        'contact_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\-\']+$/u',
        'contact_email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'contact_subject' => 'required|string|min:5|max:255',
        'contact_message' => 'required|string|min:10|max:1000',
    ];

    protected $messages = [
        'contact_name.required' => 'Please enter your name.',
        'contact_name.min' => 'Name must be at least 2 characters.',
        'contact_name.regex' => 'Name may only contain letters, spaces, hyphens, and apostrophes.',
        'contact_email.required' => 'Please enter your email address.',
        'contact_email.email' => 'Please enter a valid email address.',
        'contact_email.regex' => 'Please enter a valid email format.',
        'contact_subject.required' => 'Please select a subject.',
        'contact_subject.min' => 'Subject must be at least 5 characters.',
        'contact_message.required' => 'Please enter your message.',
        'contact_message.min' => 'Message must be at least 10 characters.',
        'contact_message.max' => 'Message cannot exceed 1000 characters.',
    ];

    public function submit()
    {
        // Sanitize inputs before validation
        $this->sanitizeInputs();

        $this->validate();

        try {
            // Here you would typically send an email or save to database
            // For now, we'll just log the contact form submission
            Log::info('Contact form submitted', [
                'name' => $this->contact_name,
                'email' => $this->contact_email,
                'subject' => $this->contact_subject,
                'message' => substr($this->contact_message, 0, 100) . '...'
            ]);

            $enquiry = ['name' => $this->contact_name,
                'email' => $this->contact_email,
                'subject' => $this->contact_subject,
                'message' => $this->contact_message
            ];

            // Send email to owner
            $ownerEmail = config('mail.owner_email', 'kevin.wilson@kevinlwilson.co.uk');
            Mail::to($ownerEmail)->send(new Enquiry($enquiry));

            Log::info('Enquiry email sent successfully', ['to' => $ownerEmail]);

            // Clear the form
            $this->reset();
            $this->contact_subject = 'General Inquiry';



            // Show success message
            session()->flash('contact_success', 'Thank you for your message! We\'ll get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());
            session()->flash('contact_error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }

    /**
     * Sanitize all form inputs to prevent XSS and ensure data integrity
     */
    private function sanitizeInputs()
    {
        $this->contact_name = $this->sanitizeName($this->contact_name);
        $this->contact_email = $this->sanitizeEmail($this->contact_email);
        $this->contact_subject = $this->sanitizeText($this->contact_subject);
        $this->contact_message = $this->sanitizeMessage($this->contact_message);
    }

    /**
     * Sanitize name input
     */
    private function sanitizeName($input)
    {
        if (empty($input)) return '';

        // Remove HTML tags and encode special characters
        $sanitized = strip_tags(trim($input));
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        // Remove any characters that aren't letters, spaces, hyphens, or apostrophes
        $sanitized = preg_replace('/[^a-zA-Z\s\-\']/u', '', $sanitized);

        // Limit consecutive spaces and trim
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);

        return trim($sanitized);
    }

    /**
     * Sanitize email input
     */
    private function sanitizeEmail($input)
    {
        if (empty($input)) return '';

        // Remove HTML tags and encode special characters
        $sanitized = strip_tags(trim($input));
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        // Remove spaces and convert to lowercase
        $sanitized = strtolower(str_replace(' ', '', $sanitized));

        return $sanitized;
    }

    /**
     * Sanitize subject text
     */
    private function sanitizeText($input)
    {
        if (empty($input)) return '';

        // Remove HTML tags and encode special characters
        $sanitized = strip_tags(trim($input));
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        // Remove any potentially dangerous characters while preserving normal punctuation
        $sanitized = preg_replace('/[<>{}]/', '', $sanitized);

        // Limit consecutive spaces and trim
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);

        return trim($sanitized);
    }

    /**
     * Sanitize message content
     */
    private function sanitizeMessage($input)
    {
        if (empty($input)) return '';

        // Remove HTML tags and encode special characters
        $sanitized = strip_tags(trim($input));
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        // Remove any potentially dangerous characters while preserving normal punctuation and line breaks
        $sanitized = preg_replace('/[<>{}]/', '', $sanitized);

        // Normalize line breaks
        $sanitized = preg_replace('/\r\n|\r|\n/', "\n", $sanitized);

        // Limit consecutive spaces but preserve line breaks
        $sanitized = preg_replace('/[ \t]+/', ' ', $sanitized);

        return trim($sanitized);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
