<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table      = 'site_settings';
    protected $primaryKey = 'setting_key';
    protected $returnType = 'array';

    /**
     * ['setting_key' => 'value'] 형태로 반환. 빈 문자열은 null 로 통일한다.
     */
    public function getMap(): array
    {
        $map = [];

        foreach ($this->select('setting_key, value')->findAll() as $row) {
            $value                    = $row['value'] !== null ? trim($row['value']) : null;
            $map[$row['setting_key']] = $value === '' ? null : $value;
        }

        return $map;
    }
}
