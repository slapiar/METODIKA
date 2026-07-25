<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class DiagnosticsGateStepController extends BaseController
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;
    private const TEST_SESSION_ID = 1;
    private const TEST_STEP_NUMBER = 1;

    public function create(): ResponseInterface
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(404);
        }

        try {
            $sessionModel = new IniSessionModel();
            if ($sessionModel->find(self::TEST_SESSION_ID) === null) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'ok' => false,
                        'message' => 'Testovacia session 1 neexistuje.',
                    ]);
            }

            $stepModel = new IniStepModel();
            $existing = $stepModel
                ->where('session_id', self::TEST_SESSION_ID)
                ->where('step_number', self::TEST_STEP_NUMBER)
                ->first();

            $created = false;
            $stepId = $existing['id'] ?? null;

            if ($existing === null) {
                $stepId = $stepModel->insert([
                    'session_id' => self::TEST_SESSION_ID,
                    'step_number' => self::TEST_STEP_NUMBER,
                    'name' => 'Inicializácia',
                    'status' => 'valid',
                    'validated_at' => date('Y-m-d H:i:s'),
                ]);
                $created = true;
            }

            $steps = $stepModel
                ->where('session_id', self::TEST_SESSION_ID)
                ->orderBy('step_number', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'ok' => true,
                'created' => $created,
                'step_id' => $stepId,
                'count' => count($steps),
                'steps' => $steps,
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Create test step failed: {message}', [
                'message' => $e->getMessage(),
            ]);

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'ok' => false,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
        }
    }

    private function isAuthorized(): bool
    {
        $enabled = getenv('METODIKA_DIAGNOSTICS_ENABLED');
        if (! is_string($enabled) || trim($enabled) !== '1') {
            return false;
        }

        $session = session();
        if ($session->get(self::AUTH_SESSION_KEY) !== true) {
            return false;
        }

        $authenticatedAt = $session->get(self::AUTH_TIME_SESSION_KEY);
        if (! is_int($authenticatedAt) && ! ctype_digit((string) $authenticatedAt)) {
            return false;
        }

        return time() - (int) $authenticatedAt <= self::AUTH_TTL_SECONDS;
    }
}
