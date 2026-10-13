<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointRule;
use Illuminate\Support\Facades\DB;

class PointRuleController extends Controller
{
    public function index()
    {
        $rules = DB::table('point_rules')->orderBy('sort_order')->get();
        return view('point-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('point-rules.create');
    }

    public function store(Request $request)
    {
        $data = [
            'name' => $request->name,
            'condition_type' => 'attendance_status',
            'attendance_status' => $request->attendance_status,
            'threshold' => $request->threshold,
            'point_type' => $request->point_type,
            'point_score' => $request->point_score,
            'reason_template' => $this->generateReason($request->attendance_status),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('point_rules')->insert($data);

        return redirect()->route('point-rules.index')->with('success', 'Peraturan poin berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $rule = DB::table('point_rules')->where('id', $id)->first();
        return view('point-rules.edit', compact('rule'));
    }

    public function update(Request $request, $id)
    {
        $data = [
            'name' => $request->name,
            'condition_type' => 'attendance_status',
            'attendance_status' => $request->attendance_status,
            'threshold' => $request->threshold,
            'point_type' => $request->point_type,
            'point_score' => $request->point_score,
            'reason_template' => $this->generateReason($request->attendance_status),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        DB::table('point_rules')->where('id', $id)->update($data);

        return redirect()->route('point-rules.index')->with('success', 'Peraturan poin berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('point_rules')->where('id', $id)->delete();
        return redirect()->route('point-rules.index')->with('success', 'Peraturan poin berhasil dihapus!');
    }

    private function generateReason($status)
    {
        $arr = [
            'late' => 'Terlambat {count} kali',
            'absent' => 'Alpa {count} kali',
            'present' => 'Hadir {count} hari',
            'excused' => 'Izin {count} hari',
        ];
        return $arr[$status] ?? 'Status tidak diketahui';
    }
}
