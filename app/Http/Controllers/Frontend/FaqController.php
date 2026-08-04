<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('frontend.faqs.index', compact('faqs'));
    }
}
