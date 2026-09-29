<?php

namespace App\Controllers;

use App\Models\CourseModel;

/**
 * 상담 문의 페이지 (?course=과정번호 로 들어오면 그 과정을 미리 선택)
 */
class Contact extends BaseController
{
    public function index()
    {
        $courses  = (new CourseModel())->getPublished();
        $selected = (string) $this->request->getGet('course');

        return $this->render('contact/index', '상담 문의', [
            'courses'        => $courses,
            'selectedCourse' => in_array($selected, array_map('strval', array_column($courses, 'id')), true) ? $selected : '',
        ], '사전 상담 신청');
    }
}
