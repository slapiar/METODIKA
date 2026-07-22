<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationTraces extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_traces` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `trace_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `run_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `dependency_id` BIGINT UNSIGNED NULL,
  `event_type` VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `event_payload_snapshot` LONGTEXT NOT NULL,
  `occurred_at` DATETIME(6) NOT NULL,
  `sequence_number` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdt_trace_reference` (`trace_reference`),
  UNIQUE KEY `uq_qdt_run_sequence` (`run_id`, `sequence_number`),
  KEY `ix_qdt_run_time` (`run_id`, `occurred_at`),
  KEY `ix_qdt_branch` (`branch_id`),
  KEY `ix_qdt_dependency` (`dependency_id`),
  CONSTRAINT `fk_qdt_run` FOREIGN KEY (`run_id`)
    REFERENCES `question_derivation_runs` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_qdt_branch` FOREIGN KEY (`branch_id`)
    REFERENCES `question_derivation_branches` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_qdt_dependency` FOREIGN KEY (`dependency_id`)
    REFERENCES `question_derivation_branch_dependencies` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_traces', true);
    }
}
