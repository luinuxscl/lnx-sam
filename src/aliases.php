<?php

/**
 * Compatibility aliases for the names the runtime had inside the host.
 *
 * The runtime lived in the application as `App\Ai\Sam` and its commands as
 * `App\Console\Commands\Sam*`. Instead of eagerly aliasing every class (which
 * would load the whole runtime and its host dependencies on every request),
 * the map is resolved on demand by an autoloader.
 *
 * The aliases are removed in the next major version.
 */
spl_autoload_register(static function (string $class): void {
    /** @var array<string, string> $aliases */
    static $aliases = [
        'App\Console\Commands\SamCapabilitiesCommand' => 'Luinuxscl\LnxSam\Console\Commands\SamCapabilitiesCommand',
        'App\Console\Commands\SamDescribeCommand' => 'Luinuxscl\LnxSam\Console\Commands\SamDescribeCommand',
        'App\Console\Commands\SamDoctorCommand' => 'Luinuxscl\LnxSam\Console\Commands\SamDoctorCommand',
        'App\Console\Commands\SamRunCommand' => 'Luinuxscl\LnxSam\Console\Commands\SamRunCommand',
        'App\Ai\Sam\LnxSamServiceProvider' => 'Luinuxscl\LnxSam\LnxSamServiceProvider',
        'App\Ai\Sam\SamAccess' => 'Luinuxscl\LnxSam\SamAccess',
        'App\Ai\Sam\SamAction' => 'Luinuxscl\LnxSam\SamAction',
        'App\Ai\Sam\SamAgent' => 'Luinuxscl\LnxSam\SamAgent',
        'App\Ai\Sam\SamAmbientSignals' => 'Luinuxscl\LnxSam\SamAmbientSignals',
        'App\Ai\Sam\SamBrief' => 'Luinuxscl\LnxSam\SamBrief',
        'App\Ai\Sam\SamBriefNarrativeAgent' => 'Luinuxscl\LnxSam\SamBriefNarrativeAgent',
        'App\Ai\Sam\SamBriefNarrativeVerifier' => 'Luinuxscl\LnxSam\SamBriefNarrativeVerifier',
        'App\Ai\Sam\SamBriefPresenter' => 'Luinuxscl\LnxSam\SamBriefPresenter',
        'App\Ai\Sam\SamCapability' => 'Luinuxscl\LnxSam\SamCapability',
        'App\Ai\Sam\SamCapabilityInspector' => 'Luinuxscl\LnxSam\SamCapabilityInspector',
        'App\Ai\Sam\SamCapabilityKind' => 'Luinuxscl\LnxSam\SamCapabilityKind',
        'App\Ai\Sam\SamCapabilityProvider' => 'Luinuxscl\LnxSam\SamCapabilityProvider',
        'App\Ai\Sam\SamContext' => 'Luinuxscl\LnxSam\SamContext',
        'App\Ai\Sam\SamContributor' => 'Luinuxscl\LnxSam\SamContributor',
        'App\Ai\Sam\SamFilterAnalysis' => 'Luinuxscl\LnxSam\SamFilterAnalysis',
        'App\Ai\Sam\SamInsight' => 'Luinuxscl\LnxSam\SamInsight',
        'App\Ai\Sam\SamInstructions' => 'Luinuxscl\LnxSam\SamInstructions',
        'App\Ai\Sam\SamListSignals' => 'Luinuxscl\LnxSam\SamListSignals',
        'App\Ai\Sam\SamMode' => 'Luinuxscl\LnxSam\SamMode',
        'App\Ai\Sam\SamModeResolver' => 'Luinuxscl\LnxSam\SamModeResolver',
        'App\Ai\Sam\SamPreferences' => 'Luinuxscl\LnxSam\SamPreferences',
        'App\Ai\Sam\SamPreferencesPresenter' => 'Luinuxscl\LnxSam\SamPreferencesPresenter',
        'App\Ai\Sam\SamProfile' => 'Luinuxscl\LnxSam\SamProfile',
        'App\Ai\Sam\SamProposal' => 'Luinuxscl\LnxSam\SamProposal',
        'App\Ai\Sam\SamProposalPresenter' => 'Luinuxscl\LnxSam\SamProposalPresenter',
        'App\Ai\Sam\SamProposalResult' => 'Luinuxscl\LnxSam\SamProposalResult',
        'App\Ai\Sam\SamProposalUndoer' => 'Luinuxscl\LnxSam\SamProposalUndoer',
        'App\Ai\Sam\SamRedactor' => 'Luinuxscl\LnxSam\SamRedactor',
        'App\Ai\Sam\SamRegistry' => 'Luinuxscl\LnxSam\SamRegistry',
        'App\Ai\Sam\SamReply' => 'Luinuxscl\LnxSam\SamReply',
        'App\Ai\Sam\SamRisk' => 'Luinuxscl\LnxSam\SamRisk',
        'App\Ai\Sam\SamRowSignals' => 'Luinuxscl\LnxSam\SamRowSignals',
        'App\Ai\Sam\SamRuntime' => 'Luinuxscl\LnxSam\SamRuntime',
        'App\Ai\Sam\SamSignal' => 'Luinuxscl\LnxSam\SamSignal',
        'App\Ai\Sam\SamTitle' => 'Luinuxscl\LnxSam\SamTitle',
        'App\Ai\Sam\SamTraceEntry' => 'Luinuxscl\LnxSam\SamTraceEntry',
        'App\Ai\Sam\SamTracePresenter' => 'Luinuxscl\LnxSam\SamTracePresenter',
        'App\Ai\Sam\SamTurn' => 'Luinuxscl\LnxSam\SamTurn',
        'App\Ai\Sam\SamTurnTrace' => 'Luinuxscl\LnxSam\SamTurnTrace',
        'App\Ai\Sam\Tools\SamDraftPreview' => 'Luinuxscl\LnxSam\Tools\SamDraftPreview',
        'App\Ai\Sam\Tools\SamProposeTool' => 'Luinuxscl\LnxSam\Tools\SamProposeTool',
        'App\Ai\Sam\Tools\SamReadResult' => 'Luinuxscl\LnxSam\Tools\SamReadResult',
        'App\Ai\Sam\Tools\SamReadTool' => 'Luinuxscl\LnxSam\Tools\SamReadTool',
        'App\Ai\Sam\Tools\SamTool' => 'Luinuxscl\LnxSam\Tools\SamTool',
    ];

    if (! isset($aliases[$class])) {
        return;
    }

    class_alias($aliases[$class], $class);
});
