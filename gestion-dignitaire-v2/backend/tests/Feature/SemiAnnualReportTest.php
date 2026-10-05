<?php

namespace Tests\Feature;

use App\Mail\RapportPeriodiqueGenere;
use App\Models\Rapport;
use App\Support\Reports\ReportPeriodResolver;
use App\Support\Reports\SynthesisReportBuilder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SemiAnnualReportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_semester_boundaries_always_target_the_last_completed_semester(): void
    {
        $resolver = app(ReportPeriodResolver::class);

        [$firstStart, $firstEnd] = $resolver->previous('semestriel', Carbon::parse('2026-02-15'));
        $this->assertSame('2025-07-01', $firstStart->toDateString());
        $this->assertSame('2025-12-31', $firstEnd->toDateString());

        [$secondStart, $secondEnd] = $resolver->previous('semestriel', Carbon::parse('2026-10-05'));
        $this->assertSame('2026-01-01', $secondStart->toDateString());
        $this->assertSame('2026-06-30', $secondEnd->toDateString());
        $this->assertTrue($firstStart->isStartOfDay());
        $this->assertTrue($firstEnd->isEndOfDay());
    }

    public function test_semester_report_is_generated_archived_and_emailed(): void
    {
        Storage::fake('local');
        Mail::fake();
        Carbon::setTestNow('2026-10-05 10:00:00');

        try {
            $exitCode = Artisan::call('rapports:generer', ['--periode' => 'semestriel']);
            $this->assertSame(0, $exitCode, Artisan::output());

            $rapport = Rapport::query()->latest('id')->first();
            $this->assertNotNull($rapport);
            $this->assertSame('semestriel', $rapport->type);
            $this->assertSame('2026-01-01', $rapport->periode_debut->toDateString());
            $this->assertSame('2026-06-30', $rapport->periode_fin->toDateString());
            $this->assertStringStartsWith('rapport-semestriel-2026-01-01', $rapport->nom_fichier);
            Storage::disk('local')->assertExists($rapport->chemin_fichier);
            Mail::assertSent(RapportPeriodiqueGenere::class);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_subannual_diploma_count_is_not_fabricated_and_schedule_contains_semester(): void
    {
        $builder = app(SynthesisReportBuilder::class);
        $semester = $builder->buildData(Carbon::parse('2026-01-01'), Carbon::parse('2026-06-30')->endOfDay());
        $annual = $builder->buildData(Carbon::parse('2025-01-01'), Carbon::parse('2025-12-31')->endOfDay());

        $this->assertNull($semester['periode']['diplomesObtenus']);
        $this->assertIsInt($annual['periode']['diplomesObtenus']);

        Artisan::call('schedule:list');
        $this->assertStringContainsString('rapports:generer --periode=semestriel', Artisan::output());

        $this->assertSame(1, Artisan::call('rapports:generer', ['--periode' => 'invalide']));
        $this->assertStringContainsString('semestriel', Artisan::output());
    }
}
