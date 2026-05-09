@props(['currentStep' => 1, 'totalSteps' => 2])

<div class="flex items-center gap-2 justify-center">
    @for($i = 1; $i <= $totalSteps; $i++)
        <span class="px-3 py-1.5 rounded-full text-xs font-title font-bold
            @if($i === $currentStep) bg-coral text-cream
            @elseif($i < $currentStep) bg-coral/20 text-coral
            @else bg-muted text-muted-foreground
            @endif
        ">{{ $i }}</span>
    @endfor
</div>
