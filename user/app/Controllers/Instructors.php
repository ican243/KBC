<?php

namespace App\Controllers;

use App\Models\InstructorModel;

/**
 * 강사진 (실명·사진·프로필·경력은 게시 동의한 강사만 - InstructorModel 에서 처리)
 */
class Instructors extends BaseController
{
    public function index()
    {
        return $this->render('instructors/index', '강사진', [
            'instructors' => (new InstructorModel())->getPublished(),
        ], '댄스·보컬·메이크업·방송 제작·SNS 분야별 강사진');
    }
}
