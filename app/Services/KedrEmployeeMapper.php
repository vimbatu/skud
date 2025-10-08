<?php

namespace App\Services;

class KedrEmployeeMapper
{
    public function map(array $data): array
    {
        return [
            'name' => ($data['fam'] ?? '') . ' ' . ($data['name'] ?? ''),
            'position' => $data['appointment'] ?? null,
            'status' => $data['group_name'] === 'Сотрудники' ? 'active' : 'remote',
        ];
    }
}
