<?php

namespace App\Http\Controllers;

use App\Services\InboundEmailService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InboundEmailWebhookController extends Controller
{
    public function __invoke(Request $request, InboundEmailService $inboundEmailService): Response
    {
        if (! $this->verifyMailgunSignature($request)) {
            Log::warning('Inbound email webhook: invalid Mailgun signature');

            return response('Invalid signature', 403);
        }

        $sender = (string) $request->input('sender', $request->input('from', ''));
        $user = $inboundEmailService->resolveUserFromSender($sender);

        if ($user === null) {
            Log::info('Inbound email ignored: unknown sender', ['sender' => $sender]);

            return response('Unknown sender', 406);
        }

        $attachments = $inboundEmailService->attachmentsFromMailgunRequest(
            $request->all(),
            $request->allFiles()
        );

        try {
            $import = $inboundEmailService->process($user, $attachments, $request->input('subject'));

            Log::info('Inbound email processed', [
                'user_id' => $user->id,
                'import_id' => $import->id,
                'status' => $import->status,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Inbound email processing failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response('Processing failed', 422);
        }

        return response('OK', 200);
    }

    private function verifyMailgunSignature(Request $request): bool
    {
        $signingKey = config('pricesignal.mailgun_webhook_signing_key');

        if (blank($signingKey)) {
            return app()->environment(['local', 'testing']);
        }

        $timestamp = $request->input('timestamp');
        $token = $request->input('token');
        $signature = $request->input('signature');

        if (! $timestamp || ! $token || ! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.$token, $signingKey);

        return hash_equals($expected, $signature);
    }
}
