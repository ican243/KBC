<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EventModel;
use App\Models\InstructorModel;
use App\Models\NoticeModel;
use App\Models\SiteSettingModel;

class Home extends BaseController
{
    public function index()
    {
        return view('home/index', [
            'title'       => 'KBC아카데미 | 방송 진행을 배우고, 실습으로 완성합니다',
            'description' => '개인방송·커머스방송·협업형 라이브 진행을 위한 댄스, 보컬, 화술, 촬영, 편집, SNS 실습',
            'settings'    => (new SiteSettingModel())->getMap(),
            'courses'     => (new CourseModel())->getPublished(),
            'instructors' => (new InstructorModel())->getPublished(),
            'events'      => (new EventModel())->getUpcoming(3),
            'notices'     => (new NoticeModel())->getLatest(3),
        ]);
    }
}
