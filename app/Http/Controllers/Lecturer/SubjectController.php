<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $lecturer = Auth::user();

        $subjects = $lecturer->subjects()
            ->paginate(10, ['*'], 'subjects_page');
            
        $subjectRequests = $lecturer->subjectRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'requests_page');

        return view('lecturer.subjects.index', compact('subjects', 'subjectRequests'));
    }

    public function createRequestForm()
    {
        return view('lecturer.subjects.request');
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        SubjectRequest::create([
            'lecturer_id' => Auth::id(),
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('lecturer.subjects.index')->with('status', 'Permintaan mata pelajaran baru telah terkirim dan sedang menunggu persetujuan admin.');
    }
}