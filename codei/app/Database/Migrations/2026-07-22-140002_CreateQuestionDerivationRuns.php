<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationRuns extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_runs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `derivation_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `request_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `response_target_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `request_source_snapshot` LONGTEXT NOT NULL,
  `source_question_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `derivation_subject_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `purpose_snapshot` LONGTEXT NOT NULL,
  `context_snapshot` LONGTEXT NOT NULL,
  `scope_snapshot` LONGTEXT NOT NULL,
  `actor_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `authority_context_snapshot` LONGTEXT NOT NULL,
  `run_mode` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `gate_state` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  `run_state` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  `stop_reason_snapshot` LONGTEXT NULL,
  `failed_control_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `started_at` DATETIME(6) NOT NULL,
  `completed_at` DATETIME(6) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdr_reservation_id` (`reservation_id`),
  UNIQUE KEY `uq_qdr_derivation_reference` (`derivation_reference`),
  UNIQUE KEY `uq_qdr_request_reference` (`request_reference`),
  CONSTRAINT `fk_qdr_reservation` FOREIGN KEY (`reservation_id`)
    REFERENCES `question_derivation_request_reservations` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_runs', true);
    }
}
