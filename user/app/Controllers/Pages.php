<?php

namespace App\Controllers;

use App\Models\SiteSettingModel;

/**
 * 고정 안내 페이지
 */
class Pages extends BaseController
{
    public function privacy()
    {
        return view('pages/privacy', [
            'title'       => '개인정보 처리방침 | KBC아카데미',
            'description' => 'KBC아카데미 개인정보 처리방침',
            'settings'    => (new SiteSettingModel())->getMap(),
        ]);
    }
}
