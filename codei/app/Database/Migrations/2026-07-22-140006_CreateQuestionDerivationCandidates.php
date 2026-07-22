<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationCandidates extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_candidates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `candidate_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `branch_id` BIGINT UNSIGNED NOT NULL,
  `run_id` BIGINT UNSIGNED NOT NULL,
  `candidate_content_snapshot` LONGTEXT NOT NULL,
  `primary_dimension` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `meaning_of_one_snapshot` LONGTEXT NOT NULL,
  `meaning_of_zero_snapshot` LONGTEXT NOT NULL,
  `required_question_context_snapshot` LONGTEXT NOT NULL,
  `intended_applicability_scope_snapshot` LONGTEXT NOT NULL,
  `created_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdc_candidate_reference` (`candidate_reference`),
  UNIQUE KEY `uq_qdc_branch_id` (`branch_id`),
  KEY `ix_qdc_run` (`run_id`),
  CONSTRAINT `fk_qdc_branch` FOREIGN KEY (`branch_id`)
    REFERENCES `question_derivation_branches` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_qdc_run` FOREIGN KEY (`run_id`)
    REFERENCES `question_derivation_runs` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_candidates', true);
    }
}
