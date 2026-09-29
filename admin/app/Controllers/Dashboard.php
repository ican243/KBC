<?php

namespace App\Controllers;

use App\Models\ConsultInquiryModel;
use App\Models\NotificationLogModel;
use App\Models\PartnerInquiryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $consults = new ConsultInquiryModel();
        $partners = new PartnerInquiryModel();
        $today    = date('Y-m-d') . ' 00:00:00';

        return view('dashboard', [
            'title'          => '대시보드',
            'adminName'      => session('admin_name'),
            'newConsults'    => $consults->where('status', 'new')->countAllResults(),
            'newPartners'    => $partners->where('status', 'new')->countAllResults(),
            'todayCount'     => $consults->where('created_at >=', $today)->countAllResults()
                              + $partners->where('created_at >=', $today)->countAllResults(),
            'failedMails'    => (new NotificationLogModel())->recentFailures(7),
            'recentConsults' => $consults->filtered([])->findAll(5),
            'recentPartners' => $partners->filtered([])->findAll(5),
        ]);
    }
}
