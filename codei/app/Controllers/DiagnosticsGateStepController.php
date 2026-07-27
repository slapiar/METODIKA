<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class DiagnosticsGateStepController extends BaseController
{
    private const TEST_SESSION_SESSION_KEY = 'metodika_gate_test_session_id';
    private const TEST_STEP_SESSION_KEY = 'metodika_gate_test_step_id';
    private const TEST_STEP_NUMBER = 1;

    public function create(): ResponseInterface
    {
        try {
            $session = session();
            $testSessionId = $this->positiveSessionId($session->get(self::TEST_SESSION_SESSION_KEY));
            if ($testSessionId === null || ! is_array((new IniSessionModel())->find($testSessionId))) {
                return $this->json([
                    'ok' => false,
                    'errorCode' => 'GATE_TEST_SESSION_REQUIRED',
                    'csrfHash' => csrf_hash(),
                ], 409);
            }

            $stepModel = new IniStepModel();
            $existing = $stepModel
                ->where('session_id', $testSessionId)
                ->where('step_number', self::TEST_STEP_NUMBER)
                ->first();

            $created = ! is_array($existing);
            $stepId = $existing['id'] ?? null;
            if ($created) {
                $stepId = $stepModel->insert([
                    'session_id' => $testSessionId,
                    'step_number' => self::TEST_STEP_NUMBER,
                    'name' => 'Inicializácia',
                    'status' => 'valid',
                    'validated_at' => gmdate('Y-m-d H:i:s'),
                ]);
            }

            if (! is_numeric($stepId) || (int) $stepId <= 0) {
                $raceWinner = (new IniStepModel())
                    ->where('session_id', $testSessionId)
                    ->where('step_number', self::TEST_STEP_NUMBER)
                    ->first();

                if (is_array($raceWinner) && is_numeric($raceWinner['id'] ?? null)) {
                    $stepId = (int) $raceWinner['id'];
                    $created = false;
                }
            }

            if (! is_numeric($stepId) || (int) $stepId <= 0) {
                return $this->serverError('GATE_TEST_STEP_CREATE_FAILED');
            }

            $session->set(self::TEST_STEP_SESSION_KEY, (int) $stepId);

            return $this->json([
                'ok' => true,
                'created' => $created,
                'session_id' => $testSessionId,
                'step_id' => (int) $stepId,
                'csrfHash' => csrf_hash(),
            ], $created ? 201 : 200);
        } catch (Throwable $exception) {
            log_message('error', 'Create diagnostic GATE step failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->serverError('GATE_TEST_STEP_CREATE_FAILED');
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
