<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniEvidenceModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class DiagnosticsGateEvidenceController extends BaseController
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;
    private const TEST_STEP_ID = 1;
    private const TEST_TYPE = 'diagnostic';
    private const TEST_CONTENT = 'Overenie Evidence kroku 1 cez diagnosticku stranku';

    public function create(): ResponseInterface
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(404);
        }

        try {
            $stepModel = new IniStepModel();
            if ($stepModel->find(self::TEST_STEP_ID) === null) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'ok' => false,
                        'message' => 'Testovaci krok 1 neexistuje.',
                    ]);
            }

            $evidenceModel = new IniEvidenceModel();
            $existing = $evidenceModel
                ->where('step_id', self::TEST_STEP_ID)
                ->where('type', self::TEST_TYPE)
                ->where('content', self::TEST_CONTENT)
                ->first();

            $created = false;
            $evidenceId = $existing['id'] ?? null;

            if ($existing === null) {
                $evidenceId = $evidenceModel->insert([
                    'step_id' => self::TEST_STEP_ID,
                    'type' => self::TEST_TYPE,
                    'content' => self::TEST_CONTENT,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $created = true;
            }

            $evidence = $evidenceModel
                ->where('step_id', self::TEST_STEP_ID)
                ->orderBy('id', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'ok' => true,
                'created' => $created,
                'evidence_id' => $evidenceId,
                'count' => count($evidence),
                'evidence' => $evidence,
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Create test evidence failed: {message}', [
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
