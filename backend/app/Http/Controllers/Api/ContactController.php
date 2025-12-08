<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Handle contact form submission.
     * Sends an email to the admin with the contact form details.
     *
     * @param ContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        // Get admin email from config (MAIL_FROM_ADDRESS)
        $adminEmail = config('mail.from.address', 'nbody4650@gmail.com');
        $adminName = config('mail.from.name', 'Notter');

        // Prepare email data
        $emailData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        $subject = 'Contact Form: ' . $data['subject'];

        try {
            $sent = $this->emailService->sendView(
                $adminEmail,
                'emails.contact',
                $emailData,
                $subject,
                $data['email'] // Set Reply-To to the user's email
            );

            if ($sent) {
                Log::info('Contact form submitted successfully', [
                    'from' => $data['email'],
                    'subject' => $data['subject']
                ]);

                return response()->json([
                    'message' => 'Thank you for contacting us! We will get back to you soon.',
                ], 200);
            } else {
                Log::error('Failed to send contact form email', [
                    'from' => $data['email'],
                    'to' => $adminEmail
                ]);

                return response()->json([
                    'message' => 'Failed to send your message. Please try again later.',
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error('Contact form exception: ' . $e->getMessage(), [
                'from' => $data['email'],
                'exception' => get_class($e)
            ]);

            return response()->json([
                'message' => 'An error occurred while sending your message. Please try again later.',
            ], 500);
        }
    }
}

