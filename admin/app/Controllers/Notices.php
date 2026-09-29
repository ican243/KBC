<?php

namespace App\Controllers;

use App\Models\NoticeModel;
use CodeIgniter\Model;

/**
 * 공지 관리 (게시일이 지나야 홈페이지에 표시 → 예약 게시 가능)
 */
class Notices extends ContentController
{
    protected string $path  = 'notices';
    protected string $label = '공지';

    protected function model(): Model
    {
        return new NoticeModel();
    }

    protected function rules(?int $id): array
    {
        return [
            'title'        => ['label' => '제목', 'rules' => 'required|max_length[200]'],
            'content'      => ['label' => '내용', 'rules' => 'required|max_length[10000]'],
            'published_at' => ['label' => '게시일', 'rules' => 'required|valid_date[Y-m-d\TH:i]'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        $data = [
            'title'        => trim($post['title']),
            'content'      => trim($post['content']),
            'published_at' => self::datetime($post, 'published_at'),
            'is_published' => self::flag($post, 'is_published'),
        ];

        if ($isNew) {
            $data['admin_id'] = session('admin_id');
        }

        return $data;
    }

    protected function columns(): array
    {
        return [
            '제목'   => static fn (array $r): string => esc($r['title']),
            '게시일' => static fn (array $r): string => dt($r['published_at'])
                . (strtotime((string) $r['published_at']) > time() ? ' <span class="badge badge-contacted">예약</span>' : ''),
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 1, 'published_at' => date('Y-m-d H:i:s')];
    }
}
