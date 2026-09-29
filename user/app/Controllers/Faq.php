<?php

namespace App\Controllers;

use App\Models\FaqModel;

/**
 * 자주 묻는 질문
 */
class Faq extends BaseController
{
    public function index()
    {
        return $this->render('faq/index', '자주 묻는 질문', [
            'groups' => (new FaqModel())->getPublishedGrouped(),
        ], '수강, 교습비·환불, 연령 등 자주 묻는 질문');
    }
}
