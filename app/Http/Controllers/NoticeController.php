<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //In NoticeController.php
    public function index()
    {
        $notices = Notice::latest('published_at')->paginate(10);
        $categories = Notice::distinct()->pluck('categories');
        $departments = Notice::whereNotNull('department')->distinct()->pluck('department');

        return view('welcome', compact('notices', 'categories', 'departments'));
    }

    public function filter(Request $request)
    {
        $perPage = 8;
        $notices = Notice::when($request->categories != 'All', function ($q) use ($request) {
            $q->where('categories', $request->categories);
        })
            ->when($request->department != 'All', function ($q) use ($request) {
                $q->where('department', $request->department);
            })
            ->when($request->date, function ($q) use ($request) {
                $q->whereDate('published_at', $request->date);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->latest('published_at')
            ->paginate($perPage);

        return response()->json([
            'notices' => $notices->items(),
            'current_page' => $notices->currentPage(),
            'last_page' => $notices->lastPage(),
            'total' => $notices->total(),
            'per_page' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        //
    }
}
