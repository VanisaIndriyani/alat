<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Carbon::setLocale('id');
        $total = Equipment::count();
        $loaned = Equipment::where('status','loaned')->count();
        $available = Equipment::where('status','available')->count();
        $damaged = Equipment::where('status','damaged')->count();

        $lateLoans = Loan::whereNull('returned_at')
            ->where('planned_return_at','<', now()->toDateString())
            ->with('equipment')
            ->get()
            ->map(function ($loan) {
                $daysLate = now()->startOfDay()->diffInDays(Carbon::parse($loan->planned_return_at), false) * -1;
                $fine = max($daysLate, 0) * 1000; // Rp 1.000/hari
                return [
                    'student_name' => $loan->student_name,
                    'student_nis' => $loan->student_nis,
                    'planned_return_at' => $loan->planned_return_at,
                    'days_late' => $daysLate,
                    'fine' => $fine,
                    'equipment' => $loan->equipment?->name,
                ];
            });
        $fineTotal = $lateLoans->sum('fine');

        $recentLoans = Loan::with('equipment')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $labelsWeek = [];
        $seriesWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $labelsWeek[] = $d->translatedFormat('D');
            $seriesWeek[] = Loan::whereDate('borrowed_at', $d->toDateString())->count();
        }

        $labelsMonth = [];
        $seriesMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labelsMonth[] = $m->translatedFormat('M');
            $seriesMonth[] = Loan::whereBetween('borrowed_at', [$m->copy()->startOfMonth()->toDateString(), $m->copy()->endOfMonth()->toDateString()])->count();
        }

        return view('dashboard', [
            'total' => $total,
            'loaned' => $loaned,
            'available' => $available,
            'damaged' => $damaged,
            'lateLoans' => $lateLoans,
            'fineTotal' => $fineTotal,
            'recentLoans' => $recentLoans,
            'chartWeek' => ['labels' => $labelsWeek, 'series' => $seriesWeek],
            'chartMonth' => ['labels' => $labelsMonth, 'series' => $seriesMonth],
        ]);
    }
}