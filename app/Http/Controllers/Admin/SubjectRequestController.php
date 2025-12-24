<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectRequestController extends Controller
{
    public function index()
    {
        $requests = SubjectRequest::with('lecturer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.subject_requests.index', compact('requests'));
    }

    public function approve(SubjectRequest $subject_request)
    {
        if ($subject_request->status !== 'pending') {
            return redirect()->route('admin.subject-requests.index')->with('error', 'Permintaan ini sudah diproses.');
        }

        DB::transaction(function () use ($subject_request) {
            // Create the new subject
            Subject::create([
                'name' => $subject_request->name,
                'code' => $subject_request->code,
                'user_id' => $subject_request->lecturer_id, // Assign the subject to the lecturer who requested it
            ]);

            // Update the request status
            $subject_request->status = 'approved';
            $subject_request->save();
        });

        return redirect()->route('admin.subject-requests.index')->with('status', 'Mata pelajaran telah berhasil dibuat dan permintaan disetujui.');
    }

    public function reject(Request $request, SubjectRequest $subject_request)
    {
        if ($subject_request->status !== 'pending') {
            return redirect()->route('admin.subject-requests.index')->with('error', 'Permintaan ini sudah diproses.');
        }
        
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $subject_request->status = 'rejected';
        $subject_request->admin_notes = $validated['admin_notes'];
        $subject_request->save();

        return redirect()->route('admin.subject-requests.index')->with('status', 'Permintaan telah ditolak.');
    }
}
