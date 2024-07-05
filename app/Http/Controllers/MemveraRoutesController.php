<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Lead\PipelineRepository;
use App\DataStructure\Lead\Models\Lead;
use App\DataStructure\Lead\Models\Pipeline;

class MemveraRoutesController extends Controller
{

    /**
     * Lead repository instance.
     *
     * @var \App\Repositories\Lead\LeadRepository
     */
    protected $leadRepository;

    /**
     * Pipeline repository instance.
     *
     * @var \App\Repositories\Lead\PipelineRepository
     */
    protected $pipelineRepository;

    public function __construct(
        LeadRepository $leadRepository,
        PipelineRepository $pipelineRepository,
    ) {
        $this->leadRepository = $leadRepository;
        $this->pipelineRepository = $pipelineRepository;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $pipelines = $this->pipelineRepository->all();
      
        $totalLeads = Lead::count();
        $todayLeads = Lead::whereDate('created_at', today())->count();
        $weeklyLeads = Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $leads = Lead::with('pipeline')->get();

        // Group the leads by pipeline
        $leadsPerPipeline = $leads->groupBy('lead_pipeline_id');
    
        // You can further transform the data as needed, for example:
        $formattedLeads = $leadsPerPipeline->map(function ($leads, $pipelineId) {
            $pipelineName = $leads->first()->pipeline->name;
            $leadCount = $leads->count();
            $totalLeads = Lead::count();
            $leadPercentage = ($totalLeads != 'undefined' && $totalLeads != 0) ? ($leadCount / $totalLeads) * 100 : 0;
            return [
                'pipeline_id' => $pipelineId,
                'pipeline_name' => $pipelineName,
                'lead_count' => $leadPercentage,
            ];
        })->toArray();

        if (empty($formattedLeads)) {
            $formattedLeads[] = [
                'pipeline_id' => null,
                'pipeline_name' => 'No Pipeline',
                'lead_count' => 100,
            ];
        }
        $labels = array_column($formattedLeads, 'pipeline_name');
        $data = array_column($formattedLeads, 'lead_count');

        return Inertia::render('dashboards/ecommerce/index', [
            'auth' => [ 'user' => $user ? $user->load('permissions') : null, ],
            'totalLeads' => $totalLeads,
            'todayLeads' => $todayLeads,
            'weeklyLeads' => $weeklyLeads,
            'labels' => $labels,
            'leadsPerPipeline' => $data,
        ]);
    }

    public function pages_starter() {
        return Inertia::render('pages/starter');
    }

    public function pages_maintenance() {
        return Inertia::render('pages/maintenance');
    }

    public function pages_coming_soon() {
        return Inertia::render('pages/coming-soon');
    }

    public function auth_signin_basic() {
        return Inertia::render('auth-pages/signin/basic');
    }

    public function auth_signin_cover() {
        return Inertia::render('auth-pages/signin/cover');
    }

    public function auth_signup_basic() {
        return Inertia::render('auth-pages/signup/basic');
    }

    public function auth_signup_cover() {
        return Inertia::render('auth-pages/signup/cover');
    }

    public function auth_reset_pwd_basic() {
        return Inertia::render('auth-pages/reset/basic');
    }

    public function auth_reset_pwd_cover() {
        return Inertia::render('auth-pages/reset/cover');
    }

    public function auth_create_pwd_basic() {
        return Inertia::render('auth-pages/create/basic');
    }

    public function auth_create_pwd_cover() {
        return Inertia::render('auth-pages/create/cover');
    }

    public function auth_lockscreen_basic() {
        return Inertia::render('auth-pages/lockscreen/basic');
    }

    public function auth_lockscreen_cover() {
        return Inertia::render('auth-pages/lockscreen/cover');
    }

    public function auth_twostep_basic() {
        return Inertia::render('auth-pages/twostep/basic');
    }

    public function auth_twostep_cover() {
        return Inertia::render('auth-pages/twostep/cover');
    }

    public function auth_404() {
        return Inertia::render('auth-pages/errors/404');
    }

    public function auth_500() {
        return Inertia::render('auth-pages/errors/500');
    }

    public function auth_404_basic() {
        return Inertia::render('auth-pages/errors/404-basic');
    }

    public function auth_404_cover() {
        return Inertia::render('auth-pages/errors/404-cover');
    }

    public function auth_ofline() {
        return Inertia::render('auth-pages/errors/ofline');
    }

    public function auth_logout_basic() {
        return Inertia::render('auth-pages/logout/basic');
    }

    public function auth_logout_cover() {
        return Inertia::render('auth-pages/logout/cover');
    }

    public function auth_success_msg_basic() {
        return Inertia::render('auth-pages/success-msg/basic');
    }

    public function auth_success_msg_cover() {
        return Inertia::render('auth-pages/success-msg/cover');
    }

}
