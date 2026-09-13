<?php
declare(strict_types=1);

/**
 * Computes BIR withholding tax using configurable tax_tables.
 * Uses semi-monthly brackets by default (Philippines).
 * Rates are illustrative; verify official values.
 */
final class TaxService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function compute(string $taxType, float $taxableIncome, string $asOfDate): float
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM tax_tables
             WHERE tax_type = :t
               AND is_active = 1
               AND effective_from <= :d
               AND (effective_to IS NULL OR effective_to >= :d)
               AND :i BETWEEN income_min AND income_max
             ORDER BY income_min ASC LIMIT 1'
        );
        $stmt->execute([':t' => $taxType, ':d' => $asOfDate, ':i' => $taxableIncome]);
        $row = $stmt->fetch();
        if (!$row) {
            return 0.0;
        }
        $excess = max(0, $taxableIncome - (float) $row['excess_over']);
        $tax = (float) $row['base_tax'] + ($excess * (float) $row['excess_rate']);
        return round(max(0, $tax), 2);
    }
}