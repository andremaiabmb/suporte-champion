<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->get('q', '');
        $table  = (new Faq)->getTable();

        $faqs = Faq::query()
            ->published()
            ->when($search !== '', function ($q) use ($search, $table) {
                $q->where(function ($w) use ($search, $table) {
                    if (Schema::hasColumn($table, 'question')) {
                        $w->where('question', 'like', "%{$search}%");
                    }
                    if (Schema::hasColumn($table, 'answer')) {
                        $w->orWhere('answer', 'like', "%{$search}%");
                    }
                });
            })
            // carrega os passos ordenados e já traz a contagem
            ->with(['steps' => fn ($q) => $q->orderBy('step_number')])
            ->withCount('steps')
            ->when(Schema::hasColumn($table, 'position'), fn ($q) => $q->orderBy('position'))
            ->when(!Schema::hasColumn($table, 'position'), function ($q) use ($table) {
                if (Schema::hasColumn($table, 'published_at')) {
                    $q->orderByDesc('published_at');
                }
                return $q->latest('updated_at');
            })
            ->paginate(20)
            ->withQueryString();

        return view('faqs.index', compact('faqs', 'search'));
    }

    public function show(Faq $faq)
    {
        if (empty($faq->published_at) || Carbon::parse($faq->published_at)->isFuture()) {
            abort(404);
        }

        // garante passos carregados e ordenados no show
        $faq->load(['steps' => fn ($q) => $q->orderBy('step_number')]);

        return view('faqs.show', compact('faq'));
    }
}
