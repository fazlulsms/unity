<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BoosterContribution;
use App\Models\BoosterPayment;
use App\Models\Member;
use Illuminate\Http\Request;

class BoosterContributionController extends Controller
{
    public function index()
    {
        $drives = BoosterContribution::withCount('members')->latest('period_date')->latest('id')->get();

        $totals = [
            'expected'  => 0.0,
            'deposited' => 0.0,
        ];
        foreach ($drives as $d) {
            $totals['expected']  += $d->total_expected;
            $totals['deposited'] += $d->total_deposited;
        }
        $totals['due'] = max(0.0, $totals['expected'] - $totals['deposited']);

        return view('admin.booster.index', compact('drives', 'totals'));
    }

    public function create()
    {
        $members = Member::with('user')->where('status', 'active')->orderBy('id')->get();
        return view('admin.booster.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                      => 'required|string|max:255',
            'period_date'                => 'required|date',
            'expected_amount_per_member' => 'required|numeric|min:0',
            'status'                     => 'required|in:active,closed',
            'remarks'                    => 'nullable|string|max:1000',
            'members'                    => 'nullable|array',
            'members.*'                  => 'exists:members,id',
        ]);

        $drive = BoosterContribution::create([
            'title'                      => $data['title'],
            'period_date'                => $data['period_date'],
            'expected_amount_per_member' => $data['expected_amount_per_member'],
            'status'                     => $data['status'],
            'remarks'                    => $data['remarks'] ?? null,
            'created_by'                 => auth()->id(),
        ]);

        $this->syncMembers($drive, $request->input('members', []), (float) $data['expected_amount_per_member']);

        AuditLog::record('booster_created', $drive, [], $drive->toArray());

        return redirect()->route('admin.booster.show', $drive)
            ->with('success', 'Booster Contribution drive created.');
    }

    public function show(BoosterContribution $booster)
    {
        $booster->load('creator');

        $rows = $booster->members()->with('user')->orderBy('id')->get()->map(function ($m) use ($booster) {
            $expected  = (float) $m->pivot->expected_amount;
            $deposited = $booster->depositedForMember($m->id);
            return [
                'member'    => $m,
                'expected'  => $expected,
                'deposited' => $deposited,
                'due'       => max(0.0, $expected - $deposited),
                'status'    => $deposited <= 0 ? 'due' : ($deposited >= $expected ? 'paid' : 'partial'),
            ];
        });

        $payments = $booster->payments()->with('member.user', 'creator')->latest('payment_date')->latest('id')->get();

        return view('admin.booster.show', compact('booster', 'rows', 'payments'));
    }

    public function edit(BoosterContribution $booster)
    {
        $members  = Member::with('user')->where('status', 'active')->orderBy('id')->get();
        $assigned = $booster->members()->pluck('members.id')->all();
        return view('admin.booster.edit', compact('booster', 'members', 'assigned'));
    }

    public function update(Request $request, BoosterContribution $booster)
    {
        $old  = $booster->toArray();
        $data = $request->validate([
            'title'                      => 'required|string|max:255',
            'period_date'                => 'required|date',
            'expected_amount_per_member' => 'required|numeric|min:0',
            'status'                     => 'required|in:active,closed',
            'remarks'                    => 'nullable|string|max:1000',
            'members'                    => 'nullable|array',
            'members.*'                  => 'exists:members,id',
        ]);

        $booster->update([
            'title'                      => $data['title'],
            'period_date'                => $data['period_date'],
            'expected_amount_per_member' => $data['expected_amount_per_member'],
            'status'                     => $data['status'],
            'remarks'                    => $data['remarks'] ?? null,
            'updated_by'                 => auth()->id(),
        ]);

        $this->syncMembers($booster, $request->input('members', []), (float) $data['expected_amount_per_member']);

        AuditLog::record('booster_updated', $booster, $old, $booster->fresh()->toArray());

        return redirect()->route('admin.booster.show', $booster)
            ->with('success', 'Booster Contribution drive updated.');
    }

    // ── Record a member payment toward a drive ───────────────────────────────

    public function storePayment(Request $request, BoosterContribution $booster)
    {
        $data = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'reference'      => 'nullable|string|max:255',
            'remarks'        => 'nullable|string|max:1000',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Member must be part of this drive.
        if (!$booster->members()->where('members.id', $data['member_id'])->exists()) {
            return back()->with('error', 'That member is not part of this Booster drive.')->withInput();
        }

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = $file->hashName();
            $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir      = $base . '/uploads/booster';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'booster/' . $filename;
        }

        $data['booster_contribution_id'] = $booster->id;
        $data['created_by']              = auth()->id();

        $payment = BoosterPayment::create($data);
        AuditLog::record('booster_payment_added', $payment, [], $payment->toArray());

        return redirect()->route('admin.booster.show', $booster)
            ->with('success', 'Booster payment recorded.');
    }

    private function syncMembers(BoosterContribution $drive, array $memberIds, float $defaultExpected): void
    {
        $sync = [];
        foreach ($memberIds as $id) {
            $sync[$id] = ['expected_amount' => $defaultExpected];
        }
        // Preserve any custom expected amounts already stored for members kept in the list.
        $existing = $drive->members()->pluck('expected_amount', 'members.id');
        foreach ($sync as $id => $row) {
            if (isset($existing[$id]) && (float) $existing[$id] > 0) {
                $sync[$id]['expected_amount'] = (float) $existing[$id];
            }
        }
        $drive->members()->sync($sync);
    }
}
