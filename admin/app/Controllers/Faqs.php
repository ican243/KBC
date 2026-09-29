<?php

namespace App\Controllers;

use App\Models\FaqModel;
use CodeIgniter\Model;

/**
 * 자주 묻는 질문 관리
 */
class Faqs extends ContentController
{
    protected string $path   = 'faqs';
    protected string $label  = 'FAQ';
    protected bool $sortable = true;

    /** 분류 입력 도움말 (기획안 2장: 수강·환불·연령 안내) */
    public const CATEGORIES = ['수강', '교습비·환불', '연령', '진로', '기타'];

    protected function model(): Model
    {
        return new FaqModel();
    }

    protected function rules(?int $id): array
    {
        return [
            'category'   => ['label' => '분류', 'rules' => 'permit_empty|max_length[50]'],
            'question'   => ['label' => '질문', 'rules' => 'required|max_length[255]'],
            'answer'     => ['label' => '답변', 'rules' => 'required|max_length[5000]'],
            'sort_order' => ['label' => '순서', 'rules' => 'permit_empty|integer'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        return [
            'category'     => self::nullable($post, 'category'),
            'question'     => trim($post['question']),
            'answer'       => trim($post['answer']),
            'is_published' => self::flag($post, 'is_published'),
            'sort_order'   => (int) ($post['sort_order'] ?? 0),
        ];
    }

    protected function columns(): array
    {
        return [
            '분류' => static fn (array $r): string => esc($r['category'] ?? '-'),
            '질문' => static fn (array $r): string => esc($r['question']),
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 1, 'sort_order' => 0];
    }
}
