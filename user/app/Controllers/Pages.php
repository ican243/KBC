<?php

namespace App\Controllers;

use App\Models\InstructorModel;

/**
 * 고정 안내 페이지 (아카데미 소개, 진로 연계, 개인정보 처리방침)
 * 문구는 기획안·협업 제안서 기준. 공개 문구 규칙은 기획안 8장을 따른다.
 */
class Pages extends BaseController
{
    public function about()
    {
        return $this->render('pages/about', '아카데미 소개', [
            'fields' => (new InstructorModel())->getPublished(),
        ], '개인방송·커머스방송·협업형 라이브 방송 진행자를 양성하는 실습 중심 교육');
    }

    public function career()
    {
        return $this->render('pages/career', '진로 연계', [], '포트폴리오, 업체 설명회, 면접·현장 테스트로 이어지는 진로 연계 절차');
    }

    public function privacy()
    {
        return $this->render('pages/privacy', '개인정보 처리방침', [], 'KBC아카데미 개인정보 처리방침');
    }
}
