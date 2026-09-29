<?php

namespace App\Controllers;

use App\Models\EventModel;
use CodeIgniter\Model;

/**
 * 설명회 일정 관리 (지난 일정은 홈페이지에서 자동으로 빠짐)
 */
class Events extends ContentController
{
    protected string $path  = 'events';
    protected string $label = '설명회';

    protected function model(): Model
    {
        return new EventModel();
    }

    protected function rules(?int $id): array
    {
        return [
            'title'       => ['label' => '제목', 'rules' => 'required|max_length[200]'],
            'event_at'    => ['label' => '일시', 'rules' => 'required|valid_date[Y-m-d\TH:i]'],
            'place'       => ['label' => '장소', 'rules' => 'permit_empty|max_length[200]'],
            'description' => ['label' => '설명', 'rules' => 'permit_empty|max_length[2000]'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        return [
            'title'        => trim($post['title']),
            'event_at'     => self::datetime($post, 'event_at'),
            'place'        => self::nullable($post, 'place'),
            'description'  => self::nullable($post, 'description'),
            'is_published' => self::flag($post, 'is_published'),
        ];
    }

    protected function columns(): array
    {
        return [
            '제목' => static fn (array $r): string => esc($r['title']),
            '일시' => static fn (array $r): string => dt($r['event_at'])
                . (strtotime($r['event_at']) < time() ? ' <span class="badge badge-hold">지난 일정</span>' : ''),
            '장소' => static fn (array $r): string => esc($r['place'] ?? '-'),
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 1];
    }
}
