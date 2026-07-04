<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosShift;
use App\Models\PosTransaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $shifts = PosShift::with('user')->latest('opened_at')->limit(30)->get();
        return view('pos.shifts.index', compact('shifts'));
    }

    public function showOpen(Request $request): View|RedirectResponse
    {
        if ($existing = $request->user()->currentShift()) {
            return redirect()->route('pos.shifts.index')->with('info', 'Kamu masih punya shift yang belum ditutup.');
        }
        return view('pos.shifts.open');
    }

    public function open(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'opening_cash' => ['required','numeric','min:0'],
            'opening_notes' => ['nullable','string','max:500'],
        ]);

        if ($request->user()->currentShift()) {
            return back()->with('info','Shift sudah terbuka.');
        }

        PosShift::create([
            'user_id' => $request->user()->id,
            'opening_cash' => $data['opening_cash'],
            'opened_at' => Carbon::now(),
            'status' => 'open',
            'opening_notes' => $data['opening_notes'] ?? null,
            'transaction_count' => 0,
            'total_sales' => 0,
        ]);

        return redirect()->route('pos.dashboard')->with('success','Shift dibuka. Selamat bertugas!');
    }

    public function close(Request $request, PosShift $shift): RedirectResponse
    {
        abort_if($shift->user_id !== $request->user()->id && ! $request->user()->isAdmin(), 403);
        abort_if($shift->status !== 'open', 400, 'Shift sudah tertutup.');

        $data = $request->validate([
            'closing_cash' => ['required','numeric','min:0'],
            'closing_notes' => ['nullable','string','max:500'],
        ]);

        DB::transaction(function () use ($shift, $data) {
            $locked = PosShift::lockForUpdate()->find($shift->id);
            $locked->recalculateTotals();
            $locked->update([
                'closing_cash'    => $data['closing_cash'],
                'cash_difference' => (float)$data['closing_cash'] - (float)$locked->expected_cash,
                'closed_at'       => Carbon::now(),
                'status'          => 'closed',
                'closing_notes'   => $data['closing_notes'] ?? null,
            ]);
        });

        return redirect()->route('pos.shifts.index')->with('success', 'Shift ditutup dengan sukses.');
    }

    public function show(PosShift $shift): View {
        $shift->load('transactions.cashier','transactions.items');
        return view('pos.shifts.show', compact('shift'));
    }
}
