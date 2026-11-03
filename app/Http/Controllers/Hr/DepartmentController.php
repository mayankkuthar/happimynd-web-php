<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Department picker (API_SPEC §6). There is no rename or delete: records store
 * the label itself, so renaming would strand existing rows.
 */
class DepartmentController extends HrController
{
    public function index(): JsonResponse
    {
        return $this->ok(['departments' => $this->departments()]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $this->jsonBody($request);
        $raw = $body['name'] ?? null;

        if ($raw === null || $raw === '') {
            return $this->missing('name', 'body');
        }

        $name = trim((string) preg_replace('/\s+/u', ' ', (string) $raw));

        if ($name === '' || mb_strlen($name) > 40) {
            return $this->invalid('name', 'Department name must be between 1 and 40 characters');
        }

        $existing = $this->db()->table('departments')
            ->whereRaw('LOWER(`name`) = ?', [mb_strtolower($name)])
            ->value('name');

        if ($existing !== null) {
            return $this->detail("Department '" . $existing . "' already exists.", 409);
        }

        try {
            $this->db()->insert('INSERT IGNORE INTO `departments` (`name`,`created_at`) VALUES (?, NOW(3))', [$name]);
        } catch (\Throwable $e) {
            return $this->detail('Internal Server Error', 500);
        }

        $this->db()->insert(
            'INSERT INTO `audit_log` (`actor`,`action`,`entity_type`,`entity_id`,`detail`,`created_at`) '
            . 'VALUES (?,?,?,?,CAST(? AS JSON),NOW(3))',
            [$this->actor(), 'department.create', 'department', $name, json_encode(['name' => $name])]
        );

        return $this->ok(['name' => $name, 'departments' => $this->departments()], 201);
    }
}
