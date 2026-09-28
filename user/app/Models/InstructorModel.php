<?php

namespace App\Models;

use CodeIgniter\Model;

class InstructorModel extends Model
{
    protected $table      = 'instructors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * 홈페이지에 노출하는 강사(분야)
     * 게시 동의(consent_at)가 없으면 실명·사진·경력을 비운다.
     */
    public function getPublished(): array
    {
        $rows = $this->where('is_published', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($rows as &$row) {
            if ($row['consent_at'] === null) {
                $row['name']       = null;
                $row['photo_path'] = null;
                $row['career']     = null;
            }
        }

        return $rows;
    }
}
