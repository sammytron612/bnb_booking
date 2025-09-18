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
        'contact_name' => 'required|string|min:2|max:100',
        'contact_email' => 'required|email|max:255',
        'contact_subject' => 'required|string|min:5|max:255',
        'contact_message' => 'required|string|min:10|max:1000',
    ];

    protected $messages = [
        'contact_name.required' => 'Please enter your name.',
        'contact_name.min' => 'Name must be at least 2 characters.',
        'contact_email.required' => 'Please enter your email address.',
        'contact_email.email' => 'Please enter a valid email address.',
        'contact_subject.required' => 'Please select a subject.',
        'contact_subject.min' => 'Subject must be at least 5 characters.',
        'contact_message.required' => 'Please enter your message.',
        'contact_message.min' => 'Message must be at least 10 characters.',
        'contact_message.max' => 'Message cannot exceed 1000 characters.',
    ];

    public function submit()
    {
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

    public function render()
    {
        return view('livewire.contact-form');
    }
}
