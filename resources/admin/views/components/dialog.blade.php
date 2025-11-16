<template x-if="$store.{{ $dialog }}.show">
    <div x-data="{{ $dialog }}_component" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }" class="dialog" 
        x-bind:onload="dialogInit">
        <div class="dialog-wrapper">
            <div class="dialog-container">
                {{ $slot }}
            </div>
        </div>
    </div>
</template>
<script type="module">
    Alpine.data('{{ $dialog }}_component', () => ({
        loaded: false,
        dialogInit() {
            if (this.loaded) return;
            this.loaded = true;
            feather.replace();
            const target = this.$root.querySelector('.dialog-container');
            const firstChild = target.firstElementChild;
            let dialog = this.$store.{{ $dialog }};
            firstChild.style.width = dialog.attributes?.width;
            firstChild.style.maxWidth = dialog.attributes?.width;
            firstChild.style.height = dialog.attributes?.height;
            firstChild.style.maxHeight = dialog.attributes?.height;
            this.$store.libs.playAnimateOnLoad(target, () => {
                setTimeout(() => {
                    dialog.onReady({
                        data:dialog.data,
                        config: {
                            ...dialog.attributes,
                            zIndex: this.$root.style.zIndex,
                        }
                    });
                }, 100);
            });
            this.$store.{{ $dialog }}.target = target;
        }
    }));
    Alpine.store('{{ $dialog }}', {
        target: null,
        data: null,
        show: false,
        attributes: {},
        afterClosed: () => {},
        onReady: () => {},
        config(options) {
            const {
                width,
                height,
                onReady,
            } = options;
            this.onReady = onReady ?? (() => {});
            this.attributes = {
                width: width ?? '80vw',
                height: height,
            }
        },
        open(options) {
            this.data = options?.data;
            this.afterClosed = options?.afterClosed ?? null;
            this.show = true;
        },
        close(data = null) {
            Alpine.store('animate').leave(this.target, () => {
                this.show = false;
                if (typeof this.afterClosed === 'function') {
                    this.afterClosed(data);
                }
            });
        },
    });
</script>
