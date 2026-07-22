<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationRunResults extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_run_results` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `result_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `run_id` BIGINT UNSIGNED NOT NULL,
  `request_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `response_target_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `run_mode` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `run_state` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `candidate_count` INT UNSIGNED NOT NULL,
  `stopped_branch_count` INT UNSIGNED NOT NULL,
  `decomposition_branch_count` INT UNSIGNED NOT NULL,
  `blocked_branch_count` INT UNSIGNED NOT NULL,
  `total_branch_count` INT UNSIGNED NOT NULL,
  `stop_reason_snapshot` LONGTEXT NULL,
  `failed_control_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `summary_snapshot` LONGTEXT NOT NULL,
  `completed_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdrr_result_reference` (`result_reference`),
  UNIQUE KEY `uq_qdrr_run_id` (`run_id`),
  KEY `ix_qdrr_request_reference` (`request_reference`),
  CONSTRAINT `fk_qdrr_run` FOREIGN KEY (`run_id`)
    REFERENCES `question_derivation_runs` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_run_results', true);
    }
}
