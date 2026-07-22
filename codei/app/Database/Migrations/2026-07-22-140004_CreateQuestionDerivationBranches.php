<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationBranches extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_branches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `run_id` BIGINT UNSIGNED NOT NULL,
  `subject_manifestation_snapshot` LONGTEXT NOT NULL,
  `branch_state` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `stop_reason_snapshot` LONGTEXT NULL,
  `failed_control_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `created_at` DATETIME(6) NOT NULL,
  `completed_at` DATETIME(6) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdb_branch_reference` (`branch_reference`),
  KEY `ix_qdb_run_state` (`run_id`, `branch_state`),
  CONSTRAINT `fk_qdb_run` FOREIGN KEY (`run_id`)
    REFERENCES `question_derivation_runs` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_branches', true);
    }
}
