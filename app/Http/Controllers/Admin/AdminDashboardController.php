<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $safeCount = function (string $table, ?callable $query = null) {
            try {
                if (! DB::getSchemaBuilder()->hasTable($table)) return 0;
                $builder = DB::table($table);
                if ($query) $builder = $query($builder);
                return (int) $builder->count();
            } catch (\Throwable $e) { return 0; }
        };

        $safeGet = function (string $table, array $columns = ['*'], int $limit = 5, ?callable $query = null) {
            try {
                if (! DB::getSchemaBuilder()->hasTable($table)) return collect();
                $builder = DB::table($table)->select($columns)->orderByDesc('id')->limit($limit);
                if ($query) $builder = $query($builder);
                return $builder->get();
            } catch (\Throwable $e) { return collect(); }
        };

        // Totais
        $data = [
            'total_users'        => $safeCount('users'),
            'total_events'       => $safeCount('events'),
            'total_posts'        => $safeCount('posts'),
            'total_faqs'         => $safeCount('faqs'),
            'total_supports'     => $safeCount('supports'),
            'total_sectors'      => $safeCount('sectors'),

            // KPIs Loja
            'store_orders_count' => $safeCount('store_orders'),
            'store_revenue_30d'  => $this->safeSum('store_orders', 'total', function ($q) {
                return $q->where('status', 'paid')->where('created_at', '>=', now()->subDays(30));
            }),

            // Atividade
            'active_users_30d'   => $safeCount('users', fn($q) => $q->where('last_login_at', '>=', now()->subDays(30))),
        ];

        // Listas recentes (tabelas podem não existir)
        $data['latest_users']  = $safeGet('users', ['id','name','email','created_at'], 6);
        $data['latest_posts']  = $safeGet('posts', ['id','title','published_at','updated_at'], 6);
        $data['latest_orders'] = $safeGet('store_orders', ['id','customer_name','total','status','created_at'], 6);

        return view('admin.dashboard.index', $data);
    }

    /** Soma segura (não quebra se tabela/coluna não existir) */
    private function safeSum(string $table, string $column, ?callable $query = null): float
    {
        try {
            if (! DB::getSchemaBuilder()->hasTable($table)) return 0.0;
            if (! DB::getSchemaBuilder()->hasColumn($table, $column)) return 0.0;
            $builder = DB::table($table);
            if ($query) $builder = $query($builder);
            return (float) ($builder->sum($column) ?? 0.0);
        } catch (\Throwable $e) { return 0.0; }
    }
}
