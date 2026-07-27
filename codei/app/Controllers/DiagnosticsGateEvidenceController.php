<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniEvidenceModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class DiagnosticsGateEvidenceController extends BaseController
{
    private const TEST_STEP_SESSION_KEY = 'metodika_gate_test_step_id';
    private const TEST_TYPE = 'diagnostic';
    private const TEST_CONTENT = 'Overenie Evidence diagnostického kroku METODIKA';

    public function create(): ResponseInterface
    {
        try {
            $testStepId = $this->positiveSessionId(session()->get(self::TEST_STEP_SESSION_KEY));
            if ($testStepId === null || ! is_array((new IniStepModel())->find($testStepId))) {
                return $this->json([
                    'ok' => false,
                    'errorCode' => 'GATE_TEST_STEP_REQUIRED',
                    'csrfHash' => csrf_hash(),
                ], 409);
            }

            $contentHash = hash('sha256', self::TEST_TYPE . "\0" . self::TEST_CONTENT);
            $evidenceModel = new IniEvidenceModel();
            $existing = $evidenceModel
                ->where('step_id', $testStepId)
                ->where('content_hash', $contentHash)
                ->first();

            $created = ! is_array($existing);
            $evidenceId = $existing['id'] ?? null;
            if ($created) {
                $evidenceId = $evidenceModel->insert([
                    'step_id' => $testStepId,
                    'type' => self::TEST_TYPE,
                    'content' => self::TEST_CONTENT,
                    'content_hash' => $contentHash,
                    'created_at' => gmdate('Y-m-d H:i:s'),
                ]);
            }

            if (! is_numeric($evidenceId) || (int) $evidenceId <= 0) {
                $raceWinner = (new IniEvidenceModel())
                    ->where('step_id', $testStepId)
                    ->where('content_hash', $contentHash)
                    ->first();

                if (is_array($raceWinner) && is_numeric($raceWinner['id'] ?? null)) {
                    $evidenceId = (int) $raceWinner['id'];
                    $created = false;
                }
            }

            if (! is_numeric($evidenceId) || (int) $evidenceId <= 0) {
                return $this->serverError('GATE_TEST_EVIDENCE_CREATE_FAILED');
            }

            return $this->json([
                'ok' => true,
                'created' => $created,
                'step_id' => $testStepId,
                'evidence_id' => (int) $evidenceId,
                'csrfHash' => csrf_hash(),
            ], $created ? 201 : 200);
        } catch (Throwable $exception) {
            log_message('error', 'Create diagnostic GATE evidence failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->serverError('GATE_TEST_EVIDENCE_CREATE_FAILED');
        }
    }

    private function positiveSessionId(mixed $value): ?int
    {
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function serverError(string $errorCode): ResponseInterface
    {
        return $this->json([
            'ok' => false,
            'errorCode' => $errorCode,
            'csrfHash' => csrf_hash(),
        ], 500);
    }

    /** @param array<string, mixed> $payload */
    private function json(array $payload, int $statusCode): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'")
            ->setJSON($payload);
    }
}
