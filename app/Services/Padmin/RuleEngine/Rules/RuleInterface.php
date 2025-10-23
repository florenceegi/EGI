<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

/**
 * Base Rule Interface
 *
 * All rule checkers must implement this interface.
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 */
interface RuleInterface {
    /**
     * Check code for rule violations
     *
     * @param array $ast AST nodes from parser
     * @param string $filePath File being checked
     * @param string $code Raw code content
     * @return array Array of violations found
     */
    public function check(array $ast, string $filePath, string $code): array;

    /**
     * Get rule name
     *
     * @return string Rule identifier
     */
    public function getName(): string;

    /**
     * Get rule description
     *
     * @return string Human-readable description
     */
    public function getDescription(): string;
}
