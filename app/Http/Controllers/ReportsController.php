<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');
        $start = $request->query('start');
        $end = $request->query('end');
        $status = $request->query('status', 'all');

        if ($month) {
            $m = Carbon::parse($month.'-01');
            $start = $m->startOfMonth()->toDateString();
            $end = $m->endOfMonth()->toDateString();
        }

        $query = Loan::with('equipment');
        if ($start && $end) {
            $query->whereBetween('borrowed_at', [$start, $end]);
        }
        if ($status !== 'all') {
            $query->where('status', $status === 'active' ? 'active' : 'returned');
        }
        $loans = $query->orderBy('borrowed_at', 'desc')->get();

        return view('reports.index', [
            'loans' => $loans,
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'status' => $status,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->merge(['status' => $request->query('status', 'all')]);
        $data = $this->filteredData($request);
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['NO TRANSAKSI','TGL PINJAM','PEMINJAM','NIS','KEPERLUAN','ALAT','STATUS','DENDA']);
        foreach ($data as $l) {
            fputcsv($csv, [
                'TRX-'.Carbon::parse($l->borrowed_at)->format('Ymd').'-'.str_pad($l->id,3,'0',STR_PAD_LEFT),
                Carbon::parse($l->borrowed_at)->format('d/m/Y'),
                $l->student_name,
                $l->student_nis,
                $l->purpose,
                $l->equipment?->name,
                $l->status === 'returned' ? 'Selesai' : 'Aktif',
                $l->fine_amount,
            ]);
        }
        rewind($csv);
        $out = stream_get_contents($csv);
        return response($out)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="laporan.csv"');
    }

    public function printView(Request $request)
    {
        $data = $this->filteredData($request);
        return view('reports.print', ['loans' => $data]);
    }

    private function filteredData(Request $request)
    {
        $month = $request->query('month');
        $start = $request->query('start');
        $end = $request->query('end');
        $status = $request->query('status', 'all');
        if ($month) {
            $m = Carbon::parse($month.'-01');
            $start = $m->startOfMonth()->toDateString();
            $end = $m->endOfMonth()->toDateString();
        }
        $q = Loan::with('equipment');
        if ($start && $end) { $q->whereBetween('borrowed_at', [$start, $end]); }
        if ($status !== 'all') { $q->where('status', $status === 'active' ? 'active' : 'returned'); }
        return $q->orderBy('borrowed_at', 'desc')->get();
    }
}