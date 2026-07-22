<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationBranchDependencies extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_branch_dependencies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dependency_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `dependent_branch_id` BIGINT UNSIGNED NOT NULL,
  `prerequisite_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `dependency_type` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `justification_snapshot` LONGTEXT NOT NULL,
  `determined_by_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `validation_control_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdbd_dependency_reference` (`dependency_reference`),
  KEY `ix_qdbd_branch` (`dependent_branch_id`),
  CONSTRAINT `fk_qdbd_branch` FOREIGN KEY (`dependent_branch_id`)
    REFERENCES `question_derivation_branches` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_branch_dependencies', true);
    }
}
