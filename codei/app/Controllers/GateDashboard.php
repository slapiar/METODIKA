<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class GateDashboard extends BaseController
{
    public function index(): ResponseInterface
    {
        return $this->html(view('gate_dashboard', [
            'sessionsUrl' => site_url('api/gate/sessions'),
            'sessionBaseUrl' => site_url('gate/session'),
        ]));
    }

    public function session(string $id): ResponseInterface
    {
        if (! ctype_digit($id) || (int) $id <= 0) {
            return $this->notFound();
        }

        try {
            $session = (new IniSessionModel())->find((int) $id);
            if (! is_array($session)) {
                return $this->notFound();
            }

            $steps = (new IniStepModel())
                ->where('session_id', (int) $id)
                ->orderBy('step_number', 'ASC')
                ->findAll(15);

            return $this->html(view('gate_session', [
                'session' => $session,
                'steps' => $steps,
                'stepsUrl' => site_url('api/gate/session/' . (int) $id . '/steps'),
                'stateUrl' => site_url('api/gate/session/' . (int) $id . '/state'),
                'stepWriteUrl' => site_url('api/gate/session/' . (int) $id . '/step'),
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]));
        } catch (Throwable $exception) {
            log_message('error', 'GATE dashboard detail failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->notFound();
        }
    }

    private function notFound(): ResponseInterface
    {
        return $this->html(view('errors/html/diagnostics_fallback_404'), 404);
    }

    private function html(string $body, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader(
                'Content-Security-Policy',
                "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; style-src 'self'; script-src 'self'",
            )
            ->setContentType('text/html', 'UTF-8')
            ->setBody($body);
    }
}
