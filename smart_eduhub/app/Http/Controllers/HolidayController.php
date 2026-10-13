<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('type')->orderBy('date')->orderBy('day_of_month')->get();
        return view('holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:special,monthly',
            'date' => 'nullable|required_if:type,special|date',
            'day_of_month' => 'nullable|required_if:type,monthly|integer|min:1|max:31',
            'name' => 'required|string|max:255',
        ]);

        Holiday::create($request->all());

        return redirect()->route('holidays')->with('success', 'Hari libur berhasil ditambahkan!');
    }

    public function storeRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'name' => 'required|string|max:255',
        ]);

        Holiday::create([
            'type' => 'range',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'name' => $request->name,
        ]);

        return redirect()->route('holidays')->with('success', 'Hari libur range berhasil ditambahkan!');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'type' => 'required|in:special,monthly,range',
            'date' => 'nullable|required_if:type,special|date',
            'day_of_month' => 'nullable|required_if:type,monthly|integer|min:1|max:31',
            'start_date' => 'nullable|required_if:type,range|date',
            'end_date' => 'nullable|required_if:type,range|date|after_or_equal:start_date',
            'name' => 'required|string|max:255',
        ]);

        $holiday->update($request->all());

        return redirect()->route('holidays')->with('success', 'Hari libur berhasil diperbarui!');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays')->with('success', 'Hari libur berhasil dihapus!');
    }
}

