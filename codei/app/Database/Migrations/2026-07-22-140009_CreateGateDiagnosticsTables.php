<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateGateDiagnosticsTables extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ini_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_name` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `agent_name` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `gate_state` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'locked',
  `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);

        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ini_steps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `step_number` TINYINT UNSIGNED NOT NULL,
  `name` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'pending',
  `validated_at` DATETIME(6) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ini_steps_session_number` (`session_id`, `step_number`),
  KEY `ix_ini_steps_session_status` (`session_id`, `status`),
  CONSTRAINT `fk_ini_steps_session` FOREIGN KEY (`session_id`)
    REFERENCES `ini_sessions` (`id`)
    ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);

        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ini_evidence` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `step_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content_hash` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ini_evidence_step_hash` (`step_id`, `content_hash`),
  KEY `ix_ini_evidence_step_created` (`step_id`, `created_at`),
  CONSTRAINT `fk_ini_evidence_step` FOREIGN KEY (`step_id`)
    REFERENCES `ini_steps` (`id`)
    ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);

        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ini_gate_state` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `state` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `updated_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_ini_gate_state_session_updated` (`session_id`, `updated_at`),
  CONSTRAINT `fk_ini_gate_state_session` FOREIGN KEY (`session_id`)
    REFERENCES `ini_sessions` (`id`)
    ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);

        $this->hardenPreExistingTables();
    }

    public function down(): void
    {
        // Zámerne nedestruktívne: tabuľky GATE mohli existovať ešte pred
        // zavedením migračného kontraktu. Automatický rollback preto nesmie
        // zmazať historické session, kroky ani Evidence.
    }

    private function hardenPreExistingTables(): void
    {
        if (! $this->db->fieldExists('content_hash', 'ini_evidence')) {
            $this->db->query(
                'ALTER TABLE `ini_evidence` ADD `content_hash` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL AFTER `content`',
            );
            $this->db->query(
                "UPDATE `ini_evidence` SET `content_hash` = SHA2(CONCAT(`type`, CHAR(0), `content`), 256) WHERE `content_hash` IS NULL",
            );
            $this->db->query(
                'ALTER TABLE `ini_evidence` MODIFY `content_hash` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            );
        }

        if (! $this->indexExists('ini_steps', 'uq_ini_steps_session_number')) {
            $this->db->query(
                'ALTER TABLE `ini_steps` ADD UNIQUE KEY `uq_ini_steps_session_number` (`session_id`, `step_number`)',
            );
        }

        if (! $this->indexExists('ini_evidence', 'uq_ini_evidence_step_hash')) {
            $this->db->query(
                'ALTER TABLE `ini_evidence` ADD UNIQUE KEY `uq_ini_evidence_step_hash` (`step_id`, `content_hash`)',
            );
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $row = $this->db->query(
            <<<'SQL'
SELECT COUNT(*) AS `index_count`
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = ?
  AND INDEX_NAME = ?
SQL,
            [$table, $index],
        )->getRowArray();

        return is_array($row) && (int) ($row['index_count'] ?? 0) > 0;
    }
}
