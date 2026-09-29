<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 사이트 설정 (관리자)
 */
class SiteSettingModel extends Model
{
    protected $table         = 'site_settings';
    protected $primaryKey    = 'setting_key';
    protected $returnType    = 'array';
    protected $allowedFields = ['value', 'updated_at'];

    /**
     * ['setting_key' => ['label' => ..., 'value' => ...]]
     */
    public function allByKey(): array
    {
        $rows = [];
        foreach ($this->orderBy('sort_order')->findAll() as $row) {
            $rows[$row['setting_key']] = $row;
        }

        return $rows;
    }
}
