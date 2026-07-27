<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\IniEvidenceModel;
use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class GateSupervisor extends BaseController
{
    private const STEP_STATUSES = ['pending', 'valid', 'invalid'];

    public function createSession(): ResponseInterface
    {
        try {
            $data = $this->jsonObject();
            if ($data === null) {
                return $this->validationError();
            }

            $projectName = $this->validatedText($data['project_name'] ?? null, 1, 120);
            $agentName = $this->validatedText($data['agent_name'] ?? null, 1, 120);
            if ($projectName === null || $agentName === null) {
                return $this->validationError();
            }

            $sessionModel = new IniSessionModel();
            $sessionId = $sessionModel->insert([
                'project_name' => $projectName,
                'agent_name' => $agentName,
                'gate_state' => 'locked',
            ]);

            if (! is_numeric($sessionId)) {
                return $this->serverError('GATE_SESSION_CREATE_FAILED');
            }

            return $this->json([
                'ok' => true,
                'created' => true,
                'session_id' => (int) $sessionId,
                'csrfHash' => csrf_hash(),
            ], 201);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_SESSION_CREATE_FAILED');
        }
    }

    public function getSession(string $id): ResponseInterface
    {
        $sessionId = $this->positiveId($id);
        if ($sessionId === null) {
            return $this->notFound();
        }

        try {
            $session = (new IniSessionModel())->find($sessionId);
            if (! is_array($session)) {
                return $this->notFound();
            }

            return $this->json([
                'ok' => true,
                'session' => $this->publicSession($session),
            ]);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_SESSION_READ_FAILED');
        }
    }

    public function updateStep(string $sessionIdValue): ResponseInterface
    {
        $sessionId = $this->positiveId($sessionIdValue);
        if ($sessionId === null) {
            return $this->notFound();
        }

        try {
            if (! is_array((new IniSessionModel())->find($sessionId))) {
                return $this->notFound();
            }

            $data = $this->jsonObject();
            if ($data === null) {
                return $this->validationError();
            }

            $stepNumber = $this->boundedInteger($data['step_number'] ?? null, 1, 15);
            $name = $this->validatedText($data['name'] ?? null, 1, 120);
            $status = is_string($data['status'] ?? null) ? trim($data['status']) : '';

            if ($stepNumber === null || $name === null || ! in_array($status, self::STEP_STATUSES, true)) {
                return $this->validationError();
            }

            $stepModel = new IniStepModel();
            $existing = $stepModel
                ->where('session_id', $sessionId)
                ->where('step_number', $stepNumber)
                ->first();

            $payload = [
                'session_id' => $sessionId,
                'step_number' => $stepNumber,
                'name' => $name,
                'status' => $status,
                'validated_at' => $status === 'valid' ? gmdate('Y-m-d H:i:s') : null,
            ];

            $created = ! is_array($existing);
            if ($created) {
                $stepId = $stepModel->insert($payload);
            } else {
                $stepId = (int) ($existing['id'] ?? 0);
                $stepModel->update($stepId, $payload);
            }

            if (! is_numeric($stepId) || (int) $stepId <= 0) {
                $raceWinner = (new IniStepModel())
                    ->where('session_id', $sessionId)
                    ->where('step_number', $stepNumber)
                    ->first();

                if (is_array($raceWinner) && is_numeric($raceWinner['id'] ?? null)) {
                    $stepId = (int) $raceWinner['id'];
                    $created = false;
                    (new IniStepModel())->update($stepId, $payload);
                }
            }

            if (! is_numeric($stepId) || (int) $stepId <= 0) {
                return $this->serverError('GATE_STEP_WRITE_FAILED');
            }

            $state = $this->calculateGateState($sessionId);
            (new IniSessionModel())->update($sessionId, ['gate_state' => $state]);

            return $this->json([
                'ok' => true,
                'created' => $created,
                'step_id' => (int) $stepId,
                'state' => $state,
                'csrfHash' => csrf_hash(),
            ], $created ? 201 : 200);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_STEP_WRITE_FAILED');
        }
    }

    public function getSteps(string $sessionIdValue): ResponseInterface
    {
        $sessionId = $this->positiveId($sessionIdValue);
        if ($sessionId === null) {
            return $this->notFound();
        }

        try {
            if (! is_array((new IniSessionModel())->find($sessionId))) {
                return $this->notFound();
            }

            $steps = (new IniStepModel())
                ->where('session_id', $sessionId)
                ->orderBy('step_number', 'ASC')
                ->findAll(15);

            return $this->json([
                'ok' => true,
                'steps' => array_map([$this, 'publicStep'], $steps),
            ]);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_STEPS_READ_FAILED');
        }
    }

    public function addEvidence(string $stepIdValue): ResponseInterface
    {
        $stepId = $this->positiveId($stepIdValue);
        if ($stepId === null) {
            return $this->notFound();
        }

        try {
            if (! is_array((new IniStepModel())->find($stepId))) {
                return $this->notFound();
            }

            $data = $this->jsonObject();
            if ($data === null) {
                return $this->validationError();
            }

            $type = is_string($data['type'] ?? null) ? trim($data['type']) : '';
            $content = $this->validatedText($data['content'] ?? null, 1, 4000);
            if (
                preg_match('/^[a-z0-9._-]{1,64}$/D', $type) !== 1
                || $content === null
            ) {
                return $this->validationError();
            }

            $contentHash = hash('sha256', $type . "\0" . $content);
            $evidenceModel = new IniEvidenceModel();
            $existing = $evidenceModel
                ->where('step_id', $stepId)
                ->where('content_hash', $contentHash)
                ->first();

            $created = ! is_array($existing);
            $evidenceId = $existing['id'] ?? null;
            if ($created) {
                $evidenceId = $evidenceModel->insert([
                    'step_id' => $stepId,
                    'type' => $type,
                    'content' => $content,
                    'content_hash' => $contentHash,
                    'created_at' => gmdate('Y-m-d H:i:s'),
                ]);
            }

            if (! is_numeric($evidenceId) || (int) $evidenceId <= 0) {
                $raceWinner = (new IniEvidenceModel())
                    ->where('step_id', $stepId)
                    ->where('content_hash', $contentHash)
                    ->first();

                if (is_array($raceWinner) && is_numeric($raceWinner['id'] ?? null)) {
                    $evidenceId = (int) $raceWinner['id'];
                    $created = false;
                }
            }

            if (! is_numeric($evidenceId) || (int) $evidenceId <= 0) {
                return $this->serverError('GATE_EVIDENCE_WRITE_FAILED');
            }

            return $this->json([
                'ok' => true,
                'created' => $created,
                'evidence_id' => (int) $evidenceId,
                'csrfHash' => csrf_hash(),
            ], $created ? 201 : 200);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_EVIDENCE_WRITE_FAILED');
        }
    }

    public function getEvidence(string $stepIdValue): ResponseInterface
    {
        $stepId = $this->positiveId($stepIdValue);
        if ($stepId === null) {
            return $this->notFound();
        }

        try {
            if (! is_array((new IniStepModel())->find($stepId))) {
                return $this->notFound();
            }

            $evidence = (new IniEvidenceModel())
                ->where('step_id', $stepId)
                ->orderBy('id', 'ASC')
                ->findAll(100);

            return $this->json([
                'ok' => true,
                'evidence' => array_map([$this, 'publicEvidence'], $evidence),
            ]);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_EVIDENCE_READ_FAILED');
        }
    }

    public function getGateState(string $sessionIdValue): ResponseInterface
    {
        $sessionId = $this->positiveId($sessionIdValue);
        if ($sessionId === null) {
            return $this->notFound();
        }

        try {
            if (! is_array((new IniSessionModel())->find($sessionId))) {
                return $this->notFound();
            }

            return $this->json([
                'ok' => true,
                'state' => $this->calculateGateState($sessionId),
            ]);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_STATE_READ_FAILED');
        }
    }

    public function getAllSessions(): ResponseInterface
    {
        try {
            $sessions = (new IniSessionModel())
                ->orderBy('id', 'DESC')
                ->findAll(100);

            return $this->json([
                'ok' => true,
                'sessions' => array_map([$this, 'publicSession'], $sessions),
            ]);
        } catch (Throwable $exception) {
            return $this->caught($exception, 'GATE_SESSIONS_READ_FAILED');
        }
    }

    private function calculateGateState(int $sessionId): string
    {
        $steps = (new IniStepModel())
            ->where('session_id', $sessionId)
            ->findAll(15);

        $valid = 0;
        foreach ($steps as $step) {
            if (($step['status'] ?? null) === 'invalid') {
                return 'locked';
            }

            if (($step['status'] ?? null) === 'valid') {
                $valid++;
            }
        }

        return $valid >= 15 ? 'open' : 'verifying';
    }

    /** @return array<string, mixed>|null */
    private function jsonObject(): ?array
    {
        $data = $this->request->getJSON(true);

        return is_array($data) ? $data : null;
    }

    private function positiveId(mixed $value): ?int
    {
        if (! is_numeric($value) || (int) $value <= 0 || (string) (int) $value !== (string) $value) {
            return null;
        }

        return (int) $value;
    }

    private function boundedInteger(mixed $value, int $minimum, int $maximum): ?int
    {
        if (! is_int($value) && ! (is_string($value) && ctype_digit($value))) {
            return null;
        }

        $integer = (int) $value;

        return $integer >= $minimum && $integer <= $maximum ? $integer : null;
    }

    private function validatedText(mixed $value, int $minimum, int $maximum): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        $length = mb_strlen($value, 'UTF-8');

        return $length >= $minimum && $length <= $maximum ? $value : null;
    }

    /** @param array<string, mixed> $session */
    private function publicSession(array $session): array
    {
        return [
            'id' => (int) ($session['id'] ?? 0),
            'project_name' => (string) ($session['project_name'] ?? ''),
            'agent_name' => (string) ($session['agent_name'] ?? ''),
            'gate_state' => (string) ($session['gate_state'] ?? 'locked'),
            'created_at' => isset($session['created_at']) ? (string) $session['created_at'] : null,
        ];
    }

    /** @param array<string, mixed> $step */
    private function publicStep(array $step): array
    {
        return [
            'id' => (int) ($step['id'] ?? 0),
            'session_id' => (int) ($step['session_id'] ?? 0),
            'step_number' => (int) ($step['step_number'] ?? 0),
            'name' => (string) ($step['name'] ?? ''),
            'status' => (string) ($step['status'] ?? 'pending'),
            'validated_at' => isset($step['validated_at']) ? (string) $step['validated_at'] : null,
        ];
    }

    /** @param array<string, mixed> $evidence */
    private function publicEvidence(array $evidence): array
    {
        return [
            'id' => (int) ($evidence['id'] ?? 0),
            'step_id' => (int) ($evidence['step_id'] ?? 0),
            'type' => (string) ($evidence['type'] ?? ''),
            'content' => (string) ($evidence['content'] ?? ''),
            'created_at' => isset($evidence['created_at']) ? (string) $evidence['created_at'] : null,
        ];
    }

    private function validationError(): ResponseInterface
    {
        return $this->json([
            'ok' => false,
            'errorCode' => 'VALIDATION_FAILED',
            'csrfHash' => csrf_hash(),
        ], 422);
    }

    private function notFound(): ResponseInterface
    {
        return $this->json([
            'ok' => false,
            'errorCode' => 'NOT_FOUND',
        ], 404);
    }

    private function caught(Throwable $exception, string $errorCode): ResponseInterface
    {
        log_message('error', 'GATE operation failed [{code}]: {message}', [
            'code' => $errorCode,
            'message' => $exception->getMessage(),
        ]);

        return $this->serverError($errorCode);
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
    private function json(array $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'")
            ->setJSON($payload);
    }
}
