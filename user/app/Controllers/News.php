<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\NoticeModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * 소식 (공지 목록·상세, 설명회 일정)
 */
class News extends BaseController
{
    public function index()
    {
        $notices = new NoticeModel();

        return $this->render('news/index', '소식', [
            'notices' => $notices->paginatePublished(10),
            'pager'   => $notices->pager,
            'events'  => (new EventModel())->getUpcoming(10),
        ], '설명회 일정과 공지');
    }

    public function show(int $id)
    {
        $notice = (new NoticeModel())->findPublished($id) ?? throw PageNotFoundException::forPageNotFound();

        return $this->render('news/show', $notice['title'], ['notice' => $notice]);
    }
}
