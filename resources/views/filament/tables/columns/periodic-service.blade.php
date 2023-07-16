<div>
    <span class="px-3 rounded-2xl {{ $getState() === 1 ? 'bg-success-500/10 text-success-700 text-sm font-medium' : 'bg-danger-500/10 text-danger-700 text-sm font-medium' }}">
        {{ $getState() === 1 ? 'Sim' : 'Não' }}
    </span>
</div>