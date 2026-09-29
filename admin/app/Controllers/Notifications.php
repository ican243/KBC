<?php

namespace App\Controllers;

use App\Models\NotificationLogModel;

/**
 * 메일 알림 발송 기록
 */
class Notifications extends BaseController
{
    public function index()
    {
        $model = new NotificationLogModel();
        $only  = $this->request->getGet('status') === 'failed' ? 'failed' : '';

        if ($only !== '') {
            $model->where('status', $only);
        }

        return view('notifications/index', [
            'title' => '알림 기록',
            'rows'  => $model->orderBy('id', 'DESC')->paginate(30),
            'pager' => $model->pager,
            'only'  => $only,
        ]);
    }
}
