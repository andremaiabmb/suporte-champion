<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqStep;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    /**
     * Listagem com busca e filtro de status.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = $request->get('status', 'all'); // all|published|draft

        $faqs = Faq::query()
            ->when($q, function ($query) use ($q) {
                $query->where('question', 'like', "%{$q}%")
                      ->orWhere('answer', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%");
            })
            ->when($status === 'published', fn($query) => $query->whereNotNull('published_at'))
            ->when($status === 'draft', fn($query) => $query->whereNull('published_at'))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.faqs.index', compact('faqs', 'q', 'status'));
    }

    public function create()
    {
        $faq = new Faq();
        return view('dashboard.faqs.create', compact('faq'));
    }

    public function store(StoreFaqRequest $request)
    {
        $data = $request->validated();

        // slug: se não vier, gerar a partir da pergunta (único)
        $slug = $data['slug'] ?? $this->uniqueSlug($data['question']);

        /** @var \App\Models\Faq $faq */
        $faq = DB::transaction(function () use ($request, $data, $slug) {
            $faq = Faq::create([
                'user_id'      => auth()->id(),
                'question'     => $data['question'],
                'slug'         => $slug,
                'answer'       => $data['answer'] ?? null,
                'published_at' => !empty($data['publish']) ? Carbon::now() : null,
            ]);

            $this->syncSteps($faq, $request);

            return $faq;
        });

        return redirect()
            ->route('admin.faqs.edit', $faq->getKey())
            ->with('status', 'FAQ criado com sucesso.');
    }

    public function show(Faq $faq)
    {
        $faq->load('steps');
        return view('dashboard.faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        $faq->load('steps');
        return view('dashboard.faqs.edit', compact('faq'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $data = $request->validated();

        // slug único (se alterado ou vazio)
        $slug = $data['slug'] ?? $faq->slug;
        if (!$slug || $slug !== $faq->slug) {
            $slug = $this->uniqueSlug($data['question'], $faq->getKey());
        }

        DB::transaction(function () use ($request, $data, $faq, $slug) {
            $faq->update([
                'question'     => $data['question'],
                'slug'         => $slug,
                'answer'       => $data['answer'] ?? null,
                'published_at' => !empty($data['publish']) ? ($faq->published_at ?? Carbon::now()) : null,
            ]);

            $this->syncSteps($faq, $request, true);
        });

        return redirect()
            ->route('admin.faqs.edit', $faq->getKey())
            ->with('status', 'FAQ atualizado com sucesso.');
    }

    public function destroy(Faq $faq)
    {
        // Apagar imagens dos steps antes (FK já deleta passos)
        foreach ($faq->steps()->get() as $step) {
            if ($step->image_path) {
                Storage::disk('public')->delete($step->image_path);
            }
        }
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ removido.');
    }

    /* ================= Helpers ================= */

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $i = 1;

        while (Faq::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))->exists()) {
            $slug = $original.'-'.$i++;
        }
        return $slug;
    }

    /**
     * Sincroniza passos com upload de imagens.
     * - Em edição ($isUpdate = true): remove imagens de passos excluídos.
     */
    private function syncSteps(Faq $faq, Request $request, bool $isUpdate = false): void
    {
        $stepsInput = $request->input('steps', []);
        $files = $request->file('steps', []);

        // Mapear imagens antigas (para limpeza se forem removidas)
        $oldImages = [];
        if ($isUpdate) {
            foreach ($faq->steps as $step) {
                if ($step->image_path) $oldImages[] = $step->image_path;
            }
        }

        // Limpar passos atuais e recriar seguindo a ordem enviada
        $faq->steps()->delete();

        $order = 1;
        foreach ($stepsInput as $idx => $row) {
            $title   = $row['title'] ?? null;
            $body    = $row['body'] ?? null;
            $caption = $row['image_caption'] ?? null;

            $imagePath = null;
            if (isset($files[$idx]['image']) && $files[$idx]['image']) {
                $imagePath = $files[$idx]['image']->store('faq_steps', 'public');
            } elseif (!empty($row['existing_image'])) {
                // Em edição, manter imagem existente se marcada
                $imagePath = $row['existing_image'];
            }

            $faq->steps()->create([
                'step_number'  => $order++,
                'title'        => $title,
                'body'         => $body,
                'image_path'   => $imagePath,
                'image_caption'=> $caption,
            ]);
        }

        // Remover imagens que existiam mas não estão mais associadas
        if ($isUpdate) {
            $currentImages = $faq->steps()->pluck('image_path')->filter()->all();
            $toDelete = array_diff($oldImages, $currentImages);
            foreach ($toDelete as $path) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
