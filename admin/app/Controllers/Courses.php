<?php

namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Model;

/**
 * 교육과정 관리 (삭제 불가, 비공개만)
 */
class Courses extends ContentController
{
    protected string $path     = 'courses';
    protected string $label    = '교육과정';
    protected bool $deletable  = false;
    protected bool $sortable   = true;

    protected function model(): Model
    {
        return new CourseModel();
    }

    protected function rules(?int $id): array
    {
        $unique = 'is_unique[courses.slug' . ($id !== null ? ',id,' . $id : '') . ']';

        return [
            'title'             => ['label' => '과정명', 'rules' => 'required|max_length[100]'],
            'slug'              => ['label' => '주소용 이름', 'rules' => 'required|alpha_dash|max_length[50]|' . $unique],
            'summary'           => ['label' => '한 줄 소개', 'rules' => 'permit_empty|max_length[255]'],
            'target'            => ['label' => '대상', 'rules' => 'permit_empty|max_length[255]'],
            'duration'          => ['label' => '기간', 'rules' => 'permit_empty|max_length[50]'],
            'sessions_per_week' => ['label' => '주당 수업', 'rules' => 'permit_empty|max_length[50]'],
            'fields'            => ['label' => '교육 분야', 'rules' => 'permit_empty|max_length[2000]'],
            'assignments'       => ['label' => '실습 과제', 'rules' => 'permit_empty|max_length[2000]'],
            'outputs'           => ['label' => '수료 산출물', 'rules' => 'permit_empty|max_length[2000]'],
            'fee_text'          => ['label' => '교습비 안내', 'rules' => 'permit_empty|max_length[255]'],
            'sort_order'        => ['label' => '순서', 'rules' => 'permit_empty|integer'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        return [
            'title'             => trim($post['title']),
            'slug'              => trim($post['slug']),
            'summary'           => self::nullable($post, 'summary'),
            'target'            => self::nullable($post, 'target'),
            'duration'          => self::nullable($post, 'duration'),
            'sessions_per_week' => self::nullable($post, 'sessions_per_week'),
            'fields'            => self::nullable($post, 'fields'),
            'assignments'       => self::nullable($post, 'assignments'),
            'outputs'           => self::nullable($post, 'outputs'),
            'fee_text'          => self::nullable($post, 'fee_text'),
            'is_confirmed'      => self::flag($post, 'is_confirmed'),
            'is_published'      => self::flag($post, 'is_published'),
            'sort_order'        => (int) ($post['sort_order'] ?? 0),
        ];
    }

    protected function columns(): array
    {
        return [
            '과정명' => static fn (array $r): string => esc($r['title']) . ' <small class="muted">' . esc($r['slug']) . '</small>',
            '기간'   => static fn (array $r): string => esc($r['duration'] ?? '-'),
            '확정'   => static fn (array $r): string => $r['is_confirmed'] ? '<span class="badge badge-done">확정</span>' : '<span class="badge badge-hold">확정 전</span>',
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 0, 'is_confirmed' => 0, 'sort_order' => 0];
    }
}
