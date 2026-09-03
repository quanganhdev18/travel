<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InsuranceController extends Controller
{
    /**
     * Hiển thị trang Bảo hiểm du lịch (trang giới thiệu tĩnh)
     */
    public function index(): View
    {
        return view('frontend.insurance.index');
    }
}
