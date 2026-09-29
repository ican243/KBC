<?php

namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * 교육과정 목록·상세
 */
class Courses extends BaseController
{
    public function index()
    {
        return $this->render('courses/index', '교육과정', [
            'courses' => (new CourseModel())->getPublished(),
        ], '속성 6주·기본 3개월·심화 6개월 과정 비교');
    }

    public function show(string $slug)
    {
        $model  = new CourseModel();
        $course = $model->findPublishedBySlug($slug) ?? throw PageNotFoundException::forPageNotFound();

        return $this->render('courses/show', $course['title'], [
            'course' => $course,
            'others' => array_filter($model->getPublished(), static fn (array $c): bool => $c['id'] !== $course['id']),
        ], (string) $course['summary']);
    }
}
