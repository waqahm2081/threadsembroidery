<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\UniformSchoolAccess;
use Illuminate\View\View;

class UniformShopPageController extends Controller
{
    public function __construct(private UniformSchoolAccess $access)
    {
    }

    public function index(): View
    {
        $schools = School::query()
            ->where('is_active', true)
            ->withCount(['products as active_product_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('uniforms.index', [
            'pageTitle' => 'Uniform Shop | Threads Embroidery & Print',
            'pageDescription' => 'Browse school uniform shops. Enter your school PIN to view garments ready for embroidery and print.',
            'pageActive' => 'uniforms',
            'pageStyles' => ['pages.css', 'portal.css', 'uniform-shop.css'],
            'mount' => ['active' => 'uniforms'],
            'schools' => $schools,
        ]);
    }

    public function school(School $school): View
    {
        abort_unless($school->is_active, 404);

        return view('uniforms.school', [
            'pageTitle' => $school->name.' | Uniform Shop | Threads Embroidery & Print',
            'pageDescription' => 'Shop '.$school->name.' uniforms — enter your PIN to browse the school catalogue.',
            'pageActive' => 'uniforms',
            'pageStyles' => ['pages.css', 'portal.css', 'uniform-shop.css'],
            'pageScripts' => ['stripe-checkout.js', 'uniform-shop.js'],
            'mount' => false,
            'school' => $school,
            'unlocked' => $this->access->isUnlocked($school),
        ]);
    }
}
