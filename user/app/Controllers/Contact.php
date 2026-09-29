<?php

namespace App\Controllers;

use App\Models\CourseModel;

/**
 * 상담 문의 페이지
 * ?course=과정번호 → 그 과정을 미리 선택
 * &from=course     → 과정 상세의 상담 버튼으로 들어온 것 (통계: 과정 페이지 → 상담 전환)
 */
class Contact extends BaseController
{
    public function index()
    {
        $courses  = (new CourseModel())->getPublished();
        $selected = (string) $this->request->getGet('course');
        $slugs    = array_column($courses, 'slug', 'id');
        $selected = isset($slugs[(int) $selected]) && ctype_digit($selected) ? $selected : '';

        $entryPage = $selected !== '' && $this->request->getGet('from') === 'course'
            ? 'courses/' . $slugs[(int) $selected]
            : 'contact';

        return $this->render('contact/index', '상담 문의', [
            'courses'        => $courses,
            'selectedCourse' => $selected,
            'entryPage'      => $entryPage,
        ], '사전 상담 신청');
    }
}
