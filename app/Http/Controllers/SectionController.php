<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{    
    public function show(Section $section) {
        return $section->load('topics');
    }

    public function index() {
        return Section::all();
    }

    public function store() {
        $attr = request()->validate([
            'subject' => ['required', Rule::unique('sections', 'subject')]
        ]);
        $section = Section::create($attr);
        return $section;
    }

    public function update(Section $section) {
        $attr = request()->validate([
            'subject' => ['required', Rule::unique('sections', 'subject')]
        ]);
        $section->update($attr);
        return $section;
    }

    public function destroy(Section $section) {
        $section->delete();
    }
}
