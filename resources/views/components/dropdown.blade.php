@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close();
            }

            this.open = true;

            this.$nextTick(() => {
                const trigger = this.$refs.button;
                const panel = this.$refs.panel;
                const rect = trigger.getBoundingClientRect();

                let newTop = rect.bottom;
                let newLeft;

                if ('{{ $align }}' === 'left') {
                    newLeft = rect.left;
                } else {
                    newLeft = rect.right - panel.offsetWidth;
                }

                // Prevent panel from going off-screen vertically
                if (newTop + panel.offsetHeight > window.innerHeight) {
                    newTop = rect.top - panel.offsetHeight;
                }

                // Prevent panel from going off-screen horizontally
                if (newLeft < 0) {
                    newLeft = 0;
                } else if (newLeft + panel.offsetWidth > window.innerWidth) {
                    newLeft = window.innerWidth - panel.offsetWidth;
                }

                panel.style.top = `${newTop}px`;
                panel.style.left = `${newLeft}px`;
            });
        },
        close(focusAfter) {
            this.open = false;
            if (focusAfter) {
                focusAfter.focus();
            }
        }
    }"
    @keydown.escape.prevent.stop="close($refs.button)"
    @focusin.window="! $refs.panel.contains($event.target) && close()"
    class="relative">

    <!-- Trigger -->
    <div x-ref="button" @click="toggle()">
        {{ $trigger }}
    </div>

    <!-- Panel -->
    <template x-teleport="body">
        <div x-ref="panel"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed z-50 {{ $width }} rounded-md shadow-lg"
            style="display: none;"
            @click.outside="close($refs.button)">
            <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
                {{ $content }}
            </div>
        </div>
    </template>
</div>
