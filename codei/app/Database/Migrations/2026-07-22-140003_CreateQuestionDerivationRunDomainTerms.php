<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationRunDomainTerms extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_run_domain_terms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `run_id` BIGINT UNSIGNED NOT NULL,
  `domain_term_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `canonical_order` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdrdt_term` (`run_id`, `domain_term_reference`),
  UNIQUE KEY `uq_qdrdt_order` (`run_id`, `canonical_order`),
  CONSTRAINT `fk_qdrdt_run` FOREIGN KEY (`run_id`)
    REFERENCES `question_derivation_runs` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_run_domain_terms', true);
    }
}
