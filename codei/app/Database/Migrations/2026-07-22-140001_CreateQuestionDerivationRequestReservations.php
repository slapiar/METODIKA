<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateQuestionDerivationRequestReservations extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `question_derivation_request_reservations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `payload_fingerprint` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `derivation_reference` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `reservation_state` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `reserved_at` DATETIME(6) NOT NULL,
  `updated_at` DATETIME(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qdrr_request_reference` (`request_reference`),
  UNIQUE KEY `uq_qdrr_derivation_reference` (`derivation_reference`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin
SQL);
    }

    public function down(): void
    {
        $this->forge->dropTable('question_derivation_request_reservations', true);
    }
}
